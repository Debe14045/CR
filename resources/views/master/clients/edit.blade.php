@extends('layouts.app')

@section('title', 'Edit Master Client &middot; Administrator')

@section('content')
<div class="page-shell">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('master.clients.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Client
        </a>
    </div>

    <div class="card p-4 shadow-sm border-0 mb-4">
        <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
            <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-pencil-square fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">Edit Master Client: {{ $client->company }}</h4>
                <p class="text-muted small mb-0">Perbarui profil instansi dan kontak 3 PIC jika terjadi rotasi atau pergantian personel.</p>
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

        <form method="POST" action="{{ route('master.clients.update', $client) }}">
            @csrf
            @method('PUT')

            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-building me-2"></i> 1. Informasi Pokok Perusahaan / Klien</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label">Nama Perusahaan (Nama Resmi) <span class="required-mark">*</span></label>
                    <input type="text" name="company" class="form-control" value="{{ old('company', $client->company) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Inisial / Nickname Klien <span class="required-mark">*</span></label>
                    <input type="text" name="nickname" class="form-control" value="{{ old('nickname', $client->nickname) }}" required>
                    <div class="form-text">Contoh: ITPI</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon Kantor</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Resmi Perusahaan</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Alamat Lengkap Kantor</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $client->address) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="clientActiveSwitch" {{ $client->active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="clientActiveSwitch">Status Klien Aktif</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-people-fill me-2"></i> 2. Profil 3 PIC Khusus</h5>
            <div class="row g-4">
                {{-- PIC Marketing --}}
                <div class="col-lg-4">
                    <div class="card p-3 h-100 border-info border-opacity-25 bg-info bg-opacity-10 shadow-none">
                        <div class="fw-bold text-info fs-6 mb-2">
                            <i class="bi bi-megaphone-fill me-1"></i> A. PIC Marketing <span class="required-mark">*</span>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nama Lengkap</label>
                            <input type="text" name="pic_marketing_name" class="form-control form-control-sm" value="{{ old('pic_marketing_name', $client->pic_marketing_name) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nomor Telepon / WA</label>
                            <input type="text" name="pic_marketing_phone" class="form-control form-control-sm" value="{{ old('pic_marketing_phone', $client->pic_marketing_phone) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Email PIC</label>
                            <input type="email" name="pic_marketing_email" class="form-control form-control-sm" value="{{ old('pic_marketing_email', $client->pic_marketing_email) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Keterangan Peran</label>
                            <textarea name="pic_marketing_desc" rows="2" class="form-control form-control-sm">{{ old('pic_marketing_desc', $client->pic_marketing_desc) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- PIC IT / Programmer --}}
                <div class="col-lg-4">
                    <div class="card p-3 h-100 border-primary border-opacity-25 bg-primary bg-opacity-10 shadow-none">
                        <div class="fw-bold text-primary fs-6 mb-2">
                            <i class="bi bi-code-square me-1"></i> B. PIC IT / Programmer <span class="required-mark">*</span>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nama Lengkap</label>
                            <input type="text" name="pic_it_name" class="form-control form-control-sm" value="{{ old('pic_it_name', $client->pic_it_name) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nomor Telepon / WA</label>
                            <input type="text" name="pic_it_phone" class="form-control form-control-sm" value="{{ old('pic_it_phone', $client->pic_it_phone) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Email PIC</label>
                            <input type="email" name="pic_it_email" class="form-control form-control-sm" value="{{ old('pic_it_email', $client->pic_it_email) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Keterangan Peran</label>
                            <textarea name="pic_it_desc" rows="2" class="form-control form-control-sm">{{ old('pic_it_desc', $client->pic_it_desc) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- PIC Procurement --}}
                <div class="col-lg-4">
                    <div class="card p-3 h-100 border-warning border-opacity-25 bg-warning bg-opacity-10 shadow-none">
                        <div class="fw-bold text-warning fs-6 mb-2">
                            <i class="bi bi-cart-check-fill me-1"></i> C. PIC Procurement <span class="required-mark">*</span>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nama Lengkap</label>
                            <input type="text" name="pic_procurement_name" class="form-control form-control-sm" value="{{ old('pic_procurement_name', $client->pic_procurement_name) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nomor Telepon / WA</label>
                            <input type="text" name="pic_procurement_phone" class="form-control form-control-sm" value="{{ old('pic_procurement_phone', $client->pic_procurement_phone) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Email PIC</label>
                            <input type="email" name="pic_procurement_email" class="form-control form-control-sm" value="{{ old('pic_procurement_email', $client->pic_procurement_email) }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Keterangan Peran</label>
                            <textarea name="pic_procurement_desc" rows="2" class="form-control form-control-sm">{{ old('pic_procurement_desc', $client->pic_procurement_desc) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('master.clients.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Perbarui Data Client
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
