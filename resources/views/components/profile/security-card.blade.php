@props([
    'actionRoute' => null,
])

@php
    $action = $actionRoute ?? route('profile.password');
@endphp

{{-- ══════════════════════════════════════════════════════════════
     SecurityCard Component (Pure Native HTML/CSS)
     Background: #F0F3F6, Border: 1.5px solid #BAE6FD
     Inputs: Pure CSS, Button: #155DFC
══════════════════════════════════════════════════════════════ --}}
<div class="security-card" style="background: #F0F3F6; border: 1.5px solid #BAE6FD; border-radius: 14px; padding: 20px 24px; box-sizing: border-box; height: 100%;">
    {{-- Card Title in Cyan Uppercase --}}
    <div style="font-size: 0.72rem; font-weight: 800; color: #0284C7; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 16px;">
        KEAMANAN
    </div>

    <form method="POST" action="{{ $action }}" id="profilePwForm">
        @csrf

        {{-- Password lama --}}
        <div style="margin-bottom: 10px;">
            <input type="password" name="old_password" id="profOldPw"
                   placeholder="Password lama" required autocomplete="current-password"
                   style="width: 100%; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 38px; padding: 0 14px; font-size: 0.82rem; color: #0F172A; font-family: inherit; outline: none; box-sizing: border-box; transition: border-color 0.15s, box-shadow 0.15s;"
                   onfocus="this.style.borderColor='#0284C7'; this.style.boxShadow='0 0 0 3px rgba(2,132,199,0.12)';"
                   onblur="this.style.borderColor='#BAE6FD'; this.style.boxShadow='none';">
        </div>

        {{-- Password baru --}}
        <div style="margin-bottom: 16px;">
            <input type="password" name="password" id="profNewPw"
                   placeholder="Password baru" required autocomplete="new-password"
                   style="width: 100%; background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 38px; padding: 0 14px; font-size: 0.82rem; color: #0F172A; font-family: inherit; outline: none; box-sizing: border-box; transition: border-color 0.15s, box-shadow 0.15s;"
                   onfocus="this.style.borderColor='#0284C7'; this.style.boxShadow='0 0 0 3px rgba(2,132,199,0.12)';"
                   onblur="this.style.borderColor='#BAE6FD'; this.style.boxShadow='none';">
        </div>

        {{-- Submit button --}}
        <button type="submit" id="profSaveBtn"
                style="background: #155DFC; color: #FFFFFF; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 8px 20px; cursor: pointer; font-family: inherit; letter-spacing: 0.01em; transition: background 0.15s;"
                onmouseover="this.style.background='#0F48D6';" onmouseout="this.style.background='#155DFC';">
            Simpan password
        </button>
    </form>
</div>
