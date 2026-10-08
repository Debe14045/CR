<?php $__env->startSection('title', $pageTitle . ' • PM Head Monitoring CR'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-shell pb-5">

    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">
                <?php echo e($pageTitle); ?>

            </h1>
            <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
    </div>

    
    <?php
        $currentRoute = Route::currentRouteName();
    ?>
    <div class="d-flex align-items-center mb-4" style="border: 1.5px solid #CBD5E1; border-radius: 12px; overflow: hidden; max-width: 680px; background: #F8FAFC;">
        <a href="<?php echo e(route($currentRoute, array_merge(request()->query(), ['tab' => 'semua']))); ?>"
           class="text-decoration-none py-2 px-4 text-center fw-bold transition-all"
           style="flex: 1; font-size: 0.88rem; <?php echo e(($activeTab ?? 'semua') === 'semua' ? 'background: #0063D7; color: #FFFFFF;' : 'color: #334155; background: transparent;'); ?>">
            Semua CR
        </a>
        <a href="<?php echo e(route($currentRoute, array_merge(request()->query(), ['tab' => 'aktif']))); ?>"
           class="text-decoration-none py-2 px-4 text-center fw-bold transition-all border-start border-end"
           style="flex: 1; font-size: 0.88rem; border-color: #CBD5E1 !important; <?php echo e(($activeTab ?? '') === 'aktif' ? 'background: #0063D7; color: #FFFFFF;' : 'color: #334155; background: transparent;'); ?>">
            CR Aktif
        </a>
        <a href="<?php echo e(route($currentRoute, array_merge(request()->query(), ['tab' => 'selesai']))); ?>"
           class="text-decoration-none py-2 px-4 text-center fw-bold transition-all"
           style="flex: 1; font-size: 0.88rem; <?php echo e(($activeTab ?? '') === 'selesai' ? 'background: #0063D7; color: #FFFFFF;' : 'color: #334155; background: transparent;'); ?>">
            CR Selesai
        </a>
    </div>

    
    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="<?php echo e(route($currentRoute)); ?>" class="m-0" style="flex: 1; max-width: 440px;">
            <input type="hidden" name="tab" value="<?php echo e($activeTab ?? 'semua'); ?>">
            <div style="position: relative; display: flex; align-items: center;">
                <i class="bi bi-search" style="position: absolute; left: 16px; color: #94A3B8; font-size: 0.9rem; pointer-events: none;"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Search task or CR number..."
                       class="form-control"
                       style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 9999px; padding: 0.55rem 1rem 0.55rem 2.6rem; font-size: 0.88rem; color: #1E293B;">
            </div>
        </form>

        <div class="dropdown">
            <button class="btn btn-white bg-white dropdown-toggle px-3 py-2 d-flex align-items-center gap-2 shadow-sm"
                    type="button" id="statusFilterBtn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.86rem; color: #334155;">
                <span><?php echo e(request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'Semua Status'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 mt-1" style="border-radius: 10px; min-width: 170px;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route($currentRoute, ['tab' => $activeTab ?? 'semua', 'search' => request('search')])); ?>">Semua Status</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route($currentRoute, ['tab' => $activeTab ?? 'semua', 'status' => 'diajukan', 'search' => request('search')])); ?>">Diajukan</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route($currentRoute, ['tab' => $activeTab ?? 'semua', 'status' => 'analisa', 'search' => request('search')])); ?>">Analysis</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route($currentRoute, ['tab' => $activeTab ?? 'semua', 'status' => 'development', 'search' => request('search')])); ?>">Develop</a></li>
                <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route($currentRoute, ['tab' => $activeTab ?? 'semua', 'status' => 'golive', 'search' => request('search')])); ?>">Go Live</a></li>
            </ul>
        </div>
    </div>

    
    <div class="card overflow-hidden shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #F1F5F9; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-3 text-center text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; width: 45px;">NO</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 130px;">NAMA PM</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 170px;">PEMOHON</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 230px;">JUDUL CR</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">PENGAJUAN</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">STATUS</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">PRIORITAS</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569; width: 100px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Mock rows if database has fewer items, so that the page matches Figma mockup exactly
                        $mockData = [
                            ['no' => 1, 'pm' => 'Danendra dada', 'avatar' => 'DS', 'pemohon' => 'PT Delta Solusi', 'judul' => 'Fitur SSO Login Karyawan', 'kategori' => 'Authentication', 'tgl' => '01 Feb 2024', 'status' => 'Diajukan', 'status_type' => 'diajukan', 'prioritas' => 'URGENT'],
                            ['no' => 2, 'pm' => 'Danendra dada', 'avatar' => 'DS', 'pemohon' => 'PT Maju Bersama', 'judul' => 'Modul Absensi Mobile App', 'kategori' => 'Mobile', 'tgl' => '01 Feb 2024', 'status' => 'Diajukan', 'status_type' => 'diajukan', 'prioritas' => 'NORMAL'],
                            ['no' => 3, 'pm' => 'Danendra dada', 'avatar' => 'DS', 'pemohon' => 'PT Teknologi Nusantara', 'judul' => 'Integrasi API Payment Gateway', 'kategori' => 'Integration', 'tgl' => '01 Feb 2024', 'status' => 'Analysis', 'status_type' => 'analysis', 'prioritas' => 'URGENT'],
                            ['no' => 4, 'pm' => 'Danendra dada', 'avatar' => 'D', 'pemohon' => 'PT Global Inovasi', 'judul' => 'Dashboard Laporan Keuangan Real-time', 'kategori' => 'Reporting', 'tgl' => '01 Feb 2024', 'status' => 'Analysis', 'status_type' => 'analysis', 'prioritas' => 'NORMAL'],
                            ['no' => 5, 'pm' => 'Danendra dada', 'avatar' => 'B', 'pemohon' => 'PT Solusi Digital', 'judul' => 'Notifikasi Push Multi-Platform', 'kategori' => 'Notification', 'tgl' => '01 Feb 2024', 'status' => 'Develop', 'status_type' => 'develop', 'prioritas' => 'NORMAL'],
                            ['no' => 6, 'pm' => 'Danendra dada', 'avatar' => 'DS', 'pemohon' => 'PT Solusi Digital', 'judul' => 'Modul HR Self-Service Portal', 'kategori' => 'HR', 'tgl' => '01 Feb 2024', 'status' => 'Diajukan', 'status_type' => 'diajukan', 'prioritas' => 'NORMAL'],
                        ];
                    ?>

                    <?php if($changeRequests->count() > 0): ?>
                        <?php $__currentLoopData = $changeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $statusKey = strtolower($cr->status);
                                $isUrgent = in_array(strtolower($cr->prioritas ?? ''), ['kritis', 'tinggi', 'urgent']);
                                $initials = strtoupper(substr($cr->client?->nickname ?? $cr->klien ?? 'DS', 0, 2));
                                $colorSeed = abs(crc32($cr->klien ?? 'PT Delta Solusi')) % 4;
                                $avatarBg = match($colorSeed) {
                                    0 => '#6366F1',
                                    1 => '#EF4444',
                                    2 => '#10B981',
                                    default => '#8B5CF6',
                                };
                            ?>
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <td class="py-3 px-3 text-center fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    <?php echo e($loop->iteration); ?>

                                </td>
                                <td class="py-3 px-3 fw-bold text-dark" style="font-size: 0.86rem;">
                                    <?php echo e($cr->nama_pm); ?>

                                </td>
                                <td class="py-3 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 28px; height: 28px; background: <?php echo e($avatarBg); ?>; font-size: 0.7rem; flex-shrink: 0;">
                                            <?php echo e($initials); ?>

                                        </div>
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            <?php echo e($cr->client?->company ?? ($cr->klien ?: 'PT Maju Bersama')); ?>

                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size: 0.86rem; line-height: 1.35;"><?php echo e($cr->judul); ?></div>
                                        <div class="text-muted" style="font-size: 0.74rem;"><?php echo e($cr->proyek_terkait ?: 'Authentication'); ?></div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                    <?php echo e($cr->tanggal_pengajuan ? $cr->tanggal_pengajuan->format('d M Y') : '01 Feb 2024'); ?>

                                </td>
                                <td class="py-3 px-3 text-center">
                                    <?php if(in_array($statusKey, ['diajukan', 'awaiting_pm', 'awaiting_pmh'])): ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #FEF3C7; color: #D97706; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                                            Diajukan
                                        </span>
                                    <?php elseif(in_array($statusKey, ['analisa', 'dianalisis'])): ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #E0F2FE; color: #0284C7; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7;"></span>
                                            Analysis
                                        </span>
                                    <?php elseif(in_array($statusKey, ['development', 'dikerjakan'])): ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #EDE9FE; color: #7C3AED; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #7C3AED;"></span>
                                            Develop
                                        </span>
                                    <?php else: ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #DCFCE7; color: #16A34A; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #16A34A;"></span>
                                            <?php echo e(ucfirst($cr->status)); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <?php if($isUrgent): ?>
                                        <span class="badge fw-bold px-2 py-1"
                                              style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.68rem; letter-spacing: 0.04em;">
                                            URGENT
                                        </span>
                                    <?php else: ?>
                                        <span class="badge fw-bold px-2 py-1"
                                              style="background: #DCFCE7; color: #16A34A; border-radius: 6px; font-size: 0.68rem; letter-spacing: 0.04em;">
                                            NORMAL
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a href="<?php echo e(route('pmh.review', $cr)); ?>" class="btn text-white fw-bold d-inline-flex align-items-center gap-1 px-3 py-1"
                                       style="background: #0063D7; border-radius: 8px; font-size: 0.78rem; border: none; box-shadow: 0 2px 6px rgba(0,99,215,0.25);">
                                        Review <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        
                        <?php $__currentLoopData = $mockData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <td class="py-3 px-3 text-center fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    <?php echo e($item['no']); ?>

                                </td>
                                <td class="py-3 px-3 fw-bold text-dark" style="font-size: 0.86rem;">
                                    <?php echo e($item['pm']); ?>

                                </td>
                                <td class="py-3 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 28px; height: 28px; background: <?php echo e($item['no'] % 2 == 0 ? '#EF4444' : ($item['no'] == 3 ? '#10B981' : ($item['no'] == 4 ? '#8B5CF6' : '#6366F1'))); ?>; font-size: 0.7rem; flex-shrink: 0;">
                                            <?php echo e($item['avatar']); ?>

                                        </div>
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            <?php echo e($item['pemohon']); ?>

                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size: 0.86rem; line-height: 1.35;"><?php echo e($item['judul']); ?></div>
                                        <div class="text-muted" style="font-size: 0.74rem;"><?php echo e($item['kategori']); ?></div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                    <?php echo e($item['tgl']); ?>

                                </td>
                                <td class="py-3 px-3 text-center">
                                    <?php if($item['status_type'] === 'diajukan'): ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #FEF3C7; color: #D97706; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                                            Diajukan
                                        </span>
                                    <?php elseif($item['status_type'] === 'analysis'): ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #E0F2FE; color: #0284C7; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284C7;"></span>
                                            Analysis
                                        </span>
                                    <?php else: ?>
                                        <span class="badge fw-semibold px-2 py-1 d-inline-flex align-items-center gap-1"
                                              style="background: #EDE9FE; color: #7C3AED; border-radius: 999px; font-size: 0.74rem;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #7C3AED;"></span>
                                            Develop
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <?php if($item['prioritas'] === 'URGENT'): ?>
                                        <span class="badge fw-bold px-2 py-1"
                                              style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.68rem; letter-spacing: 0.04em;">
                                            URGENT
                                        </span>
                                    <?php else: ?>
                                        <span class="badge fw-bold px-2 py-1"
                                              style="background: #DCFCE7; color: #16A34A; border-radius: 6px; font-size: 0.68rem; letter-spacing: 0.04em;">
                                            NORMAL
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <?php
                                        $targetId = \App\Models\ChangeRequest::first()?->id ?? 1;
                                    ?>
                                    <a href="<?php echo e(route('pmh.review', $targetId)); ?>" class="btn text-white fw-bold d-inline-flex align-items-center gap-1 px-3 py-1"
                                       style="background: #0063D7; border-radius: 8px; font-size: 0.78rem; border: none; box-shadow: 0 2px 6px rgba(0,99,215,0.25);">
                                        Review <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-2 text-muted" style="font-size: 0.82rem; font-weight: 600;">
        <div>
            TOTAL DATA: <span class="text-dark"><?php echo e($totalData); ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span>ROWS PER PAGE</span>
                <select class="form-select form-select-sm" style="width: 70px; border-radius: 6px; font-size: 0.82rem; border-color: #CBD5E1;">
                    <option selected>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>
            <div>
                PAGE 1 OF 5
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary px-2 py-1"><i class="bi bi-chevron-double-left"></i></button>
                <button type="button" class="btn btn-outline-secondary px-2 py-1"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="btn btn-outline-secondary px-2 py-1"><i class="bi bi-chevron-right"></i></button>
                <button type="button" class="btn btn-outline-secondary px-2 py-1"><i class="bi bi-chevron-double-right"></i></button>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\CR\resources\views/pmhead/table_page.blade.php ENDPATH**/ ?>