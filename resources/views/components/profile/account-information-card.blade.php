@props([
    'name' => 'Siti Rahma',
    'company' => 'CV Teknologi Nusantara (disabled)',
    'initial' => 'SR (disabled)',
    'jobTitle' => 'IT Manager',
    'email' => 'siti@teknologinusantara.co.id',
    'phone' => '0812-xxxx-xxxx',
])

{{-- ══════════════════════════════════════════════════════════════
     AccountInformationCard Component (Pure Native HTML/CSS)
     Background: #F0F3F6, Border: 1.5px solid #BAE6FD
══════════════════════════════════════════════════════════════ --}}
<div class="account-info-card" style="background: #F0F3F6; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 20px 24px; box-sizing: border-box; height: 100%;">
    {{-- Card Title in Cyan Uppercase --}}
    <div style="font-size: 0.72rem; font-weight: 800; color: #0284C7; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 16px;">
        INFORMASI AKUN
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">

        {{-- 1. Nama lengkap --}}
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">Nama lengkap</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;">{{ $name }}</div>
        </div>

        {{-- 2. Nama perusahaan (disabled pill) --}}
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 4px;">Nama perusahaan</div>
            <div>
                <span style="display: inline-flex; align-items: center; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; padding: 4px 12px; font-size: 0.78rem; color: #0F172A; font-weight: 600;">
                    {{ $company }}
                </span>
            </div>
        </div>

        {{-- 3. Inisial klien (disabled pill) --}}
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 4px;">Inisial klien</div>
            <div>
                <span style="display: inline-flex; align-items: center; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; padding: 4px 12px; font-size: 0.78rem; color: #0F172A; font-weight: 600;">
                    {{ $initial }}
                </span>
            </div>
        </div>

        {{-- 4. Jabatan --}}
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">Jabatan</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;">{{ $jobTitle }}</div>
        </div>

        {{-- 5. Email --}}
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">Email</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;">{{ $email }}</div>
        </div>

        {{-- 6. No. telepon --}}
        <div>
            <div style="font-size: 0.68rem; font-weight: 500; color: #94A3B8; margin-bottom: 2px;">No. telepon</div>
            <div style="font-size: 0.84rem; font-weight: 700; color: #0F172A; line-height: 1.3;">{{ $phone }}</div>
        </div>

    </div>
</div>
