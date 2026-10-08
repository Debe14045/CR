<?php $__env->startSection('title', 'OUTSTANDING PAYMENT • Monitoring Change Request'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-shell pb-5">

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.01em;">
                OUTSTANDING PAYMENT
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda</p>
        </div>
        <div>
            <a href="<?php echo e(route('pmh.outstanding-payment.export')); ?>" class="btn text-white fw-semibold d-inline-flex align-items-center gap-2"
               style="background: #0063D7; border-radius: 8px; padding: 0.55rem 1.35rem; font-size: 0.85rem; border: none; box-shadow: 0 4px 12px rgba(0,99,215,0.25);">
                <i class="bi bi-download"></i>
                <span>Export Rekap (XLS/PDF)</span>
            </a>
        </div>
    </div>

    
    <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
        <form method="GET" action="<?php echo e(route('pmh.outstanding-payment')); ?>" class="m-0" style="flex: 1; max-width: 440px;">
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
                <span><?php echo e(request('status') ? ucfirst(request('status')) : 'Semua Status'); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1 mt-1" style="border-radius: 10px; min-width: 160px;" aria-labelledby="statusFilterBtn">
                <li><a class="dropdown-item py-1 px-3 small" href="<?php echo e(route('pmh.outstanding-payment')); ?>">Semua Status</a></li>
                <li><a class="dropdown-item py-1 px-3 small text-danger" href="<?php echo e(route('pmh.outstanding-payment', ['status' => 'belum bayar'])); ?>">Belum Bayar</a></li>
                <li><a class="dropdown-item py-1 px-3 small text-success" href="<?php echo e(route('pmh.outstanding-payment', ['status' => 'lunas'])); ?>">Lunas</a></li>
            </ul>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Total Tagihan<br>Outstanding</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-wallet2" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 1.55rem; letter-spacing: -0.02em;">
                    <?php echo e($metrics['total_tagihan']); ?>

                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Jatuh Tempo Minggu ini</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-calendar-event" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 2.8rem; line-height: 1; letter-spacing: -0.02em;">
                    <?php echo e($metrics['jatuh_tempo_minggu_ini']); ?>

                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Lewat Jatuh Tempo</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-exclamation-triangle" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 2.8rem; line-height: 1; letter-spacing: -0.02em;">
                    <?php echo e($metrics['lewat_jatuh_tempo']); ?>

                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 col-12">
            <div class="p-4 text-white h-100 shadow-sm"
                 style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="fw-bold" style="font-size: 0.88rem; line-height: 1.35;">Terbayar Bulan ini</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.08);">
                        <i class="bi bi-check2-circle" style="font-size: 0.88rem;"></i>
                    </div>
                </div>
                <div class="fw-bold my-2" style="font-size: 2.8rem; line-height: 1; letter-spacing: -0.02em;">
                    <?php echo e($metrics['terbayar_bulan_ini']); ?>

                </div>
                <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.74rem; font-weight: 500;">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Aktif Bulan Ini</span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card overflow-hidden shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background: #F1F5F9; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-3 text-center text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; width: 45px;">NO</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 170px;">PEMOHON</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569; min-width: 220px;">JUDUL CR</th>
                        <th class="py-3 px-3 text-uppercase fw-bold" style="font-size: 0.72rem; color: #475569;">NILAI</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">TANGGAL GO-LIVE</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">TARGET INVOICE</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">TANGGAL INVOICE</th>
                        <th class="py-3 px-3 text-uppercase fw-bold text-center" style="font-size: 0.72rem; color: #475569;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                            <td class="py-3 px-3 text-center fw-semibold text-secondary" style="font-size: 0.85rem;">
                                <?php echo e($p['no']); ?>

                            </td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width: 30px; height: 30px; background: <?php echo e($p['no'] % 2 == 0 ? '#EF4444' : ($p['no'] == 3 ? '#10B981' : ($p['no'] == 4 ? '#8B5CF6' : '#6366F1'))); ?>; font-size: 0.72rem; flex-shrink: 0;">
                                        <?php echo e($p['avatar']); ?>

                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                        <?php echo e($p['pemohon']); ?>

                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size: 0.86rem; line-height: 1.35;"><?php echo e($p['judul_cr']); ?></div>
                                    <div class="text-muted" style="font-size: 0.74rem;"><?php echo e($p['kategori']); ?></div>
                                </div>
                            </td>
                            <td class="py-3 px-3 fw-bold text-dark" style="font-size: 0.86rem;">
                                Rp. <?php echo e(number_format($p['nilai'], 0, ',', '.')); ?>

                            </td>
                            <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                <?php echo e($p['tgl_golive']); ?>

                            </td>
                            <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                <?php echo e($p['target_invoice']); ?>

                            </td>
                            <td class="py-3 px-3 text-center text-muted" style="font-size: 0.82rem;">
                                <?php echo e($p['tgl_invoice']); ?>

                            </td>
                            <td class="py-3 px-3 text-center">
                                <?php if($p['status'] === 'BELUM BAYAR'): ?>
                                    <span class="badge fw-bold px-3 py-1"
                                          style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.74rem; letter-spacing: 0.02em;">
                                        BELUM BAYAR
                                    </span>
                                <?php else: ?>
                                    <span class="badge fw-bold px-3 py-1"
                                          style="background: #DCFCE7; color: #16A34A; border-radius: 6px; font-size: 0.74rem; letter-spacing: 0.02em;">
                                        LUNAS
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Tidak ada data tagihan ditemukan.
                            </td>
                        </tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\CR\resources\views/pmhead/outstanding_payment.blade.php ENDPATH**/ ?>