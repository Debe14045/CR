@php
    $isEdit = isset($pegawai) && $pegawai !== null;
    $loginRoles = ['pm', 'pmh', 'presales', 'admin'];

    $defaultRole = ($isEdit && $pegawai->user_id && in_array($pegawai->role, $loginRoles, true)) ? $pegawai->role : '';
    $selectedRole = old('_token') !== null ? (string) old('role', '') : $defaultRole;
    $hasSelectedRole = in_array($selectedRole, $loginRoles, true);

    $needsPasswordOnEnable = !$isEdit || !$pegawai->user;

    $divisiOptions = \App\Models\Pegawai::DIVISI_OPTIONS;
    $departemenOptions = \App\Models\Pegawai::DEPARTEMEN_OPTIONS;
    $jabatanOptions = \App\Models\Pegawai::JABATAN_OPTIONS;

    $selectedDivisi = old('divisi', $pegawai?->divisi);
    $selectedDepartemen = old('departemen', $pegawai?->departemen);
    $selectedPosition = old('position', $pegawai?->position);

    if (filled($selectedDivisi) && !in_array($selectedDivisi, $divisiOptions, true)) {
        $divisiOptions[] = $selectedDivisi;
    }
    if (filled($selectedDepartemen) && !in_array($selectedDepartemen, $departemenOptions, true)) {
        $departemenOptions[] = $selectedDepartemen;
    }
    if (filled($selectedPosition) && !in_array($selectedPosition, $jabatanOptions, true)) {
        $jabatanOptions[] = $selectedPosition;
    }

    $hasOrgError = $errors->has('divisi') || $errors->has('departemen') || $errors->has('position');
    $emailValue = old('email', $pegawai?->user?->email ?? $pegawai?->email);
@endphp

