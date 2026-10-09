<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'currentLanguage' => 'Indonesia',
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
    'currentLanguage' => 'Indonesia',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div class="preferences-card" style="background: #F0F3F6; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 18px 24px; box-sizing: border-box;">
    
    <div style="font-size: 0.72rem; font-weight: 800; color: #0284C7; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 14px;">
        PREFERENSI
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">

        
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.84rem; font-weight: 700; color: #0F172A;">Bahasa</span>
            <div class="dropdown">
                <button type="button" id="profileLangDropBtn" data-bs-toggle="dropdown" aria-expanded="false"
                        style="background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 9999px; height: 28px; padding: 0 14px; font-size: 0.76rem; color: #334155; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-family: inherit; outline: none;">
                    <span id="profileCurrentLangLabel"><?php echo e($currentLanguage); ?></span>
                    <i class="bi bi-chevron-down" style="font-size: 0.58rem; color: #94A3B8;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm py-1" style="font-size: 0.8rem; border-radius: 8px; border: 1px solid #E2E8F0; min-width: 130px;">
                    <li>
                        <a class="dropdown-item px-3 py-1 d-flex align-items-center justify-content-between" href="javascript:void(0)"
                           id="profLangOptId" onclick="profileSetLang('Indonesia','id')">
                            <span>Indonesia</span>
                            <i class="bi bi-check" id="profLangCheckId" style="color:#1570EF;"></i>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item px-3 py-1 d-flex align-items-center justify-content-between" href="javascript:void(0)"
                           id="profLangOptEn" onclick="profileSetLang('English','en')">
                            <span>English</span>
                            <i class="bi bi-check d-none" id="profLangCheckEn" style="color:#1570EF;"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.84rem; font-weight: 700; color: #0F172A;">Notifikasi email</span>
            <div class="form-check form-switch p-0 m-0">
                <input class="form-check-input ms-0" type="checkbox" role="switch" id="profileEmailNotifSwitch" checked
                       style="width: 38px; height: 20px; cursor: pointer; background-color: #0D3F8F; border-color: #0D3F8F;">
            </div>
        </div>

    </div>
</div>
<?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/components/profile/preferences-card.blade.php ENDPATH**/ ?>