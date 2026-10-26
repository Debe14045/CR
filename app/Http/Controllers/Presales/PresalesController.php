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
        $figmaNeedApprovalCr = ChangeRequest::whereIn('status', ['awaiting_pm', 'diajukan', 'awaiting_pmh', 'dianalisis', 'analisa', 'persetujuan'])->count();
        $figmaDevCr = ChangeRequest::whereIn('status', ['development', 'dikerjakan', 'sit', 'uat', 'training'])->count();
        $figmaGoLiveCr = ChangeRequest::whereIn('status', ['golive', 'selesai', 'invoicing', 'awaiting_golive_validation'])->count();

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
        $notifDevCount = ChangeRequest::whereIn('status', ['development', 'dikerjakan', 'uat', 'sit', 'training'])->count();

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
            $query->whereIn('status', ['golive', 'selesai', 'invoicing', 'ditolak', 'revision_needed', 'awaiting_golive_validation']);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $statusVal = strtolower($request->status);
            if (in_array($statusVal, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'persetujuan', 'pending'])) {
                $query->whereIn('status', ['diajukan', 'awaiting_pm', 'awaiting_pmh']);
            } elseif (in_array($statusVal, ['analisa', 'analysis', 'dianalisis'])) {
                $query->whereIn('status', ['analisa', 'dianalisis']);
            } elseif (in_array($statusVal, ['development', 'develop', 'dikerjakan'])) {
                $query->whereIn('status', ['development', 'dikerjakan', 'sit', 'uat', 'training']);
            } elseif ($statusVal === 'sit') {
                $query->where('status', 'sit');
            } elseif ($statusVal === 'uat') {
                $query->where('status', 'uat');
            } elseif ($statusVal === 'training') {
                $query->where('status', 'training');
            } elseif (in_array($statusVal, ['golive', 'go-live', 'go_live', 'selesai'])) {
                $query->whereIn('status', ['golive', 'selesai', 'invoicing', 'awaiting_golive_validation']);
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
        $perPage = (int) $request->query('per_page', 10);
        $query = $this->buildCrQuery($request, ['validated', 'disetujui']);
        $changeRequests = $query->latest()->paginate($perPage)->withQueryString();

        return view('presales.table_page', [
            'pageTitle'      => 'Butuh Persetujuan',
            'activeMenu'     => 'persetujuan',
            'changeRequests' => $changeRequests,
            'totalData'      => $changeRequests->total(),
            'activeTab'      => $request->query('tab', 'semua'),
            'search'         => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
        ]);
    }

    public function development(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $query = $this->buildCrQuery($request, ['development', 'dikerjakan', 'sit', 'uat', 'training']);
        $changeRequests = $query->latest()->paginate($perPage)->withQueryString();

        return view('presales.table_page', [
            'pageTitle'      => 'Development CR',
            'activeMenu'     => 'development',
            'changeRequests' => $changeRequests,
            'totalData'      => $changeRequests->total(),
            'activeTab'      => $request->query('tab', 'semua'),
            'search'         => $request->query('search', ''),
            'selectedStatus' => $request->query('status', 'all'),
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
        $user = auth()->user();
        $dbNotifs = \App\Models\Notification::forUser($user)->with('changeRequest.client')->latest()->get();
        $notifications = [];

        foreach ($dbNotifs as $notif) {
            $cr = $notif->changeRequest;
            $date = $notif->created_at ?: now();
            $diffHours = max(1, (int) round(now()->diffInHours($date)));
            $timeId = $diffHours < 24 ? "{$diffHours} jam yang lalu" : ((int)round($diffHours/24)) . " hari yang lalu";
            $timeEn = $diffHours < 24 ? "{$diffHours} hours ago" : ((int)round($diffHours/24)) . " days ago";
            $detailUrl = $notif->action_url ?: ($cr ? route('presales.review', $cr) : route('presales.dashboard'));

            $theme = $notif->getColorTheme();
            $notifications[] = [
                'id' => 'db-notif-' . $notif->id,
                'category_id' => ucfirst($notif->category ?? 'Notifikasi'),
                'category_en' => ucfirst($notif->category ?? 'Notification'),
                'badge_bg' => $theme['badge_bg'] ?? '#E0F2FE',
                'badge_color' => $theme['badge_text'] ?? '#0284C7',
                'badge_border' => '#BAE6FD',
                'icon_bg' => $theme['bg'] ?? '#E0F2FE',
                'icon_color' => $theme['icon_color'] ?? '#0284C7',
                'icon' => $notif->icon ?: 'bi-bell-fill',
                'title_id' => $notif->title,
                'title_en' => $notif->title,
                'code' => $cr?->kode_cr ?? 'CR',
                'desc_id' => $notif->message,
                'desc_en' => $notif->message,
                'time_id' => $timeId,
                'time_en' => $timeEn,
                'url' => $detailUrl,
                'is_read' => (bool)$notif->is_read,
            ];
        }

        return view('presales.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
