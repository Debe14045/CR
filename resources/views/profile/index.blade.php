@extends('layouts.app')

@section('title', 'Profile • Kelola Akun & Preferensi')

@section('content')
<div class="page-shell pb-5">

    {{-- Page Header --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">Profile</h1>
        <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Kelola informasi akun dan preferensi Anda.</p>
    </div>

    {{-- User Header Banner --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-2" style="color: #0F172A; font-size: 1.35rem;">
            {{ auth()->user()->name ?? 'Maya Sari' }}
        </h3>
        <div>
            <span class="badge px-3 py-2 text-white fw-semibold" style="background: #134B8A; border-radius: 999px; font-size: 0.82rem; letter-spacing: 0.01em;">
                {{ auth()->user()->isPmh() ? 'Project Manager Head' : (auth()->user()->isPm() ? 'Project Manager' : (auth()->user()->isClient() ? 'Client' : ucfirst(auth()->user()->role))) }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Card Kiri: INFORMASI AKUN --}}
        <div class="col-lg-6 col-12">
            <div class="card p-4 h-100 shadow-sm border-0" style="border: 1.5px solid #BAE6FD !important; border-radius: 16px; background: #F8FAFC;">
                <div class="mb-3">
                    <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                        INFORMASI AKUN
                    </span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Nama lengkap</label>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                            {{ auth()->user()->name ?? 'Maya Sari' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Role</label>
                        <div class="text-muted" style="font-size: 0.9rem;">
                            {{ auth()->user()->isPmh() ? 'Project Manager Head (disabled)' : (ucfirst(auth()->user()->role) . ' (disabled)') }}
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Email</label>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                            {{ auth()->user()->email ?? 'maya.sari@itpi.co.id' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small mb-1" style="font-size: 0.76rem;">Divisi</label>
                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                            Full Stack Developer
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Kanan: GANTI PASSWORD --}}
        <div class="col-lg-6 col-12">
            <div class="card p-4 h-100 shadow-sm border-0" style="border: 1.5px solid #BAE6FD !important; border-radius: 16px; background: #F8FAFC;">
                <div class="mb-3">
                    <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                        GANTI PASSWORD
                    </span>
                </div>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="password" name="old_password" class="form-control bg-white @error('old_password') is-invalid @enderror"
                               placeholder="Password lama" required style="border-radius: 8px; border: 1px solid #CBD5E1; padding: 0.65rem 1rem; font-size: 0.88rem;">
                        @error('old_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <input type="password" name="new_password" class="form-control bg-white @error('new_password') is-invalid @enderror"
                               placeholder="Password baru" required style="border-radius: 8px; border: 1px solid #CBD5E1; padding: 0.65rem 1rem; font-size: 0.88rem;">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn text-white fw-semibold px-4"
                                style="background: #0063D7; border-radius: 8px; padding: 0.55rem 1.4rem; font-size: 0.86rem; border: none; box-shadow: 0 4px 10px rgba(0,99,215,0.25);">
                            Simpan password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Card: PREFERENSI --}}
    <div class="card p-4 shadow-sm border-0 mb-4" style="border: 1.5px solid #BAE6FD !important; border-radius: 16px; background: #F8FAFC;">
        <div class="mb-3">
            <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                PREFERENSI
            </span>
        </div>

        <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between py-1">
                <span class="fw-semibold text-dark" style="font-size: 0.9rem;">Bahasa</span>
                <select class="form-select form-select-sm" style="width: 140px; border-radius: 8px; border: 1px solid #CBD5E1; font-size: 0.85rem;">
                    <option value="id" selected>Indonesia</option>
                    <option value="en">English</option>
                </select>
            </div>

            <div class="d-flex align-items-center justify-content-between py-1 border-top" style="border-color: #E2E8F0 !important;">
                <span class="fw-semibold text-dark" style="font-size: 0.9rem;">Notifikasi email</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" checked style="width: 2.75em; height: 1.4em; cursor: pointer; background-color: #02376A; border-color: #02376A;">
                </div>
            </div>
        </div>
    </div>

    {{-- Card: PROJECT YANG DITANGANI --}}
    <div class="card p-4 shadow-sm border-0" style="border: 1.5px solid #BAE6FD !important; border-radius: 16px; background: #F8FAFC;">
        <div class="mb-3">
            <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.05em; color: #0284C7;">
                PROJECT YANG DITANGANI
            </span>
            <span class="text-muted small ms-2">&mdash; read-only</span>
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach ($projects as $p)
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 border-bottom" style="border-color: #E2E8F0 !important;">
                    <div style="min-width: 200px;">
                        <span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $p['instansi'] }}</span>
                    </div>
                    <div style="flex: 1; min-width: 220px;" class="text-center text-sm-start">
                        <span class="text-muted small" style="font-size: 0.84rem;">{{ $p['judul'] }}</span>
                    </div>
                    <div class="text-end" style="min-width: 100px;">
                        <span class="fw-semibold text-dark small" style="font-size: 0.84rem;">{{ $p['role_text'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
