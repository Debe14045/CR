@extends('layouts.app')

@section('title', 'Tambah Master Client Baru &middot; Administrator')

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
                <i class="bi bi-buildings-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1">Tambah Master Client &amp; 3 PIC Khusus</h4>
                <p class="text-muted small mb-0">Isi data instansi perusahaan dan lengkapkan profil PIC Marketing, IT/Programmer, serta Procurement.</p>
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

        <form method="POST" action="{{ route('master.clients.store') }}">
            @csrf

            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-building me-2"></i> 1. Informasi Pokok Perusahaan / Klien</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label">Nama Perusahaan (Nama Resmi) <span class="required-mark">*</span></label>
                    <input type="text" name="company" class="form-control" placeholder="Contoh: PT IT Prer Indonesia Technologi" value="{{ old('company') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Inisial / Nickname Klien <span class="required-mark">*</span></label>
                    <input type="text" name="nickname" class="form-control" placeholder="Contoh: ITPI" value="{{ old('nickname') }}" required>
                    <div class="form-text">Digunakan untuk penamaan kode transaksi.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nomor Telepon Kantor</label>
                    <input type="text" name="phone" class="form-control" placeholder="Contoh: (021) 555-0199" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Resmi Perusahaan</label>
                    <input type="email" name="email" class="form-control" placeholder="corporate@itpi.co.id" value="{{ old('email') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Alamat Lengkap Kantor</label>
                    <textarea name="address" rows="2" class="form-control" placeholder="Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan">{{ old('address') }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="clientActiveSwitch" checked>
                        <label class="form-check-label fw-bold" for="clientActiveSwitch">Status Klien Aktif</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-people-fill me-2"></i> 2. Profil 3 PIC Khusus (Mandatory)</h5>
            <p class="text-muted small mb-3">Sistem wajib menyimpan dan membedakan kontak 3 PIC spesifik berikut untuk kemudahan koordinasi.</p>

            <div class="row g-4">
                {{-- PIC Marketing --}}
                <div class="col-lg-4">
                    <div class="card p-3 h-100 border-info border-opacity-25 bg-info bg-opacity-10 shadow-none">
                        <div class="fw-bold text-info fs-6 mb-2">
                            <i class="bi bi-megaphone-fill me-1"></i> A. PIC Marketing <span class="required-mark">*</span>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nama Lengkap</label>
                            <input type="text" name="pic_marketing_name" class="form-control form-control-sm" placeholder="Nama PIC Marketing" value="{{ old('pic_marketing_name') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nomor Telepon / WA</label>
                            <input type="text" name="pic_marketing_phone" class="form-control form-control-sm" placeholder="0812xxxx" value="{{ old('pic_marketing_phone') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Email PIC</label>
                            <input type="email" name="pic_marketing_email" class="form-control form-control-sm" placeholder="marketing@client.com" value="{{ old('pic_marketing_email') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Keterangan Peran</label>
                            <textarea name="pic_marketing_desc" rows="2" class="form-control form-control-sm" placeholder="Peran/tanggung jawab PIC">{{ old('pic_marketing_desc') }}</textarea>
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
                            <input type="text" name="pic_it_name" class="form-control form-control-sm" placeholder="Nama PIC IT" value="{{ old('pic_it_name') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nomor Telepon / WA</label>
                            <input type="text" name="pic_it_phone" class="form-control form-control-sm" placeholder="0813xxxx" value="{{ old('pic_it_phone') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Email PIC</label>
                            <input type="email" name="pic_it_email" class="form-control form-control-sm" placeholder="tech@client.com" value="{{ old('pic_it_email') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Keterangan Peran</label>
                            <textarea name="pic_it_desc" rows="2" class="form-control form-control-sm" placeholder="Lead Dev / IT Manager dsb">{{ old('pic_it_desc') }}</textarea>
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
                            <input type="text" name="pic_procurement_name" class="form-control form-control-sm" placeholder="Nama PIC Procurement" value="{{ old('pic_procurement_name') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Nomor Telepon / WA</label>
                            <input type="text" name="pic_procurement_phone" class="form-control form-control-sm" placeholder="0811xxxx" value="{{ old('pic_procurement_phone') }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Email PIC</label>
                            <input type="email" name="pic_procurement_email" class="form-control form-control-sm" placeholder="procurement@client.com" value="{{ old('pic_procurement_email') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Keterangan Peran</label>
                            <textarea name="pic_procurement_desc" rows="2" class="form-control form-control-sm" placeholder="Purchasing / Vendor Management">{{ old('pic_procurement_desc') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('master.clients.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Master Client
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
