<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardMetricsService
{
    public const ANALISA_STATUSES = [
        'diajukan',
        'awaiting_pm',
        'analisa',
        'analysis',
        'dianalisis',
        'awaiting_pmh',
        'review_pmh',
        'revision_needed',
        'validated',
        'disetujui',
        'quotation',
    ];

    public const DEV_STATUSES = ['development', 'develop', 'dikerjakan'];
    public const SIT_STATUSES = ['sit'];
    public const UAT_STATUSES = ['uat'];
    public const TRAINING_STATUSES = ['training', 'deploying', 'awaiting_golive_validation'];
    public const GOLIVE_STATUSES = ['golive', 'go-live', 'go_live', 'selesai', 'invoicing'];
    public const CANCELLED_STATUSES = ['ditolak', 'dibatalkan', 'cancelled'];

    private const MONTH_NAMES = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    /**
     * Hitung seluruh metrik dan data diagram dashboard berbasis data nyata di database.
     */
    public function getMetrics(?User $user, string $role = 'client'): array
    {
        $crs = $this->scopedChangeRequests($user, $role);

        $statusCounts = [
            'analisis' => $this->countByStatuses($crs, self::ANALISA_STATUSES),
            'develop' => $this->countByStatuses($crs, self::DEV_STATUSES),
            'sit' => $this->countByStatuses($crs, self::SIT_STATUSES),
            'uat' => $this->countByStatuses($crs, self::UAT_STATUSES),
            'deploying' => $this->countByStatuses($crs, self::TRAINING_STATUSES),
            'golive' => $this->countByStatuses($crs, self::GOLIVE_STATUSES),
        ];

        $figmaTotalCr = $crs->count();
        $figmaDevCr = $statusCounts['develop'];
        $figmaUatCr = $statusCounts['sit'] + $statusCounts['uat'];
        $figmaGoLiveCr = $statusCounts['golive'];

        $figmaNeedApprovalCr = match ($role) {
            'presales' => $this->countByStatuses($crs, ['validated', 'disetujui', 'quotation']),
            'pmh' => $this->countByStatuses($crs, ['awaiting_pmh', 'review_pmh', 'diajukan', 'awaiting_pm', 'analisa', 'analysis', 'dianalisis']),
            default => $this->countByStatuses($crs, ['diajukan', 'awaiting_pm', 'analisa', 'analysis', 'dianalisis']),
        };

        $roleMetrics = [
            'total' => $figmaTotalCr,
            'pending_pmh' => $this->countByStatuses($crs, ['awaiting_pmh', 'review_pmh', 'diajukan', 'awaiting_pm', 'analisa', 'analysis', 'dianalisis']),
            'pending_golive' => $this->countByStatuses($crs, ['awaiting_golive_validation', 'training', 'uat', 'sit']),
            'approved' => $this->countByStatuses($crs, array_merge(
                ['validated', 'disetujui', 'quotation'],
                self::DEV_STATUSES,
                self::SIT_STATUSES,
                self::UAT_STATUSES,
                self::TRAINING_STATUSES,
                self::GOLIVE_STATUSES
            )),
            'rejected' => $this->countByStatuses($crs, array_merge(['revision_needed'], self::CANCELLED_STATUSES)),
            'in_dev' => $figmaDevCr,
        ];

        if ($role === 'presales') {
            $notifPendingCount = $this->countByStatuses($crs, ['validated', 'disetujui', 'quotation']);
            $notifNeedVerifCount = $this->countByStatuses($crs, ['diajukan', 'awaiting_pm', 'analisa', 'analysis', 'dianalisis', 'awaiting_pmh', 'review_pmh']);
            $notifDevCount = $figmaDevCr;
        } else {
            $notifPendingCount = $this->countByStatuses($crs, ['diajukan', 'awaiting_pm', 'awaiting_pmh', 'review_pmh']);
            $notifNeedVerifCount = $this->countByStatuses($crs, ['analisa', 'analysis', 'dianalisis']);
            $notifDevCount = $figmaDevCr + $figmaUatCr;
        }

        [$pmMonthlyTrend, $trendPeriodLabel] = $this->buildMonthlyTrend($crs);
        [$dailyTrend, $pmRequestsToday, $pmRequestsYesterday] = $this->buildDailyTrend($crs);

        return compact(
            'statusCounts',
            'figmaTotalCr',
            'figmaNeedApprovalCr',
            'figmaDevCr',
            'figmaUatCr',
            'figmaGoLiveCr',
            'roleMetrics',
            'notifPendingCount',
            'notifNeedVerifCount',
            'notifDevCount',
            'pmMonthlyTrend',
            'trendPeriodLabel',
            'dailyTrend',
            'pmRequestsToday',
            'pmRequestsYesterday'
        );
    }

    private function scopedChangeRequests(?User $user, string $role): Collection
    {
        $query = ChangeRequest::query()
            ->select(['id', 'status', 'client_id', 'user_id', 'pm_id', 'tanggal_pengajuan', 'berita_acara_date', 'created_at', 'updated_at'])
            ->whereRaw("LOWER(COALESCE(status, '')) != 'draft'");

        if ($role === 'client') {
            $clientId = optional($user?->clientPic)->client_id;
            if ($clientId) {
                $query->where('client_id', $clientId);
            } elseif ($user) {
                $query->where('user_id', $user->id);
            }
        }

        return $query->get();
    }

    private function countByStatuses(Collection $crs, array $statuses): int
    {
        return $crs->filter(function (ChangeRequest $cr) use ($statuses) {
            return in_array(strtolower((string) ($cr->status ?? '')), $statuses, true);
        })->count();
    }

    private function buildMonthlyTrend(Collection $crs): array
    {
        $startMonth = Carbon::now()->startOfMonth()->subMonths(5);
        $buckets = [];

        for ($i = 0; $i < 6; $i++) {
            $dt = $startMonth->copy()->addMonths($i);
            $key = $dt->format('Y-m');
            $buckets[$key] = [
                'month' => self::MONTH_NAMES[(int) $dt->month],
                'year' => $dt->year,
                'masuk' => 0,
                'keluar' => 0,
                'batal' => 0,
            ];
        }

        foreach ($crs as $cr) {
            $status = strtolower((string) ($cr->status ?? ''));
            $submittedAt = $cr->tanggal_pengajuan ? Carbon::parse($cr->tanggal_pengajuan) : $cr->created_at;
            if ($submittedAt && isset($buckets[$submittedAt->format('Y-m')])) {
                $buckets[$submittedAt->format('Y-m')]['masuk']++;
            }

            if (in_array($status, self::GOLIVE_STATUSES, true)) {
                $completedAt = $cr->berita_acara_date
                    ? Carbon::parse($cr->berita_acara_date)
                    : ($cr->updated_at ?: $cr->created_at);
                if ($completedAt && isset($buckets[$completedAt->format('Y-m')])) {
                    $buckets[$completedAt->format('Y-m')]['keluar']++;
                }
            }

            if (in_array($status, self::CANCELLED_STATUSES, true)) {
                $cancelledAt = $cr->updated_at ?: $cr->created_at;
                if ($cancelledAt && isset($buckets[$cancelledAt->format('Y-m')])) {
                    $buckets[$cancelledAt->format('Y-m')]['batal']++;
                }
            }
        }

        $trend = array_values($buckets);
        $first = $trend[0];
        $last = $trend[count($trend) - 1];
        $periodLabel = $first['year'] === $last['year']
            ? "{$first['month']} - {$last['month']} {$last['year']}"
            : "{$first['month']} {$first['year']} - {$last['month']} {$last['year']}";

        return [$trend, $periodLabel];
    }

    private function buildDailyTrend(Collection $crs): array
    {
        $today = Carbon::today();
        $yesterdayKey = Carbon::yesterday()->toDateString();
        $todayKey = $today->toDateString();

        $buckets = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i);
            $dKey = $d->toDateString();
            $buckets[$dKey] = [
                'label' => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Kemarin' : $i . ' Hari Lalu'),
                'short_label' => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Kemarin' : $d->format('d/m')),
                'count' => 0,
            ];
        }

        $todayCount = 0;
        $yesterdayCount = 0;

        foreach ($crs as $cr) {
            $dt = $cr->tanggal_pengajuan ? Carbon::parse($cr->tanggal_pengajuan) : $cr->created_at;
            if (!$dt) {
                continue;
            }
            $dKey = $dt->toDateString();
            if (isset($buckets[$dKey])) {
                $buckets[$dKey]['count']++;
            }
            if ($dKey === $todayKey) {
                $todayCount++;
            } elseif ($dKey === $yesterdayKey) {
                $yesterdayCount++;
            }
        }

        return [array_values($buckets), $todayCount, $yesterdayCount];
    }
}

