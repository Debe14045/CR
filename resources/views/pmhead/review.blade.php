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

    /* ── PMH Approval / Reject Custom Modals & Animations ── */
    .pmh-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .pmh-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

    .pmh-modal-container {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        z-index: 1055;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .pmh-modal-container.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .pmh-modal-card {
        background: #FFFFFF;
        border-radius: 20px;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(15, 23, 42, 0.08);
        max-width: 520px;
        width: 100%;
        padding: 2rem 2.2rem;
        text-align: center;
        transform: scale(0.92) translateY(16px);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .pmh-modal-container.show .pmh-modal-card {
        transform: scale(1) translateY(0);
    }

    /* Animated Glowing Icon Boxes */
    .pmh-icon-box {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        margin: 0 auto 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        position: relative;
    }
    .pmh-icon-box.success {
        background: linear-gradient(135deg, #DCFCE7 0%, #BBF7D0 100%);
        color: #16A34A;
        border: 2px solid #86EFAC;
    }
    .pmh-icon-box.danger {
        background: linear-gradient(135deg, #FEE2E2 0%, #FECDD3 100%);
        color: #DC2626;
        border: 2px solid #FCA5A5;
    }
    .pmh-icon-box::before {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        opacity: 0;
    }
    .pmh-icon-box.success::before {
        animation: pmhPulseGreen 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }
    .pmh-icon-box.danger::before {
        animation: pmhPulseRed 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }

    @keyframes pmhPulseGreen {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); opacity: 1; }
        70% { transform: scale(1.15); box-shadow: 0 0 0 18px rgba(34, 197, 94, 0); opacity: 0; }
        100% { transform: scale(0.9); opacity: 0; }
    }
    @keyframes pmhPulseRed {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.5); opacity: 1; }
        70% { transform: scale(1.15); box-shadow: 0 0 0 18px rgba(220, 38, 38, 0); opacity: 0; }
        100% { transform: scale(0.9); opacity: 0; }
    }

    @keyframes pmhShakeAnim {
        0%, 100% { transform: translateX(0); }
        15%, 45%, 75% { transform: translateX(-8px); }
        30%, 60%, 90% { transform: translateX(8px); }
    }
    .shake-error {
        animation: pmhShakeAnim 0.45s ease-in-out;
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
    }

    /* Buttons styling */
    .btn-pmh-action {
        border-radius: 10px;
        font-weight: 700;
        padding: 0.62rem 1.6rem;
        font-size: 0.86rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }
    .btn-pmh-action:hover {
        transform: translateY(-2px);
    }
    .btn-pmh-approve {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.32);
    }
    .btn-pmh-approve:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #FFFFFF;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.42);
    }
    .btn-pmh-reject {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.28);
    }
    .btn-pmh-reject:hover {
        background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
        color: #FFFFFF;
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.38);
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
        @if ($changeRequest->pmh_approved_at || in_array($statusKey, ['validated', 'disetujui', 'presales_review', 'quotation_submitted', 'client_approved', 'development', 'dikerjakan', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'golive', 'selesai']))
            <div class="cr-subcard p-4 text-center" style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%) !important; border: 1.5px solid #86EFAC !important;">
                <div class="d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 56px; height: 56px; border-radius: 50%; background: #16A34A; color: white;">
                    <i class="bi bi-shield-check fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #14532D; font-weight: 800; font-size: 1.1rem;">Change Request Telah Divalidasi</h5>
                <p class="text-success small mb-2" style="font-size: 0.85rem; font-weight: 500;">
                    Disetujui oleh <strong style="color: #14532D; font-weight: 800;">{{ $changeRequest->pmh_approved_by ?: 'PM Head' }}</strong> 
                    pada {{ $changeRequest->pmh_approved_at ? $changeRequest->pmh_approved_at->format('d M Y, H:i') : '-' }} WIB.
                </p>
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-2" style="background: #FFFFFF; border: 1px solid #BBF7D0; color: #15803D; font-size: 0.78rem; font-weight: 700;">
                    <i class="bi bi-check2-all"></i> Status saat ini: {{ $statusText }}
                </div>
                @if($changeRequest->catatan_approval)
                    <div class="mt-2 text-center">
                        <div class="p-2.5 px-3.5 d-inline-block rounded-3 small text-start" style="background: rgba(255,255,255,0.85); border: 1px solid #BBF7D0; color: #166534; max-width: 600px;">
                            <span class="fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">Catatan PM Head:</span>
                            &ldquo;{{ $changeRequest->catatan_approval }}&rdquo;
                        </div>
                    </div>
                @endif
            </div>
        @elseif (in_array($statusKey, ['revision_needed', 'ditolak']))
            <div class="cr-subcard p-4 text-center" style="background: #FFF1F2 !important; border: 1.5px solid #FECDD3 !important;">
                <div class="d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 56px; height: 56px; border-radius: 50%; background: #E11D48; color: white;">
                    <i class="bi bi-arrow-counterclockwise fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #881337; font-weight: 800; font-size: 1.1rem;">CR Dalam Status Revisi (Revision Needed)</h5>
                <p class="small mb-2" style="color: #9F1239; font-size: 0.85rem;">
                    Permintaan perubahan ini telah dikembalikan ke Project Manager untuk diperbaiki.
                </p>
                @if($changeRequest->reject_reason || $changeRequest->catatan_revisi)
                    <div class="mt-2 text-center">
                        <div class="p-2.5 px-3.5 d-inline-block rounded-3 small text-start" style="background: #FFFFFF; border: 1px solid #FDA4AF; color: #9F1239; max-width: 600px;">
                            <span class="fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">Alasan Revisi / Penolakan:</span>
                            &ldquo;{{ $changeRequest->reject_reason ?: $changeRequest->catatan_revisi }}&rdquo;
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="cr-subcard p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                        <h5 class="fw-bold mb-1" style="font-size: 0.98rem; font-weight: 800; color: #0F172A;">Keputusan Approval</h5>
                        <p class="text-muted small mb-0" style="font-size: 0.82rem; color: #64748B;">
                            PM <strong style="color: #0F172A; font-weight: 700;">{{ $pmVerifier }}</strong> sudah memverifikasi Change Request. Setujui untuk diteruskan ke Marketing.
                        </p>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Keputusan PM Head
                    </span>
                </div>

                <form id="pmhDecisionForm" method="POST" action="{{ route('pmh.decision', $changeRequest) }}">
                    @csrf
                    <input type="hidden" name="action" id="decision_action_input" value="approve">

                    <div class="mb-3">
                        <label class="fw-bold small mb-1" style="font-size: 0.76rem; font-weight: 700; color: #0F172A;">
                            CR Notes / Catatan Tambahan (Opsional untuk Setuju, Wajib untuk Tolak)
                        </label>
                        <textarea name="catatan_approval" id="main_catatan_approval" rows="3" class="form-control"
                                  placeholder="Catatan tambahan, prioritas, atau catatan instruksi bagi PM / Presales..."
                                  style="border-radius: 8px; border: 1px solid #CBD5E1; font-size: 0.85rem; padding: 0.75rem 1rem; color: #1E293B; background: #FFFFFF;"></textarea>
                        @error('catatan_approval')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end align-items-center pt-2 gap-2">
                        <button type="button" id="btnTriggerReject" class="btn btn-pmh-action btn-pmh-reject" onclick="openPmhRejectModal()">
                            <i class="bi bi-x-circle-fill"></i> Tolak
                        </button>
                        <button type="button" id="btnTriggerApprove" class="btn btn-pmh-action btn-pmh-approve" onclick="openPmhApproveModal()">
                            <i class="bi bi-check-circle-fill"></i> Setuju
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- ── PM HEAD ANIMATED APPROVAL & REJECTION CUSTOM MODALS ── --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div id="pmhModalBackdrop" class="pmh-modal-backdrop" onclick="closePmhModals()"></div>

