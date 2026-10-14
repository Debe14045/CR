<?php

namespace App\Http\Controllers\Presales;

use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresalesController extends Controller
{
    public function dashboard(Request $request)
    {
        $figmaTotalCr = ChangeRequest::count();
        $figmaNeedApprovalCr = ChangeRequest::whereIn('status', ['awaiting_pm', 'diajukan', 'awaiting_pmh', 'dianalisis'])->count();
        $figmaDevCr = ChangeRequest::whereIn('status', ['development', 'dikerjakan'])->count();
        $figmaGoLiveCr = ChangeRequest::whereIn('status', ['golive', 'selesai', 'awaiting_golive_validation'])->count();

        // 7 Hari Terakhir untuk grafik request CR
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $pmRequestsToday = ChangeRequest::whereDate('created_at', $today)->count();
        $pmRequestsYesterday = ChangeRequest::whereDate('created_at', $yesterday)->count();

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

        // Fallback jika database seeder berada di tanggal spesifik
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

        // Notifikasi Terkini Dinamis dari Database untuk Dashboard Presales
        $notifPendingCount = ChangeRequest::whereIn('status', ['awaiting_pmh', 'diajukan', 'analisa', 'dianalisis'])->count();
        $notifNeedVerifCount = ChangeRequest::whereIn('status', ['diajukan', 'analisa', 'dianalisis'])->count();
        $notifDevCount = ChangeRequest::whereIn('status', ['development', 'dikerjakan', 'uat', 'sit'])->count();

        $latestReviews = ChangeRequest::with(['client', 'solutionPaper'])
            ->latest()
            ->take(6)
            ->get();

        return view('presales.dashboard', [
            'figmaTotalCr'        => $figmaTotalCr,
            'figmaNeedApprovalCr' => $figmaNeedApprovalCr,
            'figmaDevCr'          => $figmaDevCr,
            'figmaGoLiveCr'       => $figmaGoLiveCr,
            'pmRequestsToday'     => $pmRequestsToday,
            'pmRequestsYesterday' => $pmRequestsYesterday,
            'dailyTrend'          => $dailyTrend,
            'notifPendingCount'   => $notifPendingCount,
            'notifNeedVerifCount' => $notifNeedVerifCount,
            'notifDevCount'       => $notifDevCount,
            'latestReviews'       => $latestReviews,
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
        $perPage = (int) $request->query('per_page', 10);
        $query = $this->buildCrQuery($request);
        $changeRequests = $query->latest()->paginate($perPage)->withQueryString();

        $pageTitle = 'Change Request';
        if ($request->filled('status') && $request->status !== 'all') {
            $statusVal = strtolower($request->status);
            $pageTitle = match($statusVal) {
                'diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan' => 'Diajukan',
                'analisa', 'analysis', 'dianalisis' => 'Analysis',
                'development', 'develop', 'dikerjakan' => 'Develop',
                'golive', 'go-live', 'go_live', 'selesai' => 'Go Live',
                default => ucfirst(str_replace('_', ' ', $request->status)),
            };
        }

        return view('presales.table_page', [
            'pageTitle'      => $pageTitle,
            'activeMenu'     => 'semua_cr',
            'changeRequests' => $changeRequests,
            'totalData'      => $changeRequests->total(),
            'activeTab'      => $request->query('tab', 'semua'),
            'search'         => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
        ]);
    }

    public function persetujuan(Request $request)
    {
        $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('presales.table_page', [
            'pageTitle'      => 'Butuh Persetujuan',
            'activeMenu'     => 'persetujuan',
            'changeRequests' => $emptyPaginator,
            'totalData'      => 0,
            'activeTab'      => $request->query('tab', 'semua'),
            'search'         => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
            'isEmptyState'   => true,
            'emptyMessage'   => 'Halaman / baris kosong. Filter status Butuh Persetujuan dapat dipantau langsung pada halaman Semua CR.',
        ]);
    }

    public function development(Request $request)
    {
        $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('presales.table_page', [
            'pageTitle'      => 'Development CR',
            'activeMenu'     => 'development',
            'changeRequests' => $emptyPaginator,
            'totalData'      => 0,
            'activeTab'      => $request->query('tab', 'semua'),
            'search'         => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
            'isEmptyState'   => true,
            'emptyMessage'   => 'Halaman / baris kosong. Filter status Development dapat dipantau langsung pada halaman Semua CR.',
        ]);
    }

    public function golive(Request $request)
    {
        return redirect()->route('presales.change-requests', ['status' => 'golive']);
    }

    public function review(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['client', 'solutionPaper', 'invoices']);

        return view('presales.review', [
            'changeRequest' => $changeRequest,
        ]);
    }

    public function notifications(Request $request)
    {
        $allCrs = ChangeRequest::with('client')->latest()->get();
        $notifications = [];

        foreach ($allCrs as $cr) {
            $clientName = $cr->client?->company ?? ($cr->klien ?: 'PT Klien');
            $detailUrl = route('presales.review', $cr);

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
                    'desc_id' => "{$cr->kode_cr} ({$cr->judul}) telah berhasil divalidasi dan siap untuk proses quotation/invoicing.",
                    'desc_en' => "{$cr->kode_cr} ({$cr->judul}) has been successfully validated and ready for commercial process.",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => true,
                ];
            } elseif (in_array($cr->status, ['rejected', 'ditolak']) || !empty($cr->reject_reason)) {
                $reasonId = $cr->reject_reason ?: 'Arsitektur autentikasi belum menyertakan spesifikasi protokol OAuth 2.0 dan token expiry.';
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
                    'title_id' => 'Pengajuan CR Ditolak',
                    'title_en' => 'CR Submission Rejected',
                    'code' => $cr->kode_cr,
                    'desc_id' => "Change Request {$cr->kode_cr} ({$cr->judul}) ditolak. Alasan: {$reasonId}",
                    'desc_en' => "Change Request {$cr->kode_cr} ({$cr->judul}) was rejected. Reason: {$reasonId}",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => false,
                ];
            } elseif (in_array($cr->status, ['awaiting_pmh', 'diajukan'])) {
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
                    'title_id' => 'Permintaan Estimasi Presales',
                    'title_en' => 'Presales Estimation Request',
                    'code' => $cr->kode_cr,
                    'desc_id' => "{$cr->kode_cr} ({$cr->judul}) memerlukan kalkulasi estimasi man-days & penawaran harga komersial.",
                    'desc_en' => "{$cr->kode_cr} ({$cr->judul}) requires man-days estimation & commercial quotation calculation.",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => false,
                ];
            } else {
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
                    'title_id' => "Tahapan Pengerjaan Diperbarui: {$cr->statusLabel()}",
                    'title_en' => "Work Phase Updated: {$cr->statusLabel()}",
                    'code' => $cr->kode_cr,
                    'desc_id' => "{$cr->kode_cr} ({$cr->judul}) telah memasuki tahapan {$cr->statusLabel()}.",
                    'desc_en' => "{$cr->kode_cr} ({$cr->judul}) has entered {$cr->statusLabel()} phase.",
                    'time_id' => $timeId,
                    'time_en' => $timeEn,
                    'url' => $detailUrl,
                    'is_read' => false,
                ];
            }
        }

        return view('presales.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
