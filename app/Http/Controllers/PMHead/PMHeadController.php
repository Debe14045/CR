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
    /**
     * Dashboard eksekutif supervisi & validasi PM Head
     */
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

    /**
     * Helper query builder untuk tabel-tabel PM Head (Semua CR, Butuh Persetujuan, Development, Go Live)
     */
    private function buildCrQuery(Request $request, ?array $defaultStatuses = null)
    {
        $query = ChangeRequest::with(['client', 'solutionPaper']);

        if ($defaultStatuses) {
            $query->whereIn('status', $defaultStatuses);
        }

        // Segmented Tabs Filter: [Semua CR, CR Aktif, CR Selesai]
        $activeTab = $request->query('tab', 'semua');
        if ($activeTab === 'aktif') {
            $query->whereIn('status', ['awaiting_pmh', 'diajukan', 'dianalisis', 'validated', 'disetujui', 'analisa', 'development', 'dikerjakan', 'sit', 'uat', 'training']);
        } elseif ($activeTab === 'selesai') {
            $query->whereIn('status', ['golive', 'selesai', 'invoicing', 'ditolak', 'revision_needed']);
        }

        // Status dropdown filter
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

        // Search filter
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

    /**
     * Halaman Change Request (Semua CR - Figma PMH)
     */
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

    /**
     * Halaman Butuh Persetujuan (Figma PMH)
     */
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

    /**
     * Halaman Development CR (Figma PMH)
     */
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

    /**
     * Halaman GO LIVE (Figma PMH)
     */
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

    /**
     * Halaman OUTSTANDING PAYMENT (Figma PMH)
     */
    public function outstandingPayment(Request $request)
    {
        // Query ChangeRequest records that have financial value / billing aspects
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

        // Map into payment presentation items
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

            // Determine status
            $isPaid = in_array(strtolower($cr->invoicing_status ?? ''), ['paid', 'lunas']) || in_array(strtolower($cr->status), ['selesai']);
            $statusLabel = $isPaid ? 'LUNAS' : 'BELUM BAYAR';

            if ($isPaid) {
                $paidCount++;
            } else {
                $unpaidCount++;
            }

            // Target dates
            $tglGolive = $cr->actual_completion_date ? $cr->actual_completion_date->format('d M Y') : ($cr->target_selesai ? $cr->target_selesai->format('d M Y') : '15 Jan 2024');
            $targetInvoice = $cr->target_selesai ? $cr->target_selesai->format('d M Y') : '01 Feb 2024';
            $tglInvoice = $cr->created_at ? $cr->created_at->format('d M Y') : '01 Feb 2024';

            // Filter status if requested
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

        // 4 KPI Metrics calculated dynamically
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

    /**
     * Export rekap outstanding payment ke CSV / Excel
     */
    public function exportPayment(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Rekap_Outstanding_Payment_'.date('Ymd_His').'.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
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

    /**
     * Lembar Telaah & Detail Change Request untuk PM Head (Figma Image 4)
     */
    public function review(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['client', 'solutionPaper', 'invoices']);

        return view('pmhead.review', [
            'changeRequest' => $changeRequest,
        ]);
    }

    /**
     * Submit keputusan validasi / review PM Head (Approve atau Reject)
     */
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

    /**
     * Halaman Profile Akun & Pengaturan (Figma Profile Image 1 & 3)
     */
    public function profile(Request $request)
    {
        $user = auth()->user();

        // Sample / dynamic project yang ditangani
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

    /**
     * Ganti Password dari halaman profil
     */
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

    /**
     * Halaman Notifikasi PM Head (Item revisi nomor 8)
     */
    public function notifications(Request $request)
    {
        // Fetch active CRs that require PM Head attention or milestone tracking
        $pendingReviews = ChangeRequest::with('client')
            ->whereIn('status', ['awaiting_pmh', 'diajukan', 'analisa', 'dianalisis'])
            ->latest()
            ->get();

        $inDevList = ChangeRequest::with('client')
            ->whereIn('status', ['development', 'dikerjakan', 'uat', 'sit'])
            ->latest()
            ->get();

        $completedList = ChangeRequest::with('client')
            ->whereIn('status', ['golive', 'selesai', 'validated'])
            ->latest()
            ->take(5)
            ->get();

        $notifications = [];

        // 1. Alert for CRs awaiting approval/review
        foreach ($pendingReviews as $cr) {
            $clientName = $cr->client?->company ?? ($cr->klien ?: 'PT Klien');
            $notifications[] = [
                'type' => 'review',
                'badge' => 'Status: Butuh Review',
                'badge_class' => 'text-danger bg-danger-subtle',
                'icon' => 'bi-exclamation-triangle-fill',
                'icon_color' => 'text-danger',
                'card_bg' => '#FEF2F2',
                'border_color' => '#FCA5A5',
                'btn_class' => 'btn-danger',
                'btn_text' => 'Tinjau Sekarang >',
                'url' => route('pmh.review', $cr),
                'title' => "CR {$cr->kode_cr}: {$cr->judul} menunggu validasi PM Head",
                'desc' => "Pengajuan dari {$clientName}. Segera lakukan review analisis teknis dan estimasi mandays.",
                'time' => $cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->diffForHumans() : $cr->created_at->diffForHumans(),
            ];
        }

        // 2. Alert for Development / Testing stage
        foreach ($inDevList as $cr) {
            $clientName = $cr->client?->company ?? ($cr->klien ?: 'PT Klien');
            $notifications[] = [
                'type' => 'development',
                'badge' => 'Tahapan: ' . ucfirst($cr->status),
                'badge_class' => 'text-primary bg-primary-subtle',
                'icon' => 'bi-gear-fill',
                'icon_color' => 'text-primary',
                'card_bg' => '#EFF6FF',
                'border_color' => '#BFDBFE',
                'btn_class' => 'btn-primary',
                'btn_text' => 'Lihat Status >',
                'url' => route('pmh.review', $cr),
                'title' => "CR {$cr->kode_cr}: Sedang dalam tahap pengerjaan & pengujian",
                'desc' => "Proyek {$cr->proyek_terkait} oleh {$clientName}. Pantau progres tim developer dan persiapan BAP/UAT.",
                'time' => $cr->updated_at ? $cr->updated_at->diffForHumans() : 'Baru saja',
            ];
        }

        // 3. Info for completed / validated
        foreach ($completedList as $cr) {
            $clientName = $cr->client?->company ?? ($cr->klien ?: 'PT Klien');
            $notifications[] = [
                'type' => 'done',
                'badge' => 'Tahapan: Selesai',
                'badge_class' => 'text-success bg-success-subtle',
                'icon' => 'bi-check2-circle',
                'icon_color' => 'text-success',
                'card_bg' => '#F8FAFC',
                'border_color' => '#E2E8F0',
                'btn_class' => 'btn-outline-secondary',
                'btn_text' => 'Detail >',
                'url' => route('pmh.review', $cr),
                'title' => "CR {$cr->kode_cr}: Selesai & Telah Divalidasi",
                'desc' => "Fitur {$cr->judul} untuk {$clientName} telah selesai dan siap untuk penagihan invoice.",
                'time' => $cr->updated_at ? $cr->updated_at->diffForHumans() : '1 minggu lalu',
            ];
        }

        return view('pmhead.notifications', [
            'notifications' => $notifications,
            'totalNew' => count($pendingReviews),
        ]);
    }
}