{{-- 1. APPROVE MODAL --}}
<div id="pmhApproveModalContainer" class="pmh-modal-container" role="dialog" aria-modal="true" aria-labelledby="pmhApproveTitle">
    <div class="pmh-modal-card">
        <div class="pmh-icon-box success shadow-sm">
            <i class="bi bi-shield-check"></i>
        </div>

        <h4 id="pmhApproveTitle" class="fw-bold mb-1" style="font-size: 1.25rem; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
            Validasi &amp; Setujui Change Request
        </h4>
        <p class="text-muted small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
            Apakah Anda yakin ingin menyetujui dan memvalidasi Change Request ini? Dokumen akan resmi disahkan dan diteruskan ke Marketing / Presales.
        </p>

        {{-- CR Summary Card --}}
        <div class="p-3 mb-3 text-start rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
            <div class="d-flex align-items-center justify-content-between mb-1.5">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-monospace fw-bold" style="font-size: 0.74rem;">
                    {{ $crCode }}
                </span>
                <span class="badge bg-light text-dark border px-2.5 py-1 fw-bold" style="font-size: 0.74rem;">
                    <i class="bi bi-clock-history me-1 text-primary"></i> Total: {{ $totalMandays }} Hari Kerja
                </span>
            </div>
            <div class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.90rem;">
                {{ $crTitle }}
            </div>
            <div class="d-flex align-items-center gap-2 text-muted small" style="font-size: 0.78rem;">
                <span class="text-truncate"><i class="bi bi-building me-1"></i> {{ $companyName }}</span>
                <span>&bull;</span>
                <span class="text-truncate"><i class="bi bi-person me-1"></i> Verifikator: {{ $pmVerifier }}</span>
            </div>
            <div class="mt-2.5 pt-2 border-top d-flex align-items-center gap-2 text-success small" style="font-size: 0.76rem; font-weight: 600;">
                <i class="bi bi-send-check fs-6"></i>
                <span>Otomatis mengirim notifikasi ke Klien, PM, dan Presales</span>
            </div>
        </div>

        {{-- Extra Notes inside Modal --}}
        <div class="text-start mb-3">
            <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.75rem;">
                Catatan Persetujuan (Opsional):
            </label>
            <input type="text" id="modal_approve_notes" class="form-control form-control-sm"
                   placeholder="Contoh: Telah ditinjau dan disetujui, harap segera proses penawaran harga."
                   style="border-radius: 8px; font-size: 0.82rem; padding: 0.55rem 0.75rem; border: 1px solid #CBD5E1;">
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end align-items-center gap-2 pt-2">
            <button type="button" id="btnCancelApproveModal" class="btn btn-light fw-semibold text-muted px-3 py-2" style="border-radius: 8px; font-size: 0.82rem; border: 1px solid #E2E8F0;" onclick="closePmhModals()">
                Batal
            </button>
            <button type="button" id="btnConfirmApproveSubmit" class="btn btn-pmh-action btn-pmh-approve px-4 py-2" onclick="executePmhApprove()">
                <span id="textConfirmApprove"><i class="bi bi-check-circle-fill me-1"></i> Ya, Setujui Sekarang</span>
                <span id="spinnerConfirmApprove" class="d-none align-items-center gap-2">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Memvalidasi CR...</span>
                </span>
            </button>
        </div>
    </div>
