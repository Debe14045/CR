@extends('layouts.app')

@section('title', 'Profile • Kelola Informasi Akun')

@section('content')
@php
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
@endphp

{{-- ══════════════════════════════════════════════════════════════
     PROFILE VIEW — NATIVE MODULAR HTML / CSS / BLADE COMPONENTS
     Structured cleanly:
     - ProfileHeader
     - AccountInformationCard
     - SecurityCard
     - PreferencesCard
     - ProjectsCard
     All styling configured via CSS: width, height, padding,
     margin, gap, border (#BAE6FD), border-radius, typography,
     colors, and alignment strictly matching Figma reference.
══════════════════════════════════════════════════════════════ --}}
<div class="profile-page-container" style="max-width: 960px; padding: 6px 0 40px; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    @if(isset($errors) && $errors->any())
    <div style="background:#FEE2E2;color:#991B1B;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:0.85rem;border:1px solid #FECACA;">
        <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Component 1: ProfileHeader --}}
    <x-profile.profile-header
        :name="$profileName"
        :roleBadge="$profileRoleBadge"
        title="Profile"
        subtitle="Kelola informasi akun dan preferensi Anda."
    />

    {{-- Component Row: AccountInformationCard & SecurityCard --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        {{-- Component 2: AccountInformationCard --}}
        <div>
            <x-profile.account-information-card
                :name="$profileName"
                :company="$profileCompany"
                :initial="$profileInitial"
                :jobTitle="$profileJobTitle"
                :email="$profileEmail"
                :phone="$profilePhone"
            />
        </div>

        {{-- Component 3: SecurityCard --}}
        <div>
            <x-profile.security-card :actionRoute="route('profile.password')" />
        </div>
    </div>

    {{-- Component 4: PreferencesCard --}}
    <div style="margin-bottom: 16px;">
        <x-profile.preferences-card currentLanguage="Indonesia" />
    </div>

    {{-- Component 5: ProjectsCard --}}
    <div>
        <x-profile.projects-card :projects="$projects" />
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
@endsection
