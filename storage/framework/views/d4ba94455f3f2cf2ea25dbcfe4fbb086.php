<?php $__env->startSection('title', 'Profile • Kelola Informasi Akun'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $profileName      = $user->name ?? '-';
    $profileRoleBadge = ucfirst($user->role ?? 'Pengguna');
    $profileEmail     = $user->email ?? '-';
    
    $profileCompany   = $clientRecord->company ?? '- (Internal/Belum diset)';
    $profileInitial   = $clientRecord->nickname ?? '-';
    $profileJobTitle  = $clientRecord->jabatan ?? '-';
    $profilePhone     = $clientRecord->phone ?? '-';

    $projects = [];
    if (isset($recentCRs)) {
        foreach ($recentCRs as $cr) {
            $statusStr = ucfirst(str_replace('_', ' ', $cr->status));
            $color = '#94A3B8';
            if (in_array($cr->status, ['draft', 'diajukan'])) $color = '#F59E0B'; // yellow/amber
            elseif (in_array($cr->status, ['development', 'testing', 'uat'])) $color = '#3B82F6'; // blue
            elseif ($cr->status === 'selesai' || $cr->status === 'golive') $color = '#10B981'; // green

            $projects[] = [
                'name' => $cr->judul,
                'status' => $statusStr,
                'color' => $color,
                'weight' => '600'
            ];
        }
    }
    
    if (empty($projects)) {
        $projects = [
            ['name' => 'Belum ada Change Request yang terkait.', 'status' => 'Kosong', 'color' => '#94A3B8', 'weight' => '500'],
        ];
    }
?>


<div class="profile-page-container" style="max-width: 960px; padding: 6px 0 40px; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    <?php if(isset($errors) && $errors->any()): ?>
    <div style="background:#FEE2E2;color:#991B1B;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:0.85rem;border:1px solid #FECACA;">
        <ul style="margin:0;padding-left:18px;"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
    </div>
    <?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginaldda7ee360e37759711c028f56060e27c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldda7ee360e37759711c028f56060e27c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.profile-header','data' => ['name' => $profileName,'roleBadge' => $profileRoleBadge,'title' => 'Profile','subtitle' => 'Kelola informasi akun dan preferensi Anda.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.profile-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileName),'roleBadge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileRoleBadge),'title' => 'Profile','subtitle' => 'Kelola informasi akun dan preferensi Anda.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldda7ee360e37759711c028f56060e27c)): ?>
<?php $attributes = $__attributesOriginaldda7ee360e37759711c028f56060e27c; ?>
<?php unset($__attributesOriginaldda7ee360e37759711c028f56060e27c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldda7ee360e37759711c028f56060e27c)): ?>
<?php $component = $__componentOriginaldda7ee360e37759711c028f56060e27c; ?>
<?php unset($__componentOriginaldda7ee360e37759711c028f56060e27c); ?>
<?php endif; ?>

    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        
        <div>
            <?php if (isset($component)) { $__componentOriginalfb32222abb35c5259f73ff19805c0566 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb32222abb35c5259f73ff19805c0566 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.account-information-card','data' => ['name' => $profileName,'company' => $profileCompany,'initial' => $profileInitial,'jobTitle' => $profileJobTitle,'email' => $profileEmail,'phone' => $profilePhone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.account-information-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileName),'company' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileCompany),'initial' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileInitial),'jobTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileJobTitle),'email' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileEmail),'phone' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profilePhone)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb32222abb35c5259f73ff19805c0566)): ?>
<?php $attributes = $__attributesOriginalfb32222abb35c5259f73ff19805c0566; ?>
<?php unset($__attributesOriginalfb32222abb35c5259f73ff19805c0566); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb32222abb35c5259f73ff19805c0566)): ?>
<?php $component = $__componentOriginalfb32222abb35c5259f73ff19805c0566; ?>
<?php unset($__componentOriginalfb32222abb35c5259f73ff19805c0566); ?>
<?php endif; ?>
        </div>

        
        <div>
            <?php if (isset($component)) { $__componentOriginald752856bc0ef753d87ab30774a1e7070 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald752856bc0ef753d87ab30774a1e7070 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.security-card','data' => ['actionRoute' => route('profile.password')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.security-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actionRoute' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.password'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald752856bc0ef753d87ab30774a1e7070)): ?>
<?php $attributes = $__attributesOriginald752856bc0ef753d87ab30774a1e7070; ?>
<?php unset($__attributesOriginald752856bc0ef753d87ab30774a1e7070); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald752856bc0ef753d87ab30774a1e7070)): ?>
<?php $component = $__componentOriginald752856bc0ef753d87ab30774a1e7070; ?>
<?php unset($__componentOriginald752856bc0ef753d87ab30774a1e7070); ?>
<?php endif; ?>
        </div>
    </div>

    
    <div style="margin-bottom: 16px;">
        <?php if (isset($component)) { $__componentOriginalf8ac7071fff63da1655a35cd1d6f1d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8ac7071fff63da1655a35cd1d6f1d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.preferences-card','data' => ['currentLanguage' => 'Indonesia']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.preferences-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentLanguage' => 'Indonesia']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8ac7071fff63da1655a35cd1d6f1d8e)): ?>
