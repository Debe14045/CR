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
        <div>
            <a href="<?php echo e(route('change-requests.create')); ?>" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #50CD89; border-radius: 9999px; padding: 0.58rem 1.45rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(80,205,137,0.3); transition: transform 0.15s ease;">
                <i class="bi bi-plus-lg"></i>
                <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        
        <div class="col-xl-3 col-md-6 col-12">
            <a href="<?php echo e(route('change-requests.index', ['view' => 'total'])); ?>" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #014DA1; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(1,77,161,0.35)';"
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
                <a href="<?php echo e(route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm'])); ?>" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #144272; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" data-i18n="card_need_approval" style="font-size: 0.95rem;">Butuh Persetujuan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                                <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                            </div>
                        </div>
                        <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                            <?php echo e($figmaNeedApprovalCr ?? 4); ?>

                        </div>
                        <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                            <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                            <span data-i18n="card_active_month">Aktif Bulan Ini</span>
                        </div>
                    </div>
                </a>
            </div>

            
            <div class="col-xl-3 col-md-6 col-12">
                <a href="<?php echo e(route('change-requests.index', ['view' => 'development'])); ?>" class="text-decoration-none d-block h-100">
                    <div class="p-4 text-white h-100 shadow-sm"
                         style="background: #205295; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
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
                         style="background: #144272; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
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
                         style="background: #205295; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
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
            <a href="<?php echo e(route('change-requests.index', ['view' => 'golive'])); ?>" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #2C74B3; border-radius: 16px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(44,116,179,0.35)';"
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
                
                <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #EEF1F6; border: 1px solid #E2E8F0 !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #0F172A; font-size: 0.98rem; font-weight: 800;">Tren CR Bulanan (Diagram Batang)</h5>
                            <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Jumlah CR Masuk, Keluar, dan Dibatalkan (Januari - Juni)</p>
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
                            
                            <line x1="45" y1="35" x2="525" y2="35" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="39" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">5</text>

                            <line x1="45" y1="65" x2="525" y2="65" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="69" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">4</text>

                            <line x1="45" y1="95" x2="525" y2="95" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="99" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">3</text>

                            <line x1="45" y1="135" x2="525" y2="135" stroke="#DCE2EC" stroke-dasharray="3, 3" stroke-width="1" />
                            <text x="34" y="139" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">1</text>

                            <line x1="45" y1="175" x2="525" y2="175" stroke="#CBD5E1" stroke-width="1.2" />
                            <text x="34" y="179" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="end">0</text>

                            <?php
                                $trendData = $pmMonthlyTrend ?? [
                                    ['month' => 'Jan', 'masuk' => 3, 'keluar' => 0, 'batal' => 0],
                                    ['month' => 'Feb', 'masuk' => 4, 'keluar' => 1, 'batal' => 0],
                                    ['month' => 'Mar', 'masuk' => 1, 'keluar' => 1, 'batal' => 1],
                                    ['month' => 'Apr', 'masuk' => 5, 'keluar' => 2, 'batal' => 0],
                                    ['month' => 'Mei', 'masuk' => 3, 'keluar' => 1, 'batal' => 1],
                                    ['month' => 'Jun', 'masuk' => 4, 'keluar' => 2, 'batal' => 0],
                                ];
                                $xCenters = [96, 181, 266, 351, 436, 511];
                            ?>

                            <?php $__currentLoopData = $trendData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $cx = $xCenters[$idx] ?? (96 + $idx * 85);
                                    $vMasuk = $m['masuk'];
                                    $vKeluar = $m['keluar'];
                                    $vBatal = $m['batal'];

                                    // Masuk Bar (#3B7CE7)
                                    $hMasuk = $vMasuk > 0 ? min(140, $vMasuk * 28) : 2;
                                    $yMasuk = $vMasuk > 0 ? (175 - $hMasuk) : 173;
                                    $tYMasuk = $vMasuk > 0 ? ($yMasuk - 7) : 169;

                                    // Keluar Bar (#31A773)
                                    $hKeluar = $vKeluar > 0 ? min(140, $vKeluar * 28) : 2;
                                    $yKeluar = $vKeluar > 0 ? (175 - $hKeluar) : 173;
                                    $tYKeluar = $vKeluar > 0 ? ($yKeluar - 7) : 169;

                                    // Batal Bar (#EF4444)
                                    $hBatal = $vBatal > 0 ? min(140, $vBatal * 28) : 2;
                                    $yBatal = $vBatal > 0 ? (175 - $hBatal) : 173;
                                    $tYBatal = $vBatal > 0 ? ($yBatal - 7) : 169;
                                ?>

                                
                                <g class="chart-month-group" style="cursor: pointer;">
                                    
                                    <text x="<?php echo e($cx - 16); ?>" y="<?php echo e($tYMasuk); ?>" font-size="10.5" font-weight="700" fill="#3B7CE7" text-anchor="middle"><?php echo e($vMasuk); ?></text>
                                    <rect x="<?php echo e($cx - 23); ?>" y="<?php echo e($yMasuk); ?>" width="14" height="<?php echo e($hMasuk); ?>" rx="2" fill="#3B7CE7" style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                                        <title><?php echo e($m['month']); ?>: <?php echo e($vMasuk); ?> CR Masuk</title>
                                    </rect>

                                    
                                    <text x="<?php echo e($cx); ?>" y="<?php echo e($tYKeluar); ?>" font-size="<?php echo e($vKeluar > 0 ? 10 : 9); ?>" font-weight="600" fill="#31A773" text-anchor="middle"><?php echo e($vKeluar); ?></text>
                                    <rect x="<?php echo e($cx - 7); ?>" y="<?php echo e($yKeluar); ?>" width="14" height="<?php echo e($hKeluar); ?>" rx="<?php echo e($vKeluar > 0 ? 2 : 1); ?>" fill="#31A773" style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                                        <title><?php echo e($m['month']); ?>: <?php echo e($vKeluar); ?> CR Keluar / Selesai</title>
                                    </rect>

                                    
                                    <text x="<?php echo e($cx + 16); ?>" y="<?php echo e($tYBatal); ?>" font-size="<?php echo e($vBatal > 0 ? 10 : 9); ?>" font-weight="600" fill="#EF4444" text-anchor="middle"><?php echo e($vBatal); ?></text>
                                    <rect x="<?php echo e($cx + 9); ?>" y="<?php echo e($yBatal); ?>" width="14" height="<?php echo e($hBatal); ?>" rx="<?php echo e($vBatal > 0 ? 2 : 1); ?>" fill="#EF4444" style="transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                                        <title><?php echo e($m['month']); ?>: <?php echo e($vBatal); ?> CR Dibatalkan</title>
                                    </rect>

                                    
                                    <text x="<?php echo e($cx); ?>" y="196" font-size="11.5" font-weight="600" fill="#64748B" text-anchor="middle"><?php echo e($m['month']); ?></text>
                                </g>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </svg>
                    </div>

                    
                    <div class="mt-2 py-1.5 px-3 rounded-2 text-end" style="background: #E3E6ED;">
                        <span style="font-size: 0.72rem; color: #64748B; font-weight: 500;">Sumber Data: Laporan Internal CR</span>
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
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 7.5px; height: 7.5px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" data-i18n="notif_title" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="<?php echo e(route('notifications.index')); ?>" class="text-decoration-none fw-semibold" data-i18n="notif_view_all" style="color: #009EF7; font-size: 0.82rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0 mt-1" data-i18n="notif_subtitle" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.15rem;"></div>

                <div class="d-flex flex-column gap-3">
                    <?php if(isset($recentNotifications) && $recentNotifications->isNotEmpty()): ?>
                        <?php $__currentLoopData = $recentNotifications->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rNotif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $rTheme = $rNotif->getColorTheme();
                                $rUrl = route('notifications.go', $rNotif->id);
                            ?>
                            <a href="<?php echo e($rUrl); ?>" class="text-decoration-none d-block">
                                <div style="background: <?php echo e($rTheme['bg']); ?>; border: 1.5px solid <?php echo e($rTheme['border']); ?>; border-radius: 14px; padding: 13px 15px; transition: transform 0.15s ease, box-shadow 0.15s ease;"
                                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 14px rgba(0,0,0,0.06)';"
                                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                                        <div style="flex-shrink: 0; width: 22px; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                                            <i class="bi <?php echo e($rNotif->icon); ?>" style="color: <?php echo e($rTheme['icon_color']); ?>; font-size: 1.15rem; line-height: 1;"></i>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="fw-bold text-truncate" style="color: <?php echo e($rTheme['text']); ?>; font-size: 0.84rem; font-weight: 700; line-height: 1.35;">
                                                    <?php echo e($rNotif->title); ?>

                                                </div>
                                                <span class="text-muted ms-2" style="font-size: 0.65rem; white-space: nowrap;"><?php echo e($rNotif->formatted_time); ?></span>
                                            </div>
                                            <div class="text-truncate" style="color: <?php echo e($rTheme['subtext']); ?>; font-size: 0.74rem; font-weight: 500; line-height: 1.3;">
                                                <?php echo e($rNotif->message); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        
                        
                        <a href="<?php echo e(route('change-requests.index', ['view' => 'total', 'status' => 'awaiting_pm'])); ?>" class="text-decoration-none d-block">
                            <div style="background: #FEF2F2; border: 1.5px solid #FECDCA; border-radius: 14px; padding: 14px 16px; transition: transform 0.15s ease, box-shadow 0.15s ease;"
                                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 14px rgba(220,38,38,0.08)';"
                                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <div style="flex-shrink: 0; width: 22px; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                                        <i class="bi bi-exclamation-triangle-fill" style="color: #DC2626; font-size: 1.15rem; line-height: 1;"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="fw-bold" data-i18n="notif_alert1_title" style="color: #991B1B; font-size: 0.84rem; font-weight: 700; line-height: 1.35; margin-bottom: 4px;">
                                            3 Perubahan Ruang Lingkup CR menunggu persetujuan &gt; 14 hari
                                        </div>
                                        <div data-i18n="notif_alert1_desc" style="color: #DC2626; font-size: 0.74rem; font-weight: 500; line-height: 1.3;">
                                            Status : Menunggu Persetujuan PM
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        
                        <a href="<?php echo e(route('change-requests.index', ['view' => 'total', 'tab' => 'pending'])); ?>" class="text-decoration-none d-block">
                            <div style="background: #FFFBEB; border: 1.5px solid #FEDF89; border-radius: 14px; padding: 14px 16px; transition: transform 0.15s ease, box-shadow 0.15s ease;"
                                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 14px rgba(217,119,6,0.08)';"
                                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <div style="flex-shrink: 0; width: 22px; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                                        <i class="bi bi-exclamation-triangle-fill" style="color: #D97706; font-size: 1.15rem; line-height: 1;"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="fw-bold" data-i18n="notif_alert2_title" style="color: #92400E; font-size: 0.84rem; font-weight: 700; line-height: 1.35; margin-bottom: 4px;">
                                            8 CR Baru Menunggu Verifikasi Kebutuhan
                                        </div>
                                        <div data-i18n="notif_alert2_desc" style="color: #B45309; font-size: 0.74rem; font-weight: 500; line-height: 1.3;">
                                            Tahapan : 1. Analisis Kebutuhan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>

                        
                        <a href="<?php echo e(route('change-requests.index', ['view' => 'uat'])); ?>" class="text-decoration-none d-block">
                            <div style="background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: 14px; padding: 14px 16px; transition: transform 0.15s ease, box-shadow 0.15s ease;"
                                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 14px rgba(37,99,235,0.08)';"
                                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <div style="flex-shrink: 0; width: 22px; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                                        <i class="bi bi-info-circle-fill" style="color: #2563EB; font-size: 1.15rem; line-height: 1;"></i>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="fw-bold" data-i18n="notif_alert3_title" style="color: #1E40AF; font-size: 0.84rem; font-weight: 700; line-height: 1.35; margin-bottom: 4px;">
                                            5 Masukan Pengguna UAT butuh tindak lanjut
                                        </div>
                                        <div data-i18n="notif_alert3_desc" style="color: #2563EB; font-size: 0.74rem; font-weight: 500; line-height: 1.3;">
                                            Tahapan : 4. UAT &amp; Pengujian
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="visually-hidden" aria-hidden="true">
        <?php $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($cr->judul); ?> - <?php echo e(in_array($cr->status, ['diajukan', 'awaiting_pm']) ? 'menunggu review awal' : ''); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php elseif($viewMode === 'draft'): ?>
    

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
             style="border-radius: 12px; border: none; background: #FEE2E2; color: #991B1B; font-size: 0.88rem;" role="alert">
            <i class="bi bi-exclamation-circle-fill" style="color: #DC2626;"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <div class="mb-4 pb-3" style="border-bottom: 2px solid #DCE6EE;">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">DRAFT CR</h1>
        <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
    </div>

    <style>
        .draft-figma-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            --bs-table-bg: transparent !important;
        }
        .draft-figma-table thead tr th {
            background-color: #C0CDDA !important;
            color: #1E293B !important;
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            border: none !important;
            border-bottom: 1px solid #B0BDCA !important;
            padding-top: 14px !important;
            padding-bottom: 14px !important;
        }
        .draft-figma-table tbody tr {
            background-color: #FFFFFF !important;
        }
        .draft-figma-table tbody tr:hover {
            background-color: #F8FAFC !important;
        }
        .draft-figma-table tbody td {
            background-color: transparent !important;
            border-bottom: 1px solid #EEF2F6 !important;
        }
        .draft-figma-table tbody tr:last-child td {
            border-bottom: none !important;
        }
    </style>

    
    <div class="card shadow-none border-0 mb-4" style="border: 1.5px solid #CBD8E2 !important; border-radius: 14px; background: #FFFFFF; overflow: hidden;">
        <div class="table-responsive">
            <table class="table draft-figma-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #C0CDDA !important; color: #1E293B !important; font-size: 0.74rem; letter-spacing: 0.06em; width: 60px; border-bottom: 1px solid #B0BDCA !important;">NO</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #C0CDDA !important; color: #1E293B !important; font-size: 0.74rem; letter-spacing: 0.06em; width: 180px; border-bottom: 1px solid #B0BDCA !important;">TERAKHIR DISIMPAN</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #C0CDDA !important; color: #1E293B !important; font-size: 0.74rem; letter-spacing: 0.06em; width: 220px; border-bottom: 1px solid #B0BDCA !important;">PEMOHON</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #C0CDDA !important; color: #1E293B !important; font-size: 0.74rem; letter-spacing: 0.06em; border-bottom: 1px solid #B0BDCA !important;">JUDUL CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #C0CDDA !important; color: #1E293B !important; font-size: 0.74rem; letter-spacing: 0.06em; width: 260px; border-bottom: 1px solid #B0BDCA !important;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $avatarFigmaColors = [
                            'Fikri Adnan'   => '#E68A00',
                            'Maya Sari'     => '#8B5CF6',
                            'Rizky Pratama' => '#10B981',
                            'Dewi Kusuma'   => '#EF4444',
                            'Bagas Wirawan' => '#0EA5E9',
                            'Siti Rahayu'   => '#EC4899',
                        ];
                        $fallbackColors = ['#E68A00', '#8B5CF6', '#10B981', '#EF4444', '#0EA5E9', '#EC4899'];
                    ?>

                    <?php $__empty_1 = true; $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pemohonName = $cr->owner_cr ?: ($cr->user?->name ?: 'User');
                            $pemohonInitials = strtoupper(substr($pemohonName, 0, 1));
                            $avColor = $avatarFigmaColors[$pemohonName] ?? $fallbackColors[$index % count($fallbackColors)];
                            $lastSaved = $cr->updated_at ?? $cr->created_at;
                        ?>
                        <tr>
                            
                            <td class="py-3 px-4" style="border-bottom: 1px solid #EEF2F6;">
                                <span class="fw-bold" style="color: #0F172A; font-size: 0.88rem;"><?php echo e($changeRequests->firstItem() + $index); ?></span>
                            </td>

                            
                            <td class="py-3 px-4" style="border-bottom: 1px solid #EEF2F6;">
                                <span style="color: #334155; font-size: 0.86rem; font-weight: 500;">
                                    <?php echo e($lastSaved ? $lastSaved->format('d M Y') : '-'); ?>

                                </span>
                            </td>

                            
                            <td class="py-3 px-4" style="border-bottom: 1px solid #EEF2F6;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                         style="width: 32px; height: 32px; background: <?php echo e($avColor); ?>; font-size: 0.82rem;">
                                        <?php echo e($pemohonInitials); ?>

                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.88rem;"><?php echo e($pemohonName); ?></span>
                                </div>
                            </td>

                            
                            <td class="py-3 px-4" style="border-bottom: 1px solid #EEF2F6;">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem; line-height: 1.3;"><?php echo e($cr->judul); ?></div>
                                <?php if($cr->proyek_terkait): ?>
                                    <div style="color: #94A3B8; font-size: 0.78rem; font-weight: 400; margin-top: 2px;"><?php echo e($cr->proyek_terkait); ?></div>
                                <?php endif; ?>
                            </td>

                            
                            <td class="py-3 px-4 text-center" style="border-bottom: 1px solid #EEF2F6;">
                                <div class="d-inline-flex align-items-center gap-2">
                                    
                                    <button type="button"
                                            class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5"
                                            onclick="confirmDeleteDraft('<?php echo e(route('change-requests.destroy', $cr)); ?>', '<?php echo e(addslashes($cr->judul)); ?>')"
                                            style="background: #D92D20; border-radius: 6px; font-size: 0.78rem; padding: 0.38rem 0.85rem; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                        <span>Hapus</span>
                                    </button>

                                    
                                    <a href="<?php echo e(route('change-requests.edit', $cr)); ?>"
                                       class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5 text-decoration-none"
                                       style="background: #EAAA08; border-radius: 6px; font-size: 0.78rem; padding: 0.38rem 0.85rem; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                        <span>Edit</span>
                                    </a>

                                    
                                    <a href="<?php echo e(route('change-requests.show', $cr)); ?>"
                                       class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5 text-decoration-none"
                                       style="background: #1570EF; border-radius: 6px; font-size: 0.78rem; padding: 0.38rem 0.95rem; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <span>Review</span>
                                        <i class="bi bi-chevron-right" style="font-size: 0.72rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-file-earmark-text fs-2 text-muted"></i></div>
                                <div class="fw-bold">Tidak ada Draft CR</div>
                                <div class="small mt-1">Belum ada permintaan perubahan yang tersimpan sebagai draft.</div>
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

    
    <div class="modal fade" id="deleteDraftModal" tabindex="-1" aria-labelledby="deleteDraftModalLabel" aria-hidden="true" style="background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(1.5px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 470px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; padding: 24px 28px; background: #FFFFFF;">
                <div class="d-flex align-items-start gap-3">
                    
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 48px; height: 48px; background: #FEE2E2; color: #DC2626;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </div>

                    
                    <div style="flex: 1; padding-top: 2px;">
                        <h3 class="fw-bold mb-1" id="deleteDraftModalLabel" style="font-size: 1.15rem; font-weight: 800; color: #0F172A; letter-spacing: -0.01em;">
                            Hapus draft ini?
                        </h3>
                        <p class="mb-0" style="font-size: 0.84rem; color: #64748B; line-height: 1.45;">
                            Draft &ldquo;<span id="deleteModalCrTitle" class="text-dark fw-semibold"></span>&rdquo; akan dihapus dan tidak bisa dikembalikan.
                        </p>
                    </div>
                </div>

                
                <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-1">
                    <button type="button" class="btn fw-semibold" data-bs-dismiss="modal"
                            style="background: #FFFFFF; border: 1.5px solid #CBD5E1; color: #334155; font-size: 0.84rem; border-radius: 9999px; padding: 0.42rem 1.6rem; transition: background 0.15s ease;">
                        Batal
                    </button>
                    <form id="deleteDraftForm" method="POST" action="" class="m-0">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn text-white fw-bold"
                                style="background: #DC2626; border: none; font-size: 0.84rem; border-radius: 9999px; padding: 0.42rem 1.8rem; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35); transition: opacity 0.15s ease;"
                                onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteDraft(actionUrl, title) {
            var modalEl = document.getElementById('deleteDraftModal');
            var formEl = document.getElementById('deleteDraftForm');
            var titleEl = document.getElementById('deleteModalCrTitle');
            if (formEl) formEl.action = actionUrl;
            if (titleEl) titleEl.textContent = title;
            if (modalEl) {
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }
    </script>

<?php else: ?>
    

    <?php
        $userRole = auth()->user()?->role ?? 'client';
        $isPmUser = in_array($userRole, ['pm', 'pmh']) || request('role') === 'pm' || request('view_as') === 'pm';

        $pageHeading = 'Change Request';
        if ($isPmUser) {
            if ($viewMode === 'development' || request('status') === 'development') {
                $pageHeading = 'Development CR';
            } elseif ($viewMode === 'uat' || request('status') === 'uat') {
                $pageHeading = 'UAT';
            } elseif ($viewMode === 'golive' || request('status') === 'golive') {
                $pageHeading = 'Go-Live';
            } else {
                $pageHeading = 'Butuh Persetujuan';
            }
        } else {
            if ($viewMode === 'development' || request('status') === 'development') {
                $pageHeading = 'Development';
            } elseif ($viewMode === 'uat' || request('status') === 'uat') {
                $pageHeading = 'UAT';
            } elseif ($viewMode === 'golive' || request('status') === 'golive') {
                $pageHeading = 'Go-Live';
            } elseif (request('status') === 'diajukan' || request('status') === 'awaiting_pm') {
                $pageHeading = 'Butuh Persetujuan';
            } elseif (request('status') === 'analisa') {
                $pageHeading = 'Analysis';
            }
        }

        $currentDropdownLabel = 'Semua Status';
        if (request('status') && !in_array(request('status'), ['development', 'uat', 'golive', 'total', 'all'])) {
            $st = strtolower(request('status'));
            if ($st === 'diajukan') $currentDropdownLabel = 'Diajukan';
            elseif (in_array($st, ['analisa', 'dianalisis', 'analysis'])) $currentDropdownLabel = 'Analysis';
            else $currentDropdownLabel = ucfirst(request('status'));
        }
    ?>

    
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 1.68rem; font-weight: 800; color: #0F172A; letter-spacing: -0.025em; margin: 0 0 4px; line-height: 1.2;"><?php echo e($pageHeading); ?></h1>
        <p style="font-size: 0.84rem; color: #64748B; margin: 0; font-weight: 400;">Pantau seluruh permintaan perubahan Anda</p>
    </div>

    <?php if($isPmUser): ?>
        
        <?php
            $currentTab = request('tab');
            $isSemuaTab = empty($currentTab) && !in_array(request('status'), ['development', 'golive', 'selesai']);
            $isAktifTab = $currentTab === 'progress' || request('status') === 'development';
            $isSelesaiTab = in_array($currentTab, ['payment', 'history']) || in_array(request('status'), ['golive', 'selesai']);
        ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; width: 100%; gap: 1.5px; background: #CBD5E1; border: 1.5px solid #CBD5E1; border-radius: 8px; overflow: hidden; margin-bottom: 20px; box-sizing: border-box;">
            <a href="<?php echo e(route('change-requests.index', ['view' => 'total'])); ?>"
               style="display: flex; align-items: center; justify-content: center; height: 44px; font-weight: 700; font-size: 0.88rem; text-decoration: none; transition: background 0.15s ease, color 0.15s ease; <?php echo e($isSemuaTab ? 'background: #0063D7; color: #FFFFFF;' : 'background: #F1F5F9; color: #0F172A;'); ?>">
                Semua CR
            </a>
            <a href="<?php echo e(route('change-requests.index', ['view' => 'total', 'tab' => 'progress'])); ?>"
               style="display: flex; align-items: center; justify-content: center; height: 44px; font-weight: 700; font-size: 0.88rem; text-decoration: none; transition: background 0.15s ease, color 0.15s ease; <?php echo e($isAktifTab ? 'background: #0063D7; color: #FFFFFF;' : 'background: #F1F5F9; color: #0F172A;'); ?>">
                CR Aktif
            </a>
            <a href="<?php echo e(route('change-requests.index', ['view' => 'total', 'tab' => 'history'])); ?>"
               style="display: flex; align-items: center; justify-content: center; height: 44px; font-weight: 700; font-size: 0.88rem; text-decoration: none; transition: background 0.15s ease, color 0.15s ease; <?php echo e($isSelesaiTab ? 'background: #0063D7; color: #FFFFFF;' : 'background: #F1F5F9; color: #0F172A;'); ?>">
                CR Selesai
            </a>
        </div>
    <?php endif; ?>

    
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; gap: 12px; flex-wrap: wrap;">
        <form method="GET" action="<?php echo e(route('change-requests.index')); ?>" style="flex: 1; max-width: 380px; margin: 0;">
            <input type="hidden" name="view" value="<?php echo e($viewMode); ?>">
            <?php if(request('status')): ?>
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
            <?php endif; ?>
            <?php if(request('tab')): ?>
                <input type="hidden" name="tab" value="<?php echo e(request('tab')); ?>">
            <?php endif; ?>
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 16px; color: #94A3B8; font-size: 0.88rem; pointer-events: none;"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Search task or CR number..."
                       style="width: 100%; background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 999px; padding: 0.52rem 1.25rem 0.52rem 2.55rem; font-size: 0.84rem; color: #1E293B; font-family: inherit; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='#155DFC'; this.style.boxShadow='0 0 0 3px rgba(21,93,252,0.10)';"
                       onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none';">
            </div>
        </form>
        <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false" id="statusFilterBtn"
                    style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 10px; height: 40px; padding: 0 16px; font-size: 0.84rem; color: #334155; font-weight: 500; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-family: inherit; outline: none; white-space: nowrap;">
                <span id="statusFilterLabel"><?php echo e($currentDropdownLabel); ?></span>
                <i class="bi bi-chevron-down" style="font-size: 0.68rem; color: #64748B;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm py-1" style="font-size: 0.82rem; border-radius: 10px; border: 1px solid #E2E8F0; min-width: 175px;">
                <li><a class="dropdown-item px-3 py-1.5 <?php echo e($viewMode === 'total' && !request('status') ? 'fw-bold text-primary' : ''); ?>" href="<?php echo e(route('change-requests.index', ['view' => 'total'])); ?>">Semua Status</a></li>
                <li><a class="dropdown-item px-3 py-1.5 <?php echo e($viewMode === 'development' && !request('status') ? 'fw-bold text-primary' : ''); ?>" href="<?php echo e(route('change-requests.index', ['view' => 'development'])); ?>">Development</a></li>
                <li><a class="dropdown-item px-3 py-1.5 <?php echo e($viewMode === 'uat' && !request('status') ? 'fw-bold text-primary' : ''); ?>" href="<?php echo e(route('change-requests.index', ['view' => 'uat'])); ?>">UAT</a></li>
                <li><a class="dropdown-item px-3 py-1.5 <?php echo e($viewMode === 'golive' && !request('status') ? 'fw-bold text-primary' : ''); ?>" href="<?php echo e(route('change-requests.index', ['view' => 'golive'])); ?>">Go-Live</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item px-3 py-1.5 <?php echo e(request('status') === 'diajukan' ? 'fw-bold text-primary' : ''); ?>" href="<?php echo e(route('change-requests.index', ['view' => $viewMode ?: 'total', 'status' => 'diajukan'])); ?>">Diajukan</a></li>
                <li><a class="dropdown-item px-3 py-1.5 <?php echo e(in_array(request('status'), ['analisa', 'analysis']) ? 'fw-bold text-primary' : ''); ?>" href="<?php echo e(route('change-requests.index', ['view' => $viewMode ?: 'total', 'status' => 'analisa'])); ?>">Analysis</a></li>
            </ul>
        </div>
    </div>

    
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(15,23,42,0.04);">
        <div class="table-responsive" style="margin: 0; padding: 0;">
            <table style="width: 100%; min-width: 980px; table-layout: fixed; border-collapse: collapse; font-family: 'Plus Jakarta Sans', sans-serif;">
                <thead>
                    <tr style="background: #ECF1F9; border-bottom: 1px solid #E2E8F0; height: 44px;">
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; width: 60px;">NO</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; width: 140px;">KODE CR</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; width: 220px;">PEMOHON</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; width: auto;">JUDUL CR</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; width: 130px;">PENGAJUAN</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; width: 130px;">STATUS</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: center; width: 110px;">PRIORITAS</th>
                        <th style="padding: 12px 18px; font-size: 0.72rem; font-weight: 700; color: #334155; letter-spacing: 0.06em; text-transform: uppercase; text-align: right; width: 110px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $avColorPool = ['#8B5CF6','#EF4444','#22C55E','#8B5CF6','#8B5CF6','#EF4444','#3B82F6','#F97316'];
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pemohonName = $cr->owner_cr ?: ($cr->user?->name ?: 'PT Delta Solusi');
                            $companyName = $cr->klien ?: ($cr->client?->company ?: $pemohonName);
                            
                            // Generate initials from company name (e.g. PT Delta Solusi -> DS)
                            $cleanCompany = preg_replace('/^(PT|CV|UD)\.?\s+/i', '', trim($companyName));
                            $words = preg_split('/\s+/', trim($cleanCompany ?: $companyName));
                            $initials = strtoupper(
                                (isset($words[0]) ? substr($words[0], 0, 1) : '') .
                                (isset($words[1]) ? substr($words[1], 0, 1) : (isset($words[0]) ? substr($words[0], 1, 1) : ''))
                            );
                            if (!$initials) $initials = 'DS';
                            $avColor = $avColorPool[$index % count($avColorPool)];

                            $statusText  = 'Diajukan';
                            $statusBg    = '#FFFBEB';
                            $statusColor = '#C3631D';
                            $dotColor    = '#FFB900';
                            $stLower = strtolower($cr->status ?? '');

                            if (in_array($stLower, ['analisa', 'dianalisis', 'analisis'])) {
                                $statusText  = 'Analysis';
                                $statusBg    = '#EFF6FF';
                                $statusColor = '#1D4ED8';
                                $dotColor    = '#3B82F6';
                            } elseif (in_array($stLower, ['development', 'dikerjakan', 'develop'])) {
                                $statusText  = 'Develop';
                                $statusBg    = '#F5F3FF';
                                $statusColor = '#6D28D9';
                                $dotColor    = '#8B5CF6';
                            } elseif (in_array($stLower, ['uat', 'sit'])) {
                                $statusText  = 'UAT';
                                $statusBg    = '#FDF2F8';
                                $statusColor = '#BE185D';
                                $dotColor    = '#EC4899';
                            } elseif (in_array($stLower, ['golive', 'selesai', 'invoicing'])) {
                                $statusText  = 'Go-Live';
                                $statusBg    = '#ECFDF5';
                                $statusColor = '#047857';
                                $dotColor    = '#10B981';
                            }

                            $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['urgent', 'kritis', 'tinggi', 'high']);
                            $tglPengajuan = $cr->tanggal_pengajuan
                                ? $cr->tanggal_pengajuan->format('d M Y')
                                : ($cr->created_at ? $cr->created_at->format('d M Y') : '01 Feb 2024');
                        ?>
                        <tr style="border-bottom: 1px solid #F1F5F9; height: 56px; transition: background 0.12s;"
                            onmouseover="this.style.background='#F8FAFC';"
                            onmouseout="this.style.background='transparent';">
                            <td style="padding: 12px 18px; font-size: 0.85rem; font-weight: 700; color: #0F172A; width: 60px; box-sizing: border-box;">
                                <?php echo e($changeRequests->firstItem() + $index); ?>

                            </td>
                            <td style="padding: 12px 18px; width: 140px; box-sizing: border-box;">
                                <span style="font-size: 0.82rem; font-weight: 500; color: #334155; white-space: nowrap; font-family: 'Space Grotesk', monospace;">
                                    <?php echo e($cr->kode_cr ?? ('CR-' . date('Y') . '-' . str_pad($changeRequests->firstItem() + $index, 3, '0', STR_PAD_LEFT))); ?>

                                </span>
                            </td>
                            <td style="padding: 12px 18px; width: 220px; box-sizing: border-box; overflow: hidden;">
                                <div style="display: flex; align-items: center; gap: 8px; width: 100%; overflow: hidden;">
                                    <span style="font-size: 0.84rem; font-weight: 600; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; min-width: 0;" title="<?php echo e($companyName); ?>">
                                        <?php echo e($companyName); ?>

                                    </span>
                                    <div style="width: 22px; height: 22px; border-radius: 50%; background: <?php echo e($avColor); ?>; color: #FFFFFF; font-size: 0.65rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; letter-spacing: -0.02em;">
                                        <?php echo e($initials); ?>

                                    </div>
                                </div>
                            </td>
                            <td style="padding: 12px 18px; width: auto; box-sizing: border-box; overflow: hidden;">
                                <div style="line-height: 1.35; width: 100%; overflow: hidden;">
                                    <a href="<?php echo e(route('change-requests.show', $cr)); ?>"
                                       style="font-size: 0.84rem; font-weight: 600; color: #0F172A; text-decoration: none; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                       title="<?php echo e($cr->judul); ?>"
                                       onmouseover="this.style.color='#155DFC';" onmouseout="this.style.color='#0F172A';">
                                        <?php echo e($cr->judul); ?>

                                    </a>
                                    <div style="font-size: 0.74rem; color: #94A3B8; font-weight: 400; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo e($cr->proyek_terkait ?: 'Authentication'); ?>

                                    </div>
                                </div>
                            </td>
                            <td style="padding: 12px 18px; font-size: 0.82rem; font-weight: 400; color: #475569; white-space: nowrap; width: 130px; box-sizing: border-box;">
                                <?php echo e($tglPengajuan); ?>

                            </td>
                            <td style="padding: 12px 18px; width: 130px; box-sizing: border-box; white-space: nowrap;">
                                <span style="display: inline-flex; align-items: center; gap: 6px; background: <?php echo e($statusBg); ?>; color: <?php echo e($statusColor); ?>; border-radius: 9999px; padding: 4px 12px; font-size: 0.78rem; font-weight: 700; white-space: nowrap;">
                                    <span style="width: 6.5px; height: 6.5px; border-radius: 50%; background: <?php echo e($dotColor); ?>; flex-shrink: 0;"></span>
                                    <?php echo e($statusText); ?>

                                </span>
                            </td>
                            <td style="padding: 12px 18px; text-align: center; width: 110px; box-sizing: border-box; white-space: nowrap;">
                                <?php if($isUrgent): ?>
                                    <span style="display: inline-block; background: #FEE2E2; color: #DC2626; font-size: 0.70rem; font-weight: 800; border-radius: 6px; padding: 3px 10px; letter-spacing: 0.04em; white-space: nowrap;">URGENT</span>
                                <?php else: ?>
                                    <span style="display: inline-block; background: #DCFCE7; color: #16A34A; font-size: 0.70rem; font-weight: 800; border-radius: 6px; padding: 3px 10px; letter-spacing: 0.04em; white-space: nowrap;">NORMAL</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 18px; text-align: right; width: 110px; box-sizing: border-box; white-space: nowrap;">
                                <a href="<?php echo e(route('change-requests.show', $cr)); ?>"
                                   style="display: inline-flex; align-items: center; gap: 4px; background: #155DFC; color: #FFFFFF; font-size: 0.78rem; font-weight: 600; border-radius: 8px; padding: 5px 14px; text-decoration: none; border: none; transition: background 0.15s; white-space: nowrap;"
                                   onmouseover="this.style.background='#0052CC';" onmouseout="this.style.background='#155DFC';">
                                    <span>Review</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.7rem; font-weight: 800;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 56px 16px; color: #94A3B8;">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: #F1F5F9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                                    <i class="bi bi-inbox text-muted" style="font-size: 1.5rem;"></i>
                                </div>
                                <div style="font-weight: 700; color: #334155; font-size: 0.92rem;">Tidak ada Change Request</div>
                                <div style="font-size: 0.78rem; color: #94A3B8; margin-top: 4px;">Tidak ada data yang sesuai dengan filter atau tab ini.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if($changeRequests->total() > 0): ?>
    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; padding: 4px 4px; flex-wrap: wrap; gap: 12px;">
        <span style="font-size: 0.78rem; font-weight: 700; color: #475569; letter-spacing: 0.02em;">
            TOTAL DATA: <?php echo e($changeRequests->total()); ?>

        </span>
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 0.78rem; color: #475569; font-weight: 600; white-space: nowrap;">ROWS PER PAGE</span>
                <div class="dropdown">
                    <button type="button" data-bs-toggle="dropdown"
                            style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 6px; padding: 3px 10px; font-size: 0.78rem; font-weight: 600; color: #334155; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-family: inherit; outline: none;">
                        <?php echo e(request('per_page', 10)); ?> <i class="bi bi-chevron-down" style="font-size: 0.6rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 80px;">
                        <?php $__currentLoopData = [10, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item px-3 py-1 <?php echo e(request('per_page',10) == $pp ? 'fw-bold' : ''); ?>"
                               href="<?php echo e(request()->fullUrlWithQuery(['per_page' => $pp, 'page' => 1])); ?>"><?php echo e($pp); ?></a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <span style="font-size: 0.78rem; font-weight: 600; color: #334155; white-space: nowrap;">
                PAGE <?php echo e($changeRequests->currentPage()); ?> OF <?php echo e($changeRequests->lastPage()); ?>

            </span>
            <div style="display: flex; align-items: center; gap: 4px;">
                <a href="<?php echo e($changeRequests->url(1)); ?>" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #CBD5E1;border-radius:6px;background:#FFFFFF;color:<?php echo e($changeRequests->onFirstPage() ? '#CBD5E1' : '#334155'); ?>;text-decoration:none;font-size:0.7rem;<?php echo e($changeRequests->onFirstPage() ? 'pointer-events:none;' : ''); ?>"><i class="bi bi-chevron-double-left"></i></a>
                <a href="<?php echo e($changeRequests->previousPageUrl() ?? '#'); ?>" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #CBD5E1;border-radius:6px;background:#FFFFFF;color:<?php echo e($changeRequests->onFirstPage() ? '#CBD5E1' : '#334155'); ?>;text-decoration:none;font-size:0.7rem;<?php echo e($changeRequests->onFirstPage() ? 'pointer-events:none;' : ''); ?>"><i class="bi bi-chevron-left"></i></a>
                <a href="<?php echo e($changeRequests->nextPageUrl() ?? '#'); ?>" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #CBD5E1;border-radius:6px;background:#FFFFFF;color:<?php echo e($changeRequests->hasMorePages() ? '#334155' : '#CBD5E1'); ?>;text-decoration:none;font-size:0.7rem;<?php echo e(!$changeRequests->hasMorePages() ? 'pointer-events:none;' : ''); ?>"><i class="bi bi-chevron-right"></i></a>
                <a href="<?php echo e($changeRequests->url($changeRequests->lastPage())); ?>" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #CBD5E1;border-radius:6px;background:#FFFFFF;color:<?php echo e($changeRequests->hasMorePages() ? '#334155' : '#CBD5E1'); ?>;text-decoration:none;font-size:0.7rem;<?php echo e(!$changeRequests->hasMorePages() ? 'pointer-events:none;' : ''); ?>"><i class="bi bi-chevron-double-right"></i></a>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/cr/index.blade.php ENDPATH**/ ?>