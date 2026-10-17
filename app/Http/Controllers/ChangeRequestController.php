<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\Client;
use App\Models\CrInvoice;
use App\Models\Pegawai;
use App\Models\SolutionPaper;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ChangeRequest::with(['client', 'solutionPaper', 'invoices']);

        $user = auth()->user();
        $role = $user?->role ?? 'guest';

        // Strict Client filter: client only sees their own CRs
        if ($role === 'client') {
            $query->where('user_id', $user->id);
        }

        // Presales dedicated dashboard redirect
        if ($role === 'presales' && ! $request->filled('view') && ! $request->filled('status') && ! $request->filled('search') && ! $request->filled('tab')) {
            return redirect()->route('presales.dashboard');
        }

        // PM Head Queue Rule:
        if ($role === 'pmh') {
            if ($request->status === 'awaiting_pm' || $request->status === 'diajukan') {
                $query->whereRaw('1 = 0');
            } elseif (! $request->filled('status')) {
                $query->whereNotIn('status', ['awaiting_pm', 'diajukan']);
            }
        }

        // Determine view mode
        $viewMode = $request->query('view');
        if (! $viewMode) {
            if ($request->status === 'development') {
                $viewMode = 'development';
            } elseif ($request->status === 'uat') {
                $viewMode = 'uat';
            } elseif ($request->status === 'golive') {
                $viewMode = 'golive';
            } elseif ($request->filled('search') || $request->filled('status') || $request->filled('tab')) {
                $viewMode = 'total';
            } else {
                $viewMode = 'dashboard';
            }
        }

        // Apply view-specific filters
        if ($viewMode === 'development') {
            $query->whereIn('status', ['development', 'dikerjakan']);
        } elseif ($viewMode === 'uat') {
            $query->whereIn('status', ['uat', 'sit']);
        } elseif ($viewMode === 'golive') {
            $query->whereIn('status', ['golive', 'selesai', 'invoicing']);
        } elseif ($viewMode === 'draft') {
            $query->where('status', 'draft');
        } elseif ($viewMode === 'total') {
            // All CRs - no status restriction
        } elseif ($viewMode === 'status') {
            // Ordered by pipeline stage
        }

        if ($request->filled('status') && !in_array(strtolower($request->status), ['total', 'all', 'semua', 'semua status'])) {
            $statusVal = strtolower($request->status);
            if (in_array($statusVal, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan', 'pending'])) {
                $query->whereIn('status', ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan']);
            } elseif (in_array($statusVal, ['analisa', 'analysis', 'dianalisis'])) {
                $query->whereIn('status', ['analisa', 'analysis', 'dianalisis']);
            } elseif (in_array($statusVal, ['development', 'develop', 'dikerjakan'])) {
                $query->whereIn('status', ['development', 'develop', 'dikerjakan']);
            } elseif (in_array($statusVal, ['uat', 'sit'])) {
                $query->whereIn('status', ['uat', 'sit']);
            } elseif (in_array($statusVal, ['golive', 'go-live', 'go_live'])) {
                $query->whereIn('status', ['golive', 'selesai', 'invoicing']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Category Tab Filter
        if ($request->filled('tab')) {
            match ($request->tab) {
                'pending' => $query->whereIn('status', ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'revision_needed', 'dianalisis']),
                'progress' => $query->whereIn('status', ['validated', 'disetujui', 'analisa', 'development', 'dikerjakan', 'sit', 'uat', 'training']),
                'payment' => $query->whereIn('status', ['golive', 'awaiting_golive_validation', 'invoicing']),
                'history' => $query->whereIn('status', ['golive', 'invoicing', 'selesai', 'ditolak', 'awaiting_golive_validation']),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('klien', 'like', "%{$search}%")
                  ->orWhere('kode_cr', 'like', "%{$search}%")
                  ->orWhere('proyek_terkait', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', in_array($viewMode, ['total', 'draft']) ? 10 : 15);
        if (!in_array($perPage, [5, 10, 20, 50, 100])) {
            $perPage = in_array($viewMode, ['total', 'draft']) ? 10 : 15;
        }

        if ($viewMode === 'draft') {
            $changeRequests = $query->orderBy('tanggal_pengajuan', 'asc')->orderBy('kode_cr', 'asc')->paginate($perPage)->withQueryString();
        } else {
            $changeRequests = $query->latest()->paginate($perPage)->withQueryString();
        }

        // Metrics for Figma Dashboard (4 Cards: Semua CR, Butuh Persetujuan, Development, GO LIVE)
        $cardQuery = ChangeRequest::query();
        if ($role === 'client') {
            $cardQuery->where('user_id', $user->id);
        }
        $figmaTotalCr = (clone $cardQuery)->count();
        $figmaNeedApprovalCr = (clone $cardQuery)->whereIn('status', ['awaiting_pm', 'diajukan', 'awaiting_pmh'])->count();
        $figmaDevCr = (clone $cardQuery)->whereIn('status', ['development', 'dikerjakan'])->count();
        $figmaUatCr = (clone $cardQuery)->whereIn('status', ['uat', 'sit'])->count();
        $figmaGoLiveCr = (clone $cardQuery)->whereIn('status', ['golive', 'selesai', 'invoicing'])->count();
        $figmaDraftCr = (clone $cardQuery)->where('status', 'draft')->count();

        // Status breakdown counts for Donut Chart & Progress bars
        $statusCounts = [
            'analisis'  => (clone $cardQuery)->whereIn('status', ['analisa', 'dianalisis', 'awaiting_pm', 'diajukan', 'awaiting_pmh'])->count(),
            'develop'   => (clone $cardQuery)->whereIn('status', ['development', 'dikerjakan'])->count(),
            'sit'       => (clone $cardQuery)->where('status', 'sit')->count(),
            'uat'       => (clone $cardQuery)->where('status', 'uat')->count(),
            'deploying' => (clone $cardQuery)->whereIn('status', ['training', 'awaiting_golive_validation'])->count(),
            'golive'    => (clone $cardQuery)->whereIn('status', ['golive', 'selesai', 'invoicing'])->count(),
        ];
        $totalStatusItems = array_sum($statusCounts) ?: 1;

        $statistik = ChangeRequest::query();
        if ($role === 'client') {
            $statistik->where('user_id', $user->id);
        }
        $statistik = $statistik->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ongoingStatuses = ['awaiting_pm', 'awaiting_pmh', 'validated', 'analisa', 'development', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'diajukan', 'dianalisis', 'disetujui', 'dikerjakan'];
        $ongoingTotal = $statistik->only($ongoingStatuses)->sum();
        $maxStat = max($statistik->toArray() ?: [0]);

        $totalBiaya = ChangeRequest::query()
            ->when($role === 'client', fn ($q) => $q->where('user_id', $user->id))
            ->sum('biaya_pengerjaan');

        $roleMetrics = [
            'total' => $figmaTotalCr,
            'in_dev' => $figmaDevCr,
            'uat' => $figmaUatCr,
            'golive' => $figmaGoLiveCr,
            'draft_count' => $figmaDraftCr,
        ];

        // ── Data Request dari PM (Hari Ini, Kemarin, dan Hari-hari Sebelumnya) ──
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $pmRequestsToday = ChangeRequest::whereDate('created_at', $today)->count();
        $pmRequestsYesterday = ChangeRequest::whereDate('created_at', $yesterday)->count();

        // 7 Hari Terakhir untuk grafik garis / area request harian dari PM
        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $count = ChangeRequest::whereDate('created_at', $d)->count();
            $dailyTrend[] = [
                'date' => $d->format('Y-m-d'),
                'label' => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Kemarin' : $d->translatedFormat('d M')),
                'short_label' => $d->format('d/m'),
                'count' => $count,
            ];
        }

        // Jika tidak ada data dalam 7 hari terakhir kalender hari ini, ambil 7 tanggal transaksi terakhir dari database agar grafik dinamis & akurat
        $totalDailyCount = array_sum(array_column($dailyTrend, 'count'));
        if ($totalDailyCount === 0) {
            $lastCrDate = ChangeRequest::latest('created_at')->value('created_at');
            if ($lastCrDate) {
                $refDate = Carbon::parse($lastCrDate);
                $dailyTrend = [];
                for ($i = 6; $i >= 0; $i--) {
                    $d = (clone $refDate)->subDays($i);
                    $c = ChangeRequest::whereDate('created_at', $d->toDateString())->count();
                    $dailyTrend[] = [
                        'date' => $d->format('Y-m-d'),
                        'label' => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Kemarin' : $d->format('d/m')),
                        'short_label' => $d->format('d/m'),
                        'count' => $c,
                    ];
                }
                $pmRequestsToday = ChangeRequest::whereDate('created_at', $refDate->toDateString())->count();
                $pmRequestsYesterday = ChangeRequest::whereDate('created_at', (clone $refDate)->subDay()->toDateString())->count();
            }
        }

        // Notifikasi Terkini Dinamis dari Database untuk Dashboard
        $notifPendingCount = ChangeRequest::whereIn('status', ['awaiting_pmh', 'diajukan', 'analisa', 'dianalisis'])->count();
        $notifNeedVerifCount = ChangeRequest::whereIn('status', ['diajukan', 'analisa', 'dianalisis'])->count();
        $notifDevCount = ChangeRequest::whereIn('status', ['development', 'dikerjakan', 'uat', 'sit'])->count();
        $notifGoLiveCount = ChangeRequest::whereIn('status', ['golive', 'selesai', 'validated'])->count();

        // Monthly trend dataset for PM Dashboard (Diagram Batang)
        $defaultMonthlyTrend = [
            ['month' => 'Jan', 'masuk' => 3, 'keluar' => 0, 'batal' => 0],
            ['month' => 'Feb', 'masuk' => 4, 'keluar' => 1, 'batal' => 0],
            ['month' => 'Mar', 'masuk' => 1, 'keluar' => 1, 'batal' => 1],
            ['month' => 'Apr', 'masuk' => 5, 'keluar' => 2, 'batal' => 0],
            ['month' => 'Mei', 'masuk' => 3, 'keluar' => 1, 'batal' => 1],
            ['month' => 'Jun', 'masuk' => 4, 'keluar' => 2, 'batal' => 0],
        ];

        $pmMonthlyTrend = [];
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun'
        ];
        foreach ($months as $mNum => $mLabel) {
            $masuk = ChangeRequest::whereMonth('created_at', $mNum)->count();
            $keluar = ChangeRequest::whereMonth('created_at', $mNum)->whereIn('status', ['golive', 'selesai', 'invoicing'])->count();
            $batal = ChangeRequest::whereMonth('created_at', $mNum)->whereIn('status', ['ditolak', 'cancelled'])->count();
            $pmMonthlyTrend[] = [
                'month' => $mLabel,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'batal' => $batal,
            ];
        }

        return view('cr.index', [
            'changeRequests'       => $changeRequests,
            'viewMode'             => $viewMode,
            'figmaTotalCr'         => $figmaTotalCr,
            'figmaNeedApprovalCr'  => $figmaNeedApprovalCr,
            'figmaDevCr'           => $figmaDevCr,
            'figmaUatCr'           => $figmaUatCr,
            'figmaGoLiveCr'        => $figmaGoLiveCr,
            'pmRequestsToday'      => $pmRequestsToday,
            'pmRequestsYesterday'  => $pmRequestsYesterday,
            'dailyTrend'           => $dailyTrend,
            'statusCounts'         => $statusCounts,
            'totalStatusItems'     => $totalStatusItems,
            'statistik'            => $statistik,
            'statusList'           => ChangeRequest::statusList(),
            'ongoingTotal'         => $ongoingTotal,
            'maxStat'              => $maxStat ?: 1,
            'totalBiaya'           => $totalBiaya,
            'user'                 => $user,
            'roleMetrics'          => $roleMetrics,
            'notifPendingCount'    => $notifPendingCount,
            'notifNeedVerifCount'  => $notifNeedVerifCount,
            'notifDevCount'        => $notifDevCount,
            'notifGoLiveCount'     => $notifGoLiveCount,
            'pmMonthlyTrend'       => $pmMonthlyTrend,
        ]);
    }

    /**
     * PM Dev Board — tampilan pipeline semua CR aktif (analisa s.d. golive)
     */
    public function devboard(Request $request)
    {
        $activeStatuses = ['analisa', 'development', 'sit', 'uat', 'training', 'golive'];

        $query = ChangeRequest::with(['client', 'solutionPaper'])
            ->whereIn('status', $activeStatuses);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_cr', 'like', "%{$search}%")
                  ->orWhere('klien', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $changeRequests = $query->latest()->paginate(15)->withQueryString();

        $stageStats = ChangeRequest::selectRaw('status, count(*) as total')
            ->whereIn('status', $activeStatuses)
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdueCount = ChangeRequest::whereIn('status', $activeStatuses)
            ->whereNotNull('target_selesai')
            ->where('target_selesai', '<', now()->startOfDay())
            ->count();

        return view('cr.dashboard.devboard', [
            'changeRequests' => $changeRequests,
            'stageStats'     => $stageStats,
            'overdueCount'   => $overdueCount,
            'statusList'     => ChangeRequest::statusList(),
            'activeStatuses' => $activeStatuses,
        ]);
    }

    /**
     * Profil Klien — Melihat data profil instansi dan 3 PIC master klien
     */
    public function profile()
    {
        $user = auth()->user();
        $clientRecord = Client::where('email', $user->email)->first();

        if (!$clientRecord) {
            $clientRecord = Client::where('name', 'like', "%{$user->name}%")->first();
        }
        
        $recentCRs = [];
        if ($user->isClient() && $clientRecord) {
            $recentCRs = ChangeRequest::where('client_id', $clientRecord->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } elseif ($user->isPm()) {
            $recentCRs = ChangeRequest::where('pic_sales', $user->id)
                ->orWhere('owner_cr', $user->name)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } else {
            $recentCRs = ChangeRequest::orderBy('created_at', 'desc')->take(5)->get();
        }

        return view('client.profile', compact('user', 'clientRecord', 'recentCRs'));
    }

    /**
     * Update Password Klien dari Halaman Profil
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required'],
            'password'     => ['required', 'string', 'min:6'],
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'password.required'     => 'Password baru wajib diisi.',
            'password.min'          => 'Password baru minimal 6 karakter.',
        ]);

        $user = auth()->user();
        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Password lama yang dimasukkan tidak sesuai.');
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * WORKFLOW 1: DUAL-PATH CR SUBMISSION
     */
    public function create()
    {
        $user = auth()->user();
        $isClient = $user->isClient();
        $isPm = $user->isPm() || $user->isAdmin();

        $clientRecord = null;
        $clients = Client::where('active', true)->orderBy('company')->get();
        $figmaClients = $clients->pluck('company')->toArray();
        $pegawais = Pegawai::where('is_active', true)->orderBy('name')->get();
        $nextKodeCr = ChangeRequest::generateKode();

        if ($isClient) {
            $clientRecord = Client::where('email', $user->email)->first();
        }

        return view('cr.create', compact('clientRecord', 'clients', 'figmaClients', 'pegawais', 'isClient', 'isPm', 'nextKodeCr'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isClient = $user->isClient();

        // Harmonize field aliases from Figma form
        $input = $request->all();
        if ($request->input('action') === 'draft') {
            if (empty($input['judul']) && !$request->filled('cr_name')) {
                $input['judul'] = 'Draft Pengajuan CR (' . date('d M Y H:i') . ')';
            }
            if (empty($input['proyek_terkait']) && !$request->filled('nama_project')) {
                $input['proyek_terkait'] = 'Draft Proyek CR';
            }
        }
        if ($request->filled('cr_name') && ! $request->filled('judul')) {
            $input['judul'] = $request->input('cr_name');
        }
        if ($request->filled('nama_project') && ! $request->filled('proyek_terkait')) {
            $input['proyek_terkait'] = $request->input('nama_project');
        }
        if ($request->filled('cr_owner') && ! $request->filled('owner_cr')) {
            $input['owner_cr'] = $request->input('cr_owner');
        }
        if ($request->filled('nama_pic') && ! $request->filled('pic_sales')) {
            $input['pic_sales'] = $request->input('nama_pic');
        }
        if ($request->filled('date')) {
            $timestamp = strtotime($request->input('date'));
            if ($timestamp) {
                $input['bap_date'] = date('Y-m-d', $timestamp);
                $input['tanggal_pengajuan'] = date('Y-m-d', $timestamp);
            }
        }
        if ($request->filled('request_date') && ! $request->filled('target_selesai')) {
            $timestamp = strtotime($request->input('request_date'));
            $input['target_selesai'] = $timestamp ? date('Y-m-d', $timestamp) : $request->input('request_date');
        }
        if ($request->filled('target_golive') && ! $request->filled('target_selesai')) {
            $timestamp = strtotime($request->input('target_golive'));
            $input['target_selesai'] = $timestamp ? date('Y-m-d', $timestamp) : $request->input('target_golive');
        }
        if ($request->filled('bap') && ! $request->filled('bap_date')) {
            $input['bap_date'] = $request->input('bap');
        }
        if ($request->filled('detail_cr') && ! $request->filled('deskripsi')) {
            $input['deskripsi'] = $request->input('detail_cr');
        }
        if ($request->filled('deskripsi_cr') && ! $request->filled('deskripsi')) {
            $input['deskripsi'] = $request->input('deskripsi_cr');
        }
        if (empty($input['deskripsi'])) {
            $input['deskripsi'] = "Kebutuhan penambahan dan integrasi sistem sesuai pengajuan Change Request.";
        }
        if ($request->filled('cr_notes') && ! $request->filled('catatan_pengajuan')) {
            $input['catatan_pengajuan'] = $request->input('cr_notes');
        }
        if ($request->filled('nama_perusahaan') && ! $request->filled('klien')) {
            $input['klien'] = $request->input('nama_perusahaan');
        }
        if (! isset($input['tanggal_pengajuan'])) {
            $input['tanggal_pengajuan'] = now()->toDateString();
        }

        $request->merge($input);

        $rules = [
            'judul'               => 'required|string|max:255',
            'proyek_terkait'      => 'required|string|max:255',
            'deskripsi'           => 'required|string',
            'alasan'              => 'nullable|string',
            'catatan_pengajuan'   => 'nullable|string|max:5000',
            'lampiran_pengajuan'  => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,zip|max:20480',
            'dokumen_fsd'         => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,zip|max:20480',
            'tanggal_pengajuan'   => 'nullable|date',
            'bap_date'            => 'nullable|date',
            'prioritas'           => 'nullable|string|max:50',
            'google_drive_url'    => 'nullable|url|max:2048',
            'target_selesai'      => 'nullable|date',
            'pic_sales'           => 'nullable|string|max:255',
            'pm_name'             => 'nullable|string|max:255',
            'client_id'           => 'nullable',
            'klien'               => 'nullable|string|max:255',
            'owner_cr'            => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        // Map prioritas
        $prioritasInput = strtolower($validated['prioritas'] ?? 'normal');
        if (in_array($prioritasInput, ['urgent', 'darurat', 'kritis'])) {
            $validated['prioritas'] = 'kritis';
        } else {
            $validated['prioritas'] = 'normal';
        }

        // File upload handling
        $file = $request->file('lampiran_pengajuan') ?? $request->file('dokumen_fsd');
        if ($file) {
            $validated['lampiran_pengajuan'] = $file->store('change-request-submissions', 'public');
        }

        $validated['kode_cr'] = ChangeRequest::generateKode();
        $validated['tanggal_pengajuan'] = $validated['tanggal_pengajuan'] ?? now()->toDateString();
        $validated['user_id'] = $user->id;

        // Resolve client company name
        if (! empty($validated['client_id']) && is_numeric($validated['client_id'])) {
            $clientObj = Client::find($validated['client_id']);
            if ($clientObj) {
                $validated['klien'] = $clientObj->company;
            }
        }
        if (empty($validated['klien'])) {
            $validated['klien'] = $request->input('nama_perusahaan') ?: ($user->client?->company ?: ($user->name ?? 'Klien'));
        }

        if (empty($validated['owner_cr'])) {
            $validated['owner_cr'] = $user->name ?? 'Pemohon CR';
        }

        $isDraft = ($request->input('action') === 'draft' || $request->input('status') === 'draft');
        $validated['status'] = $isDraft ? 'draft' : 'diajukan';

        $changeRequest = ChangeRequest::create($validated);

        if ($isDraft) {
            return redirect()->route('change-requests.index', ['view' => 'draft'])
                ->with('success', 'Draft Change Request ' . $changeRequest->kode_cr . ' berhasil disimpan.');
        }

        // Notify PM and Admin on real submission
        Notification::sendNotification([
            'target_role'       => 'pm',
            'change_request_id' => $changeRequest->id,
            'title'             => 'Pengajuan CR Baru Masuk',
            'message'           => ($changeRequest->klien ?? 'Klien') . " telah mengajukan {$changeRequest->kode_cr}: \"{$changeRequest->judul}\". Menunggu verifikasi awal Project Manager.",
            'type'              => 'warning',
            'category'          => 'cr_submitted',
            'action_url'        => route('change-requests.show', $changeRequest->id),
        ]);

        return redirect()->route('change-requests.index')
            ->with('success', 'Change Request ' . $changeRequest->kode_cr . ' berhasil diajukan.');
    }

    public function show(ChangeRequest $changeRequest)
    {
        $this->ensureClientOwnsRequest($changeRequest);

        $changeRequest->load(['client', 'solutionPaper.pegawai', 'invoices.creator']);
        $user = auth()->user();

        if ($user?->isPmh()) {
            return view('pmhead.review', compact('changeRequest'));
        }

        $isClient = $user?->isClient();
        $pegawais = Pegawai::where('is_active', true)->get();

        return view('cr.show', compact('changeRequest', 'isClient', 'pegawais'));
    }

    /**
     * PM / PMH / Internal Edit Form & Draft CR Edit
     */
    public function edit(ChangeRequest $changeRequest)
    {
        $user = auth()->user();

        // For draft CRs, open submission form in edit mode
        if ($changeRequest->status === 'draft') {
            if ($user?->isClient() && $changeRequest->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk mengedit draft ini.');
            }
            $changeRequest->load(['client', 'solutionPaper']);
            $clients = Client::where('active', true)->orderBy('company')->get();
            $pegawais = Pegawai::where('is_active', true)->orderBy('name')->get();

            return view('cr.create', [
                'changeRequest' => $changeRequest,
                'clients'       => $clients,
                'pegawais'      => $pegawais,
                'isEdit'        => true,
            ]);
        }

        abort_if($user->isClient(), 403, 'Klien tidak diizinkan mengakses menu ini. Gunakan fitur Edit Klien.');

        $changeRequest->load(['client', 'solutionPaper']);
        $clients = Client::where('active', true)->orderBy('company')->get();
        $pegawais = Pegawai::where('is_active', true)->orderBy('name')->get();

        return view('cr.edit', compact('changeRequest', 'clients', 'pegawais'));
    }

    /**
     * WORKFLOW 2 & 3: PM VERIFICATION & PM HEAD VALIDATION & MONITORING UPDATE
     */
    public function update(Request $request, ChangeRequest $changeRequest)
    {
        $user = auth()->user();

        // Handle Draft CR updates
        if ($changeRequest->status === 'draft') {
            $input = $request->all();
            if ($request->filled('cr_name') && ! $request->filled('judul')) {
                $input['judul'] = $request->input('cr_name');
            }
            if ($request->filled('nama_project') && ! $request->filled('proyek_terkait')) {
                $input['proyek_terkait'] = $request->input('nama_project');
            }
            if ($request->filled('nama_perusahaan') && ! $request->filled('klien')) {
                $input['klien'] = $request->input('nama_perusahaan');
            }
            if ($request->filled('cr_owner') && ! $request->filled('owner_cr')) {
                $input['owner_cr'] = $request->input('cr_owner');
            }
            if ($request->filled('nama_pic') && ! $request->filled('pic_sales')) {
                $input['pic_sales'] = $request->input('nama_pic');
            }
            if ($request->filled('detail_cr') && ! $request->filled('deskripsi')) {
                $input['deskripsi'] = $request->input('detail_cr');
            }
            if ($request->filled('deskripsi_cr') && ! $request->filled('deskripsi')) {
                $input['deskripsi'] = $request->input('deskripsi_cr');
            }
            if (empty($input['deskripsi'])) {
                $input['deskripsi'] = $changeRequest->deskripsi ?: "Kebutuhan penambahan dan integrasi sistem sesuai pengajuan Change Request.";
            }
            if ($request->filled('cr_notes') && ! $request->filled('catatan_pengajuan')) {
                $input['catatan_pengajuan'] = $request->input('cr_notes');
            }
            if ($request->filled('prioritas')) {
                $p = strtolower($request->input('prioritas'));
                $input['prioritas'] = in_array($p, ['urgent', 'kritis', 'tinggi']) ? 'kritis' : 'normal';
            }

            $isSubmit = $request->has('submit_cr') || $request->input('action') === 'submit';
            $changeRequest->update([
                'judul'             => $input['judul'] ?? $changeRequest->judul,
                'proyek_terkait'    => $input['proyek_terkait'] ?? $changeRequest->proyek_terkait,
                'deskripsi'         => $input['deskripsi'],
                'klien'             => $input['klien'] ?? $changeRequest->klien,
                'owner_cr'          => $input['owner_cr'] ?? $changeRequest->owner_cr,
                'pic_sales'         => $input['pic_sales'] ?? $changeRequest->pic_sales,
                'catatan_pengajuan' => $input['catatan_pengajuan'] ?? $changeRequest->catatan_pengajuan,
                'prioritas'         => $input['prioritas'] ?? $changeRequest->prioritas,
                'status'            => $isSubmit ? 'diajukan' : 'draft',
            ]);

            if ($isSubmit) {
                return redirect()->route('change-requests.index')
                    ->with('success', 'Change Request ' . $changeRequest->kode_cr . ' berhasil diajukan.');
            }

            return redirect()->route('change-requests.index', ['view' => 'draft'])
                ->with('success', 'Draft CR ' . $changeRequest->kode_cr . ' berhasil disimpan.');
        }

        abort_if($user->isClient(), 403, 'Akses ditolak.');

        $role = $user->role;

        // PM HEAD VALIDATION ACTION (APPROVE / REJECT)
        if (in_array($role, ['pmh', 'admin'], true) && $request->has('pmh_action')) {
            $action = $request->input('pmh_action'); // 'approve' or 'reject'

            // Aturan Utama: Verifikasi oleh PM harus diselesaikan terlebih dahulu sebelum sistem mengizinkan Validasi oleh PM Head (PMH)
            if (in_array($changeRequest->status, ['awaiting_pm', 'diajukan'], true)) {
                return back()->with('error', 'Validasi ditolak. Verifikasi oleh PM harus diselesaikan terlebih dahulu sebelum sistem mengizinkan validasi oleh PM Head.');
            }

            if ($action === 'reject') {
                // Strict Validation Rule: PM Head MUST fill out Reject Reason (Mandatory)
                $validated = $request->validate([
                    'reject_reason' => 'required|string|min:5|max:2000',
                ], [
                    'reject_reason.required' => 'Alasan penolakan (Reject Reason) wajib diisi jika PM Head menolak CR.',
                    'reject_reason.min' => 'Alasan penolakan minimal 5 karakter.',
                ]);

                // Routing Logic: Routed back to assigned PM for revision (Status: revision_needed)
                $changeRequest->update([
                    'status' => 'revision_needed',
                    'reject_reason' => $validated['reject_reason'],
                    'catatan_revisi' => $validated['reject_reason'],
                    'direvisi_pada' => now(),
                    'pmh_approved_at' => null,
                ]);

                // Notify PM
                Notification::sendNotification([
                    'target_role'       => 'pm',
                    'change_request_id' => $changeRequest->id,
                    'title'             => 'CR Dikembalikan oleh PM Head',
                    'message'           => "PM Head menolak {$changeRequest->kode_cr} dan meminta revisi. Alasan: {$validated['reject_reason']}",
                    'type'              => 'warning',
                    'category'          => 'cr_rejection',
                    'action_url'        => route('change-requests.show', $changeRequest->id),
                ]);

                return redirect()->route('change-requests.index')
                    ->with('warning', 'CR berhasil ditolak dan dikembalikan ke PM dengan status Revision Needed.')
                    ->with('cr_action_type', 'reject')
                    ->with('cr_code', $changeRequest->kode_cr)
                    ->with('cr_title', $changeRequest->judul);
            }

            if ($action === 'approve') {
                // If Approved: Reason is optional. Status becomes Validated.
                $validated = $request->validate([
                    'catatan_approval' => 'nullable|string|max:2000',
                ]);

                $changeRequest->update([
                    'status' => 'validated',
                    'catatan_approval' => $validated['catatan_approval'] ?? 'Disetujui oleh PM Head',
                    'pmh_approved_by' => $user->name,
                    'pmh_approved_at' => now(),
                    'reject_reason' => null,
                ]);

                // Notify Client, PM, and Presales
                Notification::sendNotification([
                    'user_id'           => $changeRequest->user_id,
                    'target_role'       => 'client',
                    'change_request_id' => $changeRequest->id,
                    'title'             => 'CR Divalidasi oleh PM Head',
                    'message'           => "Change Request {$changeRequest->kode_cr} ({$changeRequest->judul}) telah disetujui dan divalidasi oleh PM Head.",
                    'type'              => 'success',
                    'category'          => 'cr_approval',
                    'action_url'        => route('change-requests.show', $changeRequest->id),
                ]);

                Notification::sendNotification([
                    'target_role'       => 'pm',
                    'change_request_id' => $changeRequest->id,
                    'title'             => 'CR Divalidasi oleh PM Head',
                    'message'           => "Change Request {$changeRequest->kode_cr} ({$changeRequest->judul}) telah disetujui oleh PM Head.",
                    'type'              => 'success',
                    'category'          => 'cr_approval',
                    'action_url'        => route('change-requests.show', $changeRequest->id),
                ]);

                Notification::sendNotification([
                    'target_role'       => 'presales',
                    'change_request_id' => $changeRequest->id,
                    'title'             => 'CR Baru Siap Diberikan Penawaran (Quotation)',
                    'message'           => "Change Request {$changeRequest->kode_cr} ({$changeRequest->judul}) telah disetujui PM Head dan siap diproses quotation oleh Presales.",
                    'type'              => 'info',
                    'category'          => 'cr_approval',
                    'action_url'        => route('presales.review', $changeRequest->id),
                ]);

                return redirect()->route('change-requests.index')
                    ->with('success', 'CR berhasil divalidasi oleh PM Head.')
                    ->with('cr_action_type', 'approve')
                    ->with('cr_code', $changeRequest->kode_cr)
                    ->with('cr_title', $changeRequest->judul);
            }

            // PM Head Go-Live Validation
            if ($action === 'validate_golive') {
                $changeRequest->update([
                    'status' => 'golive',
                    'golive_validated_at' => now(),
                    'golive_validated_by' => $user->name,
                    'actual_completion_date' => $changeRequest->actual_completion_date ?: now(),
                ]);

                return redirect()->route('change-requests.show', $changeRequest)
                    ->with('success', 'Go-Live berhasil divalidasi oleh PM Head! Data CR otomatis dilempar ke bagian Marketing / Keuangan untuk pencatatan invoice & dokumen pendukung.');
            }
        }

        // PM VERIFICATION & MONITORING UPDATE
        $validated = $request->validate([
            'status' => 'nullable|string',
            'prioritas' => 'nullable|in:rendah,normal,tinggi,kritis',
            'target_selesai' => 'nullable|date',
            'target_start_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'solution_paper_required' => 'nullable|boolean',
            'mindesk_analisis' => 'nullable|numeric|min:0',
            'mindesk_development' => 'nullable|numeric|min:0',
            'mindesk_testing' => 'nullable|numeric|min:0',
            'biaya_pengerjaan' => 'nullable|numeric|min:0',
            'harga_penawaran' => 'nullable|numeric|min:0',
            'potensi_penjualan' => 'nullable|string',
            'catatan_analisis' => 'nullable|string',
            'catatan_approval' => 'nullable|string',
            'reject_reason' => 'nullable|string|max:2000',
            'action' => 'nullable|string',
            // Solution paper fields
            'solution_paper_status' => 'nullable|string',
            'solution_paper_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
            'pegawai_id' => 'nullable|exists:pegawais,id',
            'sp_target_start' => 'nullable|date',
            'sp_target_end' => 'nullable|date',
            'solution_paper_notes' => 'nullable|string',
            // Go-Live Berita Acara (BA)
            'berita_acara_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
            'berita_acara_date' => 'nullable|date',
            'berita_acara_no' => 'nullable|string|max:100',
        ]);

        if ($role !== 'presales') {
            unset($validated['biaya_pengerjaan'], $validated['harga_penawaran'], $validated['potensi_penjualan']);
        }

        if ($role === 'presales') {
            if (isset($validated['harga_penawaran']) && $validated['harga_penawaran'] !== null && $validated['harga_penawaran'] !== '') {
                if (! $changeRequest->canBeQuoted()) {
                    return back()->withErrors([
                        'harga_penawaran' => "CR ini masih berstatus '{$changeRequest->statusLabel()}' dan belum disetujui oleh PM Head. Penawaran harga hanya dapat diinput setelah PM Head menyetujui CR.",
                    ])->withInput();
                }
            }
        }

        if (in_array($role, ['pmh', 'admin'], true)) {
            if (($validated['status'] ?? null) === 'disetujui' || ($validated['status'] ?? null) === 'validated') {
                $validated['status'] = 'disetujui';
                $validated['pmh_approved_at'] = now();
                $validated['pmh_approved_by'] = $validated['pmh_approved_by'] ?? $user->name;
            }
        }

        $solutionPaperRequired = $request->boolean('solution_paper_required');
        $validated['solution_paper_required'] = $solutionPaperRequired;

        // Solution Paper check: If YES, PM must have uploaded or upload Solution Paper file before inputting Man-Days
        $hasSolutionPaperFile = ($changeRequest->solutionPaper && $changeRequest->solutionPaper->solution_paper_file)
            || $request->hasFile('solution_paper_file');

        if ($solutionPaperRequired && ! $hasSolutionPaperFile && ($request->filled('mindesk_analisis') || $request->filled('mindesk_development') || $request->filled('mindesk_testing'))) {
            if ($request->input('action') !== 'reject') {
                return back()->withErrors([
                    'solution_paper_file' => 'Anda memilih "CR Membutuhkan Solution Paper". Berkas Solution Paper wajib diunggah sebelum mengisi rincian Man-Days.',
                ])->withInput();
            }
        }

        $newStatus = $request->input('status', $changeRequest->status);

        // Enforce requirement 5: Tab Status CR belum bisa diisi jika belum melakukan pembayaran/quotation
        if ($request->input('from_tab') === 'status') {
            $isUnlocked = in_array($changeRequest->status, ['development', 'dikerjakan', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'golive', 'invoicing', 'selesai'])
                || ($changeRequest->harga_penawaran !== null && $changeRequest->isPmhApproved());
            if (!$isUnlocked) {
                return back()->with('error', 'Tab Status CR belum dapat diisi sebelum proses Verifikasi PM, Approval PM Head, dan Quotation/Pembayaran selesai.');
            }
        }

        // GO-LIVE TRIGGER:
        // When PM transitions status to "Go-Live" (or submits for Go-Live validation):
        // System must require PM to upload Berita Acara (BA) file and BA date!
        if ($newStatus === 'golive' || $newStatus === 'awaiting_golive_validation') {
            $hasBaFile = $changeRequest->berita_acara_file || $request->hasFile('berita_acara_file');
            $hasBaDate = $changeRequest->berita_acara_date || $request->filled('berita_acara_date');

            if (! $hasBaFile) {
                return back()->withErrors([
                    'berita_acara_file' => 'Untuk mengubah status menjadi Go-Live, Berita Acara (BA) wajib diisi.',
                ])->withInput();
            }

            if (! $hasBaDate) {
                $validated['berita_acara_date'] = now();
            }

            // Route to PM Head for Go-Live Validation before hitting finance!
            $validated['status'] = 'awaiting_golive_validation';
        }

        // Handle BA file upload
        if ($request->hasFile('berita_acara_file')) {
            $validated['berita_acara_file'] = $request->file('berita_acara_file')->store('berita-acara', 'public');
        }

        // Handle submission / verification to PM Head or rejection
        $successMessage = 'Change Request berhasil diperbarui.';
        if ($request->input('action') === 'reject') {
            $reason = $request->input('reject_reason') ?: $request->input('alasan_penolakan', 'Perlu revisi sesuai catatan PM');
            $validated['status'] = 'revision_needed';
            $validated['reject_reason'] = $reason;
            $validated['catatan_revisi'] = $reason;
            $validated['direvisi_pada'] = now();
            $successMessage = 'Change Request berhasil ditolak dan dikembalikan ke pemohon untuk revisi.';

            // Notify Client that CR was rejected / needs revision
            Notification::sendNotification([
                'user_id'           => $changeRequest->user_id,
                'target_role'       => 'client',
                'change_request_id' => $changeRequest->id,
                'title'             => 'Pengajuan CR Ditolak oleh PM',
                'message'           => "Change Request {$changeRequest->kode_cr} ({$changeRequest->judul}) ditolak oleh PM. Alasan: {$reason}. Silakan periksa catatan dan ajukan perbaikan.",
                'type'              => 'danger',
                'category'          => 'cr_rejection',
                'action_url'        => route('change-requests.show', $changeRequest->id),
            ]);
        } elseif ($request->input('action') === 'approve' || ($request->has('submit_to_pmh') && $request->boolean('submit_to_pmh'))) {
            if ($solutionPaperRequired && ! $hasSolutionPaperFile && ! $request->hasFile('solution_paper_file')) {
                // If solution paper required and not yet uploaded, require upload or warn gracefully
            }
            $validated['status'] = 'awaiting_pmh';
            $successMessage = 'Verifikasi oleh PM telah selesai dan dokumen pendukung lengkap. Data CR berhasil dikirimkan ke antrean PM Head untuk validasi.';

            // Notify PM Head
            Notification::sendNotification([
                'target_role'       => 'pmh',
                'change_request_id' => $changeRequest->id,
                'title'             => 'CR Menunggu Validasi PM Head',
                'message'           => "Change Request {$changeRequest->kode_cr} telah diverifikasi oleh PM {$user->name} dan siap divalidasi.",
                'type'              => 'info',
                'category'          => 'cr_submitted',
                'action_url'        => route('change-requests.show', $changeRequest->id),
            ]);
        }

        $changeRequest->update($validated);

        // Update or create Solution Paper
        if ($solutionPaperRequired || $request->hasFile('solution_paper_file') || $request->filled('sp_target_start')) {
            $sp = $changeRequest->solutionPaper ?: new SolutionPaper(['change_request_id' => $changeRequest->id]);
            $sp->status = $request->input('solution_paper_status', $sp->status ?: 'Draft');
            $sp->pegawai_id = $request->input('pegawai_id', $sp->pegawai_id);
            $sp->target_date_start = $request->input('sp_target_start', $sp->target_date_start);
            $sp->target_date_end = $request->input('sp_target_end', $sp->target_date_end);
            $sp->notes = $request->input('solution_paper_notes', $sp->notes);

            if ($request->hasFile('solution_paper_file')) {
                $sp->solution_paper_file = $request->file('solution_paper_file')->store('solution-papers', 'public');
                $sp->actual_completion_date = now();
                $sp->status = 'Completed';
            }

            $sp->save();
        }

        if ($request->input('from_tab') === 'status') {
            return redirect()->route('change-requests.show', [$changeRequest, 'tab' => 'status'])
                ->with('success', 'Status CR berhasil diperbarui menjadi ' . $changeRequest->statusLabel() . '.');
        }

        return redirect()->route('change-requests.index')
            ->with('success', $successMessage);
    }

    /**
     * Fitur Edit Klien (Minor vs Major Edit Rules)
     * Klien diperbolehkan melakukan perbaikan kecil (typo deskripsi, perbaikan berkas).
     * Jika status sudah lebih lanjut dari tahap verifikasi awal, edit dilarang.
     */
    public function clientMinorEdit(Request $request, ChangeRequest $changeRequest)
    {
        $this->ensureClientOwnsRequest($changeRequest);
        abort_unless(auth()->user()?->isClient(), 403, 'Akses ditolak.');

        // Hanya boleh diedit sebelum disetujui / diproses lebih jauh
        if (!in_array($changeRequest->status, ['diajukan', 'awaiting_pm', 'revision_needed'], true)) {
            return back()->with('error', 'Pengajuan CR ini sudah dalam proses verifikasi/pengerjaan lanjut dan tidak dapat diedit langsung. Silakan ajukan CR Baru jika terdapat perubahan kebutuhan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:5000',
            'alasan' => 'nullable|string|max:2000',
            'lampiran_pengajuan' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,zip|max:20480',
        ]);

        if ($request->hasFile('lampiran_pengajuan')) {
            $validated['lampiran_pengajuan'] = $request->file('lampiran_pengajuan')->store('change-request-submissions', 'public');
        }

        $changeRequest->update($validated);

        return back()->with('success', 'Perubahan minor berhasil disimpan.');
    }

    public function sendClientMessage(Request $request, ChangeRequest $changeRequest)
    {
        $this->ensureClientOwnsRequest($changeRequest);
        abort_unless(auth()->user()?->isClient(), 403);

        $validated = $request->validate([
            'pesan_client' => 'required|string|max:1000',
        ]);

        $changeRequest->update($validated);

        return back()->with('success', 'Pesan berhasil ditambahkan ke Change Request.');
    }

    public function approveQuotationClient(ChangeRequest $changeRequest)
    {
        $this->ensureClientOwnsRequest($changeRequest);
        abort_unless(auth()->user()?->isClient(), 403);

        if (! $changeRequest->canBeQuoted() || $changeRequest->harga_penawaran === null) {
            return back()->with('error', 'Penawaran harga belum tersedia atau CR belum disetujui oleh PM Head.');
        }

        $changeRequest->update(['quotation_approved_client_at' => now()]);
        $this->maybeAdvanceToDevelopment($changeRequest);

        return back()->with('success', 'Quotation berhasil disetujui oleh Klien.');
    }

    public function approveQuotationTp(ChangeRequest $changeRequest)
    {
        abort_unless(in_array(auth()->user()?->role, ['pm', 'pmh', 'admin'], true), 403);

        if (! $changeRequest->canBeQuoted() || $changeRequest->harga_penawaran === null) {
            return back()->with('error', 'Penawaran harga belum tersedia atau CR belum disetujui oleh PM Head.');
        }

        $changeRequest->update(['quotation_approved_tp_at' => now()]);
        $this->maybeAdvanceToDevelopment($changeRequest);

        return back()->with('success', 'Quotation berhasil disetujui oleh Tim Proyek (TP).');
    }

    private function maybeAdvanceToDevelopment(ChangeRequest $changeRequest): void
    {
        $changeRequest->refresh();

        if ($changeRequest->quotation_approved_client_at !== null && $changeRequest->quotation_approved_tp_at !== null) {
            if (in_array($changeRequest->status, ['diajukan', 'dianalisis', 'disetujui', 'awaiting_pm', 'awaiting_pmh', 'validated'], true)) {
                $changeRequest->update([
                    'status' => 'dikerjakan',
                    'mulai_dikerjakan_pada' => $changeRequest->mulai_dikerjakan_pada ?: now(),
                ]);
            }
        }
    }

    /**
     * WORKFLOW 4: GRANULAR SEQUENTIAL STATUS STEPPER UPDATE (PM ONLY)
     */
    public function updateSequentialStatus(Request $request, ChangeRequest $changeRequest)
    {
        $user = auth()->user();
        abort_unless($user->isPm() || $user->isAdmin(), 403, 'Hanya PM yang berwenang memperbarui status pemantauan pengerjaan CR.');

        $validated = $request->validate([
            'status' => 'required|in:analisa,development,sit,uat,training,golive',
            'catatan_status' => 'required|string|min:3|max:2000',
            'berita_acara_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
            'berita_acara_date' => 'nullable|date',
            'berita_acara_no' => 'nullable|string|max:100',
        ], [
            'catatan_status.required' => 'Field catatan/notes wajib diisi untuk setiap pembaruan status pengerjaan.',
            'catatan_status.min' => 'Catatan status minimal terdiri dari 3 karakter.',
        ]);

        $nextStatus = $validated['status'];

        // Record history note to catatan_analisis or append
        $existingNote = $changeRequest->catatan_analisis ? $changeRequest->catatan_analisis . "\n" : '';
        $changeRequest->catatan_analisis = $existingNote . '[' . now()->format('d/m/Y H:i') . ' - ' . strtoupper($nextStatus) . ' by ' . $user->name . ']: ' . $validated['catatan_status'];

        // Berita Acara (BA) Validation Rule:
        // - SIT: Non-mandatory (opsional)
        // - UAT: Mandatory upload BA & Tanggal BA
        // - Go-Live: Mandatory upload BA & Tanggal BA
        if (in_array($nextStatus, ['uat', 'golive'], true)) {
            $hasBaFile = $changeRequest->berita_acara_file || $request->hasFile('berita_acara_file');
            $hasBaDate = $changeRequest->berita_acara_date || $request->filled('berita_acara_date');

            if (! $hasBaFile || ! $hasBaDate) {
                return back()->with('error', 'Tahap ' . strtoupper($nextStatus) . ' mewajibkan unggah dokumen Berita Acara (BA) dan pengisian Tanggal BA.');
            }
        }

        if ($request->hasFile('berita_acara_file')) {
            $changeRequest->berita_acara_file = $request->file('berita_acara_file')->store('berita-acara', 'public');
        }
        if ($request->filled('berita_acara_date')) {
            $changeRequest->berita_acara_date = $validated['berita_acara_date'];
        }
        if ($request->filled('berita_acara_no')) {
            $changeRequest->berita_acara_no = $validated['berita_acara_no'];
        }

        if ($nextStatus === 'golive') {
            // Alur Transisi Data Setelah Go-Live:
            // Masuk ke antrean PM Head untuk validasi kelayakan terlebih dahulu!
            $changeRequest->status = 'awaiting_golive_validation';
            $changeRequest->save();

            return back()->with('success', 'Berita Acara berhasil diunggah. CR beralih ke antrean PM Head untuk validasi Go-Live.');
        }

        $changeRequest->status = $nextStatus;
        $changeRequest->save();

        // Notify Client regarding status progress
        $statusLabel = ChangeRequest::SEQUENTIAL_STATUSES[$nextStatus] ?? strtoupper($nextStatus);
        Notification::sendNotification([
            'user_id'           => $changeRequest->user_id,
            'target_role'       => 'client',
            'change_request_id' => $changeRequest->id,
            'title'             => "Progres CR Diperbarui: {$statusLabel}",
            'message'           => "Change Request {$changeRequest->kode_cr} kini berada pada tahapan {$statusLabel}. Catatan: {$validated['catatan_status']}",
            'type'              => $nextStatus === 'golive' ? 'success' : 'info',
            'category'          => $nextStatus === 'golive' ? 'cr_approval' : 'status_update',
            'action_url'        => route('change-requests.show', $changeRequest->id),
        ]);

        return back()->with('success', 'Status pengerjaan CR berhasil diperbarui ke: ' . (ChangeRequest::SEQUENTIAL_STATUSES[$nextStatus] ?? $nextStatus));
    }

    /**
     * WORKFLOW 5: INTERNAL QUOTATION & INVOICING (FINANCE / MARKETING)
     * Strictly hidden from Clients and PM!
     */
    public function saveQuotation(Request $request, ChangeRequest $changeRequest)
    {
        $user = auth()->user();
        abort_unless($user?->isPresales() || $user?->isAdmin(), 403, 'Akses ditolak. Modul ini adalah pencatatan internal Presales / Marketing.');

        // Clean numeric values if formatted as string currency
        $rawHarga = $request->input('harga_penawaran');
        if (is_string($rawHarga)) {
            $cleaned = preg_replace('/[^0-9]/', '', $rawHarga);
            $request->merge(['harga_penawaran' => $cleaned !== '' ? (float) $cleaned : null]);
        }
        $rawBiaya = $request->input('biaya_pengerjaan');
        if (is_string($rawBiaya)) {
            $cleaned = preg_replace('/[^0-9]/', '', $rawBiaya);
            $request->merge(['biaya_pengerjaan' => $cleaned !== '' ? (float) $cleaned : null]);
        }

        $validated = $request->validate([
            'harga_penawaran' => 'required|numeric|min:0',
            'biaya_pengerjaan' => 'nullable|numeric|min:0',
            'estimasi_waktu' => 'nullable|string|max:100',
            'estimasi_resource' => 'nullable|string|max:255',
            'quotation_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
            'signed_quotation_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:20480',
        ]);

        $updateData = [
            'harga_penawaran' => $validated['harga_penawaran'],
        ];
        if (array_key_exists('biaya_pengerjaan', $validated) && $validated['biaya_pengerjaan'] !== null) {
            $updateData['biaya_pengerjaan'] = $validated['biaya_pengerjaan'];
        }
        if (!empty($validated['estimasi_waktu'])) {
            $updateData['estimasi_waktu'] = $validated['estimasi_waktu'];
        }
        if (!empty($validated['estimasi_resource'])) {
            $updateData['estimasi_resource'] = $validated['estimasi_resource'];
        }

        // Step 10: Setelah quotation disimpan, status CR dibuka ke development & dikerjakan
        $updateData['status'] = 'development';
        $updateData['mulai_dikerjakan_pada'] = $changeRequest->mulai_dikerjakan_pada ?: now();
        $updateData['quotation_approved_tp_at'] = $changeRequest->quotation_approved_tp_at ?: now();
        $updateData['quotation_approved_client_at'] = $changeRequest->quotation_approved_client_at ?: now();

        $changeRequest->update($updateData);

        // Store into CrInvoice or update first invoice quotation
        $invoice = $changeRequest->invoices()->first() ?: new CrInvoice(['change_request_id' => $changeRequest->id]);
        $invNum = $request->input('invoice_number', $request->input('nomor_invoice'));
        if ($invNum) {
            $invoice->invoice_number = $invNum;
        } elseif (!$invoice->invoice_number) {
            $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad($changeRequest->id, 3, '0', STR_PAD_LEFT);
        }
        $invStatus = $request->input('payment_status', $request->input('status_invoice'));
        if ($invStatus && in_array(strtolower($invStatus), ['pending', 'paid'], true)) {
            $invoice->payment_status = strtolower($invStatus);
        } elseif (!$invoice->payment_status) {
            $invoice->payment_status = 'pending';
        }

        if ($request->hasFile('quotation_file')) {
            $invoice->quotation_file = $request->file('quotation_file')->store('quotations', 'public');
        }
        if ($request->hasFile('signed_quotation_file')) {
            $invoice->signed_quotation_file = $request->file('signed_quotation_file')->store('quotations', 'public');
        }
        $invoice->nominal = $invoice->nominal ?: $validated['harga_penawaran'];
        $invoice->created_by = auth()->id();
        $invoice->save();

        Notification::sendNotification([
            'target_role'       => 'pm',
            'change_request_id' => $changeRequest->id,
            'title'             => 'Quotation CR Selesai - Siap Dikerjakan',
            'message'           => "Presales telah menyimpan Quotation untuk {$changeRequest->kode_cr}. Status CR kini aktif dalam tahap Development dan pengerjaan dapat dimulai.",
            'type'              => 'success',
            'category'          => 'cr_approval',
            'action_url'        => route('change-requests.show', $changeRequest->id),
        ]);

        Notification::sendNotification([
            'user_id'           => $changeRequest->user_id,
            'target_role'       => 'client',
            'change_request_id' => $changeRequest->id,
            'title'             => 'Quotation CR Selesai - Masuk Development',
            'message'           => "Quotation untuk Change Request {$changeRequest->kode_cr} telah disimpan oleh Presales. Proyek kini memasuki tahap Development.",
            'type'              => 'info',
            'category'          => 'cr_approval',
            'action_url'        => route('change-requests.show', $changeRequest->id),
        ]);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Data Quotation berhasil disimpan. Status CR kini aktif dalam tahap Development dan pengerjaan dapat dimulai.');
        }

        return redirect()->route('presales.persetujuan')->with('success', 'Data Quotation berhasil disimpan. Status CR kini aktif dalam tahap Development dan pengerjaan dapat dimulai.');
    }

    public function storeInvoice(Request $request, ChangeRequest $changeRequest)
    {
        $user = auth()->user();
        abort_unless($user?->isPresales() || $user?->isAdmin(), 403, 'Akses ditolak. Modul Invoicing adalah wewenang bagian Presales.');

        // Aturan Khusus: CR harus berstatus Go-Live dan tervalidasi PM Head sebelum dapat diterbitkan invoice
        if (! $changeRequest->isReadyForInvoicing()) {
            return back()->with('error', 'Pencatatan Invoice baru dapat dilakukan setelah proyek dinyatakan Go-Live dan divalidasi oleh PM Head.');
        }

        // Spesifikasi Lengkap Modul Invoicing: 3 Input & 3-4 Upload
        $validated = $request->validate([
            // Input 1: Nomor Invoice & Tanggal Invoice
            'invoice_number' => 'required|string|max:100',
            'invoice_date' => 'required|date',
            // Input 2: Nominal Pekerjaan & Deskripsi Pekerjaan
            'nominal' => 'required|numeric|min:0',
            'job_description' => 'required|string|max:2000',
            // Input 3: Plan Tanggal Bayar & Rencana Tanggal Bayar
            'planned_payment_date' => 'required|date',
            'actual_payment_date' => 'nullable|date',
            // ToP optional metadata
            'term_name' => 'nullable|string|max:100',
            'percentage' => 'nullable|numeric|min:0|max:100',
            // 3-4 Uploads: GR, Jurnal, BA, Quotation
            'gr_number' => 'nullable|string|max:100',
            'gr_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg,zip|max:20480',
            'journal_number' => 'nullable|string|max:100',
            'journal_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg,zip|max:20480',
            'ba_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg,zip|max:20480',
            'ba_date' => 'nullable|date',
            'quotation_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg,zip|max:20480',
            'payment_status' => 'required|in:pending,paid',
        ]);

        if ($request->hasFile('gr_file')) {
            $validated['gr_file'] = $request->file('gr_file')->store('invoices/gr', 'public');
        }
        if ($request->hasFile('journal_file')) {
            $validated['journal_file'] = $request->file('journal_file')->store('invoices/journal', 'public');
        }
        if ($request->hasFile('ba_file')) {
            $validated['ba_file'] = $request->file('ba_file')->store('invoices/ba', 'public');
        } elseif ($changeRequest->berita_acara_file) {
            // Re-use Berita Acara previously uploaded by PM upon Go-Live
            $validated['ba_file'] = $changeRequest->berita_acara_file;
            $validated['ba_date'] = $validated['ba_date'] ?? $changeRequest->berita_acara_date?->format('Y-m-d');
        }

        if ($request->hasFile('quotation_file')) {
            $validated['quotation_file'] = $request->file('quotation_file')->store('invoices/quotation', 'public');
        } else {
            // Re-use existing quotation file if already uploaded
            $existingQuotation = $changeRequest->invoices()->whereNotNull('quotation_file')->first()?->quotation_file;
            if ($existingQuotation) {
                $validated['quotation_file'] = $existingQuotation;
            }
        }

        $validated['change_request_id'] = $changeRequest->id;
        $validated['created_by'] = auth()->id();

        CrInvoice::create($validated);

        // Mark CR as invoicing
        $changeRequest->update(['status' => 'invoicing']);

        return back()->with('success', 'Data Invoice internal & berkas pendukung (GR, Jurnal, BA, Quotation) berhasil dicatat.');
    }

    public function updateInvoiceStatus(Request $request, CrInvoice $invoice)
    {
        $user = auth()->user();
        abort_unless($user?->isPresales() || $user?->isAdmin(), 403, 'Akses ditolak. Perubahan status pembayaran hanya dapat dilakukan oleh Presales.');

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid',
            'actual_payment_date' => 'nullable|date',
        ]);

        if ($validated['payment_status'] === 'paid' && empty($validated['actual_payment_date'])) {
            $validated['actual_payment_date'] = now();
        }

        $invoice->update($validated);

        return back()->with('success', 'Status pembayaran invoice berhasil diperbarui.');
    }

    public function destroy(ChangeRequest $changeRequest)
    {
        $user = auth()->user();

        // If status is draft, allow deletion
        if ($changeRequest->status === 'draft') {
            if ($user?->isClient() && $changeRequest->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk menghapus draft milik pengguna lain.');
            }
            $changeRequest->delete();
            return redirect()->route('change-requests.index', ['view' => 'draft'])
                ->with('success', 'Draft CR berhasil dihapus.');
        }

        // Only PM/PMH/Admin can delete non-draft CR
        if (!in_array($user?->role, ['pm', 'pmh', 'admin'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus CR ini.');
        }

        $changeRequest->delete();

        return redirect()->route('change-requests.index', ['view' => 'draft'])
            ->with('success', 'Draft CR berhasil dihapus.');
    }

    private function ensureClientOwnsRequest(ChangeRequest $changeRequest): void
    {
        // Allow reviewing draft CRs
        if ($changeRequest->status === 'draft') {
            return;
        }

        if (auth()->user()?->isClient() && $changeRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak dapat mengakses Change Request milik klien lain.');
        }
    }

    public function downloadInitialAttachment(ChangeRequest $changeRequest)
    {
        $this->ensureClientOwnsRequest($changeRequest);

        abort_unless($changeRequest->lampiran_pengajuan && Storage::disk('public')->exists($changeRequest->lampiran_pengajuan), 404);

        return response()->download(
            Storage::disk('public')->path($changeRequest->lampiran_pengajuan),
            basename($changeRequest->lampiran_pengajuan)
        );
    }

    public function downloadInvoice(ChangeRequest $changeRequest)
    {
        abort_unless(auth()->user()?->isInternal(), 403);

        $pdf = Pdf::loadView('exports.change-request-invoice', compact('changeRequest'));
        return $pdf->download('invoice-' . $changeRequest->kode_cr . '.pdf');
    }



    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        if ($user?->isClient()) {
            abort(403, 'Klien tidak memiliki akses ekspor PDF');
        }

        $query = ChangeRequest::with(['client', 'solutionPaper'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $changeRequests = $query->get();
        $pdf = Pdf::loadView('exports.change-requests-pdf', compact('changeRequests'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-cr-' . date('Ymd-His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $query = ChangeRequest::query();

        if ($user?->isClient()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $crs = $query->latest()->get();

        return response()->streamDownload(function () use ($crs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode CR', 'Judul', 'Klien', 'Status', 'Target Selesai', 'Total Man-Days', 'Harga Penawaran', 'Tanggal Pengajuan']);
            foreach ($crs as $cr) {
                fputcsv($handle, [
                    $cr->kode_cr,
                    $cr->judul,
                    $cr->klien,
                    $cr->statusLabel(),
                    $cr->target_selesai?->format('Y-m-d') ?? '-',
                    $cr->totalMindesk(),
                    auth()->user()?->isClient() ? '-' : ($cr->harga_penawaran ?? 0),
                    $cr->tanggal_pengajuan?->format('Y-m-d') ?? '-',
                ]);
            }
            fclose($handle);
        }, 'change-requests-' . date('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
