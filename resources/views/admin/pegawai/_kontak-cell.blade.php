<td class="py-3 px-4">
    @php $displayEmail = $pegawai->user?->email ?? $pegawai->email; @endphp
    @if($displayEmail || $pegawai->phone || $pegawai->address)
        <div class="small">
            @if($displayEmail)
                <div class="text-dark d-flex align-items-center gap-2 {{ $pegawai->phone || $pegawai->address ? 'mb-1' : '' }}" style="font-size: 0.78rem;">
                    <i class="bi bi-envelope text-muted flex-shrink-0" style="font-size: 0.75rem;"></i>
                    <span class="text-truncate" style="max-width: 220px;" title="{{ $displayEmail }}">{{ $displayEmail }}</span>
                </div>
            @endif
            @if($pegawai->phone)
                <div class="text-dark d-flex align-items-center gap-2" style="font-size: 0.80rem;">
                    <i class="bi bi-telephone text-muted flex-shrink-0" style="font-size: 0.75rem;"></i>
                    <span>{{ $pegawai->phone }}</span>
                </div>
            @endif
            @if($pegawai->address)
                <div class="text-muted small text-truncate d-flex align-items-center gap-2 mt-1" style="max-width: 220px; font-size: 0.72rem;" title="{{ $pegawai->address }}">
                    <i class="bi bi-geo-alt flex-shrink-0"></i>
                    <span class="text-truncate">{{ $pegawai->address }}</span>
                </div>
            @endif
        </div>
    @else
        <span class="text-muted fst-italic" style="font-size: 0.78rem;">Kontak belum dilengkapi</span>
    @endif
</td>

