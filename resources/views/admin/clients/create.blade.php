@extends('layouts.app')

@section('title', 'Tambah Klien & Akun PIC • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; padding: 0.25rem 0.5rem 3rem; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (Sesuai Gaya Ajukan CR) ── --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.68rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            Tambah Klien &amp; Akun Login PIC
        </h1>
        <div style="color: #64748B; font-size: 0.82rem; font-weight: 400;">
            Daftarkan perusahaan klien mitra, buatkan akun login PIC resmi, dan siapkan proyek awal
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

    <form action="{{ route('admin.clients.store') }}" method="POST">
        @csrf

        {{-- ── CARD 1: Data Perusahaan Klien ── --}}
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
                    Lengkapi informasi resmi badan usaha atau instansi mitra klien
                </div>
            </div>
            <div class="p-4" style="background: #FFFFFF;">
                <div class="row g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Perusahaan / Instansi <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="company" maxlength="255" class="form-control @error('company') is-invalid @enderror"
                               placeholder="Contoh: PT. Bank Central Asia Tbk"
                               value="{{ old('company') }}" required
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
                               placeholder="Contoh: BCA"
                               value="{{ old('nickname') }}"
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
                                  placeholder="Contoh: Menara BCA Grand Indonesia Lt. 18, Jl. M.H. Thamrin No. 1, Jakarta Pusat"
                                  style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: Akun Login PIC Resmi (Marketing, Procurement, IT) ── --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                        <i class="bi bi-people-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">2. Pembuatan Akun Login PIC Resmi (Client)</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Setiap PIC akan dibuatkan akun pengguna agar bisa login ke sistem CR
                </div>
            </div>
            <div class="p-4" style="background: #FFFFFF;">
                <div class="alert d-flex align-items-start gap-2 mb-4" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; color: #1E3A8A; font-size: 0.82rem;">
                    <i class="bi bi-shield-check text-primary mt-1 flex-shrink-0"></i>
                    <div>
                        <strong>Password Default Akun PIC:</strong> Setiap akun login PIC klien akan otomatis dibuat menggunakan password default
                        <code class="px-2 py-1 rounded" style="background: #DBEAFE; color: #1D4ED8; font-weight: 700;">{{ \App\Models\User::DEFAULT_PASSWORD }}</code> (klien dapat mengubahnya setelah login).
                    </div>
                </div>
                <div class="row g-4">
                    {{-- PIC 1: MARKETING --}}
                    <div class="col-lg-4 col-12">
                        <div class="p-4 h-100" style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge px-2 py-1" style="background: #2563EB; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; border-radius: 6px;">Divisi 1</span>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">PIC Marketing</span>
                                </div>
                                <i class="bi bi-megaphone fs-5 text-primary"></i>
                            </div>
                            <input type="hidden" name="pics[0][role_type]" value="marketing">

                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Nama Lengkap <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="pics[0][name]" maxlength="255" class="form-control"
                                       placeholder="Nama PIC Marketing"
                                       value="{{ old('pics.0.name') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Email Login <span style="color: #EF4444;">*</span></label>
                                <input type="email" name="pics[0][email]" maxlength="255" class="form-control"
                                       placeholder="marketing@klien.com"
                                       value="{{ old('pics.0.email') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                            <div>
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">No. WhatsApp / HP <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="pics[0][phone]" maxlength="25" class="form-control"
                                       placeholder="0812xxxxxxxx"
                                       value="{{ old('pics.0.phone') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                        </div>
                    </div>

                    {{-- PIC 2: PROCUREMENT --}}
                    <div class="col-lg-4 col-12">
                        <div class="p-4 h-100" style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge px-2 py-1" style="background: #059669; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; border-radius: 6px;">Divisi 2</span>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">PIC Procurement</span>
                                </div>
                                <i class="bi bi-cart-check fs-5 text-success"></i>
                            </div>
                            <input type="hidden" name="pics[1][role_type]" value="procurement">

                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Nama Lengkap <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="pics[1][name]" maxlength="255" class="form-control"
                                       placeholder="Nama PIC Procurement"
                                       value="{{ old('pics.1.name') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Email Login <span style="color: #EF4444;">*</span></label>
                                <input type="email" name="pics[1][email]" maxlength="255" class="form-control"
                                       placeholder="procurement@klien.com"
                                       value="{{ old('pics.1.email') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                            <div>
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">No. WhatsApp / HP <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="pics[1][phone]" maxlength="25" class="form-control"
                                       placeholder="0813xxxxxxxx"
                                       value="{{ old('pics.1.phone') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                        </div>
                    </div>

                    {{-- PIC 3: IT / TECHNICAL --}}
                    <div class="col-lg-4 col-12">
                        <div class="p-4 h-100" style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge px-2 py-1" style="background: #7C3AED; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; border-radius: 6px;">Divisi 3</span>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">PIC IT / Technical</span>
                                </div>
                                <i class="bi bi-cpu fs-5" style="color: #7C3AED;"></i>
                            </div>
                            <input type="hidden" name="pics[2][role_type]" value="it">

                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Nama Lengkap <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="pics[2][name]" maxlength="255" class="form-control"
                                       placeholder="Nama PIC IT"
                                       value="{{ old('pics.2.name') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">Email Login <span style="color: #EF4444;">*</span></label>
                                <input type="email" name="pics[2][email]" maxlength="255" class="form-control"
                                       placeholder="it@klien.com"
                                       value="{{ old('pics.2.email') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                            <div>
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">No. WhatsApp / HP <span style="color: #EF4444;">*</span></label>
                                <input type="text" name="pics[2][phone]" maxlength="25" class="form-control"
                                       placeholder="0811xxxxxxxx"
                                       value="{{ old('pics.2.phone') }}" required
                                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CARD 3: Proyek Awal Klien (Opsional) ── --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                        <i class="bi bi-kanban" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">3. Inisialisasi Proyek Klien <span class="text-muted fw-normal small">(Opsional)</span></span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Agar Klien dapat langsung memilih proyek dan mengajukan Change Request segera setelah login
                </div>
            </div>
            <div class="p-4" style="background: #FFFFFF;">
                <div class="row g-3">
                    <div class="col-md-4 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Kode Proyek
                        </label>
                        <input type="text" name="kode_proyek" maxlength="50" class="form-control text-uppercase font-monospace @error('kode_proyek') is-invalid @enderror"
                               placeholder="Contoh: PRJ-BCA-001"
                               value="{{ old('kode_proyek') }}"
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        <div class="form-text small" style="font-size: 0.75rem; color: #64748B;">Kode unik identifikasi proyek internal.</div>
                        @error('kode_proyek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Proyek
                        </label>
                        <input type="text" name="nama_proyek" maxlength="255" class="form-control @error('nama_proyek') is-invalid @enderror"
                               placeholder="Contoh: Implementasi Payment Gateway QRIS & E-Wallet"
                               value="{{ old('nama_proyek') }}"
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        <div class="form-text small" style="font-size: 0.75rem; color: #64748B;">Nama sistem atau aplikasi yang sedang dikembangkan.</div>
                        @error('nama_proyek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Deskripsi Lingkup Proyek <span class="text-muted fw-normal small">(Opsional)</span>
                        </label>
                        <textarea name="deskripsi_proyek" rows="3" maxlength="65535" class="form-control"
                                  placeholder="Deskripsi singkat lingkup pekerjaan proyek awal..."
                                  style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">{{ old('deskripsi_proyek') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tombol Aksi Bawah (Sesuai Gaya Ajukan CR) ── --}}
        <div class="d-flex justify-content-end align-items-center gap-3 pt-2 mb-5">
            <a href="{{ route('admin.clients.index') }}"
               class="btn fw-semibold"
               style="background: #F1F5F9; color: #475569; border-radius: 8px; font-size: 0.88rem; padding: 0.65rem 1.6rem; border: 1px solid #E2E8F0; text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
                    style="background: #10B981; border-radius: 8px; font-size: 0.88rem; padding: 0.65rem 1.8rem; border: none; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                <i class="bi bi-check-lg" style="font-size: 1.1rem;"></i>
                <span>Simpan &amp; Daftarkan Klien</span>
            </button>
        </div>
    </form>

</div>
@endsection
