@extends('layouts.app')

@section('title', 'Detail Change Request • ' . ($changeRequest->kode_cr ?: 'CR-2026-09-0042'))

@section('content')
@php
    $crTitle = $changeRequest->judul ?: ('Change Request #' . $changeRequest->id);
    $firstChar = strtoupper(substr($crTitle, 0, 1)) ?: 'C';
    $crCode = $changeRequest->kode_cr ?: ('CR-' . str_pad($changeRequest->id, 4, '0', STR_PAD_LEFT));

    $statusKey = strtolower($changeRequest->status ?? '');

    // Status pill in sub-bar
    $statusPillText = match($statusKey) {
        'draft' => 'Draft',
        'diajukan' => 'Menunggu Review PM',
        'awaiting_pm', 'analisa', 'dianalisis' => 'Menunggu Review PM',
        'awaiting_pmh' => 'Menunggu Review (In Progress)',
        'validated', 'disetujui' => 'Disetujui',
        'quotation' => 'Quotation Disiapkan',
        'development', 'dikerjakan' => 'Sedang Dikerjakan',
        'golive', 'selesai' => 'Selesai Go-Live',
        'ditolak', 'revision_needed' => 'Revisi Dibutuhkan',
        default => 'Menunggu Review (In Progress)',
    };

    // Status badge next to title
    $statusBadgeText = match($statusKey) {
        'validated', 'disetujui' => 'Disetujui',
        'awaiting_pmh' => 'Approval PM Head',
        'diajukan' => 'Diajukan',
        'draft' => 'Draft',
        default => 'Analisa',
    };

    $tglPengajuan = $changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->format('d M Y') : ($changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '04 Sep 2026');
    $targetSelesai = $changeRequest->target_selesai ? $changeRequest->target_selesai->format('d M Y') : ($changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->format('d M Y') : '15 Jan 2024');

    $companyName = $changeRequest->client?->company ?: ($changeRequest->klien ?: 'PT Maju Bersama');
    $clientInitial = $changeRequest->client?->nickname ?: ($changeRequest->klien ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $changeRequest->klien), 0, 2)) : 'AF');
    $picName = $changeRequest->user?->name ?: ($changeRequest->nama_pic ?: 'Totok Antok');
    $projectName = $changeRequest->project?->nama_proyek ?: ($changeRequest->proyek_terkait ?: ($changeRequest->nama_project ?: 'Sistem E-commerce'));
    $crOwner = $changeRequest->pic_sales ?: ($changeRequest->cr_owner ?: ($changeRequest->user?->name ?: 'Andik Virmansyah'));
    $priority = $changeRequest->prioritas ? ucfirst($changeRequest->prioritas) : 'Normal';

    // Document links (hanya dari file riil yang tersimpan)
    $docUrl = null;
    $docName = null;
    if (!empty($changeRequest->dokumen_cr)) {
        if (str_starts_with($changeRequest->dokumen_cr, 'http')) {
            $docUrl = $changeRequest->dokumen_cr;
            $docName = $changeRequest->dokumen_cr;
        } else {
            $docUrl = \Illuminate\Support\Facades\Storage::url($changeRequest->dokumen_cr);
            $docName = basename($changeRequest->dokumen_cr);
        }
    } elseif (!empty($changeRequest->google_drive_url)) {
        $docUrl = $changeRequest->google_drive_url;
        $docName = $changeRequest->google_drive_url;
    } elseif (!empty($changeRequest->lampiran_pengajuan)) {
        $docUrl = \Illuminate\Support\Facades\Storage::url($changeRequest->lampiran_pengajuan);
        $docName = basename($changeRequest->lampiran_pengajuan);
    }

    $spUrl = null;
    $spName = null;
    if (!empty($changeRequest->solution_paper_file)) {
        if (str_starts_with($changeRequest->solution_paper_file, 'http')) {
            $spUrl = $changeRequest->solution_paper_file;
            $spName = $changeRequest->solution_paper_file;
        } else {
            $spUrl = \Illuminate\Support\Facades\Storage::url($changeRequest->solution_paper_file);
            $spName = basename($changeRequest->solution_paper_file);
        }
    } elseif (!empty($changeRequest->solution_paper_url)) {
        $spUrl = $changeRequest->solution_paper_url;
        $spName = $changeRequest->solution_paper_url;
    } elseif (!empty($changeRequest->solutionPaper?->solution_paper_file)) {
        $spUrl = \Illuminate\Support\Facades\Storage::url($changeRequest->solutionPaper->solution_paper_file);
        $spName = basename($changeRequest->solutionPaper->solution_paper_file);
    }

    // Mandays
    $mandaysAnalisis = (int)($changeRequest->mindesk_analisis ?? 0);
    $mandaysDev = (int)($changeRequest->mindesk_development ?? 0);
    $mandaysTest = (int)($changeRequest->mindesk_testing ?? 0);
    $totalMandays = $mandaysAnalisis + $mandaysDev + $mandaysTest;

    // CR Notes
    $catatanCR = $changeRequest->catatan_analisis ?: ($changeRequest->catatan_pengajuan ?: '-');
    
    // PM verifier name
    $pmVerifier = $changeRequest->nama_pm ?: ($changeRequest->project?->pm?->name ?: null);
    if (!$pmVerifier) {
        try {
            $pmVerifier = \App\Models\User::where('role', 'pm')->first()?->name ?: '-';
        } catch (\Throwable $e) {
            $pmVerifier = '-';
        }
    }

    $statusBadgeCfg = $changeRequest->statusBadgeConfig();

    // Current step in Alur CR
    $currentStep = match(true) {
        in_array($statusKey, ['diajukan', 'draft']) => 1,
        in_array($statusKey, ['awaiting_pm', 'dianalisis', 'analisa', 'analysis']) => 2,
        in_array($statusKey, ['awaiting_pmh', 'revision_needed']) => 3,
        in_array($statusKey, ['validated', 'disetujui', 'quotation', 'presales_review']) => 4,
        in_array($statusKey, ['development', 'dikerjakan', 'sit', 'uat', 'training']) => 5,
        in_array($statusKey, ['awaiting_golive_validation', 'golive', 'invoicing', 'selesai']) => 6,
        default => 3,
    };

    $pctProgress = match($currentStep) {
        1 => 17,
        2 => 33,
        3 => 50,
        4 => 67,
        5 => 83,
        6 => 100,
        default => 50,
    };

    $stepLabels = [
        1 => 'Diajukan',
        2 => 'Review PM',
        3 => 'Approval PM Head',
        4 => 'Quotation',
        5 => 'Development',
        6 => 'Invoice',
    ];
