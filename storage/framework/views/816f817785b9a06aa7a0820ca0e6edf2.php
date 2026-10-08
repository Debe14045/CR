<?php $__env->startSection('title', $viewMode === 'dashboard' ? 'Dashboard Monitoring CR' : strtoupper(str_replace('_', ' ', $viewMode)) . ' • Monitoring Change Request'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-shell">

<?php if($viewMode === 'dashboard'): ?>
    
    <?php
        $userRole = auth()->user()?->role ?? 'client';
        $isPmDashboard = in_array($userRole, ['pm', 'pmh']) || request('role') === 'pm' || request('view_as') === 'pm';
    ?>

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" data-i18n="dashboard_title" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <?php if(!$isPmDashboard): ?>
            <div>
                <a href="<?php echo e(route('change-requests.create')); ?>" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
                   style="background: #22C55E; border-radius: 8px; padding: 0.6rem 1.45rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(34,197,94,0.3); transition: transform 0.15s ease;">
                    <i class="bi bi-plus-lg"></i>
                    <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php
        $isPmh = (auth()->check() && auth()->user()->isPmh()) || $userRole === 'pmh' || request('role') === 'pmh';
        $linkCard1 = $isPmh ? route('pmh.change-requests') : route('change-requests.index', ['view' => 'total']);
        $linkCard2 = $isPmh ? route('pmh.change-requests', ['status' => 'diajukan']) : route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm']);
        $linkCard3 = $isPmh ? route('pmh.change-requests', ['status' => 'development']) : route('change-requests.index', ['view' => 'development']);
        $linkCard4 = $isPmh ? route('pmh.change-requests', ['status' => 'golive']) : route('change-requests.index', ['view' => 'golive']);
    ?>

    
    <div class="row g-3 mb-4">
        
        <div class="col-xl-3 col-md-6 col-12">
            <a href="<?php echo e($linkCard1); ?>" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_all_cr" style="font-size: 0.95rem;">Semua CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        <?php echo e($figmaTotalCr); ?>

                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <?php if($isPmDashboard): ?>
            
            <div class="col-xl-3 col-md-6 col-12">
                <a href="<?php echo e($linkCard2); ?>" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_need_approval" style="font-size: 0.95rem;">Butuh Persetujuan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                 <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            <?php echo e($figmaNeedApprovalCr ?? 0); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            
            <div class="col-xl-3 col-md-6 col-12">
                <a href="<?php echo e($linkCard3); ?>" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            <?php echo e($figmaDevCr); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php else: ?>
            
            <div class="col-xl-3 col-md-6 col-12">
                <a href="<?php echo e(route('change-requests.index', ['view' => 'development'])); ?>" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_development" style="font-size: 0.95rem;">Development</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            <?php echo e($figmaDevCr); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            
            <div class="col-xl-3 col-md-6 col-12">
                <a href="<?php echo e(route('change-requests.index', ['view' => 'uat'])); ?>" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_uat" style="font-size: 0.95rem;">UAT</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            <?php echo e($figmaUatCr); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        
        <div class="col-xl-3 col-md-6 col-12">
            <a href="<?php echo e($linkCard4); ?>" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" data-i18n="card_golive" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        <?php echo e($figmaGoLiveCr); ?>

                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        
        <div class="col-lg-7 col-12">
            <?php if($isPmDashboard): ?>
                
                <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 1rem; font-weight: 800;">Grafik Request dari PM</h5>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Total permintaan CR dari PM: hari ini, kemarin, dan hari-hari sebelumnya</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge px-3 py-2 fw-semibold" style="background: #EFF6FF; color: #0063D7; border-radius: 999px; font-size: 0.76rem;">
                                <i class="bi bi-clock-history me-1"></i> 7 Hari Terakhir
                            </span>
                        </div>
                    </div>

                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 px-3 rounded-3" style="background: #F0F9FF; border: 1px solid #BAE6FD;">
                                <div class="text-muted small" style="font-size: 0.72rem; font-weight: 600;">REQUEST HARI INI</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="fw-bold text-primary" style="font-size: 1.4rem;"><?php echo e($pmRequestsToday ?? 0); ?></span>
                                    <span class="text-muted small" style="font-size: 0.72rem;">CR Baru</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 px-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <div class="text-muted small" style="font-size: 0.72rem; font-weight: 600;">REQUEST KEMARIN</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="fw-bold text-dark" style="font-size: 1.4rem;"><?php echo e($pmRequestsYesterday ?? 0); ?></span>
                                    <span class="text-muted small" style="font-size: 0.72rem;">CR Diproses</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <?php
                        $trendList = $dailyTrend ?? [
                            ['label' => '5 Hari Lalu', 'short_label' => '03/10', 'count' => 1],
                            ['label' => '4 Hari Lalu', 'short_label' => '04/10', 'count' => 3],
                            ['label' => '3 Hari Lalu', 'short_label' => '05/10', 'count' => 2],
                            ['label' => '2 Hari Lalu', 'short_label' => '06/10', 'count' => 5],
                            ['label' => 'Sebelumnya', 'short_label' => '07/10', 'count' => 3],
                            ['label' => 'Kemarin', 'short_label' => 'Kemarin', 'count' => 2],
                            ['label' => 'Hari Ini', 'short_label' => 'Hari Ini', 'count' => 4],
                        ];
                        $maxCount = max(array_merge([6], array_column($trendList, 'count')));
                        $points = [];
                        $svgWidth = 520;
                        $svgHeight = 160;
                        $startX = 50;
                        $endX = 490;
                        $stepX = ($endX - $startX) / (count($trendList) - 1 ?: 1);

                        foreach ($trendList as $idx => $t) {
                            $x = $startX + ($idx * $stepX);
                            $val = $t['count'];
                            // Y: 20 (top) to 135 (baseline)
                            $y = 135 - (($val / ($maxCount ?: 1)) * 105);
                            $points[] = ['x' => $x, 'y' => $y, 'count' => $val, 'label' => $t['short_label']];
                        }

                        // Build SVG Polyline & Area Path
                        $pathD = 'M ' . $points[0]['x'] . ' ' . $points[0]['y'];
                        for ($k = 1; $k < count($points); $k++) {
                            $prev = $points[$k - 1];
                            $curr = $points[$k];
                            $cx1 = $prev['x'] + ($stepX / 2);
                            $cy1 = $prev['y'];
                            $cx2 = $prev['x'] + ($stepX / 2);
                            $cy2 = $curr['y'];
                            $pathD .= " C $cx1 $cy1, $cx2 $cy2, {$curr['x']} {$curr['y']}";
                        }
                        $areaD = $pathD . " L {$points[count($points)-1]['x']} 140 L {$points[0]['x']} 140 Z";
                    ?>

                    <div class="w-100 position-relative pt-1">
                        <svg viewBox="0 0 530 175" class="w-100" style="overflow: visible; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <defs>
                                <linearGradient id="gradientPmRequest" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#0063D7" stop-opacity="0.25" />
                                    <stop offset="100%" stop-color="#0063D7" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            
                            <line x1="45" y1="30" x2="505" y2="30" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="35" y="34" font-size="10" fill="#94A3B8" text-anchor="end"><?php echo e($maxCount); ?></text>

                            <line x1="45" y1="82" x2="505" y2="82" stroke="#F1F5F9" stroke-width="1.2" />
                            <text x="35" y="86" font-size="10" fill="#94A3B8" text-anchor="end"><?php echo e(round($maxCount / 2)); ?></text>

                            <line x1="45" y1="135" x2="505" y2="135" stroke="#E2E8F0" stroke-width="1.2" />
                            <text x="35" y="139" font-size="10" fill="#94A3B8" text-anchor="end">0</text>

                            
                            <path d="<?php echo e($areaD); ?>" fill="url(#gradientPmRequest)" />

                            
                            <path d="<?php echo e($pathD); ?>" fill="none" stroke="#0063D7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                            
                            <?php $__currentLoopData = $points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <circle cx="<?php echo e($p['x']); ?>" cy="<?php echo e($p['y']); ?>" r="4.5" fill="#0063D7" stroke="#FFFFFF" stroke-width="2" />
                                <text x="<?php echo e($p['x']); ?>" y="<?php echo e($p['y'] - 8); ?>" font-size="11" font-weight="700" fill="#02376A" text-anchor="middle"><?php echo e($p['count']); ?></text>
                                <text x="<?php echo e($p['x']); ?>" y="156" font-size="10" font-weight="600" fill="#64748B" text-anchor="middle"><?php echo e($p['label']); ?></text>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </svg>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top text-muted" style="font-size: 0.74rem;">
                        <span class="d-flex align-items-center gap-1">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0063D7; display: inline-block;"></span>
                            Volume pengajuan CR dari PM per hari
                        </span>
                        <span class="fw-semibold text-dark">Data Terkini &bull; Real-time Monitoring</span>
                    </div>
                </div>
            <?php else: ?>
                
                <div class="card p-4 border-0 h-100" style="border-radius: 20px; background: #F0F3F6; border: 1px solid #E2E8F0 !important;">
                    <div>
                        <h5 class="fw-bold mb-1 text-uppercase" data-i18n="status_title" style="color: #0F172A; font-size: 1rem; font-weight: 800; letter-spacing: 0.02em;">STATUS</h5>
                        <p class="text-muted small mb-0" data-i18n="status_subtitle" style="font-size: 0.78rem; color: #64748B;">Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI</p>
                    </div>
                    <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                    <div class="row align-items-center g-3 pt-1">
                        
                        <div class="col-sm-5 col-12 text-center">
                            <div style="position: relative; width: 155px; height: 155px; margin: 0 auto;">
                                <?php
                                    $cAnalisa  = $statusCounts['analisis'] ?? 2;
                                    $cDevelop  = $statusCounts['develop'] ?? 0;
                                    $cSit      = $statusCounts['sit'] ?? 1;
                                    $cUat      = $statusCounts['uat'] ?? 0;
                                    $cTraining = $statusCounts['deploying'] ?? 0;
                                    $cGoLive   = $statusCounts['golive'] ?? 2;
                                    $totalDonut = $cAnalisa + $cDevelop + $cSit + $cUat + $cTraining + $cGoLive;
                                    if ($totalDonut == 0) $totalDonut = 1;

                                    $p1 = ($cAnalisa / $totalDonut) * 100;
                                    $p2 = $p1 + (($cDevelop / $totalDonut) * 100);
                                    $p3 = $p2 + (($cSit / $totalDonut) * 100);
                                    $p4 = $p3 + (($cUat / $totalDonut) * 100);
                                    $p5 = $p4 + (($cTraining / $totalDonut) * 100);
                                ?>
                                <div style="width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(
                                    #00A3FF 0% <?php echo e($p1); ?>%,
                                    #4338CA <?php echo e($p1); ?>% <?php echo e($p2); ?>%,
                                    #F59E0B <?php echo e($p2); ?>% <?php echo e($p3); ?>%,
                                    #EC4899 <?php echo e($p3); ?>% <?php echo e($p4); ?>%,
                                    #047857 <?php echo e($p4); ?>% <?php echo e($p5); ?>%,
                                    #10B981 <?php echo e($p5); ?>% 100%
                                ); display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 96px; height: 96px; background: #F0F3F6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="fw-bold" style="font-size: 2.2rem; line-height: 1; color: #0F172A; font-weight: 800;"><?php echo e($totalDonut); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-sm-7 col-12">
                            <div class="d-flex flex-column gap-3">
                                
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #00A3FF; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_analisa">Analisa</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: <?php echo e(($cAnalisa / $totalDonut) * 100); ?>%; height: 100%; background: #00A3FF; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;"><?php echo e($cAnalisa); ?></span>
                                </div>

                                
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #4338CA; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_develop">Develop</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: <?php echo e(($cDevelop / $totalDonut) * 100); ?>%; height: 100%; background: #4338CA; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;"><?php echo e($cDevelop); ?></span>
                                </div>

                                
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_sit">SIT</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: <?php echo e(($cSit / $totalDonut) * 100); ?>%; height: 100%; background: #F59E0B; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;"><?php echo e($cSit); ?></span>
                                </div>

                                
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EC4899; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_uat">UAT</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: <?php echo e(($cUat / $totalDonut) * 100); ?>%; height: 100%; background: #EC4899; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;"><?php echo e($cUat); ?></span>
                                </div>

                                
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #047857; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_training">Training</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: <?php echo e(($cTraining / $totalDonut) * 100); ?>%; height: 100%; background: #047857; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;"><?php echo e($cTraining); ?></span>
                                </div>

                                
                                <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                    <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; flex-shrink: 0;"></span>
                                        <span data-i18n="stage_golive">Go Live</span>
                                    </span>
                                    <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                        <div style="width: <?php echo e(($cGoLive / $totalDonut) * 100); ?>%; height: 100%; background: #10B981; border-radius: 999px;"></div>
                                    </div>
                                    <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;"><?php echo e($cGoLive); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="col-lg-5 col-12">
            <div class="card p-4 border-0 h-100" style="border-radius: 20px; background: #F0F3F6; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="<?php echo e($isPmh ? route('pmh.notifications') : 'javascript:void(0)'); ?>" class="text-decoration-none fw-semibold" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    
                    <a href="<?php echo e($isPmh ? route('pmh.persetujuan') : route('change-requests.index', ['status' => 'diajukan'])); ?>" class="text-decoration-none">
                        <div class="p-3" style="background: #FEF3F4; border: 1px solid #FECDCA; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #DC2626; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #991B1B; font-size: 0.82rem;">
                                        <?php echo e($notifPendingCount ?? 3); ?> Perubahan Ruang Lingkup CR menunggu persetujuan
                                    </div>
                                    <div style="color: #DC2626; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Status : Menunggu Persetujuan PM Head
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    
                    <a href="<?php echo e($isPmh ? route('pmh.change-requests', ['status' => 'analisa']) : route('change-requests.index', ['status' => 'analisa'])); ?>" class="text-decoration-none">
                        <div class="p-3" style="background: #FDFBF0; border: 1px solid #FEDF89; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #92400E; font-size: 0.82rem;">
                                        <?php echo e($notifNeedVerifCount ?? 3); ?> CR Menunggu Verifikasi Kebutuhan
                                    </div>
                                    <div style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 1. Analisis Kebutuhan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    
                    <a href="<?php echo e($isPmh ? route('pmh.development') : route('change-requests.index', ['view' => 'development'])); ?>" class="text-decoration-none">
                        <div class="p-3" style="background: #F2F7FE; border: 1px solid #BAE6FD; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #1E40AF; font-size: 0.82rem;">
                                        <?php echo e($notifDevCount ?? 1); ?> CR Sedang Dikerjakan & Butuh Tindak Lanjut
                                    </div>
                                    <div style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 2. Development & Testing
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="visually-hidden" aria-hidden="true">
        <?php $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($cr->judul); ?> - <?php echo e(in_array($cr->status, ['diajukan', 'awaiting_pm']) ? 'menunggu review awal' : ''); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php else: ?>
    

    <?php
        $pageTitle = match($viewMode) {
            'development' => 'DEVELOPMENT',
            'uat' => 'UAT',
            'golive' => 'GO LIVE',
            'status' => 'STATUS CR',
            default => 'TOTAL CR',
        };
    ?>

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;"><?php echo e($pageTitle); ?></h1>
            <p class="text-muted mb-0" data-i18n="dashboard_subtitle" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('change-requests.create')); ?>" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #22C55E; border-radius: 8px; padding: 0.55rem 1.3rem; font-size: 0.84rem; border: none; box-shadow: 0 4px 12px rgba(34,197,94,0.3);">
                <i class="bi bi-plus-lg"></i>
                <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    
    <div class="card overflow-hidden shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_submission_date" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 170px;">TGL PENGAJUAN</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_requester" style="font-size: 0.72rem; letter-spacing: 0.06em;">PEMOHON</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_cr_title" style="font-size: 0.72rem; letter-spacing: 0.06em;">JUDUL CR</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase" data-i18n="th_status" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 150px;">STATUS</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase text-center" data-i18n="th_priority" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 120px;">PRIORITAS</th>
                        <th class="py-3 px-4 text-muted fw-bold text-uppercase text-end" data-i18n="th_action" style="font-size: 0.72rem; letter-spacing: 0.06em; width: 130px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $avatarColors = ['#F59E0B', '#10B981', '#3B82F6', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
                    ?>

                    <?php $__empty_1 = true; $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $colorIndex = $index % count($avatarColors);
                            $avColor = $avatarColors[$colorIndex];

                            $pemohonName = $cr->owner_cr ?: ($cr->user?->name ?: 'Totok Antok');
                            $pemohonInitials = strtoupper(substr($pemohonName, 0, 1));
                            $companyName = $cr->klien ?: ($cr->client?->company ?: 'PT Maju Bersama');

                            // Status badge mapping
                            $statusText = 'Diajukan';
                            $statusBg = '#FEF3C7';
                            $statusColor = '#D97706';

                            if (in_array($cr->status, ['analisa', 'dianalisis'])) {
                                $statusText = 'Analisis';
                                $statusBg = '#DBEAFE';
                                $statusColor = '#2563EB';
                            } elseif (in_array($cr->status, ['development', 'dikerjakan'])) {
                                $statusText = 'Develop';
                                $statusBg = '#EDE9FE';
                                $statusColor = '#7C3AED';
                            } elseif (in_array($cr->status, ['uat', 'sit'])) {
                                $statusText = 'UAT';
                                $statusBg = '#FCE7F3';
                                $statusColor = '#DB2777';
                            } elseif (in_array($cr->status, ['golive', 'selesai', 'invoicing'])) {
                                $statusText = 'Go-Live';
                                $statusBg = '#D1FAE5';
                                $statusColor = '#059669';
                            }

                            // Priority badge
                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['urgent', 'kritis', 'tinggi', 'high']);
                        ?>
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                    <?php echo e($cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->translatedFormat('d M Y') : '15 Sep 2024'); ?>

                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    <?php echo e($cr->created_at ? $cr->created_at->format('H:i:s') : '09:30:12'); ?>

                                </div>
                            </td>

                            
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width: 34px; height: 34px; background: <?php echo e($avColor); ?>; font-size: 0.82rem;">
                                        <?php echo e($pemohonInitials); ?>

                                    </div>
                                    <div style="line-height: 1.2;">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?php echo e($pemohonName); ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?php echo e($companyName); ?></div>
                                    </div>
                                </div>
                            </td>

                            
                            <td class="py-3 px-4">
                                <div style="line-height: 1.25;">
                                    <a href="<?php echo e(route('change-requests.show', $cr)); ?>" class="fw-bold text-dark text-decoration-none hover-primary" style="font-size: 0.88rem;">
                                        <?php echo e($cr->judul); ?>

                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <?php echo e($cr->proyek_terkait ?: 'Akses & Dashboard'); ?>

                                    </div>
                                </div>
                            </td>

                            
                            <td class="py-3 px-4">
                                <span class="d-inline-flex align-items-center gap-1 px-3 py-1 fw-bold"
                                      style="background: <?php echo e($statusBg); ?>; color: <?php echo e($statusColor); ?>; border-radius: 999px; font-size: 0.76rem;">
                                    ● <?php echo e($statusText); ?>

                                </span>
                            </td>

                            
                            <td class="py-3 px-4 text-center">
                                <?php if($isUrgent): ?>
                                    <span class="badge fw-bold" style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.72rem; padding: 4px 8px;">
                                        HIGH
                                    </span>
                                <?php else: ?>
                                    <span class="badge fw-medium" style="background: #F1F5F9; color: #64748B; border-radius: 6px; font-size: 0.72rem; padding: 4px 8px;">
                                        NORMAL
                                    </span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="py-3 px-4 text-end">
                                <a href="<?php echo e(route('change-requests.show', $cr)); ?>"
                                   class="btn text-white fw-bold px-3 py-1 d-inline-flex align-items-center gap-1 shadow-sm"
                                   style="background: #2563EB; border-radius: 999px; font-size: 0.78rem; border: none;">
                                    <span>Review</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-inbox fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Change Request pada kategori ini</div>
                                <div class="small mt-1">Gunakan tombol "+ Ajukan CR Baru" untuk membuat pengajuan baru.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($changeRequests->hasPages()): ?>
            <div class="px-4 py-3 border-top w-100">
                <?php echo e($changeRequests->links()); ?>

            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Magang_ITPI\CR\resources\views/cr/index.blade.php ENDPATH**/ ?>