</div>

{{-- 2. REJECT / REVISION MODAL --}}
<div id="pmhRejectModalContainer" class="pmh-modal-container" role="dialog" aria-modal="true" aria-labelledby="pmhRejectTitle">
    <div class="pmh-modal-card">
        <div class="pmh-icon-box danger shadow-sm">
            <i class="bi bi-arrow-counterclockwise"></i>
        </div>

        <h4 id="pmhRejectTitle" class="fw-bold mb-1" style="font-size: 1.25rem; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
            Kembalikan CR untuk Revisi
        </h4>
        <p class="text-muted small mb-3" style="font-size: 0.84rem; line-height: 1.5;">
            Permintaan perubahan akan dikembalikan ke Project Manager dengan status <strong>Revision Needed</strong>. Mohon berikan alasan dan arahan revisi.
        </p>

        {{-- CR Summary Card --}}
        <div class="p-3 mb-3 text-start rounded-3" style="background: #FFF1F2; border: 1px solid #FECDD3;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 font-monospace fw-bold" style="font-size: 0.74rem;">
                    {{ $crCode }}
                </span>
                <span class="badge bg-danger text-white px-2 py-0.5 fw-bold" style="font-size: 0.70rem;">
                    Revisi PM
                </span>
            </div>
            <div class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.90rem;">
                {{ $crTitle }}
            </div>
            <div class="small text-danger" style="font-size: 0.76rem; font-weight: 600;">
                <i class="bi bi-info-circle me-1"></i> Project Manager akan menerima notifikasi beserta catatan revisi Anda.
            </div>
        </div>

        {{-- Mandatory Reason Textarea --}}
        <div class="text-start mb-3">
            <label class="form-label small fw-bold text-dark mb-1" style="font-size: 0.76rem;">
                Alasan Penolakan / Catatan Revisi PM <span class="text-danger">*</span>
            </label>
            <textarea id="modal_reject_notes" class="form-control" rows="3"
                      placeholder="Tuliskan poin-poin yang perlu direvisi atau disesuaikan oleh PM..."
                      style="border-radius: 8px; font-size: 0.82rem; padding: 0.6rem 0.75rem; border: 1px solid #CBD5E1;"></textarea>
            <div id="rejectErrorMsg" class="text-danger small mt-1.5 d-none" style="font-size: 0.76rem; font-weight: 600;">
                <i class="bi bi-exclamation-octagon-fill me-1"></i> Alasan penolakan wajib diisi (minimal 5 karakter)!
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end align-items-center gap-2 pt-2">
            <button type="button" id="btnCancelRejectModal" class="btn btn-light fw-semibold text-muted px-3 py-2" style="border-radius: 8px; font-size: 0.82rem; border: 1px solid #E2E8F0;" onclick="closePmhModals()">
                Batal
            </button>
            <button type="button" id="btnConfirmRejectSubmit" class="btn btn-pmh-action btn-pmh-reject px-4 py-2" onclick="executePmhReject()">
                <span id="textConfirmReject"><i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan ke PM</span>
                <span id="spinnerConfirmReject" class="d-none align-items-center gap-2">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Mengembalikan CR...</span>
                </span>
            </button>
        </div>
    </div>
