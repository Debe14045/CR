@extends('layouts.app')

@section('title', 'Edit Proyek ' . $project->nama_proyek . ' • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; padding: 0.25rem 0.5rem 3rem; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (Sesuai Gaya Ajukan CR) ── --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.68rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            Edit Proyek Klien
        </h1>
        <div style="color: #64748B; font-size: 0.82rem; font-weight: 400;">
            Perbarui informasi kode proyek, nama sistem, atau tetapkan ulang klien pemilik
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

    <form action="{{ route('admin.projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── CARD 1: Informasi & Lingkup Proyek (#DFEDFD Header Strip) ── --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                        <i class="bi bi-pencil-square" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">1. Informasi &amp; Lingkup Proyek</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Perbarui data spesifikasi master proyek klien
                </div>
            </div>
            <div class="p-4" style="background: #FFFFFF;">
                <div class="row g-3">
                    <div class="col-md-4 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Pilih Klien Pemilik Proyek <span style="color: #EF4444;">*</span>
                        </label>
                        <select name="client_id" class="form-select @error('client_id') is-invalid @enderror" required
                                style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            <option value="">-- Pilih Perusahaan Klien --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->company }} {{ $client->nickname ? '('.$client->nickname.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Kode Proyek <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="kode_proyek" maxlength="50" class="form-control font-monospace text-uppercase @error('kode_proyek') is-invalid @enderror"
                               value="{{ old('kode_proyek', $project->kode_proyek) }}" required
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        @error('kode_proyek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Proyek / Sistem <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="nama_proyek" maxlength="255" class="form-control @error('nama_proyek') is-invalid @enderror"
                               value="{{ old('nama_proyek', $project->nama_proyek) }}" required
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        @error('nama_proyek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.projects._pm-fields', ['pm1Value' => $pm1Id ?? $project->pm_id, 'pm2Value' => $pm2Id ?? null])

                    <div class="col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Deskripsi Lingkup Pekerjaan <span class="text-muted fw-normal small">(Opsional)</span>
                        </label>
                        <textarea name="deskripsi" rows="3" maxlength="65535" class="form-control @error('deskripsi') is-invalid @enderror"
                                  style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">{{ old('deskripsi', $project->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tombol Aksi ── --}}
        <div class="d-flex justify-content-end align-items-center gap-3 mt-4 pt-2">
            <a href="{{ route('admin.projects.index') }}" class="btn"
               style="border: 1px solid #CBD5E1; background: #FFFFFF; color: #475569; border-radius: 8px; padding: 0.6rem 1.6rem; font-size: 0.86rem; font-weight: 600; text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn fw-bold d-inline-flex align-items-center gap-2"
                    style="background: #10B981; border: none; border-radius: 8px; padding: 0.6rem 1.8rem; font-size: 0.86rem; font-weight: 700; color: #FFFFFF; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                <span>Perbarui Proyek</span>
            </button>
        </div>
    </form>

</div>
@endsection
