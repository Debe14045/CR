@extends('layouts.app')

@section('title', 'Detail Change Request · ' . ($changeRequest->kode_cr ?: 'CR-2026-06-0042'))

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $isClientUser = (auth()->check() && auth()->user()->isClient()) && request('role') !== 'pm';
    $statusKey = strtolower($changeRequest->status ?? '');

    $isVerificationLocked = in_array($statusKey, ['validated', 'disetujui', 'awaiting_pmh', 'development', 'dikerjakan', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'golive', 'selesai']);

    // ── 1. STEPPER & ALUR STATUS UTAMA ──
    $currentStep = match(true) {
        in_array($statusKey, ['diajukan', 'draft']) => 1,
        in_array($statusKey, ['awaiting_pm', 'dianalisis', 'analisa', 'analysis']) => 2,
        in_array($statusKey, ['awaiting_pmh', 'revision_needed']) => 3,
        in_array($statusKey, ['validated', 'disetujui', 'quotation']) => 4,
        in_array($statusKey, ['development', 'dikerjakan', 'sit', 'uat', 'training']) => 5,
        in_array($statusKey, ['awaiting_golive_validation', 'golive', 'invoicing', 'selesai']) => 6,
        default => 1,
    };

    $mainStageNum = $currentStep;
    $progressPct = match($currentStep) {
        1 => 17,
        2 => 33,
        3 => 50,
        4 => 67,
        5 => 83,
        6 => 100,
        default => 17,
    };

    // Sub-stepper pengerjaan teknis (Develop, SIT, UAT, Training, Go-Live) - 5 Sub-Tahap Aktif
    $pmStatusStep = match($statusKey) {
        'sit' => 2,
        'uat' => 3,
        'training' => 4,
        'awaiting_golive_validation', 'golive', 'selesai', 'invoicing' => 5,
        default => 1,
    };

    $subStepperData = match($pmStatusStep) {
        1 => ['pct' => '20%', 'label' => 'Develop', 'line' => '0%'],
        2 => ['pct' => '40%', 'label' => 'SIT', 'line' => '23%'],
        3 => ['pct' => '60%', 'label' => 'UAT', 'line' => '46%'],
        4 => ['pct' => '80%', 'label' => 'Training', 'line' => '69%'],
        5 => ['pct' => '100%', 'label' => 'Go Live', 'line' => '92%'],
        default => ['pct' => '20%', 'label' => 'Develop', 'line' => '0%'],
    };

    $pmStatusPct = $subStepperData['pct'];
    $pmStatusLabel = $subStepperData['label'];
    $pmStatusLineWidth = $subStepperData['line'];

    $isStatusCrUnlocked = in_array($statusKey, ['development', 'dikerjakan', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'golive', 'invoicing', 'selesai'])
        || ($changeRequest->harga_penawaran !== null && $changeRequest->isPmhApproved());

    // ── 2. DATA UTAMA & IDENTITAS CR ──
    $crTitle = $changeRequest->judul ?: ('Change Request #' . $changeRequest->id);
    $crCode = $changeRequest->kode_cr ?: ('CR-' . str_pad($changeRequest->id, 4, '0', STR_PAD_LEFT));
    $firstChar = strtoupper(substr($crTitle, 0, 1)) ?: 'C';

    $companyName = $changeRequest->klien ?: ($changeRequest->client?->company ?: '-');
    $cleanName = trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $companyName));
    $words = preg_split('/\s+/', $cleanName);
    if (count($words) >= 2) {
        $clientInitial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } elseif (!empty($words[0])) {
        $clientInitial = strtoupper(substr($words[0], 0, min(2, strlen($words[0]))));
    } else {
        $clientInitial = 'CR';
    }

    $picName = $changeRequest->pic_sales ?: ($changeRequest->owner_cr ?: ($changeRequest->user?->name ?: '-'));
    $projectName = $changeRequest->proyek_terkait ?: '-';
    $crOwner = $changeRequest->owner_cr ?: ($changeRequest->main_desk ?: '-');
    $priorityLabel = ucfirst($changeRequest->prioritas ?: 'Normal');

    $tanggalPengajuan = $changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->translatedFormat('d M Y') : ($changeRequest->created_at ? $changeRequest->created_at->translatedFormat('d M Y') : '-');
    $headerTanggalPengajuan = $tanggalPengajuan;
    $requestGoLive = $changeRequest->target_selesai ? $changeRequest->target_selesai->translatedFormat('d M Y') : '-';

    $docCrUrl = $changeRequest->google_drive_url ?: null;
    $solutionPaperUrl = $changeRequest->solution_paper_url ?: null;

    $mandaysAnalisis = (int)($changeRequest->mindesk_analisis ?? 0);
    $mandaysDevelopment = (int)($changeRequest->mindesk_development ?? 0);
    $mandaysTesting = (int)($changeRequest->mindesk_testing ?? 0);
    $totalMandays = $mandaysAnalisis + $mandaysDevelopment + $mandaysTesting;

    $catatanPengajuan = $changeRequest->catatan_pengajuan ?: ($changeRequest->pesan_client ?: '-');
    
    $statusDisplay = 'Menunggu Review';
    if ($statusKey === 'dikerjakan' || $statusKey === 'development') {
        $statusDisplay = 'Sedang Dikerjakan';
    } elseif ($statusKey === 'disetujui' || $statusKey === 'validated') {
        $statusDisplay = 'Disetujui';
    } elseif ($statusKey === 'selesai' || $statusKey === 'golive') {
        $statusDisplay = 'Selesai Go-Live';
    } elseif ($statusKey === 'ditolak') {
        $statusDisplay = 'Ditolak';
    }

    $stages = [
        1 => ['num' => 1, 'name' => '1. Diajukan'],
        2 => ['num' => 2, 'name' => 'Review PM'],
        3 => ['num' => 3, 'name' => 'Approval PM Head'],
        4 => ['num' => 4, 'name' => 'Quotation'],
        5 => ['num' => 5, 'name' => 'Development'],
        6 => ['num' => 6, 'name' => 'Invoice'],
    ];

    $activeTab = request('tab', 'detail');
    if ($isClientUser) {
        $activeTab = 'detail';
    } elseif ($activeTab === 'status' && !$isStatusCrUnlocked) {
        $activeTab = 'detail';
    }
