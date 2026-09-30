@extends('layouts.app')

@section('title', 'Verifikasi CR · ' . $changeRequest->kode_cr)

@section('content')
<div class="page-shell">

    {{-- ══ Header: Kembali & Judul Halaman (Figma MacBook 7) ══ --}}
    <div class="mb-3">
        <a href="{{ route('change-requests.show', $changeRequest) }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-chevron-left" style="font-size: 0.8rem;"></i> Kembali
        </a>
        <h4 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.35rem; letter-spacing: -0.02em;">Verifikasi CR</h4>
    </div>

    {{-- ══ CR Summary Card (Figma MacBook 7) ══ --}}
    <div class="card p-3 px-4 mb-4 shadow-sm border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                     style="width: 44px; height: 44px; background: #F97316; font-size: 1.15rem; font-family: var(--font-sans);">
                    {{ strtoupper(substr($changeRequest->judul ?: ($changeRequest->klien ?: 'C'), 0, 1)) }}
                </div>
                <div>
                    <div class="fw-bold text-dark mb-0" style="font-size: 1.05rem; letter-spacing: -0.01em;">
                        {{ $changeRequest->judul }}
                    </div>
                    <div class="text-muted small" style="font-size: 0.82rem;">
                        <span>{{ $changeRequest->kode_cr }}</span> &bull;
                        <span>{{ $changeRequest->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                        @if($changeRequest->proyek_terkait)
                            &bull; <span>{{ $changeRequest->proyek_terkait }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill fw-medium"
                      style="background: #FFF7ED; color: #EA580C; border: 1px solid #FFEDD5; font-size: 0.78rem; padding: 0.4rem 0.9rem;">
                    &bull; {{ $changeRequest->statusLabel() }}
                </span>
            </div>
        </div>
    </div>

    {{-- PM Head Validation Decision (Khusus PM Head & Admin) --}}
    @if (auth()->user()->isPmh() || auth()->user()->isAdmin())
        <div class="card p-4 mb-4 shadow-sm border-0" style="border: 1px solid #BFDBFE !important; background: #F0F7FF; border-radius: 14px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-primary mb-0"><i class="bi bi-shield-check me-1"></i> Otoritas Validasi PM Head</h6>
                <span class="badge bg-primary text-white">PM Head Board</span>
            </div>
            <p class="small text-muted mb-3">Tinjau kelayakan CR dan estimasi teknis. Jika menolak, alasan penolakan wajib diisi untuk dikembalikan ke PM.</p>

            <form method="POST" action="{{ route('change-requests.update', $changeRequest) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label small fw-bold">Alasan Penolakan / Catatan Approval PM Head:</label>
                    <textarea name="reject_reason" id="pmh_reject_reason" rows="2" class="form-control" placeholder="Wajib jika Reject, opsional jika Approve...">{{ old('reject_reason', $changeRequest->reject_reason) }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="pmh_action" value="approve" class="btn btn-success btn-sm px-3" onclick="return confirm('Approve & validasi CR ini?')">
                        <i class="bi bi-check-lg me-1"></i> Approve &amp; Validasi
                    </button>
                    <button type="submit" name="pmh_action" value="reject" class="btn btn-danger btn-sm px-3" onclick="if(!document.getElementById('pmh_reject_reason').value.trim()){ alert('Alasan penolakan wajib diisi jika menolak!'); return false; }">
                        <i class="bi bi-x-lg me-1"></i> Reject (Revisi ke PM)
                    </button>
                    @if ($changeRequest->status === 'awaiting_golive_validation')
                        <button type="submit" name="pmh_action" value="validate_golive" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-rocket-takeoff me-1"></i> Validasi Go-Live
                        </button>
                    @endif
                </div>
            </form>
        </div>
    @endif

    {{-- Error messages --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm" style="border-radius: 12px;">
            <div class="fw-bold small mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Periksa isian formulir:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ══ Form Verifikasi CR (Figma MacBook 7) ══ --}}
    <form method="POST" action="{{ route('change-requests.update', $changeRequest) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- CARD 1: INPUT PM --}}
        <div class="card p-4 shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
            <div class="text-uppercase fw-bold text-muted mb-3" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #64748B !important;">
                INPUT PM
            </div>

            <div class="row g-3">
                {{-- Target Date & Actual Date --}}
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-dark">Target Date <span class="text-danger">*</span></label>
                    <input type="date" name="target_selesai" class="form-control"
                           value="{{ old('target_selesai', $changeRequest->target_selesai?->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-dark">Actual Date</label>
                    <input type="date" name="actual_completion_date" class="form-control"
                           value="{{ old('actual_completion_date', $changeRequest->actual_completion_date?->format('Y-m-d')) }}">
                </div>

                {{-- Dokumen PM (PDF) Dropzone --}}
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark">Dokumen PM (PDF)</label>
                    <div class="p-3 text-center rounded-3 position-relative" style="border: 2px dashed #CBD5E1; background: #F8FAFC;">
                        <i class="bi bi-cloud-arrow-up text-muted fs-3 d-block mb-1"></i>
                        <span class="small text-muted d-block">Klik untuk upload PDF atau seret berkas ke sini</span>
                        <input type="file" name="dokumen_pm" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,.doc,.docx" style="cursor: pointer;">
                    </div>
                    @if ($changeRequest->lampiran_revisi)
                        <div class="mt-1 small text-success"><i class="bi bi-file-earmark-check me-1"></i> File terarsip: {{ $changeRequest->lampiran_revisi }}</div>
                    @endif
                </div>

                {{-- Note PM --}}
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark">Note PM</label>
                    <textarea name="catatan_analisis" rows="3" class="form-control"
                              placeholder="Catatan teknis, asumsi, atau hal yang perlu diperhatikan...">{{ old('catatan_analisis', $changeRequest->catatan_analisis) }}</textarea>
                </div>
            </div>
        </div>

        {{-- CARD 2: SOLUTION PAPER TOGGLE (KHUSUS PM) --}}
        @php
            $hasExistingSpFile = (bool) ($changeRequest->solutionPaper?->solution_paper_file || $changeRequest->solutionPaper?->file_path);
            $spRequiredVal = old('solution_paper_required', $changeRequest->solution_paper_required ? '1' : ($hasExistingSpFile ? '1' : '0'));
        @endphp
        <div class="card p-4 shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #64748B !important;">
                    KEBIJAKAN SOLUTION PAPER
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">
                    Otoritas PM
                </span>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark mb-2">Apakah CR ini membutuhkan Solution Paper?</label>
                <div class="d-flex gap-2">
                    <label class="btn btn-outline-primary btn-sm px-3 d-flex align-items-center gap-2 {{ $spRequiredVal == '1' ? 'active' : '' }}" id="labelSpYa" style="border-radius: 8px;">
                        <input type="radio" name="solution_paper_required" value="1" id="radioSpYa"
                               @checked($spRequiredVal == '1') onchange="handleSpToggle(true)">
                        <span><i class="bi bi-file-earmark-check me-1"></i> Ya (Perlu Solution Paper)</span>
                    </label>
                    <label class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-2 {{ $spRequiredVal == '0' ? 'active' : '' }}" id="labelSpTidak" style="border-radius: 8px;">
                        <input type="radio" name="solution_paper_required" value="0" id="radioSpTidak"
                               @checked($spRequiredVal == '0') onchange="handleSpToggle(false)">
                        <span><i class="bi bi-dash-circle me-1"></i> Tidak (CR Kecil / Langsung Mandays)</span>
                    </label>
                </div>
                <div class="form-text mt-2" id="spHelpText" style="font-size: 0.76rem;">
                    @if($spRequiredVal == '1')
                        <span class="text-danger fw-semibold"><i class="bi bi-lock-fill me-1"></i> Jika YA: Kolom Man-Days dikunci. Unggah file Solution Paper terlebih dahulu untuk membuka kolom Man-Days.</span>
                    @else
                        <span class="text-success fw-semibold"><i class="bi bi-unlock-fill me-1"></i> Jika TIDAK: Kolom Man-Days langsung terbuka untuk diinput manual.</span>
                    @endif
                </div>
            </div>

            {{-- Upload Solution Paper Section (Muncul jika YA) --}}
            <div id="spUploadSection" style="{{ $spRequiredVal == '1' ? 'display: block;' : 'display: none;' }}">
                <div class="row g-3 pt-2 border-top">
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark mb-1">
                            Upload Solution Paper (PDF/DOC) <span class="text-danger">*</span>
                        </label>
                        <div class="p-3 text-center rounded-3 position-relative" style="border: 2px dashed #CBD5E1; background: #F8FAFC;">
                            <i class="bi bi-cloud-arrow-up text-primary fs-3 d-block mb-1"></i>
                            <span class="small text-muted d-block" id="spFileLabel">Klik untuk upload berkas Solution Paper</span>
                            <input type="file" name="solution_paper_file" id="solution_paper_file"
                                   class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                   accept=".pdf,.doc,.docx,.zip" style="cursor: pointer;" onchange="onSpFileChosen(this)">
                        </div>
                        @if ($hasExistingSpFile)
                            <div class="mt-2 small text-success fw-semibold" id="existingSpNotice">
                                <i class="bi bi-file-earmark-check me-1"></i> File Solution Paper sudah terunggah di sistem.
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">Target Tanggal Mulai SP</label>
                        <input type="date" name="sp_target_start" class="form-control"
                               value="{{ old('sp_target_start', $changeRequest->solutionPaper?->target_date_start?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">Target Tanggal Selesai SP</label>
                        <input type="date" name="sp_target_end" class="form-control"
                               value="{{ old('sp_target_end', $changeRequest->solutionPaper?->target_date_end?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 3: ESTIMASI MANDAYS (PM) --}}
        <div class="card p-4 shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #64748B !important;">
                    ESTIMASI MANDAYS (PM)
                </div>
                <span id="mandaysLockBadge" class="badge bg-light text-muted border" style="font-size: 0.72rem;">
                    Status Mandays
                </span>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-dark">Analisis (hari)</label>
                    <input type="number" step="0.5" min="0" name="mindesk_analisis" id="mindesk_analisis" class="form-control mandays-field"
                           value="{{ old('mindesk_analisis', $changeRequest->mindesk_analisis ?? 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-dark">Development (hari)</label>
                    <input type="number" step="0.5" min="0" name="mindesk_development" id="mindesk_development" class="form-control mandays-field"
                           value="{{ old('mindesk_development', $changeRequest->mindesk_development ?? 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-dark">Testing (hari)</label>
                    <input type="number" step="0.5" min="0" name="mindesk_testing" id="mindesk_testing" class="form-control mandays-field"
                           value="{{ old('mindesk_testing', $changeRequest->mindesk_testing ?? 0) }}">
                </div>
            </div>
        </div>

        {{-- ══ Bottom Action Buttons (Figma MacBook 7) ══ --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('change-requests.show', $changeRequest) }}" class="btn text-white fw-semibold px-4 py-2"
               style="background: #EF4444; border-radius: 8px; font-size: 0.88rem; border: none;">
                ✕ Batal
            </a>
            <button type="submit" class="btn text-white fw-semibold px-4 py-2"
                    style="background: #10B981; border-radius: 8px; font-size: 0.88rem; border: none; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">
                ✓ Simpan &amp; Perbarui CR
            </button>
        </div>

    </form>

</div>

<script>
    const hasExistingSp = {{ $hasExistingSpFile ? 'true' : 'false' }};

    function handleSpToggle(isYes) {
        const uploadSec = document.getElementById('spUploadSection');
        const helpText = document.getElementById('spHelpText');
        const labelYa = document.getElementById('labelSpYa');
        const labelTidak = document.getElementById('labelSpTidak');

        if (isYes) {
            uploadSec.style.display = 'block';
            labelYa.classList.add('active');
            labelTidak.classList.remove('active');
            helpText.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-lock-fill me-1"></i> Jika YA: Kolom Man-Days dikunci. Unggah file Solution Paper terlebih dahulu untuk membuka kolom Man-Days.</span>';
        } else {
            uploadSec.style.display = 'none';
            labelYa.classList.remove('active');
            labelTidak.classList.add('active');
            helpText.innerHTML = '<span class="text-success fw-semibold"><i class="bi bi-unlock-fill me-1"></i> Jika TIDAK: Kolom Man-Days langsung terbuka untuk diinput manual.</span>';
        }

        evaluateMandaysLock();
    }

    function onSpFileChosen(input) {
        const fileLabel = document.getElementById('spFileLabel');
        if (input.files && input.files[0]) {
            fileLabel.innerHTML = '<strong class="text-success"><i class="bi bi-file-earmark-check me-1"></i> ' + input.files[0].name + '</strong>';
        }
        evaluateMandaysLock();
    }

    function evaluateMandaysLock() {
        const radioYa = document.getElementById('radioSpYa');
        const isSpYes = radioYa && radioYa.checked;
        const spFileInput = document.getElementById('solution_paper_file');
        const hasChosenFile = spFileInput && spFileInput.files && spFileInput.files.length > 0;
        const isSpUploaded = hasExistingSp || hasChosenFile;

        const mandaysFields = document.querySelectorAll('.mandays-field');
        const badge = document.getElementById('mandaysLockBadge');

        if (isSpYes && !isSpUploaded) {
            // Lock fields
            mandaysFields.forEach(f => {
                f.readOnly = true;
                f.style.backgroundColor = '#E2E8F0';
                f.style.cursor = 'not-allowed';
            });
            if (badge) {
                badge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
                badge.innerHTML = '<i class="bi bi-lock-fill me-1"></i> Terkunci (Wajib Upload Solution Paper)';
            }
        } else {
            // Unlock fields
            mandaysFields.forEach(f => {
                f.readOnly = false;
                f.style.backgroundColor = '#FFFFFF';
                f.style.cursor = 'text';
            });
            if (badge) {
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                badge.innerHTML = '<i class="bi bi-unlock-fill me-1"></i> Terbuka (Siap Input)';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        evaluateMandaysLock();
    });
</script>
@endsection
