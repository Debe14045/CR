@extends('layouts.app')

@section('title', 'Detail Change Request • ' . ($changeRequest->kode_cr ?: 'CR-2026-09-0042'))

@section('content')
@php
    $crTitle = $changeRequest->judul ?: ('Change Request #' . $changeRequest->id);
    $firstChar = strtoupper(substr($crTitle, 0, 1)) ?: 'C';
    $crCode = $changeRequest->kode_cr ?: ('CR-' . str_pad($changeRequest->id, 4, '0', STR_PAD_LEFT));

    $statusKey = strtolower($changeRequest->status ?? '');
    $statusText = match($statusKey) {
        'validated', 'disetujui' => 'Disetujui',
        'development', 'dikerjakan' => 'Sedang Dikerjakan',
        'golive', 'selesai' => 'Selesai Go-Live',
        'ditolak', 'revision_needed' => 'Revisi Dibutuhkan',
        default => $changeRequest->statusLabel(),
    };

    $tglPengajuan = $changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->format('d M Y') : ($changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '-');
    $tglHeader = $tglPengajuan;
    $targetSelesai = $changeRequest->target_selesai ? $changeRequest->target_selesai->format('d M Y') : '-';

    $companyName = $changeRequest->client?->company ?: ($changeRequest->klien ?: '-');
    $clientInitial = $changeRequest->client?->nickname ?: ($changeRequest->klien ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $changeRequest->klien), 0, 2)) : '-');
    $picName = $changeRequest->user?->name ?: ($changeRequest->owner_cr ?: '-');
    $projectName = $changeRequest->proyek_terkait ?: '-';
    $crOwner = $changeRequest->pic_sales ?: ($changeRequest->main_desk ?: ($changeRequest->nama_pm ?: '-'));
    $priority = $changeRequest->prioritas ? ucfirst($changeRequest->prioritas) : 'Normal';

    $docUrl = $changeRequest->google_drive_url ?: null;
    $solutionPaperUrl = $changeRequest->solution_paper_url ?: null;

    $rawAnalisis = (float)($changeRequest->mindesk_analisis ?? 0);
    $rawDev = (float)($changeRequest->mindesk_development ?? 0);
    $rawTest = (float)($changeRequest->mindesk_testing ?? 0);

    $mandaysAnalisis = (int)$rawAnalisis;
    $mandaysDev = (int)$rawDev;
    $mandaysTest = (int)$rawTest;
    $totalMandays = $mandaysAnalisis + $mandaysDev + $mandaysTest;

    $catatanCR = $changeRequest->catatan_pengajuan ?: ($changeRequest->pesan_client ?: '-');
    $pmVerifier = $changeRequest->nama_pm ?: ($changeRequest->user?->name ?: '-');
@endphp

<style>
    .cr-detail-container {
        border: 1.5px solid #DCE6F1 !important;
        border-radius: 20px !important;
        background: #F0F3F6 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .cr-subcard {
        border: 1px solid #E2E8F0 !important;
        border-radius: 12px !important;
        background: #FFFFFF !important;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .flow-bar-item {
        flex: 1;
        height: 5px;
        background: #E2E8F0;
    }
    .flow-bar-item.active {
        background: #0098EA;
    }
    .flow-bar-item.completed {
        background: #10B981;
    }
    .flow-step-box {
        border-radius: 8px;
        padding: 0.6rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.15s ease;
    }
    .doc-pill-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #F0F9FF;
        border-radius: 8px;
        padding: 7px 14px;
        color: #0284C7;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s ease;
        max-width: 100%;
        border: 1px solid #E0F2FE;
    }
    .doc-pill-box:hover {
        background: #E0F2FE;
        color: #0369A1;
    }
</style>

