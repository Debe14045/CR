<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminPegawaiController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MasterStatusController;
use App\Services\DashboardMetricsService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Landing Page Utama
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 2. Rute Autentikasi (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Rute Terproteksi Middleware Auth
Route::middleware(['auth'])->group(function () {
    
    // Rute Redirect Universal (Dicari oleh AuthController setelah berhasil login)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Cek active role jika ada session khusus (misal switcher PMH <-> PM), jika tidak ambil dari property user->role
        $role = session('active_role', $user->role ?? 'client');

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'client') {
            return redirect()->route('client.dashboard');
        } elseif ($role === 'pm') {
            return redirect()->route('pm.dashboard');
        } elseif ($role === 'pmh') {
            return redirect()->route('pmh.dashboard');
        } elseif ($role === 'presales') {
            return redirect()->route('presales.dashboard');
        }

        // Fallback default jika role tidak dikenali
        return redirect()->route('client.dashboard');
    })->name('dashboard');

    // ── 4. RUTE DASHBOARD BERDASARKAN ROLE ──

    // A. Role Admin -> Dashboard, Kelola Klien/PIC, Pegawai, dan Proyek
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('clients', ClientController::class);
        Route::resource('pegawai', AdminPegawaiController::class);
        Route::post('pegawai/{pegawai}/toggle-status', [AdminPegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');
        Route::resource('projects', ProjectController::class);
    });

    // B. Role Client -> resources/views/dashboard/client.blade.php
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', function (DashboardMetricsService $metricsService) {
            return view('dashboard.client', $metricsService->getMetrics(auth()->user(), 'client'));
        })->name('dashboard');
    });

    // C. Role PM (Project Manager) -> resources/views/dashboard/pm.blade.php
    Route::prefix('pm')->name('pm.')->group(function () {
        Route::get('/dashboard', function (DashboardMetricsService $metricsService) {
            return view('dashboard.pm', $metricsService->getMetrics(auth()->user(), 'pm'));
        })->name('dashboard');
    });

    // D. Role PMH (Project Manager Head) -> resources/views/dashboard/pmhead.blade.php
    Route::prefix('pmh')->name('pmh.')->group(function () {
        Route::get('/dashboard', function (DashboardMetricsService $metricsService) {
            $metrics = $metricsService->getMetrics(auth()->user(), 'pmh');

            $latestReviews = \App\Models\ChangeRequest::with(['client', 'project', 'user'])
                ->whereRaw("LOWER(COALESCE(status, '')) != 'draft'")
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard.pmhead', array_merge($metrics, compact('latestReviews')));
        })->name('dashboard');
        Route::get('/change-requests', [ChangeRequestController::class, 'index'])->name('change-requests');
        Route::get('/persetujuan', [ChangeRequestController::class, 'index'])->name('persetujuan');
        Route::get('/development', [ChangeRequestController::class, 'index'])->name('development');
        Route::get('/golive', [ChangeRequestController::class, 'index'])->name('golive');
        Route::get('/outstanding-payment', function (\Illuminate\Http\Request $request) {
            $metrics = [
                'total_tagihan' => 'Rp. 200.000.000',
                'jatuh_tempo_minggu_ini' => 4,
                'lewat_jatuh_tempo' => 4,
                'terbayar_bulan_ini' => 4,
            ];

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
                    'avatar' => 'MB',
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
                    'avatar' => 'TN',
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
                    'avatar' => 'GI',
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
                    'avatar' => 'SD',
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

            if ($request->filled('status')) {
                $st = strtoupper($request->status);
                $mockPayments = array_filter($mockPayments, fn($p) => $p['status'] === $st);
            }

            if ($request->filled('search')) {
                $sr = strtolower($request->search);
                $mockPayments = array_filter($mockPayments, function($p) use ($sr) {
                    return str_contains(strtolower($p['pemohon']), $sr) || str_contains(strtolower($p['judul_cr']), $sr);
                });
            }

            $payments = array_values($mockPayments);
            $totalData = count($payments);

            return view('pmhead.outstanding_payment', compact('metrics', 'payments', 'totalData'));
        })->name('outstanding-payment');

        Route::get('/outstanding-payment/export', function () {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="Rekap_Outstanding_Payment_' . date('Ymd_His') . '.csv"',
            ];

            $callback = function () {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
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
        })->name('outstanding-payment.export');

        Route::get('/notifications', function () {
            return redirect()->route('notifications.index');
        })->name('notifications');

        Route::get('/review/{changeRequest}', function (\App\Models\ChangeRequest $changeRequest) {
            $changeRequest->load(['client', 'project.pm.pegawai', 'project.pms.pegawai', 'pm.pegawai', 'pms.pegawai', 'user', 'invoices']);
            return view('pmhead.review', compact('changeRequest'));
        })->name('review');

        Route::post('/review/{changeRequest}/decision', function (\Illuminate\Http\Request $request, \App\Models\ChangeRequest $changeRequest) {
            $user = auth()->user();
            $action = $request->input('action', 'approve');
            $notes = $request->input('catatan_approval');

            if ($action === 'approve') {
                $changeRequest->update([
                    'status' => 'validated',
                    'catatan_approval' => $notes ?: 'Disetujui oleh PM Head',
                    'pmh_approved_by' => $user->id,
                    'pmh_approved_at' => now(),
                    'reject_reason' => null,
                ]);

                // Kirim notifikasi ke seluruh PM (1-2 PM) penanggung jawab proyek/CR
                $pmUsers = $changeRequest->assignedPms();
                if ($pmUsers->isEmpty()) {
                    $fallbackPm = \App\Models\User::where('role', 'pm')->first();
                    $pmUsers = $fallbackPm ? collect([$fallbackPm]) : collect();
                }

                foreach ($pmUsers as $pmUser) {
                    \App\Models\Notification::create([
                        'user_id' => $pmUser->id,
                        'change_request_id' => $changeRequest->id,
                        'category' => 'Approval PM Head',
                        'target_role' => null,
                        'title' => 'Change Request Disetujui PM Head',
                        'message' => 'CR "' . $changeRequest->judul . '" (' . $changeRequest->kode_cr . ') telah resmi disetujui oleh PM Head.',
                        'type' => 'success',
                        'action_url' => route('change-requests.show', $changeRequest->id),
                        'is_read' => false,
                    ]);
                }

                return redirect()->route('change-requests.show', $changeRequest)
                    ->with('success', 'Change Request "' . $changeRequest->judul . '" berhasil disetujui dan divalidasi oleh PM Head.');
            }

            // Validasi alasan jika ditolak/dibatalkan (mandatory)
            $trimmedNotes = trim($notes ?? '');
            if (empty($trimmedNotes)) {
                return redirect()->route('change-requests.show', $changeRequest)
                    ->withInput()
                    ->with('error', 'Alasan penolakan / pembatalan wajib diisi pada catatan CR Notes!');
            }

            $changeRequest->update([
                'status' => 'ditolak',
                'reject_reason' => $trimmedNotes,
                'catatan_revisi' => $trimmedNotes,
                'catatan_approval' => null,
                'pmh_approved_at' => null,
                'pmh_approved_by' => null,
            ]);

            // Kirim notifikasi penolakan / revisi ke seluruh PM (1-2 PM) penanggung jawab proyek/CR
            $pmUsers = $changeRequest->assignedPms();
            if ($pmUsers->isEmpty()) {
                $fallbackPm = \App\Models\User::where('role', 'pm')->first();
                $pmUsers = $fallbackPm ? collect([$fallbackPm]) : collect();
            }

            foreach ($pmUsers as $pmUser) {
                \App\Models\Notification::create([
                    'user_id' => $pmUser->id,
                    'change_request_id' => $changeRequest->id,
                    'category' => 'Revisi PM Head',
                    'target_role' => null,
                    'title' => 'Change Request Dikembalikan untuk Revisi',
                    'message' => 'CR "' . $changeRequest->judul . '" (' . $changeRequest->kode_cr . ') dikembalikan oleh PM Head untuk revisi. Alasan: ' . $trimmedNotes,
                    'type' => 'warning',
                    'action_url' => route('change-requests.show', $changeRequest->id),
                    'is_read' => false,
                ]);
            }

            return redirect()->route('change-requests.show', $changeRequest)
                ->with('warning', 'Change Request "' . $changeRequest->judul . '" berhasil ditolak / dikembalikan untuk revisi.');
        })->name('decision');
    });

    // E. Role Presales -> resources/views/dashboard/presales.blade.php
    Route::prefix('presales')->name('presales.')->group(function () {
        Route::get('/dashboard', function (DashboardMetricsService $metricsService) {
            return view('dashboard.presales', $metricsService->getMetrics(auth()->user(), 'presales'));
        })->name('dashboard');
        Route::get('/change-requests', [ChangeRequestController::class, 'index'])->name('change-requests');
        Route::get('/persetujuan', [ChangeRequestController::class, 'index'])->name('persetujuan');
        Route::get('/development', [ChangeRequestController::class, 'index'])->name('development');
        Route::get('/golive', [ChangeRequestController::class, 'index'])->name('golive');
        Route::get('/notifications', function () {
            return redirect()->route('notifications.index');
        })->name('notifications');
        Route::get('/review/{changeRequest}', function (\App\Models\ChangeRequest $changeRequest) {
            return view('presales.review', compact('changeRequest'));
        })->name('review');
    });

    // ── 5. CHANGE REQUESTS (Index, Create, Store, Show, Edit, Update, Destroy, DevBoard) ──
    Route::get('/change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');
    Route::get('/change-requests/create', [ChangeRequestController::class, 'create'])->name('change-requests.create');
    Route::post('/change-requests', [ChangeRequestController::class, 'store'])->name('change-requests.store');
    Route::get('/change-requests/{changeRequest}', [ChangeRequestController::class, 'show'])->name('change-requests.show');
    Route::get('/change-requests/{changeRequest}/edit', [ChangeRequestController::class, 'edit'])->name('change-requests.edit');
    Route::put('/change-requests/{changeRequest}', [ChangeRequestController::class, 'update'])->name('change-requests.update');
    Route::delete('/change-requests/{changeRequest}', [ChangeRequestController::class, 'destroy'])->name('change-requests.destroy');
    Route::get('/dev-board', [ChangeRequestController::class, 'devboard'])->name('change-requests.devboard');
    Route::post('/change-requests/{changeRequest}/analysis', [ChangeRequestController::class, 'storeAnalysis'])->name('change-requests.analysis');
    Route::post('/change-requests/{changeRequest}/quotation', [ChangeRequestController::class, 'storeQuotation'])->name('change-requests.quotation');
    Route::post('/change-requests/{changeRequest}/approve-pmh', [ChangeRequestController::class, 'approvePmh'])->name('change-requests.approve-pmh');
    Route::post('/change-requests/{changeRequest}/status', [ChangeRequestController::class, 'updateStatus'])->name('change-requests.update-status');
    Route::post('/change-requests/{changeRequest}/sequential-status', [ChangeRequestController::class, 'updateStatus'])->name('change-requests.sequential-status');
    Route::post('/change-requests/{changeRequest}/quotation/save', [ChangeRequestController::class, 'storeQuotation'])->name('change-requests.quotation.save');
    Route::post('/change-requests/{changeRequest}/quotation/client', function (\Illuminate\Http\Request $request, \App\Models\ChangeRequest $changeRequest) {
        $changeRequest->update(['status' => 'development']);
        return redirect()->back()->with('success', 'Penawaran harga telah disetujui. CR masuk ke tahap pengerjaan (Development).');
    })->name('change-requests.quotation.client');
    Route::get('/change-requests/{changeRequest}/initial-file', function (\App\Models\ChangeRequest $changeRequest) {
        if ($changeRequest->dokumen_cr && \Illuminate\Support\Facades\Storage::disk('public')->exists($changeRequest->dokumen_cr)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($changeRequest->dokumen_cr);
        }
        return back()->with('error', 'Dokumen CR belum diunggah atau tidak ditemukan.');
    })->name('change-requests.initial-file');
    Route::get('/change-requests/export/excel', function (\Illuminate\Http\Request $request) {
        $crs = \App\Models\ChangeRequest::all();
        return response()->streamDownload(function () use ($crs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode CR', 'Judul', 'Klien', 'Status', 'Tanggal Pengajuan']);
            foreach ($crs as $cr) {
                fputcsv($handle, [
                    $cr->kode_cr,
                    $cr->judul,
                    $cr->client?->company ?? $cr->klien ?? '-',
                    $cr->statusLabel(),
                    $cr->created_at?->format('Y-m-d') ?? '-',
                ]);
            }
            fclose($handle);
        }, 'laporan-cr-' . date('Ymd-His') . '.csv');
    })->name('change-requests.export.excel');
    Route::get('/change-requests/export/pdf', function () {
        return redirect()->back()->with('info', 'Laporan PDF sedang disiapkan.');
    })->name('change-requests.export.pdf');

    // Switch Role (PMH <-> PM)
    Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('switch.role');

    // Profile & Pengaturan
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/profile/view', [AuthController::class, 'profile'])->name('profile.index');
    Route::post('/profile/password', [AuthController::class, 'profile'])->name('profile.password');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/go', [NotificationController::class, 'go'])->name('notifications.go');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Master Data Aliases (Kompatibilitas Tautan Lama)
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('pegawai', AdminPegawaiController::class);
        Route::resource('projects', ProjectController::class);
        Route::get('status', [MasterStatusController::class, 'index'])->name('status.index');
        Route::post('status', [MasterStatusController::class, 'store'])->name('status.store');
        Route::put('status/{status}', [MasterStatusController::class, 'update'])->name('status.update');
    });

});
