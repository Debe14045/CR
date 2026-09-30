@extends('layouts.app')

@section('title', 'Edit Pegawai ITP &middot; Administrator')

@section('content')
<div class="page-shell">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('master.pegawai.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pegawai
        </a>
    </div>

    <div class="card p-4 shadow-sm border-0 mb-4" style="max-width: 800px;">
        <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
            <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-pencil-square fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">Edit Pegawai: {{ $pegawai->name }}</h4>
                <p class="text-muted small mb-0">Perbarui identitas, alamat, atau mutasi jabatan pegawai.</p>
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

        <form method="POST" action="{{ route('master.pegawai.update', $pegawai) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nomor Induk Pegawai (NIP) <span class="required-mark">*</span></label>
                <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap Pegawai <span class="required-mark">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $pegawai->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jabatan / Posisi <span class="required-mark">*</span></label>
                <select name="position" class="form-select" required>
                    @foreach (['Project Manager (PM)', 'System Analyst', 'Senior Developer', 'Frontend Developer', 'Backend Developer', 'QA / Software Tester', 'Technical Writer', 'DevOps Engineer'] as $pos)
                        <option value="{{ $pos }}" {{ old('position', $pegawai->position) == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Domisili</label>
                <textarea name="address" rows="3" class="form-control">{{ old('address', $pegawai->address) }}</textarea>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="pegawaiActive" {{ $pegawai->is_active ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="pegawaiActive">Status Pegawai Aktif</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('master.pegawai.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Perbarui Pegawai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
