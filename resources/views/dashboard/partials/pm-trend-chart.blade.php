@php
    $trendData = $pmMonthlyTrend ?? [];
    $periodText = $trendPeriodLabel ?? now()->subMonths(5)->format('M') . ' - ' . now()->format('M Y');
    $maxVal = 0;
    foreach ($trendData as $item) {
        $maxVal = max($maxVal, (int) ($item['masuk'] ?? 0), (int) ($item['keluar'] ?? 0), (int) ($item['batal'] ?? 0));
    }
    $yStep = max(1, (int) ceil($maxVal / 4));
    $yMax = $yStep * 4;
    $yGridYs = [175, 140, 105, 70, 35];
    $xCenters = [96, 181, 266, 351, 436, 511];
@endphp

<div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #EEF1F6; border: 1px solid #E2E8F0 !important;">
    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 0.98rem; font-weight: 800;">Tren CR Bulanan (Diagram Batang)</h5>
            <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Jumlah CR Masuk, Keluar, dan Dibatalkan ({{ $periodText }})</p>
        </div>
        <div class="d-flex align-items-center gap-3 ms-auto" style="font-size: 0.75rem; font-weight: 600;">
            <div class="d-flex align-items-center gap-1.5">
                <span style="width: 11px; height: 11px; background: #3B7CE7; border-radius: 2px; display: inline-block;"></span>
                <span style="color: #334155;">CR Masuk</span>
            </div>
            <div class="d-flex align-items-center gap-1.5">
                <span style="width: 11px; height: 11px; background: #31A773; border-radius: 2px; display: inline-block;"></span>
                <span style="color: #334155;">Keluar</span>
            </div>
            <div class="d-flex align-items-center gap-1.5">
                <span style="width: 11px; height: 11px; background: #EF4444; border-radius: 2px; display: inline-block;"></span>
                <span style="color: #334155;">Dibatalkan</span>
            </div>
        </div>
    </div>

    <div class="w-100 position-relative pt-2">
        <svg viewBox="0 0 540 230" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
            @foreach($yGridYs as $stepIdx => $gy)
                @php $tickVal = $yStep * $stepIdx; @endphp
                <line x1="45" y1="{{ $gy }}" x2="525" y2="{{ $gy }}"
                      stroke="{{ $stepIdx === 0 ? '#CBD5E1' : '#DCE2EC' }}"
                      @if($stepIdx > 0) stroke-dasharray="3, 3" @endif
                      stroke-width="{{ $stepIdx === 0 ? '1.2' : '1' }}" />
                <text x="34" y="{{ $gy + 4 }}" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">{{ $tickVal }}</text>
            @endforeach

            @foreach($trendData as $idx => $m)
                @php
                    $cx = $xCenters[$idx] ?? (96 + $idx * 85);
                    $bars = [
                        ['val' => (int) ($m['masuk'] ?? 0), 'offsetX' => -23, 'textOffsetX' => -16, 'color' => '#3B7CE7', 'title' => 'CR Masuk'],
                        ['val' => (int) ($m['keluar'] ?? 0), 'offsetX' => -7, 'textOffsetX' => 0, 'color' => '#31A773', 'title' => 'CR Keluar / Go-Live'],
                        ['val' => (int) ($m['batal'] ?? 0), 'offsetX' => 9, 'textOffsetX' => 16, 'color' => '#EF4444', 'title' => 'CR Dibatalkan'],
                    ];
                @endphp

                <g class="chart-month-group" style="cursor: pointer;">
                    @foreach($bars as $bar)
                        @php
                            $val = $bar['val'];
                            $h = $val > 0 ? max(4, round(($val / $yMax) * 140, 2)) : 2;
                            $y = $val > 0 ? (175 - $h) : 173;
                        @endphp
                        @if($val > 0)
                            <text x="{{ $cx + $bar['textOffsetX'] }}" y="{{ $y - 7 }}" font-size="10.5" font-weight="700" fill="{{ $bar['color'] }}" text-anchor="middle">{{ $val }}</text>
                        @endif
                        <rect x="{{ $cx + $bar['offsetX'] }}" y="{{ $y }}" width="14" height="{{ $h }}" rx="{{ $val > 0 ? 2 : 1 }}" fill="{{ $bar['color'] }}"
                              style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                            <title>{{ $m['month'] }}: {{ $val }} {{ $bar['title'] }}</title>
                        </rect>
                    @endforeach

                    <text x="{{ $cx }}" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle">{{ $m['month'] }}</text>
                </g>
            @endforeach
        </svg>
    </div>

    <div class="mt-2 py-1.5 px-3 rounded-2 text-end" style="background: #E3E6ED;">
        <span style="font-size: 0.72rem; color: #64748B; font-weight: 500;">Sumber Data: Data Real-Time Change Request ({{ $periodText }})</span>
    </div>
</div>

