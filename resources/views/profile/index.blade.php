@extends('layouts.app')

@section('title', 'Profile • Kelola Akun & Preferensi')

@section('content')
<style>
    .profile-card-figma {
        background: #F0F3F6 !important;
        border: 1.5px solid #BAE6FD !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }
    .profile-input-figma {
        background: #FFFFFF !important;
        border: 1.5px solid #BAE6FD !important;
        border-radius: 8px !important;
        padding: 0.5rem 0.85rem !important;
        font-size: 0.82rem !important;
        color: #0F172A !important;
        height: 38px !important;
        box-shadow: none !important;
    }
    .profile-input-figma::placeholder {
        color: #94A3B8 !important;
        font-size: 0.82rem !important;
    }
    .profile-input-figma:focus {
        border-color: #0098EA !important;
        outline: none !important;
    }
</style>

<div class="page-shell pb-5">

    {{-- Page Header --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            Profile
        </h1>
        <p class="mb-0" style="font-size: 0.88rem; color: #000000; font-weight: 500;">
            Kelola informasi akun dan preferensi Anda.
        </p>
    </div>

    {{-- User Header Banner --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-2" style="color: #000000; font-size: 1.25rem; font-weight: 800; letter-spacing: -0.015em;">
            {{ auth()->user()->name ?? 'Maya Sari' }}
        </h3>
        <div>
            <span class="d-inline-block text-white fw-bold"
                  style="background: #0B3B7B; border-radius: 9999px; font-size: 0.74rem; padding: 5px 16px; letter-spacing: 0.01em;">
                {{ auth()->user()->isPmh() ? 'Project Manager Head' : (auth()->user()->isPm() ? 'Project Manager' : (auth()->user()->isClient() ? 'Client' : ucfirst(auth()->user()->role))) }}
            </span>
        </div>
    </div>

    {{-- Row 1: INFORMASI AKUN & GANTI PASSWORD --}}
    <div class="row g-3 mb-3">
        {{-- Card Kiri: INFORMASI AKUN --}}
        <div class="col-lg-6 col-12">
            <div class="profile-card-figma p-4 h-100">
                <div class="mb-3">
                    <span class="fw-bold text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #0284C7; font-weight: 800;">
                        INFORMASI AKUN
                    </span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div>
                        <div style="font-size: 0.70rem; font-weight: 600; color: #94A3B8; margin-bottom: 2px;">Nama lengkap</div>
                        <div style="font-size: 0.90rem; font-weight: 800; color: #000000; line-height: 1.3;">
                            {{ auth()->user()->name ?? 'Maya Sari' }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.70rem; font-weight: 600; color: #94A3B8; margin-bottom: 2px;">Role</div>
                        <div style="font-size: 0.84rem; font-weight: 700; color: #94A3B8; line-height: 1.3;">
                            {{ auth()->user()->isPmh() ? 'Project Manager Head (disabled)' : (ucfirst(auth()->user()->role) . ' (disabled)') }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.70rem; font-weight: 600; color: #94A3B8; margin-bottom: 2px;">Email</div>
                        <div style="font-size: 0.88rem; font-weight: 800; color: #000000; line-height: 1.3;">
                            {{ auth()->user()->email ?? 'maya.sari@itpi.co.id' }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.70rem; font-weight: 600; color: #94A3B8; margin-bottom: 2px;">Divisi</div>
                        <div style="font-size: 0.88rem; font-weight: 800; color: #000000; line-height: 1.3;">
                            Full Stack Developer
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Kanan: GANTI PASSWORD --}}
        <div class="col-lg-6 col-12">
            <div class="profile-card-figma p-4 h-100">
                <div class="mb-3">
                    <span class="fw-bold text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #0284C7; font-weight: 800;">
                        GANTI PASSWORD
                    </span>
                </div>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <input type="password" name="old_password" class="form-control profile-input-figma @error('old_password') is-invalid @enderror"
                               placeholder="Password lama" required>
                        @error('old_password')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="margin-bottom: 16px;">
                        <input type="password" name="new_password" class="form-control profile-input-figma @error('new_password') is-invalid @enderror"
                               placeholder="Password baru" required>
                        @error('new_password')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn text-white fw-bold"
                                style="background: #1964D4; border-radius: 6px; padding: 7px 18px; font-size: 0.80rem; border: none; box-shadow: none;">
                            Simpan password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Row 2: Card PREFERENSI --}}
    <div class="profile-card-figma p-4 mb-3">
        <div class="mb-3">
            <span class="fw-bold text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #0284C7; font-weight: 800;">
                PREFERENSI
            </span>
        </div>

        <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-center justify-content-between py-1">
                <span style="font-weight: 800; font-size: 0.86rem; color: #000000;">Bahasa</span>
                <div class="dropdown">
                    <button class="btn bg-white dropdown-toggle px-3 py-1 d-inline-flex align-items-center justify-content-between shadow-none"
                            type="button" id="langDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.78rem; font-weight: 600; color: #334155; min-width: 105px; gap: 8px;">
                        <span id="currentLangProfileText">Indonesia</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border p-1" style="border-radius: 8px; border-color: #E2E8F0; min-width: 115px;" aria-labelledby="langDropdownBtn">
                        <li>
                            <a class="dropdown-item py-1 px-3 small fw-semibold" href="javascript:void(0)" onclick="if(window.setAppLanguage) window.setAppLanguage('id'); document.getElementById('currentLangProfileText').textContent='Indonesia';">
                                Indonesia
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-1 px-3 small fw-semibold" href="javascript:void(0)" onclick="if(window.setAppLanguage) window.setAppLanguage('en'); document.getElementById('currentLangProfileText').textContent='English';">
                                English
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between py-1">
                <span style="font-weight: 800; font-size: 0.86rem; color: #000000;">Notifikasi email</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" checked
                           style="width: 2.75rem; height: 1.45rem; cursor: pointer; background-color: #02376A; border-color: #02376A; box-shadow: none;">
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Card PROJECT YANG DITANGANI --}}
    <div class="profile-card-figma p-4 mb-4">
        <div class="mb-3 d-flex align-items-center">
            <span class="fw-bold text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #0284C7; font-weight: 800;">
                PROJECT YANG DITANGANI
            </span>
            <span style="font-size: 0.74rem; color: #000000; font-weight: 400; margin-left: 6px;">&mdash; read-only</span>
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach ($projects as $p)
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-1">
                    <div style="min-width: 220px; flex: 1.2;">
                        <span style="font-weight: 800; font-size: 0.82rem; color: #000000;">{{ $p['instansi'] }}</span>
                    </div>
                    <div style="min-width: 260px; flex: 2;" class="text-start">
                        <span style="font-weight: 500; font-size: 0.80rem; color: #334155;">{{ $p['judul'] }}</span>
                    </div>
                    <div class="text-end" style="min-width: 90px;">
                        <span style="font-weight: 700; font-size: 0.80rem; color: #000000;">{{ $p['role_text'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
