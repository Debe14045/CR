@extends('layouts.app')

@section('title', 'Tambah Data Pegawai • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; padding: 0.25rem 0.5rem 3rem; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (Sesuai Gaya Ajukan CR) ── --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.68rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            Tambah Data Pegawai
        </h1>
        <div style="color: #64748B; font-size: 0.82rem; font-weight: 400;">
            Daftarkan pegawai internal PT. ITPI Technology (dengan akun login sistem atau tanpa akun login seperti Programmer)
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

    <form action="{{ route('admin.pegawai.store') }}" method="POST">
        @csrf

        @include('admin.pegawai._form-fields', ['pegawai' => null])

        {{-- ── Tombol Aksi (Sesuai Gaya Ajukan CR) ── --}}
        <div class="d-flex justify-content-end align-items-center gap-3 mt-4 pt-2">
            <a href="{{ route('admin.pegawai.index') }}" class="btn"
               style="border: 1px solid #CBD5E1; background: #FFFFFF; color: #475569; border-radius: 8px; padding: 0.6rem 1.6rem; font-size: 0.86rem; font-weight: 600; text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn fw-bold d-inline-flex align-items-center gap-2"
                    style="background: #10B981; border: none; border-radius: 8px; padding: 0.6rem 1.8rem; font-size: 0.86rem; font-weight: 700; color: #FFFFFF; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                <span>Simpan &amp; Daftarkan Pegawai</span>
            </button>
        </div>
    </form>

</div>
@endsection
