<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChangeRequestController extends Controller
{
    public function index(Request $request, DashboardMetricsService $metricsService)
    {
        $user = auth()->user();
        $viewMode = $request->query('view', 'total');

        $query = ChangeRequest::with(['client', 'project', 'user']);

        if ($user && $user->isClient()) {
            $clientId = optional($user->clientPic)->client_id;
            if ($clientId) {
                $query->where('client_id', $clientId);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        // Filter Draft vs Non-Draft
        if ($viewMode === 'draft') {
            $query->where('status', 'draft');
        } else {
            // "Semua CR" dan tab alur lainnya hanya untuk CR yang sudah diajukan (bukan draft)
            if ($request->filled('status')) {
                $statusInput = strtolower($request->status);
                if (in_array($statusInput, ['analisa', 'analysis', 'dianalisis', 'awaiting_pm'])) {
                    $query->whereIn('status', ['analisa', 'analysis', 'dianalisis', 'awaiting_pm']);
                } elseif (in_array($statusInput, ['development', 'develop', 'dikerjakan'])) {
                    $query->whereIn('status', ['development', 'develop', 'dikerjakan']);
                } elseif (in_array($statusInput, ['uat', 'sit'])) {
                    $query->whereIn('status', ['uat', 'sit']);
                } elseif (in_array($statusInput, ['golive', 'go-live', 'go_live', 'selesai'])) {
                    $query->whereIn('status', ['golive', 'selesai', 'invoicing']);
                } else {
                    $query->where('status', $statusInput);
                }
            } else {
                $query->where('status', '!=', 'draft');
            }

            if ($viewMode === 'development') {
                $query->whereIn('status', ['development', 'dikerjakan']);
            } elseif ($viewMode === 'uat') {
                $query->whereIn('status', ['uat', 'sit']);
            } elseif ($viewMode === 'golive') {
                $query->whereIn('status', ['golive', 'selesai', 'invoicing']);
            }
        }

        // Pencarian (Search)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_cr', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('nama_proyek', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $changeRequests = $query->latest()->paginate($perPage)->withQueryString();

        $dashboardMetrics = [];
        if ($viewMode === 'dashboard') {
            $activeRole = $request->query('view_as', $request->query('role', session('active_role', $user?->role ?? 'client')));
            $dashboardMetrics = $metricsService->getMetrics($user, $activeRole);
            $dashboardMetrics['dashboardRole'] = $activeRole;
        }

        return view('cr.index', array_merge(
            compact('changeRequests', 'viewMode'),
            $dashboardMetrics
        ));
    }

    // Form pengajuan CR baru (Bisa diakses oleh Klien & PM)
    public function create()
    {
        $user = auth()->user();
        $activeRole = session('active_role', $user->role);

        if ($user->isClient()) {
            // Jika Klien, HANYA ambil proyek perusahaannya sendiri
            $client = $user->clientPic?->client ?: $user->client;
            if (!$client) {
                $client = Client::where('company', 'like', '%Default%')->first() ?: Client::first();
            }
            $clients = $client ? collect([$client]) : collect();
            $clientId = $client?->id;
            $projects = $clientId ? Project::where('client_id', $clientId)->orderBy('nama_proyek')->get() : collect();
        } elseif ($activeRole === 'pm' || $user->role === 'pm' || $user->isAdmin()) {
            // Jika PM atau Admin, bisa memilih seluruh master klien dan memfilter proyek per klien
            $clients = Client::with('projects')->orderBy('company')->get();
            $projects = Project::with('client')->orderBy('nama_proyek')->get();
            $client = $clients->first();
        } else {
            abort(403, 'Unauthorized action.');
        }

        return view('cr.create', compact('projects', 'clients', 'client'));
    }

    // Simpan pengajuan CR baru (Mendukung Klien & PM)
    public function store(Request $request)
    {
        // 1. Normalisasi nama field dari form tampilan Figma ke kolom model
        if (!$request->has('judul') && $request->filled('cr_name')) {
            $request->merge(['judul' => $request->cr_name]);
        }
        if (!$request->has('deskripsi') && $request->filled('detail_cr')) {
            $request->merge(['deskripsi' => $request->detail_cr]);
        }
        if (!$request->has('catatan_pengajuan') && $request->filled('cr_notes')) {
            $request->merge(['catatan_pengajuan' => $request->cr_notes]);
        }
        if (!$request->hasFile('dokumen_cr') && $request->hasFile('dokumen_fsd')) {
            $request->files->set('dokumen_cr', $request->file('dokumen_fsd'));
        }

        // Normalisasi prioritas
        $prioritasInput = strtolower($request->input('prioritas', 'normal'));
        $validPriorities = ['normal', 'urgent'];
        $request->merge([
            'prioritas' => in_array($prioritasInput, $validPriorities) ? $prioritasInput : 'normal'
        ]);

        // 2. Normalisasi Project ID jika form mengirimkan nama_project
        if (!$request->filled('project_id') && $request->filled('nama_project')) {
            $proj = Project::where('nama_proyek', $request->nama_project)
                           ->orWhere('kode_proyek', $request->nama_project)->first();
            if ($proj) {
                $request->merge(['project_id' => $proj->id]);
            } else {
                $client = Client::first();
                if (!$client) {
                    $client = Client::create([
                        'company' => $request->input('nama_perusahaan', 'PT Client Default'),
                        'nickname' => $request->input('inisial_klien', 'CD'),
                    ]);
                }
                $proj = Project::create([
                    'client_id' => $client->id,
                    'kode_proyek' => 'PRJ-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->nama_project), 0, 4)) . rand(10, 99),
                    'nama_proyek' => $request->nama_project,
                ]);
                $request->merge(['project_id' => $proj->id]);
            }
        } elseif (!$request->filled('project_id')) {
            $firstProj = Project::first();
            if ($firstProj) {
                $request->merge(['project_id' => $firstProj->id]);
            } else {
                $client = Client::firstOrCreate(['company' => 'PT Mitra Solusi'], ['nickname' => 'MS']);
                $newProj = Project::create([
                    'client_id' => $client->id,
                    'kode_proyek' => 'PRJ-CORE01',
                    'nama_proyek' => 'Core System Enhancement',
                ]);
                $request->merge(['project_id' => $newProj->id]);
            }
        }

        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'judul' => 'required|string|max:155',
            'prioritas' => 'required|in:normal,urgent',
            'deskripsi' => $isDraft ? 'nullable|string' : 'required|string',
            'catatan_pengajuan' => 'nullable|string|max:25000',
            'cr_notes' => 'nullable|string|max:25000',
            'cr_owner' => 'nullable|string|max:50',
            'alasan' => 'nullable|string',
            'dokumen_cr' => 'nullable|mimes:pdf|max:5120',
            'dokumen_fsd' => 'nullable|mimes:pdf|max:5120',
            'dokumen_lainnya' => 'nullable|mimes:pdf|max:5120',
        ], [
            'judul.max' => 'Judul CR maksimal 155 karakter.',
            'dokumen_cr.mimes' => 'Format file ditolak! Dokumen CR wajib berformat PDF (.pdf). Dokumen selain PDF tidak diperbolehkan.',
            'dokumen_fsd.mimes' => 'Format file ditolak! Dokumen CR wajib berformat PDF (.pdf). Dokumen selain PDF tidak diperbolehkan.',
            'dokumen_lainnya.mimes' => 'Hanya file berformat PDF (.pdf) yang diperbolehkan.',
            'catatan_pengajuan.max' => 'Catatan pengajuan maksimal 25.000 karakter.',
            'cr_notes.max' => 'Catatan pengajuan maksimal 25.000 karakter.',
            'cr_owner.max' => 'CR Owner maksimal 50 karakter.',
        ]);

        // Validasi maksimal 25.000 karakter untuk Detail CR
        if ($request->filled('deskripsi')) {
            $plainText = html_entity_decode(strip_tags($request->deskripsi));
            $charCount = mb_strlen(trim($plainText));
            if ($charCount > 25000) {
                return back()->withErrors([
                    'deskripsi' => 'Detail CR melebihi batas maksimal 25.000 karakter (terdeteksi ' . number_format($charCount) . ' karakter). Harap ringkas penjelasan Anda.'
                ])->withInput();
            }
        }

        $user = auth()->user();
        
        // Tentukan client_id berdasarkan proyek yang dipilih
        $project = Project::findOrFail($request->project_id);

        // Validasi keamanan: Jika user klien, pastikan proyek memang milik perusahaannya
        if ($user->isClient()) {
            $myClientId = $user->clientPic?->client_id ?: ($user->client?->id);
            if ($myClientId && $project->client_id != $myClientId) {
                return back()->withErrors([
                    'project_id' => 'Proyek yang dipilih tidak terdaftar untuk perusahaan Anda.'
                ])->withInput();
            }
        }

        $clientId = $project->client_id;

        $nextNum = (ChangeRequest::max('id') ?? 0) + 1;
        $kodeCr = 'CR-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        while (ChangeRequest::where('kode_cr', $kodeCr)->exists()) {
            $nextNum++;
            $kodeCr = 'CR-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        }

        $data = $request->except(['dokumen_cr', 'dokumen_lainnya', 'dokumen_fsd', 'cr_name', 'detail_cr', 'nama_project', 'nama_perusahaan', 'action']);
        $data['kode_cr'] = $kodeCr;
        $data['user_id'] = $user->id;
        $data['client_id'] = $clientId;
        $data['pm_id'] = $project->pm_id;
        $data['status'] = $isDraft ? 'draft' : 'diajukan';
        $data['deskripsi'] = $data['deskripsi'] ?? '';

        // Parsing Tanggal Pengajuan
        if ($request->filled('date')) {
            try {
                $data['tanggal_pengajuan'] = Carbon::parse($request->date);
            } catch (\Exception $e) {
                $data['tanggal_pengajuan'] = Carbon::now();
            }
        } else {
            $data['tanggal_pengajuan'] = Carbon::now();
        }

        // Parsing Target Selesai (Request Date)
        if ($request->filled('request_date')) {
            try {
                $data['target_selesai'] = Carbon::parse($request->request_date);
            } catch (\Exception $e) {}
        }

        $uploadedDoc = $request->file('dokumen_cr') ?: $request->file('dokumen_fsd');
        if ($uploadedDoc) {
            $data['dokumen_cr'] = $uploadedDoc->store('change-requests/dokumen-cr', 'public');
        }

        if ($request->hasFile('dokumen_lainnya')) {
            $data['dokumen_lainnya'] = $request->file('dokumen_lainnya')->store('change-requests/dokumen-lainnya', 'public');
        }

        $cr = ChangeRequest::create($data);

        // Buat notifikasi jika bukan draft (dikirim ke 1-2 PM penanggung jawab proyek)
        if (!$isDraft) {
            $targetPms = $this->resolveProjectPms($project, $cr);
            if ($targetPms->isEmpty()) {
                $targetPms = collect([$user]);
            }

            foreach ($targetPms as $pm) {
                Notification::firstOrCreate(
                    [
                        'change_request_id' => $cr->id,
                        'user_id' => $pm->id,
                        'category' => 'Pengajuan',
                    ],
                    [
                        'target_role' => null,
                        'title' => 'Pengajuan CR Baru',
                        'message' => 'Pengajuan CR baru "' . $cr->judul . '" (' . $cr->kode_cr . ') memerlukan review Anda.',
                        'type' => 'info',
                        'action_url' => route('change-requests.show', $cr->id),
                        'is_read' => false,
                    ]
                );
            }
        }

        $msg = $isDraft ? 'Draft Change Request berhasil disimpan.' : 'Change Request berhasil diajukan.';
        $redirectUrl = $isDraft 
            ? route('change-requests.index', ['view' => 'draft']) 
            : route('change-requests.index');

        return redirect($redirectUrl)->with('success', $msg);
    }

    public function update(Request $request, ChangeRequest $changeRequest)
    {
        // 1. Normalisasi nama field dari form tampilan Figma ke kolom model
        if (!$request->has('judul') && $request->filled('cr_name')) {
            $request->merge(['judul' => $request->cr_name]);
        }
        if (!$request->has('deskripsi') && $request->filled('detail_cr')) {
            $request->merge(['deskripsi' => $request->detail_cr]);
        }
        if (!$request->has('catatan_pengajuan') && $request->filled('cr_notes')) {
            $request->merge(['catatan_pengajuan' => $request->cr_notes]);
        }
        if (!$request->hasFile('dokumen_cr') && $request->hasFile('dokumen_fsd')) {
            $request->files->set('dokumen_cr', $request->file('dokumen_fsd'));
        }

        // Normalisasi prioritas
        $prioritasInput = strtolower($request->input('prioritas', 'normal'));
        $validPriorities = ['normal', 'urgent'];
        $request->merge([
            'prioritas' => in_array($prioritasInput, $validPriorities) ? $prioritasInput : 'normal'
        ]);

        // 2. Normalisasi Project ID
        if (!$request->filled('project_id') && !empty($changeRequest->project_id)) {
            $request->merge(['project_id' => $changeRequest->project_id]);
        }

        if (!$request->filled('project_id') && $request->filled('nama_project')) {
            $proj = Project::where('nama_proyek', $request->nama_project)
                           ->orWhere('kode_proyek', $request->nama_project)->first();
            if ($proj) {
                $request->merge(['project_id' => $proj->id]);
            } else {
                $client = $changeRequest->client ?? Client::first();
                if (!$client) {
                    $client = Client::create([
                        'company' => $request->input('nama_perusahaan', 'PT Client Default'),
                        'nickname' => $request->input('inisial_klien', 'CD'),
                    ]);
                }
                $proj = Project::create([
                    'client_id' => $client->id,
                    'kode_proyek' => 'PRJ-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->nama_project), 0, 4)) . rand(10, 99),
                    'nama_proyek' => $request->nama_project,
                ]);
                $request->merge(['project_id' => $proj->id]);
            }
        } elseif (!$request->filled('project_id')) {
            if (!empty($changeRequest->project_id)) {
                $request->merge(['project_id' => $changeRequest->project_id]);
            } elseif ($firstProj = Project::first()) {
                $request->merge(['project_id' => $firstProj->id]);
            } else {
                $client = Client::firstOrCreate(['company' => 'PT Mitra Solusi'], ['nickname' => 'MS']);
                $newProj = Project::create([
                    'client_id' => $client->id,
                    'kode_proyek' => 'PRJ-CORE01',
                    'nama_proyek' => 'Core System Enhancement',
                ]);
                $request->merge(['project_id' => $newProj->id]);
            }
        }

        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'judul' => 'required|string|max:155',
            'prioritas' => 'required|in:normal,urgent',
            'deskripsi' => $isDraft ? 'nullable|string' : 'required|string',
            'catatan_pengajuan' => 'nullable|string|max:25000',
            'cr_notes' => 'nullable|string|max:25000',
            'cr_owner' => 'nullable|string|max:50',
            'alasan' => 'nullable|string',
            'dokumen_cr' => 'nullable|mimes:pdf|max:5120',
            'dokumen_fsd' => 'nullable|mimes:pdf|max:5120',
            'dokumen_lainnya' => 'nullable|mimes:pdf|max:5120',
        ], [
            'judul.max' => 'Judul CR maksimal 155 karakter.',
            'dokumen_cr.mimes' => 'Format file ditolak! Dokumen CR wajib berformat PDF (.pdf). Dokumen selain PDF tidak diperbolehkan.',
            'dokumen_fsd.mimes' => 'Format file ditolak! Dokumen CR wajib berformat PDF (.pdf). Dokumen selain PDF tidak diperbolehkan.',
            'dokumen_lainnya.mimes' => 'Hanya file berformat PDF (.pdf) yang diperbolehkan.',
            'catatan_pengajuan.max' => 'Catatan pengajuan maksimal 25.000 karakter.',
            'cr_notes.max' => 'Catatan pengajuan maksimal 25.000 karakter.',
            'cr_owner.max' => 'CR Owner maksimal 50 karakter.',
        ]);

        if ($request->filled('deskripsi')) {
            $plainText = html_entity_decode(strip_tags($request->deskripsi));
            $charCount = mb_strlen(trim($plainText));
            if ($charCount > 25000) {
                return back()->withErrors([
                    'deskripsi' => 'Detail CR melebihi batas maksimal 25.000 karakter (terdeteksi ' . number_format($charCount) . ' karakter). Harap ringkas penjelasan Anda.'
                ])->withInput();
            }
        }

        $project = Project::findOrFail($request->project_id);

        $user = auth()->user();
        // Validasi keamanan: Jika user klien, pastikan proyek memang milik perusahaannya
        if ($user->isClient()) {
            $myClientId = $user->clientPic?->client_id ?: ($user->client?->id);
            if ($myClientId && $project->client_id != $myClientId) {
                return back()->withErrors([
                    'project_id' => 'Proyek yang dipilih tidak terdaftar untuk perusahaan Anda.'
                ])->withInput();
            }
        }

        $data = $request->except(['dokumen_cr', 'dokumen_lainnya', 'dokumen_fsd', 'cr_name', 'detail_cr', 'nama_project', 'nama_perusahaan', 'action', '_token', '_method']);
        $data['client_id'] = $project->client_id;
        $data['pm_id'] = $project->pm_id;
        $data['deskripsi'] = $data['deskripsi'] ?? '';

        $isSubmittingDraft = !$isDraft && $changeRequest->status === 'draft';
        if ($isDraft && $changeRequest->status === 'draft') {
            $data['status'] = 'draft';
        } elseif ($isSubmittingDraft) {
            $data['status'] = 'diajukan';
        }

        if ($request->filled('date')) {
            try {
                $data['tanggal_pengajuan'] = Carbon::parse($request->date);
            } catch (\Exception $e) {}
        }

        if ($request->filled('request_date')) {
            try {
                $data['target_selesai'] = Carbon::parse($request->request_date);
            } catch (\Exception $e) {}
        }

        $uploadedDoc = $request->file('dokumen_cr') ?: $request->file('dokumen_fsd');
        if ($uploadedDoc) {
            $data['dokumen_cr'] = $uploadedDoc->store('change-requests/dokumen-cr', 'public');
        }

        if ($request->hasFile('dokumen_lainnya')) {
            $data['dokumen_lainnya'] = $request->file('dokumen_lainnya')->store('change-requests/dokumen-lainnya', 'public');
        }

        $changeRequest->update($data);

        // Kirim notifikasi ke 1-2 PM proyek saat draft diajukan secara resmi
        if ($isSubmittingDraft) {
            $targetPms = $this->resolveProjectPms($project, $changeRequest);
            if ($targetPms->isEmpty() && $user) {
                $targetPms = collect([$user]);
            }

            foreach ($targetPms as $pm) {
                Notification::firstOrCreate(
                    [
                        'change_request_id' => $changeRequest->id,
                        'user_id' => $pm->id,
                        'category' => 'Pengajuan',
                    ],
                    [
                        'target_role' => null,
                        'title' => 'Pengajuan CR Baru',
                        'message' => 'Pengajuan CR "' . $changeRequest->judul . '" (' . $changeRequest->kode_cr . ') telah resmi diajukan dan memerlukan review Anda.',
                        'type' => 'info',
                        'action_url' => route('change-requests.show', $changeRequest->id),
                        'is_read' => false,
                    ]
                );
            }
        }

        $msg = $isDraft ? 'Draft Change Request berhasil diperbarui.' : 'Change Request berhasil diajukan.';
        $redirectUrl = $isDraft 
            ? route('change-requests.index', ['view' => 'draft']) 
            : route('change-requests.index');

        return redirect($redirectUrl)->with('success', $msg);
    }

    public function show(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['client', 'project.pm.pegawai', 'project.pms.pegawai', 'pm.pegawai', 'pms.pegawai', 'user', 'invoices']);
        $user = auth()->user();
        $activeRole = session('active_role', $user?->role);

        if ($activeRole === 'pmh' || ($user && in_array($user->role, ['pmh', 'pm_head'])) || request('role') === 'pmh' || request('view_as') === 'pmh') {
            return view('pmhead.review', compact('changeRequest'));
        }

        if ($activeRole === 'presales' || ($user && $user->role === 'presales') || request('role') === 'presales' || request('view_as') === 'presales') {
            return view('presales.review', compact('changeRequest'));
        }

        return view('cr.show', compact('changeRequest'));
    }

    public function edit(ChangeRequest $changeRequest)
    {
        $user = auth()->user();
        $activeRole = session('active_role', $user->role);

        if ($user->isClient()) {
            $client = $user->clientPic?->client ?: ($user->client ?: $changeRequest->client);
            if (!$client) {
                $client = Client::find($changeRequest->client_id) ?: Client::first();
            }
            $clientId = $client?->id ?? $changeRequest->client_id;
            $projects = $clientId ? Project::where('client_id', $clientId)->orderBy('nama_proyek')->get() : collect();
            $clients = $client ? collect([$client]) : collect();
        } else {
            $clients = Client::with('projects')->orderBy('company')->get();
            $projects = Project::with('client')->orderBy('nama_proyek')->get();
            $client = $changeRequest->client ?: ($changeRequest->client_id ? Client::find($changeRequest->client_id) : $clients->first());
        }

        $isEdit = true;
        return view('cr.create', compact('projects', 'clients', 'client', 'changeRequest', 'isEdit'));
    }

    public function destroy(ChangeRequest $changeRequest)
    {
        $isDraft = $changeRequest->status === 'draft';
        $changeRequest->delete();

        $redirectUrl = $isDraft 
            ? route('change-requests.index', ['view' => 'draft']) 
            : route('change-requests.index');

        return redirect($redirectUrl)->with('success', 'Change Request berhasil dihapus.');
    }

    public function devboard()
    {
        return view('cr.dashboard.devboard');
    }

    // PM mengunggah Solution Paper & Mindesk Analisis
    public function storeAnalysis(Request $request, ChangeRequest $changeRequest)
    {
        $isSpRequired = (int)$request->input('solution_paper_required', 1) === 1;
        $hasExistingSp = !empty($changeRequest->solution_paper_file);

        $rules = [
            'mindesk_analisis' => 'required|integer|min:0',
            'mindesk_development' => 'required|integer|min:0',
            'mindesk_testing' => 'required|integer|min:0',
            'catatan_analisis' => 'nullable|string',
            'solution_paper_required' => 'nullable|in:0,1',
        ];

        if ($isSpRequired && !$hasExistingSp) {
            $rules['solution_paper_file'] = 'required|mimes:pdf|max:5120';
        } else {
            $rules['solution_paper_file'] = 'nullable|mimes:pdf|max:5120';
        }

        $messages = [
            'solution_paper_file.required' => 'Dokumen Solution Paper (PDF) wajib diunggah karena Anda memilih opsi "Butuh Solution Paper: Ya".',
            'solution_paper_file.mimes' => 'Format file ditolak! Dokumen Solution Paper wajib berekstensi PDF (.pdf).',
            'solution_paper_file.max' => 'Ukuran file Solution Paper melebihi batas maksimal 5 MB.',
        ];

        $request->validate($rules, $messages);

        $data = $request->except(['solution_paper_file', '_token', '_method']);
        $data['solution_paper_required'] = $isSpRequired ? 1 : 0;
        
        if ($request->hasFile('solution_paper_file')) {
            $data['solution_paper_file'] = $request->file('solution_paper_file')->store('change-requests/solution-papers', 'public');
        }

        $data['status'] = 'awaiting_pmh';
        $data['pmh_approved_at'] = null;
        $data['pmh_approved_by'] = null;
        $data['catatan_approval'] = null;
        $changeRequest->update($data);

        // Notifikasi ke PM Head
        $pmHeadUser = User::whereIn('role', ['pmh', 'pm_head'])->first();
        Notification::firstOrCreate(
            [
                'change_request_id' => $changeRequest->id,
                'category' => 'Approval PM Head',
            ],
            [
                'user_id' => $pmHeadUser ? $pmHeadUser->id : 1,
                'target_role' => 'pmh',
                'title' => 'Verifikasi CR & Estimasi Mandays Selesai',
                'message' => 'CR "' . $changeRequest->judul . '" (' . $changeRequest->kode_cr . ') telah selesai diverifikasi oleh PM dan siap untuk diapprove.',
                'type' => 'info',
                'action_url' => route('change-requests.show', $changeRequest->id),
                'is_read' => false,
            ]
        );

        return redirect()->route('change-requests.show', ['changeRequest' => $changeRequest->id, 'tab' => 'detail'])
                         ->with('success', 'Verifikasi CR berhasil disimpan dan diajukan ke PM Head.');
    }

    // Presales mengisi aspek komersial & Quotation
    public function storeQuotation(Request $request, ChangeRequest $changeRequest)
    {
        $request->validate([
            'harga_penawaran' => 'required|numeric|min:0',
            'biaya_pengerjaan' => 'required|numeric|min:0',
            'quotation_file' => 'nullable|mimes:pdf|max:5120',
        ]);

        $data = $request->except(['quotation_file', 'termins_json']);
        
        if ($request->hasFile('quotation_file')) {
            $data['quotation_file'] = $request->file('quotation_file')->store('change-requests/quotations', 'public');
        }

        $data['status'] = 'development';
        $changeRequest->update($data);

        // Sinkronisasi Termin of Payment ke Invoices
        if ($request->filled('termins_json')) {
            $termins = json_decode($request->termins_json, true);
            if (is_array($termins)) {
                $changeRequest->invoices()->where('payment_status', 'pending')->delete();
                foreach ($termins as $idx => $t) {
                    $invNum = 'INV-' . date('Ymd') . '-' . str_pad($changeRequest->id, 3, '0', STR_PAD_LEFT) . '-' . ($idx + 1);
                    $pct = 0;
                    if (!empty($t['badge'])) {
                        preg_match('/(\d+(?:[.,]\d+)?)/', $t['badge'], $m);
                        if (!empty($m[1])) $pct = (float)str_replace(',', '.', $m[1]);
                    }
                    if ($pct == 0 && $request->harga_penawaran > 0) {
                        $pct = round(($t['nominal'] / $request->harga_penawaran) * 100, 2);
                    }
                    \App\Models\Invoice::create([
                        'change_request_id' => $changeRequest->id,
                        'invoice_number' => $invNum,
                        'term_name' => $t['name'] . (!empty($t['badge']) ? ' (' . $t['badge'] . ')' : ''),
                        'percentage' => $pct,
                        'nominal' => $t['nominal'],
                        'job_description' => $t['deliverable'] ?? null,
                        'payment_status' => 'pending',
                    ]);
                }
            }
        }

        // Kirim notifikasi ke 1-2 PM penanggung jawab proyek/CR bahwa quotation selesai dan siap development
        $pmUsers = $this->resolveProjectPms($changeRequest->project, $changeRequest);
        if ($pmUsers->isEmpty() && $changeRequest->user && $changeRequest->user->role === 'pm') {
            $pmUsers = collect([$changeRequest->user]);
        }

        foreach ($pmUsers as $pm) {
            Notification::firstOrCreate(
                [
                    'change_request_id' => $changeRequest->id,
                    'user_id' => $pm->id,
                    'category' => 'Development CR',
                ],
                [
                    'target_role' => null,
                    'title' => 'Quotation Selesai - CR Siap Dimulai Development',
                    'message' => 'Quotation untuk CR "' . $changeRequest->judul . '" (' . $changeRequest->kode_cr . ') telah selesai disimpan oleh Presales. CR sudah bisa mulai di development.',
                    'type' => 'info',
                    'action_url' => route('change-requests.show', ['changeRequest' => $changeRequest->id, 'tab' => 'status']),
                    'is_read' => false,
                ]
            );
        }

        return redirect()->back()->with('success', 'Data penawaran harga (Quotation) berhasil disimpan. CR telah dialihkan ke tahap Development dan notifikasi telah dikirimkan ke PM.');
    }

    // PM Head memberikan Approval
    public function approvePmh(Request $request, ChangeRequest $changeRequest)
    {
        $changeRequest->update([
            'pmh_approved_at' => Carbon::now(),
            'pmh_approved_by' => auth()->id(),
            'catatan_approval' => $request->catatan_approval,
            'status' => 'validated',
        ]);

        return redirect()->back()->with('success', 'Change Request telah disetujui oleh PM Head.');
    }

    // Method terpusat untuk mengubah status Change Request
    public function updateStatus(Request $request, ChangeRequest $changeRequest)
    {
        $request->validate([
            'status' => 'required|string|in:draft,diajukan,awaiting_pm,analisa,awaiting_pmh,validated,quotation,development,sit,uat,training,golive,invoicing,selesai,ditolak',
            'catatan_revisi' => 'nullable|string',
            'reject_reason' => 'nullable|string',
            'berita_acara_file' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        // Validasi agar tahapan development teknis tidak dapat dimundurkan ke tahap sebelumnya
        $subStatusOrder = [
            'development' => 1,
            'dikerjakan' => 1,
            'develop' => 1,
            'sit' => 2,
            'uat' => 3,
            'training' => 4,
            'golive' => 5,
            'selesai' => 5,
            'invoicing' => 5,
        ];

        $curKey = strtolower($changeRequest->status ?? '');
        $newKey = strtolower($request->status ?? '');

        if (isset($subStatusOrder[$curKey]) && isset($subStatusOrder[$newKey])) {
            $curStep = $subStatusOrder[$curKey];
            $newStep = $subStatusOrder[$newKey];
            if ($newStep < $curStep) {
                return redirect()->back()->with('error', 'Status Change Request tidak dapat dimundurkan ke tahap sebelumnya.');
            }
        }

        $updateData = ['status' => $request->status];

        if ($request->hasFile('berita_acara_file')) {
            $updateData['berita_acara_file'] = $request->file('berita_acara_file')->store('change-requests/berita-acara', 'public');
        }

        // Jika status ditolak, simpan alasannya
        if ($request->status === 'ditolak') {
            $updateData['reject_reason'] = $request->reject_reason;
        }

        // Jika ada catatan revisi
        if ($request->has('catatan_revisi')) {
            $updateData['catatan_revisi'] = $request->catatan_revisi;
        }

        $changeRequest->update($updateData);

        return redirect()->back()->with('success', 'Status Change Request berhasil diperbarui menjadi: ' . $changeRequest->statusLabel());
    }

    /**
     * Ambil koleksi (1-2) PM penanggung jawab dari CR/proyek, dengan fallback ke user PM pertama bila belum ditetapkan.
     */
    private function resolveProjectPms(?Project $project, ?ChangeRequest $changeRequest = null)
    {
        $pms = $changeRequest ? $changeRequest->assignedPms() : collect();

        if ($pms->isEmpty() && $project) {
            $pms = $project->assignedPms();
        }

        if ($pms->isNotEmpty()) {
            return $pms;
        }

        $fallback = User::where('role', 'pm')->first();
        return $fallback ? collect([$fallback]) : collect();
    }
}