@endphp

<style>
    .cr-detail-container {
        border: 1.5px solid #DCE6F1 !important;
        border-radius: 18px !important;
        background: #F4F6F9 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .cr-subcard {
        border: 1px solid #E2E8F0 !important;
        border-radius: 12px !important;
        background: #FFFFFF !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .flow-step-box {
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
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
        border: 1px solid #BAE6FD;
    }
    .doc-pill-box:hover {
        background: #E0F2FE;
        color: #0369A1;
    }
</style>

<div class="page-shell pb-5">

    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            Detail Change Request
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Melihat Data Lengkap CR</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" style="border-radius: 10px;" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" style="border-radius: 10px;" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('warning') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" style="border-radius: 10px;" role="alert">
            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="cr-detail-container p-4 p-md-4 mb-4">
        
        {{-- ── 1. Top Identity Row (Squircle Avatar + Title + Status Pill) ── --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                 style="width: 38px; height: 38px; background: #0098EA; font-size: 1.25rem; font-weight: 800; border-radius: 10px; flex-shrink: 0;">
                {{ $firstChar }}
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.28rem; font-weight: 800; letter-spacing: -0.015em;">
                    {{ $crTitle }}
                </h3>
                <span class="badge px-3 py-1 fw-bold text-white d-inline-flex align-items-center gap-1.5"
                      style="background: #0098EA; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.01em;">
                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #FFFFFF;"></span>
                    <span>{{ $statusBadgeText }}</span>
                </span>
            </div>
        </div>

        {{-- ── 2. Metadata Subheader (ID, Status Badge, Tanggal Pengajuan) Tanpa Background ── --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 px-1">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span style="font-size: 0.78rem; font-weight: 700; color: #475569; font-family: 'SF Mono', Monaco, Consolas, monospace;">
                    ID: &nbsp;<strong style="color: #0F172A;">{{ $crCode }}</strong>
                </span>
                <span class="px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                      style="border: 1px solid {{ $statusBadgeCfg['bg'] }}; background: {{ $statusBadgeCfg['bg'] }}; color: {{ $statusBadgeCfg['color'] }}; border-radius: 9999px; font-size: 0.74rem;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $statusBadgeCfg['color'] }}; flex-shrink: 0;"></span>
                    <span>{{ $statusBadgeCfg['text'] }}</span>
                </span>
            </div>
            <div class="text-muted small d-flex align-items-center gap-1.5" style="font-size: 0.80rem; color: #64748B;">
                <i class="bi bi-calendar3 me-1" style="font-size: 0.84rem; color: #94A3B8;"></i>
                <span>Tanggal Pengajuan: <strong style="color: #0F172A; font-weight: 800;">{{ $tglPengajuan }}</strong></span>
            </div>
        </div>

        {{-- ── 3. STATUS ALUR CR Card (6 Steps) ── --}}
        <div class="cr-subcard p-3 p-md-3.5 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #1E293B; font-weight: 800;">
                    STATUS ALUR CR
                </span>
                <span class="text-muted small" style="font-size: 0.74rem; font-weight: 600; color: #64748B;">
                    Total Progres Alur: <strong style="font-weight: 800; color: #0F172A;">{{ $pctProgress }}%</strong>
                </span>
            </div>

            {{-- Continuous Track Bar --}}
            <div class="position-relative mb-3" style="height: 5px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: {{ $pctProgress }}%; background: #0098EA; border-radius: 9999px; transition: width 0.35s ease;"></div>
            </div>

            <div class="row g-2">
                @for ($step = 1; $step <= 6; $step++)
                    @php
                        $isCompleted = $step < $currentStep;
                        $isActive = $step === $currentStep;
                        $title = $stepLabels[$step];
                    @endphp
                    <div class="col-lg-2 col-md-4 col-6">
                        @if ($isCompleted)
                            <div class="flow-step-box h-100" style="background: #F0FDF4; border: 1.5px solid #86EFAC;">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 0.95rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.76rem; color: #0F172A;">{{ $title }}</div>
                                    <div class="text-success small fw-semibold" style="font-size: 0.66rem;">Selesai</div>
                                </div>
                            </div>
                        @elseif ($isActive)
                            <div class="flow-step-box h-100" style="background: #FFFFFF; border: 2px solid #0098EA;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                      style="width: 20px; height: 20px; background: #0098EA; font-size: 0.70rem; flex-shrink: 0;">
                                    {{ $step }}
                                </span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.76rem; color: #0098EA;">{{ $title }}</div>
                                    <div class="small fw-semibold" style="font-size: 0.66rem; color: #0098EA;">Sedang Berjalan</div>
                                </div>
                            </div>
                        @else
                            <div class="flow-step-box h-100" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-muted fw-bold"
                                      style="width: 20px; height: 20px; background: #E2E8F0; font-size: 0.70rem; flex-shrink: 0; color: #64748B;">
                                    {{ $step }}
                                </span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-semibold text-muted" style="font-size: 0.76rem; color: #64748B;">{{ $title }}</div>
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
                                    <span class="text-truncate">{{ $docName }}</span>
                                </a>
                            @else
                                <div class="fw-bold mt-1" style="font-size: 0.95rem; color: #64748B;">-</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">SOLUTION PAPER</div>
                            @if($spUrl)
                                <a href="{{ $spUrl }}" target="_blank" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-code"></i>
                                    <span class="text-truncate">{{ $spName }}</span>
                                </a>
                            @else
                                <div class="fw-bold mt-1" style="font-size: 0.95rem; color: #64748B;">-</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Detail Deskripsi CR Card (Konten Aktual dari Client) ── --}}
        <div class="cr-subcard p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text" style="color: #0098EA; font-size: 1.1rem;"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem; font-weight: 800;">Detail Deskripsi CR</h5>
                </div>
            </div>

            <div class="text-secondary" style="font-size: 0.84rem; line-height: 1.65; color: #334155 !important;">
                @php
                    $deskripsiKonten = $changeRequest->deskripsi ?: ($changeRequest->alasan ?: '');
                @endphp
                @if (trim($deskripsiKonten))
                    @if (strip_tags($deskripsiKonten) !== $deskripsiKonten)
                        {!! $deskripsiKonten !!}
                    @else
                        {!! nl2br(e($deskripsiKonten)) !!}
                    @endif
                @else
                    <div class="fw-bold text-muted" style="font-size: 0.95rem; color: #64748B;">-</div>
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

        {{-- ── 7. Keputusan Approval Card (Hilang otomatis jika sudah disetujui) ── --}}
        @if (!$changeRequest->isPmhApproved())
            <div class="cr-subcard p-4">
                <div class="mb-3">
                    <h5 class="fw-bold mb-1" style="font-size: 0.98rem; font-weight: 800; color: #0F172A;">Keputusan Approval</h5>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem; color: #64748B;">
                        PM <strong style="color: #0F172A; font-weight: 700;">{{ $pmVerifier }}</strong> sudah memverifikasi Change Request. Setujui untuk diteruskan ke Marketing.
                    </p>
                </div>

                @if (in_array($statusKey, ['ditolak', 'revision_needed']))
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3 py-2 px-3" style="border-radius: 8px; font-size: 0.82rem;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>CR saat ini dalam status penolakan / revisi: <em>{{ $changeRequest->reject_reason ?: $changeRequest->catatan_revisi }}</em></div>
                    </div>
                @endif

                <form id="pmhDecisionForm" method="POST" action="{{ route('pmh.decision', $changeRequest->id ?: 1) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="catatan_approval" class="form-label fw-bold mb-1.5" style="font-size: 0.80rem; color: #0098EA;">
                            CR Notes <span class="text-danger" style="font-size: 0.75rem; font-weight: 600;">(Wajib diisi jika ditolak / dibatalkan)</span>
                        </label>
                        <div id="rejectErrorAlert" class="alert alert-danger py-2 px-3 mb-2 small d-none" style="border-radius: 8px; font-size: 0.80rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Alasan penolakan / pembatalan wajib diisi pada catatan CR Notes!
                        </div>
                        <textarea name="catatan_approval" id="catatan_approval" rows="4" class="form-control"
                            placeholder="Catatan tambahan, prioritas, atau alasan penolakan/pembatalan yang perlu diketahui PM..."
                            style="border-radius: 10px; border: 1.5px solid #E2E8F0; font-size: 0.84rem; padding: 0.75rem 1rem; color: #1E293B; resize: vertical;">{{ old('catatan_approval', $changeRequest->catatan_approval) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-2 pt-1">
                        <button type="submit" name="action" value="reject" class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5"
                            style="background: #E11D48; border-radius: 8px; font-size: 0.84rem; padding: 0.5rem 1.4rem; border: none; box-shadow: 0 2px 6px rgba(225, 29, 72, 0.25); transition: opacity 0.15s ease;"
                            onclick="return confirmReject(event);"
                            onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                        <button type="submit" name="action" value="approve" class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5"
                            style="background: #10B981; border-radius: 8px; font-size: 0.84rem; padding: 0.5rem 1.4rem; border: none; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25); transition: opacity 0.15s ease;"
                            onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                            <i class="bi bi-check-lg"></i> Setujui
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

</div>

<script>
    function confirmReject(event) {
        const notesInput = document.getElementById('catatan_approval');
        const notes = notesInput ? notesInput.value.trim() : '';
        const errorAlert = document.getElementById('rejectErrorAlert');

        if (!notes) {
            event.preventDefault();
            if (errorAlert) {
                errorAlert.classList.remove('d-none');
            }
            if (notesInput) {
                notesInput.classList.add('is-invalid');
                notesInput.focus();
            }
            alert('Alasan penolakan / pembatalan wajib diisi pada catatan CR Notes!');
            return false;
        }

        if (errorAlert) errorAlert.classList.add('d-none');
        if (notesInput) notesInput.classList.remove('is-invalid');

        return confirm('Apakah Anda yakin ingin menolak / membatalkan Change Request ini?');
    }

    document.getElementById('catatan_approval')?.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
            document.getElementById('rejectErrorAlert')?.classList.add('d-none');
        }
    });
</script>
@endsection