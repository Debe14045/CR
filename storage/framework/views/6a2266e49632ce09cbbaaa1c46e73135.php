<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'projects' => [
        ['name' => 'Dashboard Analitik Real-time',    'status' => 'Aktif',   'color' => '#10B981', 'weight' => '600'],
        ['name' => 'Sistem Approval Dokumen Digital',  'status' => 'Selesai', 'color' => '#94A3B8', 'weight' => '500'],
    ],
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
    'projects' => [
        ['name' => 'Dashboard Analitik Real-time',    'status' => 'Aktif',   'color' => '#10B981', 'weight' => '600'],
        ['name' => 'Sistem Approval Dokumen Digital',  'status' => 'Selesai', 'color' => '#94A3B8', 'weight' => '500'],
    ],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div class="projects-card" style="background: #F0F3F6; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 18px 24px; box-sizing: border-box;">
    
    <div style="margin-bottom: 14px;">
        <span style="font-size: 0.72rem; font-weight: 800; color: #0284C7; letter-spacing: 0.06em; text-transform: uppercase;">PROJECT SAYA</span>
        <span style="font-size: 0.72rem; font-weight: 600; color: #64748B; letter-spacing: 0.06em;"> — READ-ONLY</span>
    </div>

    
    <div style="display: flex; flex-direction: column; gap: 10px;">
        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.82rem; font-weight: 700; color: #0F172A;"><?php echo e($project['name']); ?></span>
            <span style="font-size: 0.76rem; font-weight: <?php echo e($project['weight']); ?>; color: <?php echo e($project['color']); ?>;"><?php echo e($project['status']); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/components/profile/projects-card.blade.php ENDPATH**/ ?>