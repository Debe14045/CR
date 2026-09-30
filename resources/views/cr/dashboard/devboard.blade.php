@extends('layouts.app')

@section('title', 'Dev Board · Monitoring Status CR')

@section('content')
<div class="page-shell">

    {{-- ══ Header: Dev Board ══ --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.4rem; letter-spacing: -0.02em;">Dev Board</h4>
            <p class="text-muted small mb-0" style="font-size: 0.85rem;">Kelola dan perbarui progres CR yang sedang dikerjakan secara langsung</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('change-requests.index') }}" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 8px;">
                <i class="bi bi-table me-1"></i> Tabel Utama CR
            </a>
            <a href="{{ route('change-requests.create') }}" class="btn btn-sm text-white px-3" style="background: #10B981; border-radius: 8px;">
                <i class="bi bi-plus-lg me-1"></i> Ajukan CR Baru
            </a>
        </div>
    </div>

    {{-- ══ 6 Stage Summary Cards (Ukuran Seragam & Rapi) ══ --}}
    @php
        $stages = [
            'analisa'     => ['label' => 'Analisis',    'color' => '#0284C7', 'numColor' => '#0284C7'],
            'development' => ['label' => 'Develop',     'color' => '#8B5CF6', 'numColor' => '#8B5CF6'],
            'sit'         => ['label' => 'SIT',         'color' => '#F59E0B', 'numColor' => '#F59E0B'],
            'uat'         => ['label' => 'UAT',         'color' => '#EC4899', 'numColor' => '#EC4899'],
            'training'    => ['label' => 'Training',    'color' => '#10B981', 'numColor' => '#10B981'],
            'golive'      => ['label' => 'Go-Live',     'color' => '#059669', 'numColor' => '#059669'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach($stages as $key => $st)
        <div class="col-lg-2 col-md-4 col-6">
            <a href="{{ route('change-requests.devboard', ['status' => $key]) }}" class="text-decoration-none">
                <div class="card text-center p-3 shadow-sm border-0 h-100"
                     style="border: 1px solid {{ request('status') === $key ? $st['color'] : '#E2E8F0' }} !important; border-radius: 14px; background: #FFFFFF; transition: transform 0.15s, box-shadow 0.15s;">
                    <div class="fw-bold" style="font-size: 1.8rem; font-family: var(--font-sans); color: {{ $st['numColor'] }}; line-height: 1.1;">
                        {{ $stageStats[$key] ?? 0 }}
                    </div>
                    <div class="text-muted fw-semibold mt-1" style="font-size: 0.8rem;">
                        {{ $st['label'] }}
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ══ CR Pipeline Cards ══ --}}
    @if($changeRequests->isEmpty())
        <div class="card p-5 text-center shadow-sm border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
            <i class="bi bi-kanban fs-1 text-muted d-block mb-3"></i>
            <h6 class="fw-bold text-dark mb-1">Tidak ada CR dalam pipeline aktif</h6>
            <p class="text-muted small">Semua CR sudah selesai atau belum ada yang masuk tahapan pengerjaan.</p>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($changeRequests as $cr)
            @php
                $stageOrder = ['analisa', 'development', 'sit', 'uat', 'training', 'golive'];
                $currIdx = array_search($cr->status, $stageOrder);
                if ($currIdx === false) $currIdx = 0;

                $totalMD = $cr->totalMindesk();
                $avatarColors = ['#0284C7', '#0EA5E9', '#F59E0B', '#8B5CF6', '#10B981'];
                $avatarColor = $avatarColors[$loop->index % count($avatarColors)];
            @endphp
            <div class="card p-4 shadow-sm border-0"
                 style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF;">

                {{-- Header Row: Avatar, Title, Badge, Subtitle & Action --}}
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                             style="width: 44px; height: 44px; background: {{ $avatarColor }}; font-size: 1.15rem;">
                            {{ strtoupper(substr($cr->judul ?: 'C', 0, 1)) }}
                        </div>

                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="fw-bold text-dark" style="font-size: 1rem;">{{ $cr->judul }}</span>
                                <span class="badge" style="background: #EFF6FF; color: #0284C7; font-size: 0.75rem; padding: 3px 8px; border-radius: 6px; font-weight: 600;">
                                    {{ $cr->kode_cr }}
                                </span>
                            </div>
                            <div class="text-muted small" style="font-size: 0.8rem;">
                                <span>{{ $cr->klien }}</span> &bull;
                                <span>{{ $totalMD }} MD</span> &bull;
                                <span>Target: {{ $cr->target_selesai?->format('d M Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge rounded-pill fw-semibold text-uppercase"
                              style="background: #EFF6FF; color: #0284C7; border: 1px solid #BFDBFE; font-size: 0.75rem; padding: 0.35rem 0.85rem;">
                            {{ $cr->statusLabel() }}
                        </span>

                        {{-- Update Status Button (Modal 1-Step) --}}
                        <button type="button" class="btn btn-sm text-white fw-semibold px-3"
                                style="background: #0284C7; border-radius: 8px; font-size: 0.8rem;"
                                data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $cr->id }}">
                            <i class="bi bi-arrow-repeat me-1"></i> Update Status
                        </button>

                        <a href="{{ route('change-requests.show', $cr) }}" class="btn btn-sm btn-outline-secondary px-3"
                           style="border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                            Detail <i class="bi bi-chevron-right ms-1" style="font-size: 0.65rem;"></i>
                        </a>
                    </div>
                </div>

                {{-- Stepper Progress Line dengan Lebar Seragam Konsisten --}}
                <div class="position-relative py-3 px-2">
                    {{-- Connecting Track Background --}}
                    <div class="position-absolute top-50 start-0 w-100" style="height: 2px; background: #E2E8F0; transform: translateY(-50%); z-index: 1;"></div>

                    {{-- Filled Active Track --}}
                    @php
                        $percent = ($currIdx / (count($stageOrder) - 1)) * 100;
                    @endphp
                    <div class="position-absolute top-50 start-0" style="height: 3px; background: #0284C7; width: {{ $percent }}%; transform: translateY(-50%); z-index: 2; transition: width 0.3s ease;"></div>

                    {{-- Stepper Nodes --}}
                    <div class="d-flex justify-content-between position-relative w-100" style="z-index: 3;">
                        @foreach ($stageOrder as $idx => $stepKey)
                            @php
                                $isDone = $idx < $currIdx;
                                $isActive = $idx === $currIdx;
                                $stepLabel = match($stepKey) {
                                    'analisa' => 'Analisis',
                                    'development' => 'Develop',
                                    'sit' => 'SIT',
                                    'uat' => 'UAT',
                                    'training' => 'Training',
                                    'golive' => 'Go-Live',
                                    default => ucfirst($stepKey),
                                };
                            @endphp
                            <div class="d-flex flex-column align-items-center" style="width: 60px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 26px; height: 26px; font-size: 0.72rem;
                                            background: {{ $isActive ? '#0284C7' : ($isDone ? '#0284C7' : '#FFFFFF') }};
                                            color: {{ $isActive || $isDone ? '#FFFFFF' : '#94A3B8' }};
                                            border: 2px solid {{ $isActive || $isDone ? '#0284C7' : '#CBD5E1' }};">
                                    @if ($isDone)
                                        <i class="bi bi-check" style="font-size: 0.95rem;"></i>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>
                                <span class="small mt-1 text-center"
                                      style="font-size: 0.74rem; font-weight: {{ $isActive ? '700' : '500' }}; color: {{ $isActive ? '#0284C7' : '#64748B' }}; white-space: nowrap;">
                                    {{ $stepLabel }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ══ MODAL UPDATE STATUS (Field Catatan Mandatory & Upload BA SIT/UAT/Go-Live) ══ --}}
            <div class="modal fade" id="updateStatusModal{{ $cr->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="modal-header border-0 pb-0 px-4 pt-4">
                            <h5 class="modal-title fw-bold text-dark" style="font-size: 1.15rem;">
                                <i class="bi bi-sliders text-primary me-1"></i> Update Status CR · {{ $cr->kode_cr }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('change-requests.sequential-status', $cr) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body px-4 py-3">
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="small text-muted mb-1">Judul Change Request:</div>
                                    <div class="fw-bold text-dark">{{ $cr->judul }}</div>
                                    <div class="small text-muted mt-1">Klien: <strong>{{ $cr->klien }}</strong> &bull; Status Saat Ini: <span class="badge bg-primary-subtle text-primary">{{ $cr->statusLabel() }}</span></div>
                                </div>

                                <div class="row g-3">
                                    {{-- Pilihan Status Selanjutnya --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            Pilih Status Baru <span class="text-danger">*</span>
                                        </label>
                                        <select name="status" id="statusSelect{{ $cr->id }}" class="form-select" required onchange="toggleBaFields(this.value, '{{ $cr->id }}')">
                                            @foreach($stageOrder as $sKey)
                                                @php
                                                    $sLabel = match($sKey) {
                                                        'analisa' => '1. Analisis',
                                                        'development' => '2. Development',
                                                        'sit' => '3. SIT (System Integration Testing)',
                                                        'uat' => '4. UAT (User Acceptance Testing)',
                                                        'training' => '5. Training',
                                                        'golive' => '6. Go-Live',
                                                        default => ucfirst($sKey)
                                                    };
                                                @endphp
                                                <option value="{{ $sKey }}" @selected($cr->status === $sKey)>
                                                    {{ $sLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Catatan / Notes Pembaruan (MANDATORY) --}}
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            Catatan / Alasan Pembaruan Status <span class="text-danger">* (Wajib Diisi)</span>
                                        </label>
                                        <textarea name="catatan_status" class="form-control" rows="3"
                                                  placeholder="Jelaskan progres yang telah tercapai, kendala, atau justifikasi perpindahan status ini..." required></textarea>
                                        <div class="form-text small" style="font-size: 0.72rem;">Catatan ini akan otomatis diarsipkan dalam riwayat audit log CR.</div>
                                    </div>

                                    {{-- Area Berita Acara (BA): Ditampilkan dinamis (SIT opsional, UAT & Go-Live mandatory) --}}
                                    <div class="col-12" id="baSection{{ $cr->id }}" style="{{ in_array($cr->status, ['sit', 'uat', 'golive']) ? 'display: block;' : 'display: none;' }}">
                                        <div class="p-3 rounded-3 border bg-light">
                                            <div class="fw-bold small text-dark mb-2 d-flex align-items-center gap-2">
                                                <i class="bi bi-file-earmark-text text-primary"></i>
                                                <span id="baSectionTitle{{ $cr->id }}">Kelengkapan Berita Acara (BA)</span>
                                                <span class="badge bg-danger-subtle text-danger" id="baMandatoryBadge{{ $cr->id }}" style="display: none;">Wajib untuk UAT / Go-Live</span>
                                                <span class="badge bg-secondary-subtle text-secondary" id="baOptionalBadge{{ $cr->id }}">Opsional untuk SIT</span>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Upload Berita Acara (PDF/DOC)</label>
                                                    <input type="file" name="berita_acara_file" id="baFile{{ $cr->id }}" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.zip">
                                                    @if($cr->berita_acara_file)
                                                        <div class="small text-success mt-1"><i class="bi bi-check-circle me-1"></i> File BA telah terunggah</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Tanggal BA</label>
                                                    <input type="date" name="berita_acara_date" id="baDate{{ $cr->id }}" class="form-control form-control-sm"
                                                           value="{{ old('berita_acara_date', $cr->berita_acara_date?->format('Y-m-d')) }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold text-dark mb-1">Nomor BA</label>
                                                    <input type="text" name="berita_acara_no" class="form-control form-control-sm" placeholder="Contoh: BA/ITPI/2026/001"
                                                           value="{{ old('berita_acara_no', $cr->berita_acara_no) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm text-white px-4 fw-semibold" style="background: #10B981;">
                                    ✓ Simpan Pembaruan Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @endforeach
        </div>

        {{-- Pagination --}}
        @if($changeRequests->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $changeRequests->links() }}
            </div>
        @endif
    @endif

</div>

<script>
    function toggleBaFields(status, crId) {
        const baSec = document.getElementById('baSection' + crId);
        const baFile = document.getElementById('baFile' + crId);
        const baDate = document.getElementById('baDate' + crId);
        const manBadge = document.getElementById('baMandatoryBadge' + crId);
        const optBadge = document.getElementById('baOptionalBadge' + crId);

        if (!baSec) return;

        if (status === 'sit') {
            baSec.style.display = 'block';
            if (manBadge) manBadge.style.display = 'none';
            if (optBadge) optBadge.style.display = 'inline-block';
            if (baFile) baFile.required = false;
            if (baDate) baDate.required = false;
        } else if (status === 'uat' || status === 'golive') {
            baSec.style.display = 'block';
            if (manBadge) manBadge.style.display = 'inline-block';
            if (optBadge) optBadge.style.display = 'none';
            if (baFile) baFile.required = false; // let backend validate existing or newly uploaded
            if (baDate) baDate.required = true;
        } else {
            baSec.style.display = 'none';
            if (baFile) baFile.required = false;
            if (baDate) baDate.required = false;
        }
    }
</script>
@endsection
