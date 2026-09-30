@extends('layouts.app')

@section('title', 'Profil Klien • CR Manager')

@section('content')
<div class="page-shell">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('change-requests.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left"></i> <span data-i18n="back_to_cr">Kembali ke Daftar CR</span>
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="text-primary small fw-semibold text-uppercase" data-i18n="client_profile_badge">Profil Instansi Klien</span>
            </div>
            <h3 class="fw-bold mb-0 text-dark" data-i18n="client_profile_title">Data Profil &amp; Master Klien</h3>
            <p class="text-muted small mb-0" data-i18n="client_profile_subtitle">
                Informasi identitas instansi rekanan serta data 3 Person in Charge (PIC) penanggung jawab.
            </p>
        </div>
        <div>
            <a href="{{ route('change-requests.create') }}" class="btn text-white fw-semibold px-3 py-2"
               style="background: #10B981; border-radius: 8px; font-size: 0.85rem; border: none; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">
                <i class="bi bi-plus-lg me-1"></i> <span data-i18n="btn_new_cr">Ajukan CR Baru</span>
            </a>
        </div>
    </div>

    {{-- Card 1: Data Instansi Perusahaan --}}
    <div class="card p-4 shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 16px; background: #FFFFFF;">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width: 52px; height: 52px; background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%); font-size: 1.25rem;">
                    {{ strtoupper(substr($clientRecord?->company ?? $user->company ?? $user->name, 0, 2)) }}
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">{{ $clientRecord?->company ?? $user->company ?? 'PT Rekanan Klien' }}</h5>
                    <div class="text-muted small">
                        <span data-i18n="initial_label">Inisial / Kode:</span> <span class="badge bg-primary-subtle text-primary fw-bold">{{ $clientRecord?->nickname ?? 'CLN' }}</span>
                        &bull; <span data-i18n="status_partner">Status Kerjasama:</span> <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                    </div>
                </div>
            </div>
            <span class="badge bg-light text-muted border px-3 py-2" style="font-size: 0.78rem;">
                <i class="bi bi-shield-check text-success me-1"></i> <span data-i18n="verified_account">Akun Terverifikasi</span>
            </span>
        </div>

        <div class="row g-3 pt-2">
            <div class="col-md-4">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;" data-i18n="pic_main_name">NAMA KLIEN / PIC UTAMA</div>
                <div class="fw-bold text-dark fs-6">{{ $user->name }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;" data-i18n="email_contact">EMAIL TERDAFTAR</div>
                <div class="fw-bold text-dark fs-6">{{ $user->email }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;" data-i18n="phone_contact">NOMOR TELEPON / WA</div>
                <div class="fw-bold text-dark fs-6">{{ $clientRecord?->phone ?? $user->phone ?? '+62 812-3456-7890' }}</div>
            </div>
            <div class="col-12 mt-3">
                <div class="text-muted small fw-semibold mb-1" style="font-size: 0.75rem;" data-i18n="office_address">ALAMAT KANTOR OPERASIONAL</div>
                <div class="text-secondary small">{{ $clientRecord?->address ?? 'Gedung Cyber 2 Lantai 12, Jl. H. R. Rasuna Said, Jakarta Selatan, DKI Jakarta 12950' }}</div>
            </div>
        </div>
    </div>

    {{-- Card 2: 3 PIC Khusus Master Client --}}
    <div class="card p-4 shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 16px; background: #FFFFFF;">
        <div class="mb-3">
            <h6 class="fw-bold text-dark mb-1" data-i18n="three_pic_title">Daftar 3 PIC Resmi Master Client</h6>
            <p class="text-muted small mb-0" data-i18n="three_pic_desc">
                PIC penanggung jawab komunikasi lintas divisi untuk administrasi dan eskalasi pengerjaan Change Request.
            </p>
        </div>

        <div class="row g-3">
            {{-- PIC Marketing --}}
            <div class="col-md-4">
                <div class="card p-3 h-100 border-0 shadow-none" style="background: #F0F9FF; border: 1.5px solid #BAE6FD !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #0284C7;">
                            <i class="bi bi-megaphone-fill" style="font-size: 0.9rem;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;" data-i18n="pic_marketing_title">PIC Marketing</div>
                    </div>
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $clientRecord?->pic_marketing_name ?? 'Budi Santoso' }}</div>
                    <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $clientRecord?->pic_marketing_phone ?? '081234567891' }}</div>
                    <div class="small text-muted mb-2"><i class="bi bi-envelope me-1"></i>{{ $clientRecord?->pic_marketing_email ?? 'marketing@client.com' }}</div>
                    <div class="small text-secondary mt-auto pt-2 border-top border-light-subtle" style="font-size: 0.76rem;">
                        {{ $clientRecord?->pic_marketing_desc ?? 'Koordinasi penawaran harga & scope komersial.' }}
                    </div>
                </div>
            </div>

            {{-- PIC IT / Technical --}}
            <div class="col-md-4">
                <div class="card p-3 h-100 border-0 shadow-none" style="background: #EEF2FF; border: 1.5px solid #C7D2FE !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #4F46E5;">
                            <i class="bi bi-code-slash" style="font-size: 0.9rem;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;" data-i18n="pic_it_title">PIC IT / Technical</div>
                    </div>
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $clientRecord?->pic_it_name ?? 'Agus Prasetyo' }}</div>
                    <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $clientRecord?->pic_it_phone ?? '081234567892' }}</div>
                    <div class="small text-muted mb-2"><i class="bi bi-envelope me-1"></i>{{ $clientRecord?->pic_it_email ?? 'it@client.com' }}</div>
                    <div class="small text-secondary mt-auto pt-2 border-top border-light-subtle" style="font-size: 0.76rem;">
                        {{ $clientRecord?->pic_it_desc ?? 'Verifikasi teknis, integrasi API, SIT, & UAT.' }}
                    </div>
                </div>
            </div>

            {{-- PIC Procurement --}}
            <div class="col-md-4">
                <div class="card p-3 h-100 border-0 shadow-none" style="background: #FFFBEB; border: 1.5px solid #FDE68A !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #D97706;">
                            <i class="bi bi-cart-check-fill" style="font-size: 0.9rem;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;" data-i18n="pic_proc_title">PIC Procurement</div>
                    </div>
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $clientRecord?->pic_procurement_name ?? 'Dewi Lestari' }}</div>
                    <div class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $clientRecord?->pic_procurement_phone ?? '081234567893' }}</div>
                    <div class="small text-muted mb-2"><i class="bi bi-envelope me-1"></i>{{ $clientRecord?->pic_procurement_email ?? 'procurement@client.com' }}</div>
                    <div class="small text-secondary mt-auto pt-2 border-top border-light-subtle" style="font-size: 0.76rem;">
                        {{ $clientRecord?->pic_procurement_desc ?? 'Administrasi kontrak, Purchase Order (PO), & invoice.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
