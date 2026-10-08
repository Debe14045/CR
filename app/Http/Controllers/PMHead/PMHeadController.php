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
            'total' => ChangeRequest::count() ?: 12,
            'pending_pmh' => ChangeRequest::whereIn('status', ['awaiting_pmh', 'diajukan', 'dianalisis'])->count() ?: 4,
            'pending_golive' => ChangeRequest::whereIn('status', ['awaiting_golive_validation', 'training'])->count() ?: 2,
            'approved' => ChangeRequest::whereIn('status', ['validated', 'disetujui', 'development', 'dikerjakan', 'golive', 'selesai'])->count() ?: 8,
            'rejected' => ChangeRequest::whereIn('status', ['ditolak', 'revision_needed'])->count() ?: 1,
            'in_dev' => ChangeRequest::whereIn('status', ['development', 'dikerjakan', 'analisa'])->count() ?: 4,
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
            $query->where('status', $request->status);
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
            'totalData' => 200,
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
            'totalData' => 200,
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
            'totalData' => 200,
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
            'totalData' => 200,
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
        // 4 KPI Metrics matching Figma exactly
        $metrics = [
            'total_tagihan' => 'Rp. 200.000.000',
            'jatuh_tempo_minggu_ini' => 4,
            'lewat_jatuh_tempo' => 4,
            'terbayar_bulan_ini' => 4,
        ];

        // Sample list matching Figma Table 1:1
        $mockPayments = [
            [
                'no' => 1,
                'avatar' => 'DS',
                'pemohon' => 'PT Delta Solusi',
                'judul_cr' => 'Fitur SSO Login Karyawan',
                'kategori' => 'Authentication',
                'nilai' => 50000000,
                'tgl_golive' => '01 Feb 2024',
                'target_invoice' => '01 Feb 2024',
                'tgl_invoice' => '01 Feb 2024',
                'status' => 'BELUM BAYAR',
            ],
            [
                'no' => 2,
                'avatar' => 'DS',
                'pemohon' => 'PT Maju Bersama',
                'judul_cr' => 'Modul Absensi Mobile App',
                'kategori' => 'Mobile',
                'nilai' => 50000000,
                'tgl_golive' => '01 Feb 2024',
                'target_invoice' => '01 Feb 2024',
                'tgl_invoice' => '01 Feb 2024',
                'status' => 'LUNAS',
            ],
            [
                'no' => 3,
                'avatar' => 'DS',
                'pemohon' => 'PT Teknologi Nusantara',
                'judul_cr' => 'Integrasi API Payment Gateway',
                'kategori' => 'Integration',
                'nilai' => 50000000,
                'tgl_golive' => '01 Feb 2024',
                'target_invoice' => '01 Feb 2024',
                'tgl_invoice' => '01 Feb 2024',
                'status' => 'BELUM BAYAR',
            ],
            [
                'no' => 4,
                'avatar' => 'D',
                'pemohon' => 'PT Global Inovasi',
                'judul_cr' => 'Dashboard Laporan Keuangan Real-time',
                'kategori' => 'Reporting',
                'nilai' => 50000000,
                'tgl_golive' => '01 Feb 2024',
                'target_invoice' => '01 Feb 2024',
                'tgl_invoice' => '01 Feb 2024',
                'status' => 'LUNAS',
            ],
            [
                'no' => 5,
                'avatar' => 'B',
                'pemohon' => 'PT Solusi Digital',
                'judul_cr' => 'Notifikasi Push Multi-Platform',
                'kategori' => 'Notification',
                'nilai' => 50000000,
                'tgl_golive' => '01 Feb 2024',
                'target_invoice' => '01 Feb 2024',
                'tgl_invoice' => '01 Feb 2024',
                'status' => 'LUNAS',
            ],
        ];

        // Filter if search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $mockPayments = array_filter($mockPayments, function ($item) use ($search) {
                return str_contains(strtolower($item['pemohon']), $search) ||
                       str_contains(strtolower($item['judul_cr']), $search);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $statusFilter = strtoupper($request->status);
            $mockPayments = array_filter($mockPayments, function ($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        }

        return view('pmhead.outstanding_payment', [
            'metrics' => $metrics,
            'payments' => $mockPayments,
            'totalData' => 200,
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
}
