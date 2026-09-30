@extends('layouts.app')

@section('title', 'Tambah Pegawai Baru &middot; Administrator')

@section('content')
<div class="page-shell">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('master.pegawai.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pegawai
        </a>
    </div>

    <div class="card p-4 shadow-sm border-0 mb-4" style="max-width: 800px;">
        <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
            <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                <i class="bi bi-person-plus-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">Tambah Master Pegawai / Staff ITP</h4>
                <p class="text-muted small mb-0">Input identitas pegawai pelaksana proyek Change Request.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('master.pegawai.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nomor Induk Pegawai (NIP) <span class="required-mark">*</span></label>
                <input type="text" name="nip" class="form-control" placeholder="Contoh: ITP-2024-088" value="{{ old('nip') }}" required>
                <div class="form-text">NIP harus unik untuk tiap pegawai.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap Pegawai <span class="required-mark">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Aditya Pratama" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jabatan / Posisi <span class="required-mark">*</span></label>
                <select name="position" class="form-select" required>
                    <option value="" disabled selected>Pilih Jabatan...</option>
                    <option value="Project Manager (PM)" {{ old('position') == 'Project Manager (PM)' ? 'selected' : '' }}>Project Manager (PM)</option>
                    <option value="System Analyst" {{ old('position') == 'System Analyst' ? 'selected' : '' }}>System Analyst</option>
                    <option value="Senior Developer" {{ old('position') == 'Senior Developer' ? 'selected' : '' }}>Senior Developer</option>
                    <option value="Frontend Developer" {{ old('position') == 'Frontend Developer' ? 'selected' : '' }}>Frontend Developer</option>
                    <option value="Backend Developer" {{ old('position') == 'Backend Developer' ? 'selected' : '' }}>Backend Developer</option>
                    <option value="QA / Software Tester" {{ old('position') == 'QA / Software Tester' ? 'selected' : '' }}>QA / Software Tester</option>
                    <option value="Technical Writer" {{ old('position') == 'Technical Writer' ? 'selected' : '' }}>Technical Writer</option>
                    <option value="DevOps Engineer" {{ old('position') == 'DevOps Engineer' ? 'selected' : '' }}>DevOps Engineer</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Domisili</label>
                <textarea name="address" rows="3" class="form-control" placeholder="Alamat lengkap tinggal pegawai...">{{ old('address') }}</textarea>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="pegawaiActive" checked>
                    <label class="form-check-label fw-bold" for="pegawaiActive">Status Pegawai Aktif</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('master.pegawai.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Pegawai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