@endphp

<style>
    .cr-grid-info-doc {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 16px;
        margin-bottom: 18px;
        width: 100%;
        box-sizing: border-box;
    }
    .cr-grid-mandays-notes {
        display: grid;
        grid-template-columns: 1fr 1.6fr;
        gap: 16px;
        width: 100%;
        box-sizing: border-box;
    }
    .cr-grid-status-ba {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 991px) {
        .cr-grid-info-doc,
        .cr-grid-mandays-notes,
        .cr-grid-status-ba {
            grid-template-columns: 1fr !important;
        }
    }
    .pm-filter-container {
        display: flex; align-items: stretch; width: 100%;
        background: #EAF2FB; border: 1px solid #CBDCEE;
        border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem; padding: 0 !important;
    }
    .pm-filter-tab {
        flex: 1; display: flex; align-items: center; justify-content: center;
        padding: 0.75rem 1rem; font-size: 0.9rem; font-weight: 700;
        text-decoration: none; text-align: center; transition: all 0.15s ease;
        position: relative; cursor: pointer; border: none; background: transparent; color: #0B6EBD;
    }
    .pm-filter-tab.active { background: #0072D2 !important; color: #FFFFFF !important; box-shadow: none !important; }
    .pm-filter-tab:not(.active):not(.is-locked):hover { background: rgba(11,110,189,0.08) !important; }
    .pm-filter-tab.is-locked {
        background: #E2E8F0 !important;
        color: #94A3B8 !important;
        cursor: not-allowed !important;
    }
    .pm-filter-tab.is-locked:hover {
        background: #CBD5E1 !important;
        color: #64748B !important;
    }
    .pm-filter-tab.is-locked i,
    .pm-filter-tab.is-locked small {
        color: inherit !important;
    }
    .pm-filter-divider { width: 1px; background: #CBDCEE; flex-shrink: 0; }
</style>

<div style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0F172A; width: 100%; box-sizing: border-box; padding-bottom: 3.5rem;">

    {{-- HEADER HALAMAN --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 id="pageHeaderTitle" style="font-size: 1.65rem; font-weight: 800; color: #0F172A; letter-spacing: -0.02em; margin: 0 0 3px; line-height: 1.2;">
                Detail Change Request
            </h1>
            <p style="font-size: 0.82rem; color: #64748B; margin: 0; font-weight: 400;">Melihat Data Lengkap CR</p>
        </div>
    </div>
    <div style="height: 1.5px; background: #E2E8F0; margin: 0 0 20px;"></div>

    {{-- 3 TAB NAVIGASI KHUSUS PM & CLIENT --}}
    @if(!$isClientUser)
    <div class="pm-filter-container" role="tablist" aria-label="Tab Detail CR">
        <button type="button" class="pm-filter-tab {{ $activeTab === 'detail' ? 'active' : '' }}" id="tabBtnDetail" onclick="switchDetailTab('detail')" role="tab" style="border-top-left-radius: 11px; border-bottom-left-radius: 11px;">
            <span>Detail Change Request</span>
        </button>
        <div class="pm-filter-divider"></div>

        @if($isVerificationLocked)
            <button type="button" class="pm-filter-tab is-locked" onclick="alert('Verifikasi CR telah selesai dan terkunci.')" title="Verifikasi CR telah selesai dan terkunci">
                <span>Verifikasi CR</span> <i class="bi bi-lock-fill ms-1" style="font-size: 0.75rem;"></i>
            </button>
        @else
            <button type="button" class="pm-filter-tab {{ $activeTab === 'verifikasi' ? 'active' : '' }}" id="tabBtnVerifikasi" onclick="switchDetailTab('verifikasi')" role="tab">
                <span>Verifikasi CR</span>
            </button>
        @endif

        <div class="pm-filter-divider"></div>
        @if(!$isStatusCrUnlocked)
            <button type="button" class="pm-filter-tab is-locked" id="tabBtnStatus" onclick="notifyStatusLocked()" role="tab"
                    title="Halaman belum bisa dibuka, menunggu quotation"
                    style="border-top-right-radius: 11px; border-bottom-right-radius: 11px;">
                <i class="bi bi-lock-fill" style="font-size: 0.84rem; margin-right: 5px;"></i>
                <span>Status CR <small style="font-weight: 700;">(Terkunci)</small></span>
            </button>
        @else
            <button type="button" class="pm-filter-tab {{ $activeTab === 'status' ? 'active' : '' }}" id="tabBtnStatus" onclick="switchDetailTab('status')" role="tab" style="border-top-right-radius: 11px; border-bottom-right-radius: 11px;">
                <span>Status CR</span>
            </button>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
        TAB PANEL 1: DETAIL CHANGE REQUEST (DENGAN SUB-STATUS)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="tabPanel-detail" class="{{ (!$isClientUser && $activeTab !== 'detail') ? 'd-none' : '' }}">
        <div style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; padding: 24px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); width: 100%; box-sizing: border-box;">

            {{-- Top Identity Row --}}
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #0098EA; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; flex-shrink: 0;">
                    {{ $firstChar }}
                </div>
                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0;">{{ $crTitle }}</h2>
                    @php
                        $badgeCfg = $changeRequest->statusBadgeConfig();
                    @endphp
                    <span style="background: {{ $badgeCfg['bg'] }}; color: {{ $badgeCfg['color'] }}; font-size: 0.72rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px;">
                        • {{ $badgeCfg['text'] }}
                    </span>
                </div>
            </div>

            {{-- Metadata Sub-bar --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                    <span style="font-size: 0.78rem;"><strong>ID:</strong> <span style="font-weight: 800;">{{ $crCode }}</span></span>
                    <span style="background: {{ $badgeCfg['bg'] }}; color: {{ $badgeCfg['color'] }}; font-size: 0.74rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px;">
                        {{ $badgeCfg['text'] }}
                    </span>
                </div>
                <div style="font-size: 0.78rem; color: #64748B;">
                    Tanggal Pengajuan: <strong style="color: #0F172A;">{{ $headerTanggalPengajuan }}</strong>
                </div>
            </div>

            {{-- STATUS ALUR CR Card Utama --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px 20px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.74rem; font-weight: 800; color: #334155; text-transform: uppercase;">STATUS ALUR CR</span>
                    <span style="font-size: 0.72rem; color: #64748B;">Total Progres Alur: {{ $progressPct }}%</span>
                </div>
                <div style="height: 5px; background: #E2E8F0; border-radius: 9999px; margin: 12px 0 16px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $progressPct }}%; background: #0098EA; border-radius: 9999px;"></div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px;">
                    @foreach($stages as $sNum => $stg)
                        @php
                            $isPast = ($sNum < $mainStageNum);
                            $isCurrent = ($sNum === $mainStageNum);
                        @endphp
                        @if($isPast)
                            <div style="background: #F0FDF4; border: 1.5px solid #86EFAC; border-radius: 8px; padding: 9px 12px; display: flex; align-items: center; gap: 8px;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: #16A34A; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800;"><i class="bi bi-check-lg"></i></div>
                                <div><div style="font-size: 0.74rem; font-weight: 700;">{{ $stg['name'] }}</div><div style="font-size: 0.68rem; color: #16A34A;">Selesai</div></div>
                            </div>
                        @elseif($isCurrent)
                            <div style="background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; padding: 9px 12px; display: flex; align-items: center; gap: 8px;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: #0098EA; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.74rem; font-weight: 800;">{{ $stg['num'] }}</div>
                                <div><div style="font-size: 0.74rem; font-weight: 800;">{{ $stg['name'] }}</div><div style="font-size: 0.68rem; color: #0098EA;">Sedang Berjalan</div></div>
                            </div>
                        @else
                            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 9px 12px; display: flex; align-items: center; gap: 8px;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: #E2E8F0; color: #94A3B8; display: flex; align-items: center; justify-content: center; font-size: 0.74rem; font-weight: 700;">{{ $stg['num'] }}</div>
                                <div><div style="font-size: 0.72rem; font-weight: 600; color: #475569;">{{ $stg['name'] }}</div><div style="font-size: 0.66rem; color: #94A3B8;">Menunggu</div></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ── SUB-STATUS / PROGRESS PENGERJAAN TEKNIS (Hanya Tampil Jika Alur CR Sudah Sampai Development) ── --}}
            @if($currentStep >= 5)
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 28px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 18px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 28px; height: 28px; border-radius: 8px; background: #0084FF; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.84rem; font-weight: 800; color: #0F172A; text-transform: uppercase;">PROGRESS PENGERJAAN {{ strtoupper($pmStatusLabel) }}</div>
                            <div style="font-size: 0.72rem; color: #64748B;">Tahap teknis berjalan: <strong>{{ $pmStatusStep }} dari 5 sub-tahap aktif</strong></div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 0.74rem; color: #64748B;"><i class="bi bi-clock me-1"></i>Est. Selesai: {{ $requestGoLive }}</span>
                        <span style="background: #0084FF; color: #FFFFFF; font-size: 0.74rem; font-weight: 700; padding: 4px 14px; border-radius: 9999px;">
                            {{ $pmStatusPct }} — {{ $pmStatusLabel }}
                        </span>
                    </div>
                </div>

                <div style="position: relative; margin-top: 32px; margin-bottom: 8px;">
                    <div style="position: absolute; top: 15px; left: 4%; right: 4%; height: 3px; background: #E2E8F0; z-index: 1;"></div>
                    <div style="position: absolute; top: 15px; left: 4%; width: {{ $pmStatusLineWidth }}; height: 3px; background: #0084FF; z-index: 2; transition: width 0.3s ease;"></div>

                    <div style="display: flex; justify-content: space-between; position: relative; z-index: 3;">
                        @php
                            $subLabels = [1 => 'Develop', 2 => 'SIT', 3 => 'UAT', 4 => 'Training', 5 => 'Go-Live'];
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $isSubPast = ($i < $pmStatusStep);
                                $isSubCurrent = ($i === $pmStatusStep);
                            @endphp
                            <div style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                                <div style="width: 30px; height: 30px; border-radius: 50%; 
                                    @if($isSubPast || $isSubCurrent) background: #0084FF; color: #FFFFFF; font-weight: 700; 
                                    @else background: #FFFFFF; border: 2px solid #CBD5E1; color: #94A3B8; font-weight: 600; @endif
                                    display: flex; align-items: center; justify-content: center; font-size: 0.80rem;">
                                    {{ $i }}
                                </div>
                                <span style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $isSubCurrent ? '800' : '600' }}; color: {{ $isSubCurrent ? '#0084FF' : '#64748B' }}; text-align: center;">
                                    {{ $subLabels[$i] }}
                                </span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            @endif

            {{-- INFORMASI CR & DOKUMEN --}}
            <div class="cr-grid-info-doc">
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; height: 100%;">
                    <div style="color: #94A3B8; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; margin-bottom: 18px;">INFORMASI CR</div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); row-gap: 18px; column-gap: 16px;">
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">NAMA PERUSAHAAN</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $companyName }}</div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">INISIAL KLIEN</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $clientInitial }}</div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">NAMA PIC</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $picName }}</div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">NAMA PROJECT</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $projectName }}</div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">CR OWNER</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $crOwner }}</div></div>
                        <div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">TANGGAL PENGAJUAN</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $tanggalPengajuan }}</div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">REQUEST GO-LIVE</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $requestGoLive }}</div></div>
                        <div><div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700;">PRIORITAS CR</div><div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $priorityLabel }}</div></div>
                    </div>
                </div>
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; height: 100%;">
                    <div style="color: #94A3B8; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; margin-bottom: 18px;">DOKUMEN</div>
                    <div style="margin-bottom: 18px;">
                        <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; margin-bottom: 8px;">DOKUMEN CR</div>
                        @if($docCrUrl)
                        <a href="{{ $docCrUrl }}" target="_blank" style="color: #0284C7; font-size: 0.78rem; font-weight: 600;">{{ $docCrUrl }}</a>
                        @elseif($changeRequest->lampiran_pengajuan)
                        <a href="{{ route('change-requests.initial-file', $changeRequest) }}" target="_blank" style="color: #0284C7; font-size: 0.78rem; font-weight: 600;">{{ basename($changeRequest->lampiran_pengajuan) }}</a>
                        @else <span class="text-muted small">-</span> @endif
                    </div>
                    <div>
                        <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; margin-bottom: 8px;">SOLUTION PAPER</div>
                        @if($solutionPaperUrl)
                        <a href="{{ $solutionPaperUrl }}" target="_blank" style="color: #0284C7; font-size: 0.78rem; font-weight: 600;">{{ $solutionPaperUrl }}</a>
                        @elseif($changeRequest->solutionPaper?->solution_paper_file)
                        <a href="{{ Storage::url($changeRequest->solutionPaper->solution_paper_file) }}" target="_blank" style="color: #0284C7; font-size: 0.78rem; font-weight: 600;">{{ basename($changeRequest->solutionPaper->solution_paper_file) }}</a>
                        @else <span class="text-muted small">-</span> @endif
                    </div>
                </div>
            </div>

            {{-- DESKRIPSI & MANDAYS --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; margin-bottom: 18px;">
                <div style="font-weight: 800; font-size: 0.88rem; margin-bottom: 12px; color: #0F172A;">Detail Deskripsi CR</div>
                <p style="font-size: 0.80rem; color: #334155; line-height: 1.6; margin-bottom: 0;">
                    {!! nl2br(e($changeRequest->alasan ?: $changeRequest->deskripsi)) !!}
                </p>
            </div>

            <div class="cr-grid-mandays-notes">
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px;">
                    <div style="color: #94A3B8; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; margin-bottom: 16px;">ESTIMASI MANDAYS</div>
                    <div style="display: flex; justify-content: space-between;"><span style="font-size: 0.76rem; color: #64748B;">Analisis</span><strong>{{ $mandaysAnalisis }} hari</strong></div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px;"><span style="font-size: 0.76rem; color: #64748B;">Development</span><strong>{{ $mandaysDevelopment }} hari</strong></div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px;"><span style="font-size: 0.76rem; color: #64748B;">Testing</span><strong>{{ $mandaysTesting }} hari</strong></div>
                    <hr style="margin: 12px 0;">
                    <div style="display: flex; justify-content: space-between;"><span>Total</span><strong>{{ $totalMandays }} hari kerja</strong></div>
                </div>
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; display: flex; flex-direction: column;">
                    <div style="font-weight: 800; font-size: 0.84rem; margin-bottom: 12px;">CR Notes / Catatan</div>
                    <div style="flex: 1; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 15px; color: #475569; font-size: 0.78rem;">&ldquo;{{ $catatanPengajuan }}&rdquo;</div>
                </div>
            </div>

        </div>
    </div>

    @if(!$isClientUser)
    {{-- ══════════════════════════════════════════════════════════════
        TAB PANEL 2: VERIFIKASI CR (MANDATORY MANDAYS & SOLUTION PAPER)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="tabPanel-verifikasi" class="{{ $activeTab !== 'verifikasi' ? 'd-none' : '' }}">
        <div style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; padding: 26px 28px;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin-bottom: 20px;">Verifikasi Change Request: {{ $crTitle }}</h2>

            <form id="formVerifikasiCr" method="POST" action="{{ route('change-requests.analysis', $changeRequest) }}" enctype="multipart/form-data" onsubmit="return validateVerifikasiCr(event)">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #1E293B; margin-bottom: 10px; display: block;">Estimasi Mandays PM <span style="color: #EF4444;">*</span></label>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; margin-bottom: 6px; display: block;">Analisis (hari)*</label>
                            <input type="number" id="v_analisis" name="mindesk_analisis" min="0" step="1" value="{{ $changeRequest->mindesk_analisis ?? 0 }}"
                                   onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                                   onfocus="handleMandaysFocus(this)" onclick="handleMandaysFocus(this)" onblur="handleMandaysBlur(this)"
                                   oninput="handleMandaysInput(this)" required
                                   style="width: 100%; height: 40px; background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px;">
                        </div>
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; margin-bottom: 6px; display: block;">Development (hari)*</label>
                            <input type="number" id="v_dev" name="mindesk_development" min="0" step="1" value="{{ $changeRequest->mindesk_development ?? 0 }}"
                                   onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                                   onfocus="handleMandaysFocus(this)" onclick="handleMandaysFocus(this)" onblur="handleMandaysBlur(this)"
                                   oninput="handleMandaysInput(this)" required
                                   style="width: 100%; height: 40px; background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px;">
                        </div>
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; margin-bottom: 6px; display: block;">Testing (hari)*</label>
                            <input type="number" id="v_test" name="mindesk_testing" min="0" step="1" value="{{ $changeRequest->mindesk_testing ?? 0 }}"
                                   onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                                   onfocus="handleMandaysFocus(this)" onclick="handleMandaysFocus(this)" onblur="handleMandaysBlur(this)"
                                   oninput="handleMandaysInput(this)" required
                                   style="width: 100%; height: 40px; background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px;">
                        </div>
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; margin-bottom: 6px; display: block;">Total Mandays</label>
                            <input type="text" id="v_total" readonly value="{{ (($changeRequest->mindesk_analisis ?? 0) + ($changeRequest->mindesk_development ?? 0) + ($changeRequest->mindesk_testing ?? 0)) }}"
                                   style="width: 100%; height: 40px; background: #EEF2F6; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-weight: 600;">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 22px;">
                    <label style="font-size: 0.78rem; font-weight: 700; margin-bottom: 6px; display: block;">CR Notes</label>
                    <textarea name="catatan_analisis" rows="4" style="width: 100%; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 12px;">{{ $changeRequest->catatan_analisis }}</textarea>
                </div>

                {{-- TOGGLE SOLUTION PAPER (MANDATORY FILE KETIKA YA) --}}
                <div style="display: grid; grid-template-columns: auto 1fr; gap: 32px; align-items: start; margin-bottom: 30px;">
                    <div>
                        <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 8px; display: block;">Butuh Solution Paper?*</label>
                        <input type="hidden" id="input_sp_required" name="solution_paper_required" value="{{ $changeRequest->solution_paper_required ?? 1 }}">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <button type="button" id="btn_sp_ya" onclick="toggleSolutionPaper(1)"
                                    style="background: #E0F2FE; border: 1.5px solid #0098EA; border-radius: 8px; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: #0284C7; font-weight: 700; font-size: 0.82rem;">
                                <span id="dot_sp_ya" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span>
                                </span>
                                Ya
                            </button>
                            <button type="button" id="btn_sp_tidak" onclick="toggleSolutionPaper(0)"
                                    style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: #64748B; font-weight: 600; font-size: 0.82rem;">
                                <span id="dot_sp_tidak" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid #CBD5E1; display: inline-block;"></span>
                                Tidak
                            </button>
                        </div>
                    </div>

                    <div id="upload_sp_container" style="display: {{ ($changeRequest->solution_paper_required ?? 1) == 1 ? 'block' : 'none' }};">
                        <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 8px; display: block;">Upload Solution Paper (PDF)*</label>
                        <div id="sp_upload_box" style="display: flex; align-items: center; background: #FFFFFF; border: 1.5px solid #38BDF8; border-radius: 8px; height: 40px; overflow: hidden; width: 100%; max-width: 540px; transition: all 0.2s ease;">
                            <input type="file" id="sp_file_input" name="solution_paper_file" accept=".pdf,application/pdf" style="display: none;" onchange="handleSolutionPaperFile(this)">
                            <button type="button" onclick="document.getElementById('sp_file_input').click()"
                                    style="background: #0B3C70; color: #FFFFFF; font-size: 0.78rem; font-weight: 700; height: 100%; padding: 0 16px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; flex-shrink: 0;">
                                <i class="bi bi-cloud-arrow-up-fill"></i> Pilih File
                            </button>
                            <span id="sp_file_name" style="padding: 0 16px; color: {{ !empty($changeRequest->solution_paper_file) ? '#0F172A' : '#94A3B8' }}; font-size: 0.78rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">
                                @if(!empty($changeRequest->solution_paper_file))
                                    File tersimpan: {{ basename($changeRequest->solution_paper_file) }}
                                @else
                                    Tidak ada file yang dipilih
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="submit" name="action" value="approve" style="background: #10B981; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-weight: 700; cursor: pointer;">
                        Verifikasi CR
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
        TAB PANEL 3: STATUS CR (UPDATE STATUS & BERITA ACARA SEPERTI GAMBAR)
    ══════════════════════════════════════════════════════════════ --}}
    @if($isStatusCrUnlocked)
    <div id="tabPanel-status" class="{{ $activeTab !== 'status' ? 'd-none' : '' }}">
        <div style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; padding: 26px 28px;">
            <form method="POST" action="{{ route('change-requests.update-status', $changeRequest) }}" enctype="multipart/form-data">
                @csrf

                <div class="cr-grid-status-ba">
                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 22px 24px;">
                        <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin-bottom: 16px;">UPDATE STATUS CR</div>
                        <div style="margin-bottom: 18px;">
                            <label style="font-size: 0.72rem; font-weight: 800; color: #64748B; text-transform: uppercase; margin-bottom: 8px; display: block;">Status Baru</label>
                            @php
                                $subStatusOptions = [
                                    ['value' => 'development', 'label' => 'Develop', 'step' => 1],
                                    ['value' => 'sit', 'label' => 'SIT', 'step' => 2],
                                    ['value' => 'uat', 'label' => 'UAT', 'step' => 3],
                                    ['value' => 'training', 'label' => 'Training', 'step' => 4],
                                    ['value' => 'golive', 'label' => 'GO-LIVE', 'step' => 5],
                                ];
                            @endphp
                            <select name="status" style="width: 100%; height: 42px; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; padding: 0 14px; font-weight: 600;">
                                @foreach($subStatusOptions as $opt)
                                    <option value="{{ $opt['value'] }}" 
                                            @selected($pmStatusStep === $opt['step'])
                                            @if($opt['step'] < $pmStatusStep) disabled style="color: #94A3B8; background: #F1F5F9;" @endif>
                                        {{ $opt['label'] }} @if($opt['step'] < $pmStatusStep) (Selesai - Tidak dapat mundur) @elseif($opt['step'] === $pmStatusStep) (Status Saat Ini) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div style="font-size: 0.73rem; color: #64748B; margin-top: 6px;">
                                <i class="bi bi-info-circle me-1 text-primary"></i>Alur status pengerjaan bersifat progresif maju dan tidak dapat dimundurkan ke tahap sebelumnya.
                            </div>
                        </div>
                    </div>

                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 22px 24px; display: flex; flex-direction: column;">
                        <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin-bottom: 16px;">UPDATE BERITA ACARA</div>
                        <input type="file" id="ba_file_input" name="berita_acara_file" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleBaStatusFile(this)">
                        <div onclick="document.getElementById('ba_file_input').click()"
                             style="border: 1.5px dashed #93C5FD; background: #FFFFFF; border-radius: 10px; height: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-cloud-arrow-up fs-4 text-primary mb-1"></i>
                            <span id="ba_file_status_label" style="font-size: 0.80rem; color: #64748B; font-weight: 500;">
                                {{ $changeRequest->berita_acara_file ? basename($changeRequest->berita_acara_file) : 'Klik untuk upload PDF' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: #10B981; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-weight: 700; cursor: pointer;">
                        Update Status CR
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

</div>

<script>
    function handleMandaysFocus(el) {
        if (el.value === '0') {
            setTimeout(function() {
                try { el.select(); } catch(e) {}
            }, 10);
        }
    }

    function handleMandaysInput(el) {
        var raw = el.value.replace(/[^0-9]/g, '');
        if (raw.length > 1 && raw.startsWith('0')) {
            raw = String(parseInt(raw, 10));
        }
        el.value = raw;
        calcVerifikasiTotal();
    }

    function handleMandaysBlur(el) {
        if (el.value === '' || isNaN(parseInt(el.value, 10))) {
            el.value = '0';
        } else {
            el.value = String(parseInt(el.value, 10));
        }
        calcVerifikasiTotal();
    }

    function calcVerifikasiTotal() {
        ['v_analisis', 'v_dev', 'v_test'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.value !== '') {
                el.value = el.value.replace(/[^0-9]/g, '');
            }
        });
        var a = parseInt(document.getElementById('v_analisis')?.value || 0, 10) || 0;
        var d = parseInt(document.getElementById('v_dev')?.value || 0, 10) || 0;
        var t = parseInt(document.getElementById('v_test')?.value || 0, 10) || 0;
        var tot = a + d + t;
        var totInput = document.getElementById('v_total');
        if (totInput) totInput.value = tot;
    }

    function toggleSolutionPaper(val) {
        var input = document.getElementById('input_sp_required');
        var btnYa = document.getElementById('btn_sp_ya');
        var btnTidak = document.getElementById('btn_sp_tidak');
        var dotYa = document.getElementById('dot_sp_ya');
        var dotTidak = document.getElementById('dot_sp_tidak');
        var uploadContainer = document.getElementById('upload_sp_container');

        if (input) input.value = val;

        if (val === 1) {
            if (uploadContainer) uploadContainer.style.display = 'block';
            if (btnYa) {
                btnYa.style.background = '#E0F2FE';
                btnYa.style.borderColor = '#0098EA';
                btnYa.style.color = '#0284C7';
                btnYa.style.fontWeight = '700';
            }
            if (dotYa) {
                dotYa.innerHTML = '<span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span>';
                dotYa.style.borderColor = '#0098EA';
            }
            if (btnTidak) {
                btnTidak.style.background = '#FFFFFF';
                btnTidak.style.borderColor = '#CBD5E1';
                btnTidak.style.color = '#64748B';
                btnTidak.style.fontWeight = '600';
            }
            if (dotTidak) {
                dotTidak.innerHTML = '';
                dotTidak.style.borderColor = '#CBD5E1';
            }
        } else {
            if (uploadContainer) uploadContainer.style.display = 'none';
            if (btnYa) {
                btnYa.style.background = '#FFFFFF';
                btnYa.style.borderColor = '#CBD5E1';
                btnYa.style.color = '#64748B';
                btnYa.style.fontWeight = '600';
            }
            if (dotYa) {
                dotYa.innerHTML = '';
                dotYa.style.borderColor = '#CBD5E1';
            }
            if (btnTidak) {
                btnTidak.style.background = '#E0F2FE';
                btnTidak.style.borderColor = '#0098EA';
                btnTidak.style.color = '#0284C7';
                btnTidak.style.fontWeight = '700';
            }
            if (dotTidak) {
                dotTidak.innerHTML = '<span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span>';
                dotTidak.style.borderColor = '#0098EA';
            }
        }
    }

    function handleSolutionPaperFile(input) {
        var fileNameSpan = document.getElementById('sp_file_name');
        var uploadBox = document.getElementById('sp_upload_box');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var ext = file.name.split('.').pop().toLowerCase();
            if (ext !== 'pdf') {
                alert('Format dokumen tidak didukung!\nSistem hanya menerima file Solution Paper berformat PDF (.pdf).\nDokumen selain PDF tidak diperbolehkan.');
                input.value = '';
                if (fileNameSpan) {
                    fileNameSpan.textContent = 'Tidak ada file yang dipilih';
                    fileNameSpan.style.color = '#94A3B8';
                }
                if (uploadBox) uploadBox.style.borderColor = '#EF4444';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file melebihi batas maksimal 5 MB!');
                input.value = '';
                if (fileNameSpan) {
                    fileNameSpan.textContent = 'Tidak ada file yang dipilih';
                    fileNameSpan.style.color = '#94A3B8';
                }
                if (uploadBox) uploadBox.style.borderColor = '#EF4444';
                return;
            }
            if (fileNameSpan) {
                fileNameSpan.textContent = file.name;
                fileNameSpan.style.color = '#0F172A';
            }
            if (uploadBox) uploadBox.style.borderColor = '#10B981';
        }
    }

    function validateVerifikasiCr(event) {
        var a = parseInt(document.getElementById('v_analisis')?.value || 0, 10) || 0;
        var d = parseInt(document.getElementById('v_dev')?.value || 0, 10) || 0;
        var t = parseInt(document.getElementById('v_test')?.value || 0, 10) || 0;

        if (a < 0 || d < 0 || t < 0) {
            if (event) event.preventDefault();
            alert('Nilai estimasi mandays tidak boleh negatif!');
            return false;
        }

        var isSpRequired = document.getElementById('input_sp_required')?.value === '1';
        var fileInput = document.getElementById('sp_file_input');
        var hasExistingFile = {{ (!empty($changeRequest->solution_paper_file) || !empty($changeRequest->solution_paper_url)) ? 'true' : 'false' }};
        var uploadBox = document.getElementById('sp_upload_box');

        if (isSpRequired && !hasExistingFile && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
            if (event) event.preventDefault();
            var errorMsg = 'Gagal verifikasi CR!\n\nDokumen Solution Paper (PDF) wajib diunggah karena opsi "Butuh Solution Paper" dipilih YA.\nSilakan klik tombol "Pilih File" terlebih dahulu.';
            alert(errorMsg);
            if (typeof window.showAppToast === 'function') {
                window.showAppToast('Dokumen Solution Paper (PDF) wajib diunggah!', false);
            }
            if (uploadBox) {
                uploadBox.style.borderColor = '#EF4444';
                uploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        return true;
    }

    function handleBaStatusFile(input) {
        var label = document.getElementById('ba_file_status_label');
        if (input.files && input.files[0] && label) {
            label.textContent = input.files[0].name;
            label.style.color = '#0F172A';
        }
    }

    function notifyStatusLocked() {
        alert('Halaman belum bisa dibuka, menunggu quotation.');
        if (typeof window.showAppToast === 'function') {
            window.showAppToast('Halaman belum bisa dibuka, menunggu quotation.', false);
        }
    }

    function switchDetailTab(tabName) {
        if (tabName === 'verifikasi' && {{ $isVerificationLocked ? 'true' : 'false' }}) {
            alert('Verifikasi CR telah selesai dan terkunci.');
            return;
        }

        if (tabName === 'status' && {{ !$isStatusCrUnlocked ? 'true' : 'false' }}) {
            notifyStatusLocked();
            return;
        }

        var tabs = ['detail', 'verifikasi', 'status'];
        tabs.forEach(function(t) {
            var btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
            var panel = document.getElementById('tabPanel-' + t);
            if (btn && panel) {
                if (t === tabName) {
                    btn.classList.add('active');
                    panel.classList.remove('d-none');
                } else {
                    btn.classList.remove('active');
                    panel.classList.add('d-none');
                }
            }
        });

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        let initialTab = '{{ $activeTab }}';
        const url = new URL(window.location.href);
        const q = url.searchParams.get('tab');
        if (['detail', 'verifikasi', 'status'].includes(q)) {
            if (q === 'status' && {{ !$isStatusCrUnlocked ? 'true' : 'false' }}) {
                initialTab = 'detail';
            } else if (q === 'verifikasi' && {{ $isVerificationLocked ? 'true' : 'false' }}) {
                initialTab = 'detail';
            } else {
                initialTab = q;
            }
        }
        switchDetailTab(initialTab);
    });
</script>
@endsection