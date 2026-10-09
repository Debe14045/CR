@props([
    'name' => 'Siti Rahma',
    'roleBadge' => 'Clien',
    'title' => 'Profile',
    'subtitle' => 'Kelola informasi akun dan preferensi Anda.'
])

{{-- ══════════════════════════════════════════════════════════════
     ProfileHeader Component (Pure Native HTML/CSS)
     Typography & Spacing strictly matched to Figma reference
══════════════════════════════════════════════════════════════ --}}
<div class="profile-header mb-4" style="margin-bottom: 22px;">
    <h1 style="font-size: 1.65rem; font-weight: 800; color: #0F172A; letter-spacing: -0.025em; margin: 0 0 4px; line-height: 1.2;">
        {{ $title }}
    </h1>
    <p style="font-size: 0.84rem; color: #64748B; margin: 0 0 16px; font-weight: 400;">
        {{ $subtitle }}
    </p>
    <div style="font-size: 0.95rem; font-weight: 700; color: #0F172A; line-height: 1.2; margin-bottom: 6px;">
        {{ $name }}
    </div>
    <div>
        <span style="background: #0D3F8F; color: #FFFFFF; font-size: 0.74rem; font-weight: 700; border-radius: 9999px; padding: 4px 16px; display: inline-block; line-height: 1.3; letter-spacing: 0.01em;">
            {{ $roleBadge }}
        </span>
    </div>
</div>