<?php $attributes = $__attributesOriginalf8ac7071fff63da1655a35cd1d6f1d8e; ?>
<?php unset($__attributesOriginalf8ac7071fff63da1655a35cd1d6f1d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8ac7071fff63da1655a35cd1d6f1d8e)): ?>
<?php $component = $__componentOriginalf8ac7071fff63da1655a35cd1d6f1d8e; ?>
<?php unset($__componentOriginalf8ac7071fff63da1655a35cd1d6f1d8e); ?>
<?php endif; ?>
    </div>

    
    <div>
        <?php if (isset($component)) { $__componentOriginalb39f1d3ffcd887649838c3845154c2ed = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb39f1d3ffcd887649838c3845154c2ed = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.projects-card','data' => ['projects' => $projects]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.projects-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['projects' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($projects)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb39f1d3ffcd887649838c3845154c2ed)): ?>
<?php $attributes = $__attributesOriginalb39f1d3ffcd887649838c3845154c2ed; ?>
<?php unset($__attributesOriginalb39f1d3ffcd887649838c3845154c2ed); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb39f1d3ffcd887649838c3845154c2ed)): ?>
<?php $component = $__componentOriginalb39f1d3ffcd887649838c3845154c2ed; ?>
<?php unset($__componentOriginalb39f1d3ffcd887649838c3845154c2ed); ?>
<?php endif; ?>
    </div>

</div>

<script>
/* ── Language selector (profile preference card) ── */
function profileSetLang(label, code) {
    var lbl = document.getElementById('profileCurrentLangLabel');
    if (lbl) lbl.textContent = label;

    var ckId = document.getElementById('profLangCheckId');
    var ckEn = document.getElementById('profLangCheckEn');
    if (ckId) ckId.classList.toggle('d-none', code !== 'id');
    if (ckEn) ckEn.classList.toggle('d-none', code !== 'en');

    /* sync global i18n system if available */
    if (typeof window.setAppLanguage === 'function') {
        window.setAppLanguage(code);
    }

    /* close Bootstrap dropdown programmatically */
    var btn = document.getElementById('profileLangDropBtn');
    if (btn && window.bootstrap) {
        var dd = bootstrap.Dropdown.getInstance(btn);
        if (dd) dd.hide();
    }
}

/* ── Email notification toggle: persist state ── */
(function () {
    var toggle = document.getElementById('profileEmailNotifSwitch');
    if (!toggle) return;

    var saved = localStorage.getItem('profEmailNotif');
    if (saved !== null) {
        toggle.checked = (saved === '1');
        toggle.style.backgroundColor = toggle.checked ? '#0D3F8F' : '';
        toggle.style.borderColor     = toggle.checked ? '#0D3F8F' : '';
    }

    toggle.addEventListener('change', function () {
        localStorage.setItem('profEmailNotif', this.checked ? '1' : '0');
        this.style.backgroundColor = this.checked ? '#0D3F8F' : '';
        this.style.borderColor     = this.checked ? '#0D3F8F' : '';
    });
})();

/* ── Password form: lightweight client-side guard ── */
(function () {
    var form = document.getElementById('profilePwForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        var oldPw = document.getElementById('profOldPw').value.trim();
        var newPw = document.getElementById('profNewPw').value.trim();
        if (!oldPw) {
            e.preventDefault();
            document.getElementById('profOldPw').focus();
            return;
        }
        if (!newPw || newPw.length < 6) {
            e.preventDefault();
            document.getElementById('profNewPw').focus();
        }
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/client/profile.blade.php ENDPATH**/ ?>