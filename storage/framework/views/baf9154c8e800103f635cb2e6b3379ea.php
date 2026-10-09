<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'Siti Rahma',
    'roleBadge' => 'Clien',
    'title' => 'Profile',
    'subtitle' => 'Kelola informasi akun dan preferensi Anda.'
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
    'roleBadge' => 'Clien',
    'title' => 'Profile',
    'subtitle' => 'Kelola informasi akun dan preferensi Anda.'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div class="profile-header mb-4" style="margin-bottom: 22px;">
    <h1 style="font-size: 1.65rem; font-weight: 800; color: #0F172A; letter-spacing: -0.025em; margin: 0 0 4px; line-height: 1.2;">
        <?php echo e($title); ?>

    </h1>
    <p style="font-size: 0.84rem; color: #64748B; margin: 0 0 16px; font-weight: 400;">
        <?php echo e($subtitle); ?>

    </p>
    <div style="font-size: 0.95rem; font-weight: 700; color: #0F172A; line-height: 1.2; margin-bottom: 6px;">
        <?php echo e($name); ?>

    </div>
    <div>
        <span style="background: #0D3F8F; color: #FFFFFF; font-size: 0.74rem; font-weight: 700; border-radius: 9999px; padding: 4px 16px; display: inline-block; line-height: 1.3; letter-spacing: 0.01em;">
            <?php echo e($roleBadge); ?>

        </span>
    </div>
</div>
<?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/components/profile/profile-header.blade.php ENDPATH**/ ?>