</div>

<script>
    function openPmhApproveModal() {
        const mainNotes = document.getElementById('main_catatan_approval')?.value || '';
        const modalNotes = document.getElementById('modal_approve_notes');
        if (modalNotes && mainNotes.trim()) {
            modalNotes.value = mainNotes.trim();
        }
        document.getElementById('pmhModalBackdrop')?.classList.add('show');
        document.getElementById('pmhApproveModalContainer')?.classList.add('show');
    }

    function openPmhRejectModal() {
        const mainNotes = document.getElementById('main_catatan_approval')?.value || '';
        const modalNotes = document.getElementById('modal_reject_notes');
        if (modalNotes && mainNotes.trim()) {
            modalNotes.value = mainNotes.trim();
        }
        document.getElementById('rejectErrorMsg')?.classList.add('d-none');
        document.getElementById('modal_reject_notes')?.classList.remove('shake-error');
        document.getElementById('pmhModalBackdrop')?.classList.add('show');
        document.getElementById('pmhRejectModalContainer')?.classList.add('show');
        setTimeout(() => {
            modalNotes?.focus();
        }, 150);
    }

    function closePmhModals() {
        document.getElementById('pmhModalBackdrop')?.classList.remove('show');
        document.getElementById('pmhApproveModalContainer')?.classList.remove('show');
        document.getElementById('pmhRejectModalContainer')?.classList.remove('show');
    }

    function executePmhApprove() {
        const form = document.getElementById('pmhDecisionForm');
        if (!form) return;

        const modalNotes = document.getElementById('modal_approve_notes')?.value || '';
        const mainNotes = document.getElementById('main_catatan_approval');
        if (mainNotes && modalNotes.trim()) {
            mainNotes.value = modalNotes.trim();
        }

        const actionInput = document.getElementById('decision_action_input');
        if (actionInput) actionInput.value = 'approve';

        // Animate button spinner
        const textSpan = document.getElementById('textConfirmApprove');
        const spinnerSpan = document.getElementById('spinnerConfirmApprove');
        const btnSubmit = document.getElementById('btnConfirmApproveSubmit');
        const btnCancel = document.getElementById('btnCancelApproveModal');

        if (textSpan) textSpan.classList.add('d-none');
        if (spinnerSpan) spinnerSpan.classList.remove('d-none');
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnCancel) btnCancel.disabled = true;

        form.submit();
    }

    function executePmhReject() {
        const form = document.getElementById('pmhDecisionForm');
        if (!form) return;

        const modalNotes = document.getElementById('modal_reject_notes');
        const noteVal = modalNotes ? modalNotes.value.trim() : '';

        if (!noteVal || noteVal.length < 5) {
            const errorMsg = document.getElementById('rejectErrorMsg');
            if (errorMsg) errorMsg.classList.remove('d-none');
            if (modalNotes) {
                modalNotes.classList.remove('shake-error');
                void modalNotes.offsetWidth; // trigger reflow
                modalNotes.classList.add('shake-error');
                modalNotes.focus();
            }
            return;
        }

        const mainNotes = document.getElementById('main_catatan_approval');
        if (mainNotes) {
            mainNotes.value = noteVal;
        }

        const actionInput = document.getElementById('decision_action_input');
        if (actionInput) actionInput.value = 'reject';

        // Animate button spinner
        const textSpan = document.getElementById('textConfirmReject');
        const spinnerSpan = document.getElementById('spinnerConfirmReject');
        const btnSubmit = document.getElementById('btnConfirmRejectSubmit');
        const btnCancel = document.getElementById('btnCancelRejectModal');

        if (textSpan) textSpan.classList.add('d-none');
        if (spinnerSpan) spinnerSpan.classList.remove('d-none');
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnCancel) btnCancel.disabled = true;

        form.submit();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePmhModals();
        }
    });
</script>
@endsection
