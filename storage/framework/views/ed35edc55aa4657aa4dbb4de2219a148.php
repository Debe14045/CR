<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'Siti Rahma',
    'company' => 'CV Teknologi Nusantara (disabled)',
    'initial' => 'SR (disabled)',
    'jobTitle' => 'IT Manager',
    'email' => 'siti@teknologinusantara.co.id',
    'phone' => '0812-xxxx-xxxx',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name' => 'Siti Rahma',
    'company' => 'CV Teknologi Nusantara (disabled)',
    'initial' => 'SR (disabled)',
    'jobTitle' => 'IT Manager',
    'email' => 'siti@teknologinusantara.co.id',
    'phone' => '0812-xxxx-xxxx',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div class="account-info-card" style="background: #F0F3F6; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 20px 24px; box-sizing: border-box; height: 100%;">
    
    <div style="font-size: 0.72rem; font-weight: 800; color: #0284C7; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 16px;">
        INFORMASI AKUN
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">

        
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">Nama lengkap</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;"><?php echo e($name); ?></div>
        </div>

        
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 4px;">Nama perusahaan</div>
            <div>
                <span style="display: inline-flex; align-items: center; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; padding: 4px 12px; font-size: 0.78rem; color: #0F172A; font-weight: 600;">
                    <?php echo e($company); ?>

                </span>
            </div>
        </div>

        
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 4px;">Inisial klien</div>
            <div>
                <span style="display: inline-flex; align-items: center; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; padding: 4px 12px; font-size: 0.78rem; color: #0F172A; font-weight: 600;">
                    <?php echo e($initial); ?>

                </span>
            </div>
        </div>

        
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">Jabatan</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;"><?php echo e($jobTitle); ?></div>
        </div>

        
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">Email</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;"><?php echo e($email); ?></div>
        </div>

        
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">No. telepon</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;"><?php echo e($phone); ?></div>
        </div>

    </div>
</div>
<?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/components/profile/account-information-card.blade.php ENDPATH**/ ?>