<div class="page-shell pb-5">

    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            Detail Change Request
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Melihat Data Lengkap CR</p>
    </div>

    {{-- Cyan accent line matching Figma exact --}}
    <div style="height: 3px; background: #BAE6FD; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    <div class="cr-detail-container p-4 p-md-5 mb-4">
        
        {{-- ── 1. Top Identity Row (Squircle Avatar + Title + Status Pill) ── --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                 style="width: 38px; height: 38px; background: #0098EA; font-size: 1.25rem; font-weight: 800; border-radius: 10px !important; flex-shrink: 0;">
                {{ $firstChar }}
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.25rem; font-weight: 800; letter-spacing: -0.015em;">
                    {{ $crTitle }}
                </h3>
                <span class="badge px-3 py-1 fw-bold text-white d-inline-flex align-items-center gap-1.5"
                      style="background: #0098EA; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.01em;">
                    <span style="width: 4px; height: 4px; border-radius: 50%; background: #FFFFFF;"></span>
                    • {{ ucfirst(str_replace('_', ' ', $changeRequest->status ?: 'Analisa')) }}
                </span>
            </div>
        </div>

        {{-- ── 2. Metadata Subheader (ID, Status Badge, Tanggal Pengajuan) in White Card ── --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-2.5 px-3.5 mb-3"
             style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px;">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="px-2.5 py-1" style="border: 1px solid #E2E8F0; border-radius: 6px; font-size: 0.76rem; font-weight: 700; color: #475569; font-family: monospace; background: #FAFAFA;">
                    ID: &nbsp;<strong style="color: #0F172A;">{{ $crCode }}</strong>
                </span>
                <span class="px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                      style="border: 1px solid #FDE68A; background: #FEF9C3; color: #D97706; border-radius: 9999px; font-size: 0.74rem; letter-spacing: 0.01em;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706; flex-shrink: 0;"></span>
                    <span>{{ $statusText }}</span>
                </span>
            </div>
            <div class="text-muted small d-flex align-items-center gap-1.5" style="font-size: 0.80rem; color: #64748B;">
                <i class="bi bi-calendar4" style="font-size: 0.84rem; color: #94A3B8;"></i>
                <span>Tanggal Pengajuan: <strong style="color: #0F172A; font-weight: 800;">{{ $tglHeader }}</strong></span>
            </div>
        </div>

        @php
            $currentStep = match($statusKey) {
                'diajukan' => 1,
                'awaiting_pm', 'analisa', 'dianalisis' => 2,
                'awaiting_pmh' => 3,
                'validated', 'disetujui' => 4,
                'development', 'dikerjakan', 'sit', 'uat', 'training' => 5,
                'golive', 'selesai', 'invoicing' => 6,
                default => 3,
            };
            $pctProgress = match($currentStep) {
                1 => 17,
                2 => 17,
                3 => 17,
                4 => 35,
                5 => 70,
                6 => 100,
                default => 17,
            };
            $stepLabels = [
                1 => ['title' => 'Diajukan', 'desc' => 'Selesai'],
                2 => ['title' => 'Review PM', 'desc' => 'Selesai'],
                3 => ['title' => 'Approval PM Head', 'desc' => 'Sedang Berjalan'],
                4 => ['title' => 'Quotation', 'desc' => 'Menunggu'],
                5 => ['title' => 'Development', 'desc' => 'Menunggu'],
                6 => ['title' => 'Invoice', 'desc' => 'Menunggu'],
            ];
        @endphp

        {{-- ── 3. STATUS ALUR CR Card (6 Steps) ── --}}
        <div class="cr-subcard p-3 p-md-3.5 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #334155; font-weight: 800;">
                    STATUS ALUR CR
                </span>
                <span class="text-muted small" style="font-size: 0.72rem; font-weight: 600; color: #64748B;">
                    Total Progres Alur: <strong style="font-weight: 800; color: #0F172A;">{{ $pctProgress }}%</strong>
                </span>
            </div>

            {{-- 6-Segment Continuous Progress Bar matching Figma --}}
            <div class="d-flex align-items-center gap-1.5 mb-3" style="border-radius: 999px; overflow: hidden;">
                <div class="flow-bar-item {{ $currentStep > 1 ? 'completed' : ($currentStep == 1 ? 'active' : '') }}" style="border-radius: 4px;"></div>
                <div class="flow-bar-item {{ $currentStep > 2 ? 'completed' : ($currentStep == 2 ? 'active' : '') }}" style="border-radius: 4px;"></div>
                <div class="flow-bar-item {{ $currentStep > 3 ? 'completed' : ($currentStep == 3 ? 'active' : '') }}" style="border-radius: 4px;"></div>
                <div class="flow-bar-item {{ $currentStep > 4 ? 'completed' : ($currentStep == 4 ? 'active' : '') }}" style="border-radius: 4px;"></div>
                <div class="flow-bar-item {{ $currentStep > 5 ? 'completed' : ($currentStep == 5 ? 'active' : '') }}" style="border-radius: 4px;"></div>
                <div class="flow-bar-item {{ $currentStep == 6 ? 'active' : '' }}" style="border-radius: 4px;"></div>
            </div>

            <div class="row g-2">
                @for ($step = 1; $step <= 6; $step++)
                    @php
                        $isCompleted = $step < $currentStep;
                        $isActive = $step === $currentStep;
                        $stepInfo = $stepLabels[$step];
                    @endphp
                    <div class="col-lg-2 col-md-4 col-6">
                        @if ($isCompleted)
                            <div class="flow-step-box h-100" style="background: #F0FDF4; border: 1.5px solid #86EFAC;">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 0.95rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.74rem; color: #0F172A;">{{ $stepInfo['title'] }}</div>
                                    <div class="text-success small fw-semibold" style="font-size: 0.66rem;">Selesai</div>
                                </div>
                            </div>
                        @elseif ($isActive)
                            <div class="flow-step-box h-100" style="background: #FFFFFF; border: 1.5px solid #0098EA;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                      style="width: 20px; height: 20px; background: #0098EA; font-size: 0.70rem; flex-shrink: 0;">
                                    {{ $step }}
                                </span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.74rem; color: #0098EA;">{{ $stepInfo['title'] }}</div>
                                    <div class="small fw-semibold" style="font-size: 0.66rem; color: #0098EA;">Sedang Berjalan</div>
                                </div>
                            </div>
                        @else
                            <div class="flow-step-box h-100" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-muted fw-bold"
                                      style="width: 20px; height: 20px; background: #E2E8F0; font-size: 0.70rem; flex-shrink: 0; color: #94A3B8;">
                                    {{ $step }}
                                </span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-semibold text-muted" style="font-size: 0.74rem; color: #64748B;">{{ $stepInfo['title'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.66rem;">Menunggu</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        {{-- ── 4. Two-Column Grid: INFORMASI CR & DOKUMEN ── --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                        INFORMASI CR
                    </div>
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">NAMA PERUSAHAAN</div>
                            <div class="fw-bold mt-1 text-truncate" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $companyName }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">INISIAL KLIEN</div>
                            <div class="fw-bold mt-1" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $clientInitial }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">NAMA PIC</div>
                            <div class="fw-bold mt-1 text-truncate" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $picName }}</div>
                        </div>

                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">NAMA PROJECT</div>
                            <div class="fw-bold mt-1 text-truncate" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $projectName }}</div>
                        </div>
                        <div class="col-8">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">CR OWNER</div>
                            <div class="fw-bold mt-1 text-truncate" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $crOwner }}</div>
                        </div>

                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">TANGGAL PENGAJUAN</div>
                            <div class="fw-bold mt-1" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $tglPengajuan }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">REQUEST GO-LIVE</div>
                            <div class="fw-bold mt-1" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $targetSelesai }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">PRIORITAS CR</div>
                            <div class="fw-bold mt-1" style="font-size: 0.88rem; color: #0F172A; font-weight: 800;">{{ $priority }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                        DOKUMEN
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">DOKUMEN CR</div>
                            @if($docUrl)
                                <a href="{{ $docUrl }}" target="_blank" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span class="text-truncate">{{ $docUrl }}</span>
                                </a>
                            @elseif($changeRequest->lampiran_pengajuan)
                                <a href="{{ route('change-requests.initial-file', $changeRequest) }}" target="_blank" class="doc-pill-box">
                                    <i class="bi bi-paperclip"></i>
                                    <span class="text-truncate">{{ basename($changeRequest->lampiran_pengajuan) }}</span>
                                </a>
                            @else
                                <div class="text-muted small" style="font-size: 0.82rem;">Belum ada dokumen CR terlampir.</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">SOLUTION PAPER</div>
                            @if($solutionPaperUrl)
                                <a href="{{ $solutionPaperUrl }}" target="_blank" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-code"></i>
                                    <span class="text-truncate">{{ $solutionPaperUrl }}</span>
                                </a>
                            @elseif($changeRequest->solutionPaper?->solution_paper_file)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($changeRequest->solutionPaper->solution_paper_file) }}" target="_blank" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-code"></i>
                                    <span class="text-truncate">{{ basename($changeRequest->solutionPaper->solution_paper_file) }}</span>
                                </a>
                            @else
                                <div class="text-muted small" style="font-size: 0.82rem;">Belum ada Solution Paper terlampir.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Detail Deskripsi CR Card (WYSIWYG Render) ── --}}
        <div class="cr-subcard p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text" style="color: #0098EA; font-size: 1.1rem;"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem; font-weight: 800;">Detail Deskripsi CR</h5>
                </div>
                <span class="text-muted small" style="font-size: 0.72rem; color: #94A3B8;">Data Aktual CR</span>
            </div>

            <div class="text-dark" style="font-size: 0.83rem; line-height: 1.65;">
                <div class="fw-bold mb-1" style="color: #0F172A; font-weight: 800;">1. LATAR BELAKANG &amp; TUJUAN</div>
                @if($changeRequest->alasan || $changeRequest->deskripsi)
                    <p class="text-secondary mb-3" style="color: #475569 !important;">
                        {!! nl2br(e($changeRequest->alasan ?: $changeRequest->deskripsi)) !!}
                    </p>
                @else
                    <p class="text-muted mb-3 small" style="font-size: 0.82rem;">Belum ada latar belakang &amp; tujuan yang diisi.</p>
                @endif

                <div class="fw-bold mb-1" style="color: #0F172A; font-weight: 800;">2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)</div>
                @if($changeRequest->kebutuhan_spesifik || ($changeRequest->deskripsi && $changeRequest->deskripsi !== $changeRequest->alasan))
                    <div class="text-secondary mb-3" style="color: #475569 !important;">
                        {!! nl2br(e($changeRequest->kebutuhan_spesifik ?: $changeRequest->deskripsi)) !!}
                    </div>
                @else
                    <p class="text-muted mb-3 small" style="font-size: 0.82rem;">Belum ada rincian ruang lingkup perubahan.</p>
                @endif

                <div class="fw-bold mb-2" style="color: #0F172A; font-weight: 800;">3. DAMPAK TEKNIS &amp; DEPENDENCIES</div>
                @if($changeRequest->dampak || $changeRequest->solution_paper_note || $changeRequest->pesan_client)
                    <div class="p-3" style="background: #FFFBEB; border-left: 3.5px solid #F59E0B; border-radius: 6px;">
                        <div style="font-size: 0.8rem; color: #92400E; font-weight: 600; line-height: 1.5;">
                            <strong style="color: #78350F; font-weight: 800;">Catatan Dependensi:</strong> {{ $changeRequest->dampak ?: ($changeRequest->solution_paper_note ?: $changeRequest->pesan_client) }}
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0 small" style="font-size: 0.82rem;">Tidak ada dampak teknis khusus atau dependensi.</p>
                @endif
            </div>
        </div>

        {{-- ── 6. Two-Column Grid: ESTIMASI MANDAYS & CR Notes / Catatan ── --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.74rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                            ESTIMASI MANDAYS
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div>
                                <div class="d-flex justify-content-between small mb-1.5" style="font-size: 0.8rem;">
                                    <span style="color: #475569; font-weight: 500;">Analisis</span>
                                    <span style="color: #0F172A; font-weight: 800;">{{ $mandaysAnalisis }} hari</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 999px; background: #E0F2FE;">
                                    <div class="progress-bar" style="width: {{ ($mandaysAnalisis / ($totalMandays ?: 1)) * 100 }}%; background: #0098EA; border-radius: 999px;"></div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between small mb-1.5" style="font-size: 0.8rem;">
                                    <span style="color: #475569; font-weight: 500;">Development</span>
                                    <span style="color: #0F172A; font-weight: 800;">{{ $mandaysDev }} hari</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 999px; background: #E0F2FE;">
                                    <div class="progress-bar" style="width: {{ ($mandaysDev / ($totalMandays ?: 1)) * 100 }}%; background: #0098EA; border-radius: 999px;"></div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between small mb-1.5" style="font-size: 0.8rem;">
                                    <span style="color: #475569; font-weight: 500;">Testing</span>
                                    <span style="color: #0F172A; font-weight: 800;">{{ $mandaysTest }} hari</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 999px; background: #E0F2FE;">
                                    <div class="progress-bar" style="width: {{ ($mandaysTest / ($totalMandays ?: 1)) * 100 }}%; background: #0098EA; border-radius: 999px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3" style="border-top: 1px solid #F1F5F9;">
                        <span style="font-size: 0.85rem; color: #64748B; font-weight: 500;">Total</span>
                        <span style="font-size: 0.92rem; color: #0F172A; font-weight: 800;">{{ $totalMandays }} hari kerja</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-chat-left-text" style="color: #0F172A; font-size: 0.9rem;"></i>
                        <span class="fw-bold" style="font-size: 0.84rem; color: #0F172A; font-weight: 800;">
                            CR Notes / Catatan
                        </span>
                    </div>

                    <div class="p-4 flex-grow-1 d-flex align-items-center justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px;">
                        <p class="mb-0 text-center" style="font-size: 0.82rem; line-height: 1.65; color: #475569;">
                            &ldquo;{{ $catatanCR }}&rdquo;
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 7. Keputusan Approval Card ── --}}
        <div class="cr-subcard p-4">
            <h5 class="fw-bold mb-1" style="font-size: 0.95rem; font-weight: 800; color: #0F172A;">Keputusan Approval</h5>
            <p class="text-muted small mb-3" style="font-size: 0.82rem; color: #64748B;">
                PM <strong style="color: #0F172A; font-weight: 700;">{{ $pmVerifier }}</strong> sudah memverifikasi Change Request. Setujui untuk diteruskan ke Marketing.
            </p>

            <form method="POST" action="{{ route('pmh.decision', $changeRequest) }}">
                @csrf
                <div class="mb-3">
                    <label class="fw-bold small mb-1" style="font-size: 0.76rem; font-weight: 700; color: #0F172A;">CR Notes</label>
                    <textarea name="catatan_approval" rows="4" class="form-control"
                              placeholder="Catatan tambahan, prioritas, atau informasi lain yang perlu diketahui PM..."
                              style="border-radius: 8px; border: 1px solid #CBD5E1; font-size: 0.85rem; padding: 0.75rem 1rem; color: #1E293B; background: #FFFFFF;"></textarea>
                    @error('catatan_approval')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end align-items-center pt-2" style="gap: 12px;">
                    <button type="submit" name="action" value="reject" class="btn text-white fw-bold d-inline-flex align-items-center justify-content-center"
                            style="background: #DC2626; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.52rem 1.6rem; gap: 8px;"
                            onclick="return confirm('Apakah Anda yakin ingin menolak / mengembalikan CR ini untuk revisi PM? Pastikan telah mengisi alasan di CR Notes.');">
                        <i class="bi bi-x-lg"></i> Tolak
                    </button>
                    <button type="submit" name="action" value="approve" class="btn text-white fw-bold d-inline-flex align-items-center justify-content-center"
                            style="background: #22C55E; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.52rem 1.6rem; gap: 8px;"
                            onclick="return confirm('Setujui dan validasi Change Request ini untuk diteruskan ke Marketing?');">
                        <i class="bi bi-check-lg"></i> Setuju
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