{{-- ── CARD 1: Data Identitas Pegawai (#DFEDFD Header Strip) ── --}}
<div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
    <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                 style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                <i class="bi bi-person-vcard" style="font-size: 1rem;"></i>
            </div>
            <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">1. Data Identitas Pegawai</span>
        </div>
        <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
            Informasi kepegawaian internal PT. ITPI Technology
        </div>
    </div>
    <div class="p-4" style="background: #FFFFFF;">
        <div class="row g-3">
            <div class="col-md-4 col-12">
                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                    Nomor Induk Pegawai (NIP) <span style="color: #EF4444;">*</span>
                </label>
                <input type="text" name="nip" maxlength="50" class="form-control font-monospace text-uppercase @error('nip') is-invalid @enderror"
                       placeholder="Contoh: ITPI-2026-001"
                       value="{{ old('nip', $pegawai?->nip) }}" required
                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                @error('nip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-8 col-12">
                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                    Nama Lengkap <span style="color: #EF4444;">*</span>
                </label>
                <input type="text" name="name" maxlength="255" class="form-control @error('name') is-invalid @enderror"
                       placeholder="Contoh: Budi Santoso, S.Kom."
                       value="{{ old('name', $pegawai?->name) }}" required
                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 col-12">
                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                    Email Pegawai
                    <span id="emailRequiredStar" style="color: #EF4444; {{ $hasSelectedRole ? '' : 'display: none;' }}">*</span>
                    <span id="emailOptionalHint" class="text-muted fw-normal small" style="{{ $hasSelectedRole ? 'display: none;' : '' }}">(Opsional jika tanpa role)</span>
                </label>
                <input type="email" name="email" id="pegawaiEmailInput" maxlength="255"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="contoh: nama.pegawai@itpi.co.id"
                       value="{{ $emailValue }}"
                       {{ $hasSelectedRole ? 'required' : '' }}
                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                <div id="emailHelperText" class="form-text small" style="font-size: 0.74rem; color: #64748B;">
                    {{ $hasSelectedRole ? 'Digunakan sebagai email login web & tersimpan di data identitas pegawai.' : 'Tersimpan pada data identitas pegawai (pilih role di bawah jika pegawai diberi akses login web).' }}
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 col-12">
                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                    Nomor WhatsApp / HP <span style="color: #EF4444;">*</span>
                </label>
                <input type="text" name="phone" maxlength="25" class="form-control @error('phone') is-invalid @enderror"
                       placeholder="Contoh: 08123456789"
                       value="{{ old('phone', $pegawai?->phone) }}" required
                       style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Struktur Organisasi (Minimal salah satu dari Divisi, Departemen, atau Jabatan wajib diisi) --}}
            <div class="col-12">
                <div class="p-3 rounded-3" style="background: #F8FAFC; border: 1px solid {{ $hasOrgError ? '#FCA5A5' : '#E2E8F0' }};">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                        <span class="fw-bold" style="font-size: 0.82rem; color: #0F172A;">
                            <i class="bi bi-diagram-3 me-1 text-primary"></i>Penempatan Organisasi &amp; Jabatan
                        </span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem; font-weight: 600;">
                            Wajib pilih minimal 1 kolom
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 col-12 org-field-col">
                            <label for="pegawaiDivisiSelect" class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Divisi
                                <span class="org-req-star" style="color: #EF4444;">*</span>
                                <span class="org-opt-hint text-muted fw-normal small" style="display: none;">(Opsional)</span>
                            </label>
                            <select name="divisi" id="pegawaiDivisiSelect"
                                    class="form-select org-select @error('divisi') is-invalid @enderror"
                                    onchange="syncOrgMandatory()"
                                    style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisiOptions as $opt)
                                    <option value="{{ $opt }}" {{ $selectedDivisi === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 col-12 org-field-col">
                            <label for="pegawaiDepartemenSelect" class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Departemen
                                <span class="org-req-star" style="color: #EF4444;">*</span>
                                <span class="org-opt-hint text-muted fw-normal small" style="display: none;">(Opsional)</span>
                            </label>
                            <select name="departemen" id="pegawaiDepartemenSelect"
                                    class="form-select org-select @error('departemen') is-invalid @enderror"
                                    onchange="syncOrgMandatory()"
                                    style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departemenOptions as $opt)
                                    <option value="{{ $opt }}" {{ $selectedDepartemen === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 col-12 org-field-col">
                            <label for="pegawaiPositionSelect" class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Jabatan
                                <span class="org-req-star" style="color: #EF4444;">*</span>
                                <span class="org-opt-hint text-muted fw-normal small" style="display: none;">(Opsional)</span>
                            </label>
                            <select name="position" id="pegawaiPositionSelect"
                                    class="form-select org-select @error('position') is-invalid @enderror"
                                    onchange="syncOrgMandatory()"
                                    style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatanOptions as $opt)
                                    <option value="{{ $opt }}" {{ $selectedPosition === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-text small mt-2" style="font-size: 0.74rem; color: #64748B;">
                        Jika salah satu kolom (misalnya <strong>Divisi</strong>) sudah dipilih, kolom lainnya tidak wajib diisi.
                    </div>
                    @if($hasOrgError)
                        <div class="text-danger small fw-semibold mt-1" style="font-size: 0.78rem;">
                            {{ $errors->first('divisi') ?: ($errors->first('departemen') ?: $errors->first('position')) }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                    Alamat Domisili <span class="text-muted fw-normal small">(Opsional)</span>
                </label>
                <textarea name="address" rows="2" maxlength="65535" class="form-control @error('address') is-invalid @enderror"
                          placeholder="Alamat domisili pegawai..."
                          style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">{{ old('address', $pegawai?->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── CARD 2: Role Sistem & Akses Login (#DFEDFD Header Strip) ── --}}
<div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
    <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                 style="width: 32px; height: 32px; background: #156BE9; flex-shrink: 0;">
                <i class="bi bi-shield-lock-fill" style="font-size: 1rem;"></i>
            </div>
            <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">2. Role Sistem &amp; Akses Login</span>
        </div>
        <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
            Opsional — pilih role hanya jika pegawai diberikan akses login ke sistem CR
        </div>
    </div>
    <div class="p-4" style="background: #FFFFFF;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <label class="form-label fw-bold mb-0" style="font-size: 0.78rem; color: #0F172A;">
                Pilih Role Sistem CR <span class="text-muted fw-normal small">(Opsional — klik lagi untuk membatalkan)</span>
            </label>
            <button type="button" id="clearRoleBtn" onclick="clearSelectedRole()"
                    class="btn btn-sm btn-outline-secondary py-1 px-2"
                    style="font-size: 0.72rem; border-radius: 6px; {{ $hasSelectedRole ? '' : 'display: none;' }}">
                <i class="bi bi-x-circle me-1"></i>Kosongkan Role (Non-Login)
            </button>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-12">
                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 h-100 role-card" data-role-card
                       style="border-radius: 10px !important; cursor: pointer; border-color: #E2E8F0; background: #FFFFFF; transition: all 0.15s ease;">
                    <input type="radio" name="role" value="pm" class="form-check-input mt-1 role-radio"
                           {{ $selectedRole === 'pm' ? 'checked' : '' }} onchange="syncRoleState()">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <span class="badge bg-success" style="font-size: 0.68rem;">PM</span>
                            <span>Project Manager</span>
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 0.76rem; line-height: 1.4;">
                            Verifikasi CR, Solution Paper, estimasi mandays, Dev Board, status &amp; Berita Acara.
                        </div>
                    </div>
                </label>
            </div>

            <div class="col-md-6 col-12">
                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 h-100 role-card" data-role-card
                       style="border-radius: 10px !important; cursor: pointer; border-color: #E2E8F0; background: #FFFFFF; transition: all 0.15s ease;">
                    <input type="radio" name="role" value="pmh" class="form-check-input mt-1 role-radio" id="rolePmhInput"
                           {{ $selectedRole === 'pmh' ? 'checked' : '' }} onchange="syncRoleState()">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <span class="badge bg-danger" style="font-size: 0.68rem;">PMH</span>
                            <span>PM Head (Project Manager Head)</span>
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 0.76rem; line-height: 1.4;">
                            Validasi &amp; approval resmi CR, monitoring outstanding payment, validasi Go-Live.
                        </div>
                    </div>
                </label>
            </div>

            <div class="col-md-6 col-12">
                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 h-100 role-card" data-role-card
                       style="border-radius: 10px !important; cursor: pointer; border-color: #E2E8F0; background: #FFFFFF; transition: all 0.15s ease;">
                    <input type="radio" name="role" value="presales" class="form-check-input mt-1 role-radio"
                           {{ $selectedRole === 'presales' ? 'checked' : '' }} onchange="syncRoleState()">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <span class="badge bg-primary" style="font-size: 0.68rem;">Presales</span>
                            <span>Presales Engineer</span>
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 0.76rem; line-height: 1.4;">
                            Penyusunan penawaran harga/quotation CR, pipeline CR, dan review dokumen teknis.
                        </div>
                    </div>
                </label>
            </div>

            <div class="col-md-6 col-12">
                <label class="p-3 border rounded-3 d-flex align-items-start gap-3 h-100 role-card" data-role-card
                       style="border-radius: 10px !important; cursor: pointer; border-color: #E2E8F0; background: #FFFFFF; transition: all 0.15s ease;">
                    <input type="radio" name="role" value="admin" class="form-check-input mt-1 role-radio"
                           {{ $selectedRole === 'admin' ? 'checked' : '' }} onchange="syncRoleState()">
                    <div>
                        <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <span class="badge text-white" style="background: #4F46E5; font-size: 0.68rem;">Admin</span>
                            <span>Administrator</span>
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 0.76rem; line-height: 1.4;">
                            Master data perusahaan klien, pembuatan data pegawai, dan master proyek.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Opsi Khusus PM Head --}}
        <div class="mt-3" id="pmhOptionsContainer" style="{{ $selectedRole === 'pmh' ? '' : 'display: none;' }}">
            <div class="p-3 rounded-3" style="background: #FFF1F2; border: 1px solid #FECDD3;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="can_act_as_pm" value="1" id="canActAsPmCheck"
                           {{ old('can_act_as_pm', $pegawai?->user?->can_act_as_pm) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark" for="canActAsPmCheck" style="font-size: 0.84rem;">
                        Izinkan bertindak sebagai Project Manager (Fitur Switch Role)
                    </label>
                    <div class="text-muted small" style="font-size: 0.75rem;">
                        Jika dicentang, PM Head dapat beralih peran ke Dashboard PM untuk mengisi analisis teknis atau mengelola status secara langsung.
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Akun & Password ketika Role Dipilih --}}
        <div id="roleLoginInfoBox" class="mt-3" style="{{ $hasSelectedRole ? '' : 'display: none;' }}">
            @if($needsPasswordOnEnable)
                <div class="alert d-flex align-items-start gap-2 mb-0" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; color: #1E3A8A; font-size: 0.82rem;">
                    <i class="bi bi-shield-check text-primary mt-1 flex-shrink-0"></i>
                    <div>
                        <strong>Akun Login Web Aktif:</strong> Email pada <em>Data Identitas Pegawai</em> di atas akan dipakai untuk login ke web dengan password default
                        <code class="px-2 py-1 rounded" style="background: #DBEAFE; color: #1D4ED8; font-weight: 700;">{{ \App\Models\User::DEFAULT_PASSWORD }}</code> (dapat diubah pegawai di menu Profil setelah login).
                    </div>
                </div>
            @else
                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Ubah Password Baru <span class="text-muted fw-normal small">(Kosongkan jika tidak diubah)</span>
                        </label>
                        <input type="password" name="password" id="pegawaiPasswordInput" maxlength="255"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Kosongkan jika mempertahankan password lama"
                               value=""
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
                        <div class="form-text small" style="font-size: 0.74rem; color: #64748B;">
                            Isi minimal 6 karakter hanya jika ingin mereset password akun ini.
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif
        </div>

        {{-- Info ketika Tanpa Role (Non-Login) --}}
        <div id="noRoleNotice" class="mt-3" style="{{ !$hasSelectedRole ? '' : 'display: none;' }}">
            <div class="alert d-flex align-items-start gap-2 mb-0" style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 10px; color: #475569; font-size: 0.82rem;">
                <i class="bi bi-person-dash text-secondary mt-1 flex-shrink-0"></i>
                <div>
                    <strong>Tanpa Role Sistem (Tidak Bisa Login Web):</strong> Pegawai tetap tersimpan pada Data Pegawai tanpa akun login web. Pilih salah satu role di atas hanya jika pegawai perlu mengakses aplikasi CR.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function syncOrgMandatory() {
        const divisi = document.getElementById('pegawaiDivisiSelect');
        const departemen = document.getElementById('pegawaiDepartemenSelect');
        const position = document.getElementById('pegawaiPositionSelect');
        const selects = [divisi, departemen, position].filter(Boolean);
        if (!selects.length) return;

        const hasAnyFilled = selects.some(function (el) {
            return el.value.trim() !== '';
        });

        selects.forEach(function (el) {
            const wrapper = el.closest('.org-field-col');
            const star = wrapper ? wrapper.querySelector('.org-req-star') : null;
            const optHint = wrapper ? wrapper.querySelector('.org-opt-hint') : null;

            if (!hasAnyFilled) {
                el.required = true;
                if (star) star.style.display = 'inline';
                if (optHint) optHint.style.display = 'none';
            } else {
                const isFilled = el.value.trim() !== '';
                el.required = isFilled;
                if (star) star.style.display = isFilled ? 'inline' : 'none';
                if (optHint) optHint.style.display = isFilled ? 'none' : 'inline';
            }
        });
    }

    function syncRoleState() {
        const radios = document.querySelectorAll('input.role-radio[name="role"]');
        const checkedRadio = Array.from(radios).find(function (r) { return r.checked; });
        const hasRole = Boolean(checkedRadio);

        radios.forEach(function (radio) {
            const card = radio.closest('.role-card');
            if (!card) return;
            card.style.borderColor = radio.checked ? '#2563EB' : '#E2E8F0';
            card.style.background = radio.checked ? '#F8FAFF' : '#FFFFFF';
        });

        const clearBtn = document.getElementById('clearRoleBtn');
        const roleLoginInfoBox = document.getElementById('roleLoginInfoBox');
        const noRoleNotice = document.getElementById('noRoleNotice');
        const pmhContainer = document.getElementById('pmhOptionsContainer');
        const pmhInput = document.getElementById('rolePmhInput');

        const emailInput = document.getElementById('pegawaiEmailInput');
        const emailReqStar = document.getElementById('emailRequiredStar');
        const emailOptHint = document.getElementById('emailOptionalHint');
        const emailHelperText = document.getElementById('emailHelperText');

        if (clearBtn) clearBtn.style.display = hasRole ? 'inline-flex' : 'none';
        if (roleLoginInfoBox) roleLoginInfoBox.style.display = hasRole ? 'block' : 'none';
        if (noRoleNotice) noRoleNotice.style.display = hasRole ? 'none' : 'block';

        if (pmhContainer && pmhInput) {
            pmhContainer.style.display = pmhInput.checked ? 'block' : 'none';
        }

        if (emailInput) emailInput.required = hasRole;
        if (emailReqStar) emailReqStar.style.display = hasRole ? 'inline' : 'none';
        if (emailOptHint) emailOptHint.style.display = hasRole ? 'none' : 'inline';
        if (emailHelperText) {
            emailHelperText.textContent = hasRole
                ? 'Digunakan sebagai email login web & tersimpan di data identitas pegawai.'
                : 'Tersimpan pada data identitas pegawai (pilih role di bawah jika pegawai diberi akses login web).';
        }
    }

    function clearSelectedRole() {
        document.querySelectorAll('input.role-radio[name="role"]').forEach(function (r) {
            r.checked = false;
        });
        syncRoleState();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.role-card[data-role-card]').forEach(function (card) {
            const radio = card.querySelector('input.role-radio[name="role"]');
            if (!radio) return;

            card.addEventListener('pointerdown', function () {
                card.dataset.wasChecked = radio.checked ? '1' : '0';
            });

            card.addEventListener('click', function (e) {
                if (card.dataset.wasChecked === '1') {
                    e.preventDefault();
                    radio.checked = false;
                    card.dataset.wasChecked = '0';
                    syncRoleState();
                }
            });
        });

        syncRoleState();
        syncOrgMandatory();
    });
</script>
