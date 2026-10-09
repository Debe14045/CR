<?php $__env->startSection('title', 'Profile • Kelola Akun & Preferensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-shell pb-5">

    
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">Profile</h1>
        <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Kelola informasi akun dan preferensi Anda.</p>
    </div>

    
    <div class="mb-4">
        <h3 class="fw-bold mb-2" style="color: #0F172A; font-size: 1.35rem;">
            <?php echo e(auth()->user()->name ?? 'Maya Sari'); ?>

        </h3>
        <div>
            <span class="badge px-3 py-2 text-white fw-semibold" style="background: #134B8A; border-radius: 999px; font-size: 0.82rem; letter-spacing: 0.01em;">
                <?php echo e(auth()->user()->isPmh() ? 'Project Manager Head' : (auth()->user()->isPm() ? 'Project Manager' : (auth()->user()->isClient() ? 'Client' : ucfirst(auth()->user()->role)))); ?>

            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-lg-6 col-12">
            <div class="card p-4 h-100 shadow-sm border-0" style="border: none !important; border-radius: 16px; background: #F8FAFC;">
                <div class="mb-3">
                    <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                        INFORMASI AKUN
                    </span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Nama lengkap</label>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                            <?php echo e(auth()->user()->name ?? 'Maya Sari'); ?>

                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Role</label>
                        <div class="text-muted" style="font-size: 0.9rem;">
                            <?php echo e(auth()->user()->isPmh() ? 'Project Manager Head (disabled)' : (ucfirst(auth()->user()->role) . ' (disabled)')); ?>

                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Email</label>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                            <?php echo e(auth()->user()->email ?? 'maya.sari@itpi.co.id'); ?>

                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Divisi</label>
                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                            Full Stack Developer
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6 col-12">
            <div class="card p-4 h-100 shadow-sm border-0" style="border: none !important; border-radius: 16px; background: #F8FAFC;">
                <div class="mb-3">
                    <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                        GANTI PASSWORD
                    </span>
                </div>

                <form method="POST" action="<?php echo e(route('profile.password')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <input type="password" name="old_password" class="form-control bg-white <?php $__errorArgs = ['old_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Password lama" required style="border-radius: 8px; border: 1px solid #CBD5E1; padding: 0.65rem 1rem; font-size: 0.88rem;">
                        <?php $__errorArgs = ['old_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <input type="password" name="new_password" class="form-control bg-white <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Password baru" required style="border-radius: 8px; border: 1px solid #CBD5E1; padding: 0.65rem 1rem; font-size: 0.88rem;">
                        <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <button type="submit" class="btn text-white fw-semibold px-4"
                                style="background: #0063D7; border-radius: 8px; padding: 0.55rem 1.4rem; font-size: 0.86rem; border: none; box-shadow: 0 4px 10px rgba(0,99,215,0.25);">
                            Simpan password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="card p-4 shadow-sm border-0 mb-4" style="border: none !important; border-radius: 16px; background: #F8FAFC;">
        <div class="mb-3">
            <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                PREFERENSI
            </span>
        </div>

        <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between py-1">
                <span class="fw-semibold text-dark" style="font-size: 0.9rem;">Bahasa</span>
                <select class="form-select form-select-sm" style="width: 140px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 0.85rem;">
                    <option value="id" selected>Indonesia</option>
                    <option value="en">English</option>
                </select>
            </div>

            <div class="d-flex align-items-center justify-content-between py-1">
                <span class="fw-semibold text-dark" style="font-size: 0.9rem;">Notifikasi email</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" checked style="width: 2.75em; height: 1.4em; cursor: pointer; background-color: #02376A; border-color: #02376A;">
                </div>
            </div>
        </div>
    </div>

    
    <div class="card p-4 shadow-sm border-0" style="border: none !important; border-radius: 16px; background: #F8FAFC;">
        <div class="mb-3">
            <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                PROJECT YANG DITANGANI
            </span>
            <span class="text-muted small ms-2">&mdash; read-only</span>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-2">
                    <div style="min-width: 200px;">
                        <span class="fw-bold text-dark" style="font-size: 0.88rem;"><?php echo e($p['instansi']); ?></span>
                    </div>
                    <div style="flex: 1; min-width: 220px;" class="text-center text-sm-start">
                        <span class="text-muted small" style="font-size: 0.84rem;"><?php echo e($p['judul']); ?></span>
                    </div>
                    <div class="text-end" style="min-width: 100px;">
                        <span class="fw-semibold text-dark small" style="font-size: 0.84rem;"><?php echo e($p['role_text']); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Magang_ITPI\CR\resources\views/profile/index.blade.php ENDPATH**/ ?>