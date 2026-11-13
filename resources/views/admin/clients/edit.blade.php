@extends('layouts.app')

@section('title', 'Edit Klien ' . $client->company . ' • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; padding: 0.25rem 0.5rem 3rem; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (Sesuai Gaya Ajukan CR) ── --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.68rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            Edit Klien &amp; Akun PIC
        </h1>
        <div style="color: #64748B; font-size: 0.82rem; font-weight: 400;">
            Perbarui profil perusahaan klien atau perbarui nama, kontak, dan reset password login PIC
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #FECACA; background: #FEF2F2;">
            <div class="fw-bold small mb-1 text-danger">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Terdapat kesalahan pada formulir:
            </div>
            <ul class="mb-0 small text-danger ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── CARD 1: Profil Perusahaan Klien ── --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                        <i class="bi bi-buildings" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">1. Profil Perusahaan Klien</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Informasi resmi badan usaha atau instansi mitra
                </div>
            </div>
            <div class="p-4" style="background: #FFFFFF;">
                <div class="row g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Perusahaan / Instansi <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="company" maxlength="255" class="form-control @error('company') is-invalid @enderror"
                               value="{{ old('company', $client->company) }}" required
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        @error('company')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Singkat / Nickname <span class="text-muted fw-normal small">(Opsional)</span>
                        </label>
                        <input type="text" name="nickname" maxlength="50" class="form-control @error('nickname') is-invalid @enderror"
                               value="{{ old('nickname', $client->nickname) }}"
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        @error('nickname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Alamat Kantor Operasional <span class="text-muted fw-normal small">(Opsional)</span>
                        </label>
                        <textarea name="address" rows="3" maxlength="65535" class="form-control @error('address') is-invalid @enderror"
                                  style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">{{ old('address', $client->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: Pembaruan Akun PIC Klien ── --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                        <i class="bi bi-people-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">2. Akun PIC Klien &amp; Reset Password</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Perbarui nama, kontak telepon, atau reset password login akun PIC
                </div>
            </div>
            <div class="p-4" style="background: #FFFFFF;">
                <div class="row g-4">
                    @forelse($client->pics as $idx => $pic)
                        @php
                            $roleMeta = match($pic->role_type) {
                                'marketing' => ['bg' => '#2563EB', 'text' => '#FFFFFF', 'border' => '#2563EB', 'badge' => 'PIC Marketing', 'icon' => 'bi-megaphone'],
                                'procurement' => ['bg' => '#059669', 'text' => '#FFFFFF', 'border' => '#059669', 'badge' => 'PIC Procurement', 'icon' => 'bi-cart-check'],
                                'it' => ['bg' => '#7C3AED', 'text' => '#FFFFFF', 'border' => '#7C3AED', 'badge' => 'PIC IT / Technical', 'icon' => 'bi-cpu'],
                                default => ['bg' => '#475569', 'text' => '#FFFFFF', 'border' => '#475569', 'badge' => ucfirst($pic->role_type), 'icon' => 'bi-person']
                            };
                        @endphp
                        <div class="col-lg-4 col-12">
                            <div class="p-4 h-100" style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2.5 border-bottom">
                                    <span class="badge px-2.5 py-1" style="background: {{ $roleMeta['bg'] }}; color: {{ $roleMeta['text'] }}; font-size: 0.72rem; font-weight: 700; border-radius: 6px;">
                                        <i class="bi {{ $roleMeta['icon'] }} me-1"></i>{{ $roleMeta['badge'] }}
                                    </span>
                                    <span class="text-muted small font-monospace" style="font-size: 0.74rem;">ID #{{ $pic->id }}</span>
                                </div>

                                <input type="hidden" name="pics[{{ $idx }}][id]" value="{{ $pic->id }}">

                                <div class="mb-3">
                                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Email Login (Tetap)</label>
                                    <input type="email" class="form-control" value="{{ $pic->email }}" disabled
                                           style="background: #EDF2F7; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #64748B;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Nama PIC <span style="color: #EF4444;">*</span></label>
                                    <input type="text" name="pics[{{ $idx }}][name]" maxlength="255" class="form-control"
                                           value="{{ old('pics.'.$idx.'.name', $pic->name) }}" required
                                           style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">No. WhatsApp / HP <span style="color: #EF4444;">*</span></label>
                                    <input type="text" name="pics[{{ $idx }}][phone]" maxlength="25" class="form-control"
                                           value="{{ old('pics.'.$idx.'.phone', $pic->phone) }}" required
                                           style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                                </div>
                                <div>
                                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                        Reset Password
                                    </label>
                                    <input type="password" name="pics[{{ $idx }}][password]" maxlength="255" class="form-control"
                                           placeholder="Kosongkan jika tidak diubah"
                                           style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                                    <div class="form-text small mt-1" style="font-size: 0.72rem; color: #64748B;">Isi hanya jika ingin mengganti password login akun PIC ini.</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-3">Belum ada akun PIC pada klien ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Tombol Submit (Sesuai Gaya Ajukan CR) ── --}}
        <div class="d-flex justify-content-end align-items-center gap-3 pt-2 mb-5">
            <a href="{{ route('admin.clients.show', $client) }}"
               class="btn fw-semibold"
               style="background: #F1F5F9; color: #475569; border-radius: 8px; font-size: 0.88rem; padding: 0.65rem 1.6rem; border: 1px solid #E2E8F0; text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
                    style="background: #10B981; border-radius: 8px; font-size: 0.88rem; padding: 0.65rem 1.8rem; border: none; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                <i class="bi bi-check-lg" style="font-size: 1.1rem;"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>

</div>
@endsection
