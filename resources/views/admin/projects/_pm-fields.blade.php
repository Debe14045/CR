<div class="col-md-6 col-12">
    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
        PM Penanggung Jawab 1 (Utama) <span style="color: #EF4444;">*</span>
    </label>
    <select name="pm_id" id="pm_id_select" class="form-select @error('pm_id') is-invalid @enderror" required
            style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
        <option value="">-- Pilih Project Manager Utama --</option>
        @foreach($pmUsers as $pm)
            <option value="{{ $pm->id }}" {{ (string) old('pm_id', $pm1Value ?? '') === (string) $pm->id ? 'selected' : '' }}>
                {{ $pm->name }} ({{ $pm->pmRoleLabel() }})
            </option>
        @endforeach
    </select>
    <div class="form-text small" style="font-size: 0.74rem; color: #64748B;">
        PM utama penanggung jawab proyek &amp; CR terkait.
    </div>
    @error('pm_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6 col-12">
    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
        PM Penanggung Jawab 2 <span class="text-muted fw-normal small">(Opsional - Maks. 2 PM)</span>
    </label>
    <select name="pm_id_2" id="pm_id_2_select" class="form-select @error('pm_id_2') is-invalid @enderror"
            style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B;">
        <option value="">-- Tidak Ada PM ke-2 (Opsional) --</option>
        @foreach($pmUsers as $pm)
            <option value="{{ $pm->id }}" {{ (string) old('pm_id_2', $pm2Value ?? '') === (string) $pm->id ? 'selected' : '' }}>
                {{ $pm->name }} ({{ $pm->pmRoleLabel() }})
            </option>
        @endforeach
    </select>
    <div class="form-text small" style="font-size: 0.74rem; color: #64748B;">
        Tambahkan PM kedua bila proyek &amp; CR ditangani oleh 2 PM.
    </div>
    @error('pm_id_2')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pm1Select = document.getElementById('pm_id_select');
    const pm2Select = document.getElementById('pm_id_2_select');
    if (!pm1Select || !pm2Select) return;

    function syncPmDropdownOptions() {
        const pm1Val = pm1Select.value;
        if (pm1Val && pm2Select.value === pm1Val) {
            pm2Select.value = '';
        }
        Array.from(pm2Select.options).forEach(function (opt) {
            if (!opt.value) return;
            opt.disabled = Boolean(pm1Val && opt.value === pm1Val);
        });
    }

    pm1Select.addEventListener('change', syncPmDropdownOptions);
    pm2Select.addEventListener('change', function () {
        if (pm1Select.value && pm2Select.value === pm1Select.value) {
            pm2Select.value = '';
        }
    });
    syncPmDropdownOptions();
});
</script>

