<?php $__env->startSection('title', 'Dashboard • PM Head Executive Board'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-shell pb-5">

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 p-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #02376A 0%, #0063D7 100%); color: #FFFFFF;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold"
                 style="width: 52px; height: 52px; background: rgba(255,255,255,0.15); font-size: 1.6rem; color: #FFFFFF;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="text-white text-opacity-75 small fw-bold text-uppercase" style="letter-spacing: 0.08em; font-size: 0.74rem;">
                    Executive Review Board
                </div>
                <h3 class="fw-bold mb-1 text-white" style="font-size: 1.45rem;">
                    Supervisi &amp; Final Approval PM Head
                </h3>
                <p class="text-white text-opacity-80 small mb-0" style="font-size: 0.82rem;">
                    Tinjau hasil kajian teknis dari PM, evaluasi kelayakan strategis &amp; resource, dan tentukan keputusan persetujuan resmi atau penolakan CR.
                </p>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="<?php echo e(route('pmh.persetujuan')); ?>" class="btn text-white fw-semibold btn-sm px-3 py-2"
               style="background: #22C55E; border: none; border-radius: 8px; font-size: 0.84rem; box-shadow: 0 4px 10px rgba(34,197,94,0.3);">
                <i class="bi bi-check2-circle me-1"></i> Antrean Persetujuan
            </a>
            <a href="<?php echo e(route('pmh.outstanding-payment')); ?>" class="btn text-white fw-semibold btn-sm px-3 py-2"
               style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.84rem;">
                <i class="bi bi-wallet2 me-1"></i> Outstanding Payment
            </a>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        
        <div class="col-xl col-md-4 col-6">
            <a href="<?php echo e(route('pmh.change-requests')); ?>" class="text-decoration-none">
                <div class="p-3 bg-white h-100 rounded-3 shadow-sm border" style="border-color: #E2E8F0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #EFF6FF; color: #0063D7;">
                            <i class="bi bi-shield-shaded"></i>
                        </div>
                        <span class="text-muted small"><i class="bi bi-collection"></i></span>
                    </div>
                    <div class="fw-bold fs-3 text-dark mb-0"><?php echo e($roleMetrics['total'] ?? 0); ?></div>
                    <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">Total CR Masuk</div>
                </div>
            </a>
        </div>

        
        <div class="col-xl col-md-4 col-6">
            <a href="<?php echo e(route('pmh.persetujuan')); ?>" class="text-decoration-none">
                <div class="p-3 h-100 rounded-3 shadow-sm border" style="background: #FFF9FA; border-color: #FECDD3 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FFE4E6; color: #BE123C;">
                            <i class="bi bi-hourglass-top"></i>
                        </div>
                        <span class="text-danger small fw-bold">Wajib Review</span>
                    </div>
                    <div class="fw-bold fs-3 text-danger mb-0"><?php echo e($roleMetrics['pending_pmh'] ?? 0); ?></div>
                    <div class="text-danger small fw-semibold" style="font-size: 0.76rem;">Review Awal PMH</div>
                </div>
            </a>
        </div>

        
        <div class="col-xl col-md-4 col-6">
            <a href="<?php echo e(route('pmh.golive')); ?>" class="text-decoration-none">
                <div class="p-3 h-100 rounded-3 shadow-sm border" style="background: #FFFBEB; border-color: #FDE68A !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #FEF3C7; color: #D97706;">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <span class="text-warning small fw-bold">BA Siap</span>
                    </div>
                    <div class="fw-bold fs-3 text-warning mb-0"><?php echo e($roleMetrics['pending_golive'] ?? 0); ?></div>
                    <div class="text-warning small fw-semibold" style="font-size: 0.76rem;">Validasi Go-Live</div>
                </div>
            </a>
        </div>

        
        <div class="col-xl col-md-6 col-6">
            <a href="<?php echo e(route('pmh.development')); ?>" class="text-decoration-none">
                <div class="p-3 bg-white h-100 rounded-3 shadow-sm border" style="border-color: #E2E8F0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #ECFDF5; color: #059669;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <span class="text-success small"><i class="bi bi-award"></i></span>
                    </div>
                    <div class="fw-bold fs-3 text-success mb-0"><?php echo e($roleMetrics['approved'] ?? 0); ?></div>
                    <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">CR Telah Divalidasi</div>
                </div>
            </a>
        </div>

        
        <div class="col-xl col-md-6 col-12">
            <div class="p-3 bg-white h-100 rounded-3 shadow-sm border" style="border-color: #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #F8FAFC; color: #64748B;">
                        <i class="bi bi-x-circle text-danger"></i>
                    </div>
                    <span class="text-muted small"><i class="bi bi-slash-circle"></i></span>
                </div>
                <div class="fw-bold fs-3 text-dark mb-0"><?php echo e($roleMetrics['rejected'] ?? 0); ?></div>
                <div class="text-muted small fw-semibold" style="font-size: 0.76rem;">CR Ditolak / Revisi</div>
            </div>
        </div>
    </div>

    
    <div class="card p-4 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 18px; background: #FFFFFF;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Antrean CR Perlu Keputusan PM Head</h5>
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">Daftar CR yang telah diverifikasi PM dan menunggu validasi kelayakan atau go-live.</p>
            </div>
            <a href="<?php echo e(route('pmh.persetujuan')); ?>" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                Lihat Semua Antrean <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="py-2 px-3 small text-muted">ID &amp; JUDUL CR</th>
                        <th class="py-2 px-3 small text-muted">PEMOHON</th>
                        <th class="py-2 px-3 small text-muted">NAMA PM</th>
                        <th class="py-2 px-3 small text-muted text-center">STATUS</th>
                        <th class="py-2 px-3 small text-muted text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $latestReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?php echo e($cr->judul); ?></div>
                                <div class="text-muted small font-monospace"><?php echo e($cr->kode_cr); ?></div>
                            </td>
                            <td class="py-3 px-3 fw-semibold text-dark small">
                                <?php echo e($cr->client?->company ?? ($cr->klien ?: 'PT Maju Bersama')); ?>

                            </td>
                            <td class="py-3 px-3 small text-secondary">
                                <?php echo e($cr->nama_pm); ?>

                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="badge bg-warning-subtle text-warning fw-semibold px-2 py-1" style="border-radius: 999px;">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $cr->status))); ?>

                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <a href="<?php echo e(route('pmh.review', $cr)); ?>" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 6px; font-size: 0.78rem;">
                                    Review &gt;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-4 text-center text-muted small">
                                Tidak ada antrean persetujuan yang menunggu saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\CR\resources\views/pmhead/dashboard.blade.php ENDPATH**/ ?>