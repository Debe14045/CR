<?php

namespace App\Http\Controllers\PMHead;

use App\Http\Controllers\Controller;
use App\Http\Requests\PMHead\ValidateDecisionRequest;
use App\Models\ChangeRequest;
use App\Models\Client;
use App\Models\CrInvoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PMHeadController extends Controller
{
    public function dashboard(Request $request)
    {
        $roleMetrics = [
            'total' => ChangeRequest::count(),
            'pending_pmh' => ChangeRequest::whereIn('status', ['awaiting_pmh', 'diajukan', 'dianalisis', 'analisa'])->count(),
            'pending_golive' => ChangeRequest::whereIn('status', ['awaiting_golive_validation', 'training', 'uat', 'sit'])->count(),
            'approved' => ChangeRequest::whereIn('status', ['validated', 'disetujui', 'development', 'dikerjakan', 'golive', 'selesai'])->count(),
            'rejected' => ChangeRequest::whereIn('status', ['ditolak', 'revision_needed'])->count(),
            'in_dev' => ChangeRequest::whereIn('status', ['development', 'dikerjakan'])->count(),
        ];

        $latestReviews = ChangeRequest::with(['client', 'solutionPaper'])
            ->whereIn('status', ['awaiting_pmh', 'diajukan', 'dianalisis', 'awaiting_golive_validation'])
            ->latest()
            ->take(6)
            ->get();

        return view('pmhead.dashboard', [
            'roleMetrics' => $roleMetrics,
            'latestReviews' => $latestReviews,
            'currentRole' => auth()->user()->role ?? 'pmh',
        ]);
    }

    private function buildCrQuery(Request $request, ?array $defaultStatuses = null)
    {
        $query = ChangeRequest::with(['client', 'solutionPaper']);

        if ($defaultStatuses) {
            $query->whereIn('status', $defaultStatuses);
        }

        $activeTab = $request->query('tab', 'semua');
        if ($activeTab === 'aktif') {
            $query->whereIn('status', ['awaiting_pmh', 'diajukan', 'dianalisis', 'validated', 'disetujui', 'analisa', 'development', 'dikerjakan', 'sit', 'uat', 'training']);
        } elseif ($activeTab === 'selesai') {
            $query->whereIn('status', ['golive', 'selesai', 'invoicing', 'ditolak', 'revision_needed']);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $statusVal = strtolower($request->status);
            if (in_array($statusVal, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan', 'pending'])) {
                $query->whereIn('status', ['diajukan', 'awaiting_pm', 'awaiting_pmh']);
            } elseif (in_array($statusVal, ['analisa', 'analysis', 'dianalisis'])) {
                $query->whereIn('status', ['analisa', 'dianalisis']);
            } elseif (in_array($statusVal, ['development', 'develop', 'dikerjakan'])) {
                $query->whereIn('status', ['development', 'dikerjakan']);
            } elseif (in_array($statusVal, ['golive', 'go-live', 'go_live'])) {
                $query->whereIn('status', ['golive', 'selesai']);
            } else {
                $query->where('status', $statusVal);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_cr', 'like', "%{$search}%")
                  ->orWhere('klien', 'like', "%{$search}%")
                  ->orWhere('proyek_terkait', 'like', "%{$search}%")
                  ->orWhere('owner_cr', 'like', "%{$search}%")
                  ->orWhere('main_desk', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function semuaCr(Request $request)
    {
        $query = $this->buildCrQuery($request);
        $changeRequests = $query->latest()->paginate(10)->withQueryString();

        return view('pmhead.table_page', [
            'pageTitle' => 'Change Request',
            'activeMenu' => 'semua_cr',
            'changeRequests' => $changeRequests,
            'totalData' => $changeRequests->total(),
            'activeTab' => $request->query('tab', 'semua'),
            'search' => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
        ]);
    }

    public function persetujuan(Request $request)
    {
        $query = $this->buildCrQuery($request, ['awaiting_pmh', 'diajukan', 'dianalisis']);
        $changeRequests = $query->latest()->paginate(10)->withQueryString();

        return view('pmhead.table_page', [
            'pageTitle' => 'Butuh Persetujuan',
            'activeMenu' => 'persetujuan',
            'changeRequests' => $changeRequests,
            'totalData' => $changeRequests->total(),
            'activeTab' => $request->query('tab', 'semua'),
            'search' => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
        ]);
    }

    public function development(Request $request)
    {
        $query = $this->buildCrQuery($request, ['development', 'dikerjakan', 'analisa', 'sit', 'uat', 'training']);
        $changeRequests = $query->latest()->paginate(10)->withQueryString();

        return view('pmhead.table_page', [
            'pageTitle' => 'Development',
            'activeMenu' => 'development',
            'changeRequests' => $changeRequests,
            'totalData' => $changeRequests->total(),
            'activeTab' => $request->query('tab', 'semua'),
            'search' => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
        ]);
    }

    public function golive(Request $request)
    {
        $query = $this->buildCrQuery($request, ['golive', 'selesai', 'awaiting_golive_validation']);
        $changeRequests = $query->latest()->paginate(10)->withQueryString();

        return view('pmhead.table_page', [
            'pageTitle' => 'GO LIVE',
            'activeMenu' => 'golive',
            'changeRequests' => $changeRequests,
            'totalData' => $changeRequests->total(),
            'activeTab' => $request->query('tab', 'semua'),
            'search' => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
        ]);
    }

    public function outstandingPayment(Request $request)
    {
        $query = ChangeRequest::with(['client', 'invoices'])->latest();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_cr', 'like', "%{$search}%")
                  ->orWhere('klien', 'like', "%{$search}%");
            });
        }

        $allCr = $query->get();

        $payments = [];
        $no = 1;
        $totalNominal = 0;
        $unpaidCount = 0;
        $paidCount = 0;
        $overdueCount = 0;

        foreach ($allCr as $cr) {
            $clientName = $cr->client?->company ?? ($cr->klien ?: 'PT Klien');
            $avatar = strtoupper(substr($cr->client?->nickname ?? $clientName, 0, 2));
            $nominal = (float)($cr->biaya_pengerjaan ?: ($cr->harga_penawaran ?: 25000000));
            $totalNominal += $nominal;

            $isPaid = in_array(strtolower($cr->invoicing_status ?? ''), ['paid', 'lunas']) || in_array(strtolower($cr->status), ['selesai']);
            $statusLabel = $isPaid ? 'LUNAS' : 'BELUM BAYAR';

            if ($isPaid) {
                $paidCount++;
            } else {
                $unpaidCount++;
            }

            $tglGolive = $cr->actual_completion_date ? $cr->actual_completion_date->format('d M Y') : ($cr->target_selesai ? $cr->target_selesai->format('d M Y') : '15 Jan 2024');
            $targetInvoice = $cr->target_selesai ? $cr->target_selesai->format('d M Y') : '01 Feb 2024';
            $tglInvoice = $cr->created_at ? $cr->created_at->format('d M Y') : '01 Feb 2024';

            if ($request->filled('status') && $request->status !== 'all') {
                $statusFilter = strtoupper($request->status);
                if ($statusFilter === 'LUNAS' && !$isPaid) continue;
                if ($statusFilter === 'BELUM BAYAR' && $isPaid) continue;
            }

            $payments[] = [
                'no' => $no++,
                'avatar' => $avatar,
                'pemohon' => $clientName,
                'judul_cr' => $cr->judul,
                'kategori' => $cr->proyek_terkait ?: ($cr->kode_cr ?: 'System Enhancement'),
                'nilai' => $nominal,
                'tgl_golive' => $tglGolive,
                'target_invoice' => $targetInvoice,
                'tgl_invoice' => $tglInvoice,
                'status' => $statusLabel,
            ];
        }

        $metrics = [
            'total_tagihan' => 'Rp. ' . number_format($totalNominal, 0, ',', '.'),
            'jatuh_tempo_minggu_ini' => $unpaidCount,
            'lewat_jatuh_tempo' => max(0, $unpaidCount - 1),
            'terbayar_bulan_ini' => $paidCount,
        ];

        return view('pmhead.outstanding_payment', [
            'metrics' => $metrics,
            'payments' => $payments,
            'totalData' => count($payments),
        ]);
    }

    public function exportPayment(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Rekap_Outstanding_Payment_'.date('Ymd_His').'.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No', 'Pemohon', 'Judul CR', 'Kategori', 'Nilai (IDR)', 'Tgl Go-Live', 'Target Invoice', 'Tgl Invoice', 'Status']);

            $rows = [
                [1, 'PT Delta Solusi', 'Fitur SSO Login Karyawan', 'Authentication', '50.000.000', '01 Feb 2024', '01 Feb 2024', '01 Feb 2024', 'BELUM BAYAR'],
                [2, 'PT Maju Bersama', 'Modul Absensi Mobile App', 'Mobile', '50.000.000', '01 Feb 2024', '01 Feb 2024', '01 Feb 2024', 'LUNAS'],
                [3, 'PT Teknologi Nusantara', 'Integrasi API Payment Gateway', 'Integration', '50.000.000', '01 Feb 2024', '01 Feb 2024', '01 Feb 2024', 'BELUM BAYAR'],
                [4, 'PT Global Inovasi', 'Dashboard Laporan Keuangan Real-time', 'Reporting', '50.000.000', '01 Feb 2024', '01 Feb 2024', '01 Feb 2024', 'LUNAS'],
                [5, 'PT Solusi Digital', 'Notifikasi Push Multi-Platform', 'Notification', '50.000.000', '01 Feb 2024', '01 Feb 2024', '01 Feb 2024', 'LUNAS'],
            ];

            foreach ($rows as $r) {
                fputcsv($file, $r);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function review(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['client', 'solutionPaper', 'invoices']);

        return view('pmhead.review', [
            'changeRequest' => $changeRequest,
        ]);
    }

    public function submitDecision(ValidateDecisionRequest $request, ChangeRequest $changeRequest)
    {
        $user = auth()->user();
        $action = $request->input('action');
        $notes = $request->input('catatan_approval');

        if ($action === 'approve') {
            $changeRequest->update([
                'status' => 'validated',
                'catatan_approval' => $notes ?: 'Disetujui oleh PM Head',
                'pmh_approved_by' => $user->name,
                'pmh_approved_at' => now(),
                'reject_reason' => null,
            ]);

            return redirect()->route('pmh.persetujuan')
                ->with('success', 'Change Request "'.$changeRequest->judul.'" berhasil disetujui dan divalidasi oleh PM Head.');
        }

        if ($action === 'reject') {
            $changeRequest->update([
                'status' => 'revision_needed',
                'reject_reason' => $notes,
                'catatan_revisi' => $notes,
                'direvisi_pada' => now(),
                'pmh_approved_at' => null,
            ]);

            return redirect()->route('pmh.persetujuan')
                ->with('warning', 'Change Request "'.$changeRequest->judul.'" berhasil ditolak dan dikembalikan ke PM dengan status Revision Needed.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    public function profile(Request $request)
    {
        $user = auth()->user();

        $projects = [
            [
                'instansi' => 'CV Teknologi Nusantara',
                'judul' => 'Dashboard Analitik Real-time',
                'role_text' => 'PM CR',
            ],
            [
                'instansi' => 'PT Maju Bersama',
                'judul' => 'Integrasi API Pembayaran OVO',
                'role_text' => 'PM Project',
            ],
        ];

        return view('profile.index', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama tidak sesuai.'])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function notifications(Request $request)
    {
        $allCrs = ChangeRequest::with('client')->latest()->get();

        $notifications = [];

        foreach ($allCrs as $cr) {
            $clientName = $cr->client?->company ?? ($cr->klien ?: 'PT Klien');
            $detailUrl = route('pmh.review', $cr);

            // Compute Indonesian and English relative time
            $date = $cr->updated_at ?: ($cr->created_at ?: now()->subHours(21));
            $diffHours = max(1, (int) round(now()->diffInHours($date)));
            if ($diffHours < 24) {
                $timeId = "{$diffHours} jam yang lalu";
                $timeEn = "{$diffHours} hours ago";
            } else {
                $diffDays = (int) round($diffHours / 24);
                $timeId = "{$diffDays} hari yang lalu";
                $timeEn = "{$diffDays} days ago";
            }

            if (in_array($cr->status, ['golive', 'selesai', 'validated'])) {
                // 1. Persetujuan CR: HIJAU
                $notifications[] = [
                    'id' => 'notif-' . $cr->id . '-golive',
                    'category_id' => 'Persetujuan CR',
                    'category_en' => 'CR Approval',
                    'badge_bg' => '#DCFCE7',
                    'badge_color' => '#16A34A',
                    'badge_border' => '#86EFAC',
                    'icon_bg' => '#DCFCE7',
                    'icon_color' => '#16A34A',
                    'icon' => 'bi-check-circle-fill',
                    'title_id' => 'Validasi Go-Live Berhasil',
                    'title_en' => 'Go-Live Validation Successful',
                    'code' => $cr->kode_cr,
                    'desc_id' => "{$cr->kode_cr} ({$cr->judul}) telah berhasil divalidasi oleh PM Head dan resmi Go-Live.",
                    'desc_en' => "{$cr->kode_cr} ({$cr->judul}) has been successfully validated by PM Head and is officially Go-Live.",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => true,
                ];
            } elseif (in_array($cr->status, ['rejected', 'ditolak']) || !empty($cr->reject_reason)) {
                // 2. Penolakan CR: MERAH
                $reasonId = $cr->reject_reason ?: 'Arsitektur autentikasi belum menyertakan spesifikasi protokol OAuth 2.0 dan token expiry. Silakan periksa lampiran dan ajukan perbaikan.';
                $reasonEn = 'Authentication architecture does not include OAuth 2.0 protocol specifications and token expiry. Please review attachments and submit revisions.';
                $notifications[] = [
                    'id' => 'notif-' . $cr->id . '-rejected',
                    'category_id' => 'Penolakan CR',
                    'category_en' => 'CR Rejection',
                    'badge_bg' => '#FEE2E2',
                    'badge_color' => '#DC2626',
                    'badge_border' => '#FCA5A5',
                    'icon_bg' => '#FEE2E2',
                    'icon_color' => '#DC2626',
                    'icon' => 'bi-x-circle-fill',
                    'title_id' => 'Pengajuan CR Ditolak oleh PM',
                    'title_en' => 'CR Submission Rejected by PM',
                    'code' => $cr->kode_cr,
                    'desc_id' => "Change Request {$cr->kode_cr} ({$cr->judul}) ditolak oleh PM. Alasan: {$reasonId}",
                    'desc_en' => "Change Request {$cr->kode_cr} ({$cr->judul}) was rejected by PM. Reason: {$reasonEn}",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => false,
                ];
            } elseif (in_array($cr->status, ['awaiting_pmh', 'diajukan'])) {
                // 3. Revisi CR: KUNING
                $notifications[] = [
                    'id' => 'notif-' . $cr->id . '-revisi',
                    'category_id' => 'Revisi CR',
                    'category_en' => 'CR Revision',
                    'badge_bg' => '#FEF3C7',
                    'badge_color' => '#D97706',
                    'badge_border' => '#FCD34D',
                    'icon_bg' => '#FEF3C7',
                    'icon_color' => '#D97706',
                    'icon' => 'bi-x-circle-fill',
                    'title_id' => 'Permintaan Revisi dari PM Head',
                    'title_en' => 'Revision Request from PM Head',
                    'code' => $cr->kode_cr,
                    'desc_id' => "{$cr->kode_cr} ({$cr->judul}) memerlukan revisi man-hari dan rincian arsitektur sebelum dapat disetujui.",
                    'desc_en' => "{$cr->kode_cr} ({$cr->judul}) requires man-days revision and architecture details before approval.",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => false,
                ];
            } elseif (in_array($cr->status, ['analisa', 'dianalisis', 'development', 'dikerjakan', 'uat', 'sit'])) {
                // 4. Perubahan Status: BIRU
                $tahapNameId = in_array($cr->status, ['analisa', 'dianalisis']) ? 'Analisis' : (in_array($cr->status, ['uat', 'sit']) ? 'Pengujian (UAT/SIT)' : 'Development');
                $tahapNameEn = in_array($cr->status, ['analisa', 'dianalisis']) ? 'Analysis' : (in_array($cr->status, ['uat', 'sit']) ? 'Testing (UAT/SIT)' : 'Development');
                $notifications[] = [
                    'id' => 'notif-' . $cr->id . '-status',
                    'category_id' => 'Perubahan Status',
                    'category_en' => 'Status Change',
                    'badge_bg' => '#E0F2FE',
                    'badge_color' => '#0284C7',
                    'badge_border' => '#BAE6FD',
                    'icon_bg' => '#E0F2FE',
                    'icon_color' => '#0284C7',
                    'icon' => 'bi-arrow-repeat',
                    'title_id' => "Tahapan Pengerjaan Diperbarui: {$tahapNameId}",
                    'title_en' => "Work Phase Updated: {$tahapNameEn}",
                    'code' => $cr->kode_cr,
                    'desc_id' => "{$cr->kode_cr} ({$cr->judul}) telah diverifikasi dan kini memasuki tahapan {$tahapNameId}.",
                    'desc_en' => "{$cr->kode_cr} ({$cr->judul}) has been verified and has now entered the {$tahapNameEn} phase.",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => false,
                ];
            }
        }

        return view('pmhead.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
