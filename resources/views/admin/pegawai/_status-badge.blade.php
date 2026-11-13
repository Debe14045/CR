@php
    $interactive = $interactive ?? false;
@endphp

<td class="py-3 px-4 text-center" style="white-space: nowrap;">
    @if(!$pegawai->hasWebAccount())
        <span class="badge rounded-pill fw-semibold"
              style="background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; font-size: 0.72rem; padding: 0.28rem 0.75rem;"
              title="Pegawai terdaftar tanpa akun login web">
            <i class="bi bi-person-slash me-1"></i>Tanpa Akun
        </span>
    @elseif($interactive)
        <form action="{{ route('admin.pegawai.toggle-status', $pegawai) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm border-0 p-0" title="Klik untuk ubah status">
                @if($pegawai->is_active)
                    <span class="badge rounded-pill fw-bold" style="background: #DCFCE7; color: #16A34A; font-size: 0.72rem; padding: 0.28rem 0.75rem; cursor: pointer;">
                        <i class="bi bi-check-circle-fill me-1"></i>Aktif
                    </span>
                @else
                    <span class="badge rounded-pill fw-bold" style="background: #FEE2E2; color: #DC2626; font-size: 0.72rem; padding: 0.28rem 0.75rem; cursor: pointer;">
                        <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                    </span>
                @endif
            </button>
        </form>
    @else
        @if($pegawai->is_active)
            <span class="badge rounded-pill fw-bold" style="background: #DCFCE7; color: #16A34A; font-size: 0.72rem; padding: 0.28rem 0.75rem;">
                <i class="bi bi-check-circle-fill me-1"></i>Aktif
            </span>
        @else
            <span class="badge rounded-pill fw-bold" style="background: #FEE2E2; color: #DC2626; font-size: 0.72rem; padding: 0.28rem 0.75rem;">
                <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
            </span>
        @endif
    @endif
</td>

