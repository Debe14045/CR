@php
    $counts = $statusCounts ?? [];
    $cAnalisa  = (int) ($counts['analisis'] ?? 0);
    $cDevelop  = (int) ($counts['develop'] ?? 0);
    $cSit      = (int) ($counts['sit'] ?? 0);
    $cUat      = (int) ($counts['uat'] ?? 0);
    $cTraining = (int) ($counts['deploying'] ?? 0);
    $cGoLive   = (int) ($counts['golive'] ?? 0);

    $totalDonut = $cAnalisa + $cDevelop + $cSit + $cUat + $cTraining + $cGoLive;
    $totalDonutCalc = $totalDonut > 0 ? $totalDonut : 1;

    $cardBg = $cardBg ?? '#F0F3F6';
    $cardBorder = $cardBorder ?? '1.5px solid #D6E0EC !important';
    $dividerColor = $dividerColor ?? '#D5DDE6';
    $useWhiteCenter = $cardBg === '#FFFFFF';

    $stages = [
        ['label' => $trainingAliases['analisis'] ?? 'Analisa', 'i18n' => 'stage_analisa', 'color' => $useWhiteCenter ? '#00A3FF' : '#0EA5E9', 'count' => $cAnalisa],
        ['label' => 'Develop', 'i18n' => 'stage_develop', 'color' => $useWhiteCenter ? '#4338CA' : '#6366F1', 'count' => $cDevelop],
        ['label' => 'SIT', 'i18n' => 'stage_sit', 'color' => '#F59E0B', 'count' => $cSit],
        ['label' => 'UAT', 'i18n' => 'stage_uat', 'color' => '#EC4899', 'count' => $cUat],
        ['label' => $trainingLabel ?? 'Training', 'i18n' => 'stage_training', 'color' => $useWhiteCenter ? '#047857' : '#059669', 'count' => $cTraining],
        ['label' => 'Go Live', 'i18n' => 'stage_golive', 'color' => $useWhiteCenter ? '#10B981' : '#16A34A', 'count' => $cGoLive],
    ];

    $stops = [];
    $cursor = 0.0;
    foreach ($stages as $stage) {
        $pct = ($stage['count'] / $totalDonutCalc) * 100;
        $next = $cursor + $pct;
        $stops[] = "{$stage['color']} {$cursor}% {$next}%";
        $cursor = $next;
    }
    $donutBackground = $totalDonut > 0
        ? 'conic-gradient(' . implode(', ', $stops) . ')'
        : '#E2E8F0';
@endphp

<div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: {{ $cardBg }}; border: {{ $cardBorder }};">
    <div>
        <h5 class="fw-bold mb-1 text-uppercase" data-i18n="status_title" style="color: #0F172A; font-size: {{ $useWhiteCenter ? '1rem' : '1.05rem' }}; font-weight: 800; letter-spacing: 0.02em;">STATUS</h5>
        <p class="text-muted small mb-0" data-i18n="status_subtitle" style="font-size: {{ $useWhiteCenter ? '0.78rem' : '0.8rem' }}; color: #64748B;">Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI</p>
    </div>
    <div style="height: 1px; background: {{ $dividerColor }}; margin: {{ $useWhiteCenter ? '0.85rem 0 1.25rem' : '0.75rem 0 1.25rem' }};"></div>

    <div class="row align-items-center g-3 pt-1">
        <div class="col-sm-5 col-12 text-center">
            <div style="position: relative; width: 155px; height: 155px; margin: 0 auto;">
                <div style="width: 100%; height: 100%; border-radius: 50%; background: {{ $donutBackground }}; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 96px; height: 96px; background: {{ $cardBg }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="fw-bold" style="font-size: {{ $useWhiteCenter ? '2.2rem' : '2.35rem' }}; line-height: 1; color: #0F172A; font-weight: 800;">{{ $totalDonut }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-7 col-12">
            <div class="d-flex flex-column gap-3">
                @foreach($stages as $stage)
                    @php
                        $pctWidth = $totalDonut > 0 ? (($stage['count'] / $totalDonutCalc) * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                        <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $stage['color'] }}; flex-shrink: 0;"></span>
                            <span data-i18n="{{ $stage['i18n'] }}">{{ $stage['label'] }}</span>
                        </span>
                        <div style="flex: 1; height: {{ $useWhiteCenter ? '4px' : '5px' }}; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                            <div style="width: {{ $pctWidth }}%; height: 100%; background: {{ $stage['color'] }}; border-radius: 999px;"></div>
                        </div>
                        <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{ $stage['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

