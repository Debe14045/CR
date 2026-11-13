@php
    $assignedPms = $project->assignedPms();
@endphp

@if($assignedPms->isNotEmpty())
    <div class="d-flex flex-column gap-2">
        @foreach($assignedPms as $pmIdx => $pm)
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width: 26px; height: 26px; background: {{ $pmIdx === 0 ? '#059669' : '#0284C7' }}; font-size: 0.68rem; flex-shrink: 0;">
                    {{ strtoupper(substr($pm->name, 0, 2)) }}
                </div>
                <div>
                    <div class="fw-bold text-dark d-flex align-items-center gap-1" style="font-size: 0.82rem; line-height: 1.2;">
                        <span>{{ $pm->name }}</span>
                        @if($assignedPms->count() > 1)
                            <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 0.62rem;">PM {{ $pmIdx + 1 }}</span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size: 0.68rem;">
                        {{ $pm->pmRoleLabel() }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.72rem;">Belum ditetapkan</span>
@endif

