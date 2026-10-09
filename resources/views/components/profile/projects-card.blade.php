@props([
    'projects' => [
        ['name' => 'Dashboard Analitik Real-time',    'status' => 'Aktif',   'color' => '#10B981', 'weight' => '600'],
        ['name' => 'Sistem Approval Dokumen Digital',  'status' => 'Selesai', 'color' => '#94A3B8', 'weight' => '500'],
    ],
])

{{-- ══════════════════════════════════════════════════════════════
     ProjectsCard Component (Pure Native HTML/CSS)
     Background: #F0F3F6, Border: 1.5px solid #BAE6FD
     Title: PROJECT SAYA — READ-ONLY
══════════════════════════════════════════════════════════════ --}}
<div class="projects-card" style="background: #F0F3F6; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 18px 24px; box-sizing: border-box;">
    {{-- Card Title in Cyan with Muted Suffix --}}
    <div style="margin-bottom: 14px;">
        <span style="font-size: 0.72rem; font-weight: 800; color: #0284C7; letter-spacing: 0.06em; text-transform: uppercase;">PROJECT SAYA</span>
        <span style="font-size: 0.72rem; font-weight: 600; color: #64748B; letter-spacing: 0.06em;"> — READ-ONLY</span>
    </div>

    {{-- Projects List --}}
    <div style="display: flex; flex-direction: column; gap: 10px;">
        @foreach($projects as $project)
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.82rem; font-weight: 700; color: #0F172A;">{{ $project['name'] }}</span>
            <span style="font-size: 0.76rem; font-weight: {{ $project['weight'] }}; color: {{ $project['color'] }};">{{ $project['status'] }}</span>
        </div>
        @endforeach
    </div>
</div>
