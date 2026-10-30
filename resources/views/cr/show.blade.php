@extends('layouts.app')

@section('title', 'Detail Change Request · ' . ($changeRequest->kode_cr ?: 'CR-2026-06-0042'))

@section('content')
@php
    $isClientUser = (!empty($isClient) || (auth()->check() && auth()->user()->isClient()) || in_array(request('role'), ['client', 'klien']) || in_array(request('view'), ['client', 'klien'])) && request('role') !== 'pm';
    $statusKey = $changeRequest->status;

    // ══ Workflow & Stepper Mapping – baca status aktual untuk semua role termasuk client ══
    $mainStageNum = 2;
    $progressPct = 17;

    // Status mapping berlaku untuk semua role (termasuk client)
    if ($statusKey === 'diajukan' || in_array($statusKey, ['awaiting_pm', 'dianalisis', 'analisa', 'draft'])) {
        $mainStageNum = $isClientUser ? 1 : 2; // Client: step 1 (Diajukan aktif), PM: step 2 (Review PM)
        $progressPct = 17;
    } elseif (in_array($statusKey, ['awaiting_pmh', 'revision_needed'])) {
        $mainStageNum = 3;
        $progressPct = 35;
    } elseif (in_array($statusKey, ['validated', 'disetujui'])) {
        $mainStageNum = 4;
        $progressPct = 50;
    } elseif (in_array($statusKey, ['development', 'dikerjakan', 'sit', 'uat', 'training'])) {
        $mainStageNum = 5;
        $progressPct = 75;
    } elseif (in_array($statusKey, ['awaiting_golive_validation', 'golive', 'invoicing', 'selesai'])) {
        $mainStageNum = 6;
        $progressPct = 100;
    }

    // Step 5 & 10: Tab Status CR baru terbuka jika CR sudah disetujui PM Head dan quotation selesai (status development dsb)
    $isStatusCrUnlocked = in_array($statusKey, ['development', 'dikerjakan', 'sit', 'uat', 'training', 'awaiting_golive_validation', 'golive', 'invoicing', 'selesai'])
        || ($changeRequest->harga_penawaran !== null && $changeRequest->isPmhApproved());

    // Tab Verifikasi CR di-disable / dikunci ketika presale sudah melakukan payment / quotation
    $isVerifikasiCrDisabled = $changeRequest->isPresalesPaymentDone();

    $crJudul = $changeRequest->judul ?: ('Change Request #' . $changeRequest->id);
    $crKode = $changeRequest->kode_cr ?: ('CR-' . str_pad($changeRequest->id, 4, '0', STR_PAD_LEFT));
    $firstChar = strtoupper(substr($crJudul, 0, 1)) ?: 'C';

    $modalCrCodeJudul = $crKode . ': ' . $crJudul;
    $modalPemohon = $changeRequest->owner_cr ?: ($changeRequest->user?->name ?: ($changeRequest->client?->name ?: '-'));

    $companyName = $changeRequest->client?->company ?: ($changeRequest->klien ?: '-');
    $clientInitial = $changeRequest->client?->nickname ?: ($changeRequest->klien ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $changeRequest->klien), 0, 2)) : '-');
    $picName = $changeRequest->owner_cr ?: ($changeRequest->user?->name ?: '-');
    $projectName = $changeRequest->proyek_terkait ?: '-';
    $crOwner = $changeRequest->pic_sales ?: ($changeRequest->main_desk ?: ($changeRequest->nama_pm ?: '-'));
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

    $pctAnalisis = $totalMandays > 0 ? round(($mandaysAnalisis / $totalMandays) * 100) : 0;
    $pctDev = $totalMandays > 0 ? round(($mandaysDevelopment / $totalMandays) * 100) : 0;
    $pctTest = $totalMandays > 0 ? round(($mandaysTesting / $totalMandays) * 100) : 0;

    $catatanPengajuan = $changeRequest->catatan_pengajuan ?: ($changeRequest->pesan_client ?: '-');
    
    // Status Display Badge – semua status ditampilkan dengan label yang akurat
    $statusDisplay = match($statusKey) {
        'diajukan'                  => 'Diajukan',
        'awaiting_pm'               => 'Menunggu Review PM',
        'analisa', 'dianalisis'     => 'Sedang Dianalisa',
        'awaiting_pmh'              => 'Menunggu Approval PM Head',
        'revision_needed'           => 'Perlu Revisi',
        'validated', 'disetujui'    => 'Disetujui PM Head',
        'dikerjakan', 'development' => 'Sedang Dikerjakan',
        'sit'                       => 'SIT (System Integration Test)',
        'uat'                       => 'UAT (User Acceptance Test)',
        'training'                  => 'Training',
        'awaiting_golive_validation'=> 'Menunggu Validasi Go-Live',
        'golive'                    => 'Go-Live',
        'selesai', 'invoicing'      => 'Selesai / Invoicing',
        'ditolak'                   => 'Ditolak',
        'draft'                     => 'Draft',
        default                     => ucfirst(str_replace('_', ' ', $statusKey)),
    };

    // Stepper mapping for Status CR (Figma PM Update: 6 Steps: Analisa -> Develop -> SIT -> UAT -> Training -> Go Live)
    $pmStatusStep = 1;
    $pmStatusPct = '0%';
    $pmStatusLabel = 'Analisa';
    $pmStatusLineWidth = '0%';

    if ($statusKey === 'diajukan' || in_array($statusKey, ['awaiting_pm', 'dianalisis', 'analisa', 'draft', 'revision_needed', 'awaiting_pmh', 'validated', 'disetujui'])) {
        $pmStatusStep = 1;
        $pmStatusPct = '0%';
        $pmStatusLabel = 'Analisa';
        $pmStatusLineWidth = '0%';
    } elseif (in_array($statusKey, ['development', 'dikerjakan'])) {
        $pmStatusStep = 2;
        $pmStatusPct = '20%';
        $pmStatusLabel = 'Develop';
        $pmStatusLineWidth = '20%';
    } elseif ($statusKey === 'sit') {
        $pmStatusStep = 3;
        $pmStatusPct = '40%';
        $pmStatusLabel = 'SIT';
        $pmStatusLineWidth = '40%';
    } elseif ($statusKey === 'uat') {
        $pmStatusStep = 4;
        $pmStatusPct = '60%';
        $pmStatusLabel = 'UAT';
        $pmStatusLineWidth = '60%';
    } elseif ($statusKey === 'training') {
        $pmStatusStep = 5;
        $pmStatusPct = '80%';
        $pmStatusLabel = 'Pelatihan';
        $pmStatusLineWidth = '80%';
    } elseif (in_array($statusKey, ['awaiting_golive_validation', 'golive', 'selesai', 'invoicing'])) {
        $pmStatusStep = 6;
        $pmStatusPct = '100%';
        $pmStatusLabel = 'Go Live';
        $pmStatusLineWidth = '100%';
    } elseif ($statusKey === 'ditolak') {
        $pmStatusStep = 1;
        $pmStatusPct = '0%';
        $pmStatusLabel = 'Ditolak';
        $pmStatusLineWidth = '0%';
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
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.96) translateY(-8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>

<div style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0F172A; width: 100%; box-sizing: border-box; padding-bottom: 3.5rem;">

    {{-- ══ 1. HEADER JUDUL HALAMAN ══ --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: #0F172A; letter-spacing: -0.02em; margin: 0 0 3px; line-height: 1.2;" data-i18n="detail_cr_title">
                {{ $statusKey === 'draft' ? 'Detail Draft Change Request' : 'Detail Change Request' }}
            </h1>
            <p style="font-size: 0.82rem; color: #64748B; margin: 0; font-weight: 400;" data-i18n="detail_cr_subtitle">
                {{ $statusKey === 'draft' ? 'Draft Change Request belum diajukan ke Project Manager' : 'Melihat Data Lengkap CR' }}
            </p>
        </div>
        @if($statusKey === 'draft' && (auth()->user()?->isClient() || auth()->user()?->isAdmin()))
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('change-requests.edit', $changeRequest) }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5"
               style="background: #F59E0B; border-radius: 8px; font-size: 0.82rem; padding: 7px 16px; border: none; text-decoration: none;">
                <i class="bi bi-pencil-square"></i> Edit Draft
            </a>
            <form method="POST" action="{{ route('change-requests.destroy', $changeRequest) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft ini?');" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5"
                        style="background: #EF4444; border-radius: 8px; font-size: 0.82rem; padding: 7px 16px; border: none;">
                    <i class="bi bi-trash"></i> Hapus Draft
                </button>
            </form>
        </div>
        @endif
    </div>
    <div style="height: 1.5px; background: #E2E8F0; margin: 0 0 20px;"></div>

    {{-- ══ 2. 3-TAB SEGMENTED CONTROL (FOR PM & INTERNAL ROLES) ══ --}}
    @if(!$isClientUser)
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; width: 100%; border: 1.5px solid #CBD5E1; border-radius: 8px; overflow: hidden; background: #EEF2F6; margin-bottom: 22px; box-sizing: border-box;">
        <button type="button" id="tabBtn-detail" onclick="switchDetailTab('detail')"
                style="display: flex; align-items: center; justify-content: center; height: 42px; font-weight: 700; font-size: 0.88rem; border: none; cursor: pointer; transition: all 0.15s ease; {{ $activeTab === 'detail' ? 'background: #0070D2; color: #FFFFFF;' : 'background: #EEF2F6; color: #1E293B;' }}">
            Detail Change Request
        </button>
        <button type="button" id="tabBtn-verifikasi" onclick="switchDetailTab('verifikasi')"
                style="display: flex; align-items: center; justify-content: center; gap: 6px; height: 42px; font-weight: 600; font-size: 0.88rem; border: none; border-left: 1.5px solid #CBD5E1; cursor: pointer; transition: all 0.15s ease; {{ $activeTab === 'verifikasi' ? 'background: #0070D2; color: #FFFFFF;' : 'background: #EEF2F6; color: #1E293B;' }}">
            @if($isVerifikasiCrDisabled)
                <i class="bi bi-lock-fill" style="font-size: 0.84rem; opacity: 0.85;"></i>
                <span>Verifikasi CR <small style="font-weight: 700; opacity: 0.85;">(Terkunci)</small></span>
            @else
                <span>Verifikasi CR</span>
            @endif
        </button>
        <button type="button" id="tabBtn-status" onclick="switchDetailTab('status')"
                style="display: flex; align-items: center; justify-content: center; gap: 6px; height: 42px; font-weight: 600; font-size: 0.88rem; border: none; border-left: 1.5px solid #CBD5E1; cursor: pointer; transition: all 0.15s ease; {{ $activeTab === 'status' ? 'background: #0070D2; color: #FFFFFF;' : 'background: #EEF2F6; color: #1E293B;' }}">
            @if(!$isStatusCrUnlocked)
                <i class="bi bi-lock-fill" style="color: #EF4444; font-size: 0.84rem;"></i>
                <span>Status CR <small style="color: #EF4444; font-weight: 700;">(Terkunci)</small></span>
            @else
                <span>Status CR</span>
            @endif
        </button>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         TAB PANEL 1: DETAIL CHANGE REQUEST (FIGMA & USER SCREENSHOT EXACT)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="tabPanel-detail" class="{{ (!$isClientUser && $activeTab !== 'detail') ? 'd-none' : '' }}">

        {{-- Outer Gray Box Container --}}
        <div style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; padding: 24px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); width: 100%; box-sizing: border-box;">

            {{-- ── 1. Top Identity Row (Squircle + Title + Badge) ── --}}
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #0098EA; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; flex-shrink: 0;">
                    {{ $firstChar }}
                </div>
                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.01em;">
                        {{ $crJudul }}
                    </h2>
                    @php
                        $stKeyLower = strtolower($changeRequest->status ?? '');
                        $stageBadge = match($stKeyLower) {
                            'diajukan', 'awaiting_pm', 'analisa', 'dianalisis', 'awaiting_pmh' => '• Analisa',
                            'validated', 'disetujui' => '• Disetujui',
                            'development', 'dikerjakan' => '• Develop',
                            'sit' => '• SIT',
                            'uat' => '• UAT',
                            'training' => '• Pelatihan',
                            'golive', 'selesai' => '• Go-Live',
                            'invoicing' => '• Tagihan',
                            'ditolak' => '• Ditolak',
                            default => '• ' . $statusDisplay,
                        };
                    @endphp
                    <span style="background: #0098EA; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                        {{ $stageBadge }}
                    </span>
                </div>
            </div>

            {{-- ── 2. Metadata Sub-bar: ID | Menunggu Review | Tanggal Pengajuan ── --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; width: 100%; box-sizing: border-box;">
                <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
                    <span style="font-size: 0.78rem; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; display: inline-flex; align-items: center; gap: 4px;">
                        <span style="color: #475569; font-weight: 700;">ID:</span>
                        <span style="color: #0F172A; font-weight: 800;">{{ $crKode }}</span>
                    </span>
                    @if($statusKey === 'ditolak')
                    <span style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; font-size: 0.74rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <span>{{ $statusDisplay }}</span>
                    </span>
                    @elseif(in_array($statusKey, ['disetujui', 'validated', 'selesai', 'golive']))
                    <span style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A; font-size: 0.74rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A; display: inline-block;"></span>
                        <span>{{ $statusDisplay }}</span>
                    </span>
                    @else
                    <span style="background: #FFFBEB; border: 1px solid #FDE68A; color: #B45309; font-size: 0.74rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 6px; line-height: 1.2;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                        <span>{{ $statusDisplay }}</span>
                    </span>
                    @endif
                </div>

                {{-- Tanggal Pengajuan Far-Right --}}
                <div style="display: flex; align-items: center; gap: 4px; font-size: 0.78rem;">
                    <i class="bi bi-calendar4" style="color: #94A3B8; font-size: 0.82rem; margin-right: 4px;"></i>
                    <span style="color: #64748B; font-weight: 500;">Tanggal Pengajuan:</span>
                    <strong style="color: #0F172A; font-weight: 800; margin-left: 4px;">{{ $headerTanggalPengajuan }}</strong>
                </div>
            </div>

            {{-- ── 3. STATUS ALUR CR Card (6-Card Flow with Continuous Progress Line) ── --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px 20px; margin-bottom: 18px; width: 100%; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.74rem; font-weight: 800; color: #334155; letter-spacing: 0.06em; text-transform: uppercase;">STATUS ALUR CR</span>
                    <span style="font-size: 0.72rem; color: #64748B; font-weight: 500;">Total Progres Alur: {{ $progressPct }}%</span>
                </div>

                {{-- Continuous Progress Track --}}
                <div style="height: 5px; background: #E2E8F0; border-radius: 9999px; margin: 12px 0 16px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $progressPct }}%; background: #0098EA; border-radius: 9999px; transition: width 0.4s ease;"></div>
                </div>

                {{-- 6 Step Cards Grid matching Screenshot --}}
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; width: 100%;">
                    @foreach($stages as $sNum => $stg)
                        @php
                            $isPast = ($sNum < $mainStageNum);
                            $isCurrent = ($sNum === $mainStageNum);
                        @endphp
                        @if($isPast)
                            {{-- Selesai: Green outline + checkmark --}}
                            <div style="background: #F0FDF4; border: 1.5px solid #86EFAC; border-radius: 8px; padding: 9px 12px; display: flex; align-items: center; gap: 8px;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: #16A34A; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800; flex-shrink: 0;">
                                    <i class="bi bi-check-lg" style="font-size: 0.8rem;"></i>
                                </div>
                                <div style="overflow: hidden;">
                                    <div style="font-size: 0.74rem; font-weight: 700; color: #0F172A; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $stg['name'] }}</div>
                                    <div style="font-size: 0.68rem; font-weight: 600; color: #16A34A; line-height: 1.2; margin-top: 1px;">Selesai</div>
                                </div>
                            </div>
                        @elseif($isCurrent)
                            {{-- Sedang Berjalan: Active blue border & badge --}}
                            <div style="background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; padding: 9px 12px; display: flex; align-items: center; gap: 8px;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: #0098EA; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.74rem; font-weight: 800; flex-shrink: 0;">
                                    {{ $stg['num'] }}
                                </div>
                                <div style="overflow: hidden;">
                                    <div style="font-size: 0.74rem; font-weight: 800; color: #0F172A; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $stg['name'] }}</div>
                                    <div style="font-size: 0.68rem; font-weight: 700; color: #0098EA; line-height: 1.2; margin-top: 1px;">Sedang Berjalan</div>
                                </div>
                            </div>
                        @else
                            {{-- Menunggu: Grey placeholder --}}
                            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 9px 12px; display: flex; align-items: center; gap: 8px;">
                                <div style="width: 22px; height: 22px; border-radius: 50%; background: #E2E8F0; color: #94A3B8; display: flex; align-items: center; justify-content: center; font-size: 0.74rem; font-weight: 700; flex-shrink: 0;">
                                    {{ $stg['num'] }}
                                </div>
                                <div style="overflow: hidden;">
                                    <div style="font-size: 0.72rem; font-weight: 600; color: #475569; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $stg['name'] }}</div>
                                    <div style="font-size: 0.66rem; font-weight: 500; color: #94A3B8; line-height: 1.2; margin-top: 1px;">Menunggu</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ── 3b. PROGRESS PENGERJAAN TEKNIS (PM) – Visible for Client only when CR reaches Development (Stage 5+) ── --}}
            @php
                $showTechnicalProgress = !$isClientUser || $mainStageNum >= 5;
            @endphp
            @if($showTechnicalProgress)
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px 20px; margin-bottom: 18px; width: 100%; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.74rem; font-weight: 800; color: #334155; letter-spacing: 0.06em; text-transform: uppercase;">
                            PROGRESS PENGERJAAN TEKNIS (PM)
                        </span>
                        <span style="background: #E0F2FE; color: #0284C7; font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                            {{ $pmStatusPct }} — {{ $pmStatusLabel }}
                        </span>
                    </div>
                    <span style="font-size: 0.72rem; color: #64748B; font-weight: 500;">
                        Status Eksekusi Saat Ini: <strong style="color: #0F172A;">{{ $statusDisplay }}</strong>
                    </span>
                </div>

                {{-- Stepper 6 Steps --}}
                <div style="position: relative; margin: 22px 14px 10px;">
                    {{-- Constrained Track Container between circle 1 and circle 6 centers --}}
                    <div style="position: absolute; top: 15px; left: 30px; right: 30px; height: 3px; z-index: 1;">
                        {{-- Base Track --}}
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 100%; background: #E2E8F0;"></div>
                        {{-- Active Track --}}
                        <div style="position: absolute; top: 0; left: 0; width: {{ $pmStatusLineWidth }}; height: 100%; background: #0098EA; transition: width 0.3s ease;"></div>
                    </div>

                    <div style="display: flex; justify-content: space-between; position: relative; z-index: 3;">
                        @php
                            $techSteps = [
                                1 => 'Analisa',
                                2 => 'Develop',
                                3 => 'SIT',
                                4 => 'UAT',
                                5 => 'Pelatihan',
                                6 => 'Go Live',
                            ];
                        @endphp
                        @foreach($techSteps as $stNum => $stTitle)
                            @php
                                $isDone = $pmStatusStep > $stNum;
                                $isAct = $pmStatusStep === $stNum;
                            @endphp
                            <div style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                                <div style="width: 30px; height: 30px; border-radius: 50%; {{ $isDone ? 'background: #10B981; color: #FFFFFF;' : ($isAct ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #CBD5E1; color: #94A3B8;') }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                    @if($isDone)
                                        <i class="bi bi-check-lg" style="font-size: 0.85rem;"></i>
                                    @else
                                        {{ $stNum }}
                                    @endif
                                </div>
                                <span style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ ($isDone || $isAct) ? '700' : '500' }}; color: {{ $isAct ? '#0098EA' : ($isDone ? '#10B981' : '#94A3B8') }}; text-align: center;">
                                    {{ $stTitle }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($changeRequest->catatan_analisis)
                    <div style="margin-top: 14px; padding: 10px 14px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 0.76rem; color: #334155; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-info-circle-fill text-primary" style="font-size: 0.85rem;"></i>
                        <span><strong>Catatan Pengerjaan PM:</strong> {{ $changeRequest->catatan_analisis }}</span>
                    </div>
                @endif
            </div>
            @endif

            {{-- ── 4. ROW: INFORMASI CR (Left) & DOKUMEN (Right) ── --}}
            <div class="cr-grid-info-doc">
                {{-- Left: INFORMASI CR --}}
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="color: #94A3B8; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 18px;" data-i18n="section_info_cr">
                        INFORMASI CR
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); row-gap: 18px; column-gap: 16px; flex: 1; align-content: space-between;">
                        {{-- Row 1 --}}
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_company">NAMA PERUSAHAAN</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $companyName }}">{{ $companyName }}</div>
                        </div>
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_client_initial">INISIAL KLIEN</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $clientInitial }}</div>
                        </div>
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_pic_name">NAMA PIC</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $picName }}">{{ $picName }}</div>
                        </div>

                        {{-- Row 2 --}}
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_project_name">NAMA PROJECT</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $projectName }}">{{ $projectName }}</div>
                        </div>
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_cr_owner">CR OWNER</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $crOwner }}">{{ $crOwner }}</div>
                        </div>
                        <div>
                            {{-- Empty space matching Figma Row 2 Col 3 --}}
                        </div>

                        {{-- Row 3 --}}
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_submission_date">TANGGAL PENGAJUAN</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $tanggalPengajuan }}</div>
                        </div>
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_request_golive">REQUEST GO-LIVE</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $requestGoLive }}</div>
                        </div>
                        <div>
                            <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;" data-i18n="label_cr_priority">PRIORITAS CR</div>
                            <div style="color: #0F172A; font-size: 0.82rem; font-weight: 800;">{{ $priorityLabel }}</div>
                        </div>
                    </div>
                </div>

                {{-- Right: DOKUMEN --}}
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; height: 100%; box-sizing: border-box; display: flex; flex-direction: column;">
                    <div style="color: #94A3B8; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 18px;" data-i18n="section_documents">
                        DOKUMEN
                    </div>

                    {{-- Item 1: DOKUMEN CR --}}
                    <div style="margin-bottom: 18px;">
                        <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;" data-i18n="label_doc_cr">DOKUMEN CR</div>
                        @if($docCrUrl)
                        <a href="{{ $docCrUrl }}" target="_blank" style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 9999px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 8px; color: #0284C7; font-size: 0.72rem; font-weight: 600; text-decoration: none; max-width: 100%;">
                            <i class="bi bi-file-earmark" style="font-size: 0.85rem; color: #0284C7;"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $docCrUrl }}</span>
                        </a>
                        @elseif($changeRequest->lampiran_pengajuan)
                        <a href="{{ route('change-requests.initial-file', $changeRequest) }}" target="_blank" style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 9999px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 8px; color: #0284C7; font-size: 0.72rem; font-weight: 600; text-decoration: none; max-width: 100%;">
                            <i class="bi bi-paperclip" style="font-size: 0.85rem; color: #0284C7;"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ basename($changeRequest->lampiran_pengajuan) }}</span>
                        </a>
                        @else
                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                        @endif
                    </div>

                    {{-- Item 2: SOLUTION PAPER --}}
                    <div>
                        <div style="color: #94A3B8; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;" data-i18n="label_solution_paper">SOLUTION PAPER</div>
                        @if($solutionPaperUrl)
                        <a href="{{ $solutionPaperUrl }}" target="_blank" style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 9999px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 8px; color: #0284C7; font-size: 0.72rem; font-weight: 600; text-decoration: none; max-width: 100%;">
                            <i class="bi bi-file-earmark" style="font-size: 0.85rem; color: #0284C7;"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $solutionPaperUrl }}</span>
                        </a>
                        @elseif($changeRequest->solutionPaper?->solution_paper_file)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($changeRequest->solutionPaper->solution_paper_file) }}" target="_blank" style="background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 9999px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 8px; color: #0284C7; font-size: 0.72rem; font-weight: 600; text-decoration: none; max-width: 100%;">
                            <i class="bi bi-file-earmark" style="font-size: 0.85rem; color: #0284C7;"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ basename($changeRequest->solutionPaper->solution_paper_file) }}</span>
                        </a>
                        @else
                        <span class="text-muted" style="font-size: 0.78rem;">-</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── 5. CARD: DETAIL DESKRIPSI CR (WYSIWYG Rendered) ── --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; margin-bottom: 18px; width: 100%; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-file-earmark-text" style="color: #0070D2; font-size: 1.05rem;"></i>
                        <span style="font-weight: 800; font-size: 0.88rem; color: #0F172A;" data-i18n="section_detail_desc">
                            Detail Deskripsi CR
                        </span>
                    </div>
                    <span style="font-size: 0.70rem; color: #94A3B8; font-weight: 500;" data-i18n="rendered_wysiwyg">
                        Data Aktual CR
                    </span>
                </div>

                @php
                    $rawDesc = $changeRequest->deskripsi ?: ($changeRequest->alasan ?: '');
                    // Bersihkan jika ada teks template lama
                    $cleanedDesc = preg_replace('/(1\.\s*LATAR BELAKANG & TUJUAN|2\.\s*RUANG LINGKUP PERUBAHAN \(SCOPE OF WORK\)|3\.\s*DAMPAK TEKNIS & DEPENDENCIES)\s*/i', '', $rawDesc);
                    $cleanedDesc = trim($cleanedDesc);
                    $hasHtml = strip_tags($cleanedDesc) !== $cleanedDesc;
                @endphp

                <div style="font-size: 0.84rem; color: #334155; line-height: 1.65;">
                    @if(!empty($cleanedDesc))
                        @if($hasHtml)
                            {!! $cleanedDesc !!}
                        @else
                            {!! nl2br(e($cleanedDesc)) !!}
                        @endif
                    @else
                        <p class="text-muted small mb-0" style="font-size: 0.82rem; font-style: italic;">
                            Belum ada detail deskripsi yang diisi.
                        </p>
                    @endif
                </div>
            </div>

            {{-- ── 6. ROW: ESTIMASI MANDAYS (Left) & CR Notes / Catatan (Right) ── --}}
            <div class="cr-grid-mandays-notes">
                {{-- Left Card: ESTIMASI MANDAYS --}}
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="color: #94A3B8; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 16px;" data-i18n="section_mandays">
                        ESTIMASI MANDAYS
                    </div>

                    {{-- Analisis --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.76rem; color: #64748B;" data-i18n="mandays_analisis">Analisis</span>
                            <strong style="font-size: 0.76rem; font-weight: 800; color: #0F172A;">{{ $mandaysAnalisis }} hari</strong>
                        </div>
                        <div style="height: 4px; background: #E0F2FE; border-radius: 999px; margin: 6px 0 14px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $pctAnalisis }}%; background: #0098EA; border-radius: 999px;"></div>
                        </div>
                    </div>

                    {{-- Development --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.76rem; color: #64748B;" data-i18n="mandays_development">Development</span>
                            <strong style="font-size: 0.76rem; font-weight: 800; color: #0F172A;">{{ $mandaysDevelopment }} hari</strong>
                        </div>
                        <div style="height: 4px; background: #E0F2FE; border-radius: 999px; margin: 6px 0 14px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $pctDev }}%; background: #0098EA; border-radius: 999px;"></div>
                        </div>
                    </div>

                    {{-- Testing --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.76rem; color: #64748B;" data-i18n="mandays_testing">Testing</span>
                            <strong style="font-size: 0.76rem; font-weight: 800; color: #0F172A;">{{ $mandaysTesting }} hari</strong>
                        </div>
                        <div style="height: 4px; background: #E0F2FE; border-radius: 999px; margin: 6px 0 14px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $pctTest }}%; background: #0098EA; border-radius: 999px;"></div>
                        </div>
                    </div>

                    <div style="height: 1px; background: #F1F5F9; margin: 10px 0 12px;"></div>

                    {{-- Total --}}
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.78rem; color: #64748B; font-weight: 500;" data-i18n="mandays_total">Total</span>
                        <strong style="font-size: 0.84rem; font-weight: 800; color: #0F172A;">{{ $totalMandays }} hari kerja</strong>
                    </div>
                </div>

                {{-- Right Card: CR Notes / Catatan --}}
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 22px; height: 100%; box-sizing: border-box; display: flex; flex-direction: column;">
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 12px;">
                        <i class="bi bi-chat-left-text" style="font-size: 0.90rem; color: #0F172A;"></i>
                        <span style="font-weight: 800; font-size: 0.84rem; color: #0F172A;" data-i18n="section_cr_notes">
                            CR Notes / Catatan
                        </span>
                    </div>

                    <div style="flex: 1; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 20px 22px; color: #475569; font-size: 0.78rem; line-height: 1.6; display: flex; align-items: center;">
                        &ldquo;{{ $catatanPengajuan }}&rdquo;
                    </div>
                </div>
            </div>

        </div>{{-- /outer gray box --}}

    </div>{{-- /#tabPanel-detail --}}

    @if(!$isClientUser)
    {{-- ══════════════════════════════════════════════════════════════
         TAB PANEL 2: VERIFIKASI CR (ROLE PM SCREENSHOT EXACT)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="tabPanel-verifikasi" class="{{ $activeTab === 'verifikasi' ? '' : 'd-none' }}">
        <div style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; padding: 26px 28px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); width: 100%; box-sizing: border-box;">

            {{-- ── 1. Top Identity Row (Squircle + Title + Badge) ── --}}
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #0098EA; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; flex-shrink: 0;">
                    {{ $firstChar }}
                </div>
                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.01em;">
                        {{ $crJudul }}
                    </h2>
                    <span style="background: #0098EA; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                        • {{ $statusDisplay }}
                    </span>
                    @if($isVerifikasiCrDisabled)
                    <span style="background: #E2E8F0; color: #475569; font-size: 0.72rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="bi bi-lock-fill"></i> Verifikasi Terkunci
                    </span>
                    @endif
                </div>
            </div>

            {{-- ── Alert Banner: Verifikasi Terkunci jika Presales telah melakukan pembayaran/quotation ── --}}
            @if($isVerifikasiCrDisabled)
            <div style="background: #EFF6FF; border: 1.5px solid #BFDBFE; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #DBEAFE; color: #1D4ED8; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.15rem;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.88rem; font-weight: 700; color: #1E3A8A; margin-bottom: 2px;">
                        Form Verifikasi CR Dinonaktifkan (Read-Only)
                    </div>
                    <div style="font-size: 0.80rem; color: #3B82F6; line-height: 1.5;">
                        Proses quotation dan pembayaran/TOP oleh bagian Presales telah selesai dilakukan. Formulir verifikasi, estimasi mandays, target jadwal, dan Solution Paper saat ini telah dinonaktifkan (disable) untuk menjaga konsistensi data pengerjaan teknis.
                    </div>
                </div>
                <span style="background: #DBEAFE; color: #1E40AF; font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="bi bi-lock-fill"></i> Terkunci
                </span>
            </div>
            @endif

            {{-- ── 2. Form Verifikasi CR PM ── --}}
            <form method="POST" action="{{ route('change-requests.update', $changeRequest) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_tab" value="verifikasi">

                {{-- Section 1: Estimasi Mandays PM --}}
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #1E293B; margin-bottom: 10px; display: block;" data-i18n="lbl_mandays_pm">
                        Estimasi Mandays PM
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                        {{-- Analisis (hari)* --}}
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;" data-i18n="lbl_analisis_hari">
                                Analisis (hari)<span style="color: #EF4444; font-weight: 700; margin-left: 2px;">*</span>
                            </label>
                            <input type="number" id="v_analisis" name="mindesk_analisis" min="0" value="{{ $changeRequest->mindesk_analisis ?? 0 }}" oninput="calcVerifikasiTotal()"
                                   {{ $isVerifikasiCrDisabled ? 'disabled readonly' : '' }}
                                   style="width: 100%; height: 40px; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-size: 0.82rem; font-weight: 500; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#0F172A' }}; cursor: {{ $isVerifikasiCrDisabled ? 'not-allowed' : 'text' }}; outline: none; transition: border-color 0.15s ease;"
                                   @if(!$isVerifikasiCrDisabled) onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'" @endif>
                        </div>

                        {{-- Development (hari)* --}}
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;" data-i18n="lbl_dev_hari">
                                Development (hari)<span style="color: #EF4444; font-weight: 700; margin-left: 2px;">*</span>
                            </label>
                            <input type="number" id="v_dev" name="mindesk_development" min="0" value="{{ $changeRequest->mindesk_development ?? 0 }}" oninput="calcVerifikasiTotal()"
                                   {{ $isVerifikasiCrDisabled ? 'disabled readonly' : '' }}
                                   style="width: 100%; height: 40px; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-size: 0.82rem; font-weight: 500; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#0F172A' }}; cursor: {{ $isVerifikasiCrDisabled ? 'not-allowed' : 'text' }}; outline: none; transition: border-color 0.15s ease;"
                                   @if(!$isVerifikasiCrDisabled) onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'" @endif>
                        </div>

                        {{-- Testing (hari)* --}}
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;" data-i18n="lbl_test_hari">
                                Testing (hari)<span style="color: #EF4444; font-weight: 700; margin-left: 2px;">*</span>
                            </label>
                            <input type="number" id="v_test" name="mindesk_testing" min="0" value="{{ $changeRequest->mindesk_testing ?? 0 }}" oninput="calcVerifikasiTotal()"
                                   {{ $isVerifikasiCrDisabled ? 'disabled readonly' : '' }}
                                   style="width: 100%; height: 40px; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-size: 0.82rem; font-weight: 500; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#0F172A' }}; cursor: {{ $isVerifikasiCrDisabled ? 'not-allowed' : 'text' }}; outline: none; transition: border-color 0.15s ease;"
                                   @if(!$isVerifikasiCrDisabled) onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'" @endif>
                        </div>

                        {{-- Total Mandays --}}
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;" data-i18n="lbl_total_mandays">
                                Total Mandays
                            </label>
                            <input type="text" id="v_total" readonly value="{{ (($changeRequest->mindesk_analisis ?? 0) + ($changeRequest->mindesk_development ?? 0) + ($changeRequest->mindesk_testing ?? 0)) }}"
                                   style="width: 100%; height: 40px; background: #EEF2F6; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-size: 0.82rem; font-weight: 600; color: #334155; cursor: not-allowed; outline: none;">
                        </div>
                    </div>
                </div>

                {{-- Section 1b: Target Jadwal Pengerjaan & Go-Live (PM) --}}
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #1E293B; margin-bottom: 10px; display: block;">
                        Target Jadwal Pengerjaan &amp; Go-Live (PM)
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;">
                                Target Mulai Pengerjaan
                            </label>
                            <input type="date" name="target_start_date" value="{{ $changeRequest->target_start_date ? $changeRequest->target_start_date->format('Y-m-d') : ($changeRequest->mulai_dikerjakan_pada ? $changeRequest->mulai_dikerjakan_pada->format('Y-m-d') : date('Y-m-d')) }}"
                                   {{ $isVerifikasiCrDisabled ? 'disabled readonly' : '' }}
                                   style="width: 100%; height: 40px; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-size: 0.82rem; font-weight: 500; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#0F172A' }}; cursor: {{ $isVerifikasiCrDisabled ? 'not-allowed' : 'text' }}; outline: none; transition: border-color 0.15s ease;"
                                   @if(!$isVerifikasiCrDisabled) onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'" @endif>
                        </div>
                        <div>
                            <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;">
                                Target Selesai / Go-Live<span style="color: #EF4444; font-weight: 700; margin-left: 2px;">*</span>
                            </label>
                            <input type="date" name="target_selesai" value="{{ $changeRequest->target_selesai ? $changeRequest->target_selesai->format('Y-m-d') : date('Y-m-d', strtotime('+30 days')) }}"
                                   {{ $isVerifikasiCrDisabled ? 'disabled readonly' : '' }}
                                   style="width: 100%; height: 40px; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 14px; font-size: 0.82rem; font-weight: 500; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#0F172A' }}; cursor: {{ $isVerifikasiCrDisabled ? 'not-allowed' : 'text' }}; outline: none; transition: border-color 0.15s ease;"
                                   @if(!$isVerifikasiCrDisabled) onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'" @endif>
                        </div>
                    </div>
                </div>

                {{-- Section 2: CR Notes --}}
                <div style="margin-bottom: 22px;">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: block;" data-i18n="lbl_cr_notes">
                        CR Notes
                    </label>
                    <textarea name="catatan_analisis" rows="4" placeholder="Catatan tambahan atau konteks..."
                              {{ $isVerifikasiCrDisabled ? 'disabled readonly' : '' }}
                              style="width: 100%; height: 95px; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 12px 14px; font-size: 0.82rem; font-family: 'Plus Jakarta Sans', sans-serif; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#0F172A' }}; cursor: {{ $isVerifikasiCrDisabled ? 'not-allowed' : 'text' }}; outline: none; resize: {{ $isVerifikasiCrDisabled ? 'none' : 'vertical' }}; transition: border-color 0.15s ease;"
                              @if(!$isVerifikasiCrDisabled) onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'" @endif>{{ $changeRequest->catatan_analisis ?: $changeRequest->catatan_admin }}</textarea>
                </div>

                {{-- Section 3: Butuh Solution Paper? & Upload Solution Paper (PDF) --}}
                @php
                    $isSpRequired = (bool) ($changeRequest->solution_paper_required ?? true);
                @endphp
                <div style="display: grid; grid-template-columns: auto 1fr; gap: 32px; align-items: start; margin-bottom: 40px;">
                    {{-- Left: Butuh Solution Paper?* --}}
                    <div>
                        <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 8px; display: block;" data-i18n="lbl_need_sp">
                            Butuh Solution Paper?<span style="color: #EF4444; font-weight: 700; margin-left: 2px;">*</span>
                        </label>
                        <input type="hidden" id="input_sp_required" name="solution_paper_required" value="{{ $isSpRequired ? 1 : 0 }}">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            {{-- Option Ya --}}
                            <button type="button" id="btn_sp_ya" @if($isVerifikasiCrDisabled) disabled style="background: {{ $isSpRequired ? '#E0F2FE' : '#FFFFFF' }}; border: 1.5px solid {{ $isSpRequired ? '#0098EA' : '#CBD5E1' }}; border-radius: 8px; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: not-allowed; color: {{ $isSpRequired ? '#0284C7' : '#64748B' }}; font-weight: {{ $isSpRequired ? '700' : '600' }}; font-size: 0.82rem; opacity: 0.65; pointer-events: none;" @else onclick="toggleSolutionPaper(1)" style="background: {{ $isSpRequired ? '#E0F2FE' : '#FFFFFF' }}; border: 1.5px solid {{ $isSpRequired ? '#0098EA' : '#CBD5E1' }}; border-radius: 8px; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: {{ $isSpRequired ? '#0284C7' : '#64748B' }}; font-weight: {{ $isSpRequired ? '700' : '600' }}; font-size: 0.82rem; transition: all 0.15s ease;" @endif>
                                <span id="dot_sp_ya" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid {{ $isSpRequired ? '#0098EA' : '#CBD5E1' }}; display: inline-flex; align-items: center; justify-content: center;">
                                    @if($isSpRequired)<span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span>@endif
                                </span>
                                Ya
                            </button>

                            {{-- Option Tidak --}}
                            <button type="button" id="btn_sp_tidak" @if($isVerifikasiCrDisabled) disabled style="background: {{ !$isSpRequired ? '#E0F2FE' : '#FFFFFF' }}; border: 1.5px solid {{ !$isSpRequired ? '#0098EA' : '#CBD5E1' }}; border-radius: 8px; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: not-allowed; color: {{ !$isSpRequired ? '#0284C7' : '#64748B' }}; font-weight: {{ !$isSpRequired ? '700' : '600' }}; font-size: 0.82rem; opacity: 0.65; pointer-events: none;" @else onclick="toggleSolutionPaper(0)" style="background: {{ !$isSpRequired ? '#E0F2FE' : '#FFFFFF' }}; border: 1.5px solid {{ !$isSpRequired ? '#0098EA' : '#CBD5E1' }}; border-radius: 8px; padding: 7px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: {{ !$isSpRequired ? '#0284C7' : '#64748B' }}; font-weight: {{ !$isSpRequired ? '700' : '600' }}; font-size: 0.82rem; transition: all 0.15s ease;" @endif>
                                <span id="dot_sp_tidak" style="width: 16px; height: 16px; border-radius: 50%; border: 2px solid {{ !$isSpRequired ? '#0098EA' : '#CBD5E1' }}; display: inline-flex; align-items: center; justify-content: center;">
                                    @if(!$isSpRequired)<span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span>@endif
                                </span>
                                Tidak
                            </button>
                        </div>
                    </div>

                    {{-- Right: Upload Solution Paper (PDF)* --}}
                    <div id="sp_upload_container" style="{{ $isSpRequired ? '' : 'display: none;' }}">
                        <label style="font-size: 0.74rem; font-weight: 700; color: #0F172A; margin-bottom: 8px; display: block;" data-i18n="lbl_upload_sp">
                            Upload Solution Paper (PDF)<span style="color: #EF4444; font-weight: 700; margin-left: 2px;">*</span>
                        </label>
                        <div style="display: flex; align-items: center; background: {{ $isVerifikasiCrDisabled ? '#EEF2F6' : '#FFFFFF' }}; border: 1.5px solid {{ $isVerifikasiCrDisabled ? '#CBD5E1' : '#38BDF8' }}; border-radius: 8px; height: 40px; overflow: hidden; width: 100%; max-width: 540px;">
                            <input type="file" id="sp_file_input" name="solution_paper_file" accept=".pdf,application/pdf" style="display: none;" @if(!$isVerifikasiCrDisabled) onchange="handleSolutionPaperFile(this)" @endif {{ $isVerifikasiCrDisabled ? 'disabled' : '' }}>
                            @if($isVerifikasiCrDisabled)
                            <button type="button" disabled
                                    style="background: #94A3B8; color: #FFFFFF; font-size: 0.78rem; font-weight: 700; height: 100%; padding: 0 16px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: not-allowed; flex-shrink: 0; opacity: 0.75;">
                                <i class="bi bi-lock-fill" style="font-size: 0.95rem;"></i>
                                Terkunci
                            </button>
                            @else
                            <button type="button" onclick="document.getElementById('sp_file_input').click()"
                                    style="background: #0B3C70; color: #FFFFFF; font-size: 0.78rem; font-weight: 700; height: 100%; padding: 0 16px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; flex-shrink: 0; transition: background 0.15s ease;"
                                    onmouseover="this.style.background='#082E55'" onmouseout="this.style.background='#0B3C70'">
                                <i class="bi bi-cloud-arrow-up-fill" style="font-size: 0.95rem;"></i>
                                Pilih File
                            </button>
                            @endif
                            <span id="sp_file_name" style="padding: 0 16px; color: {{ $isVerifikasiCrDisabled ? '#64748B' : '#94A3B8' }}; font-size: 0.78rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">
                                {{ $changeRequest->solutionPaper?->solution_paper_file ? basename($changeRequest->solutionPaper->solution_paper_file) : ($changeRequest->solutionPaper?->file_path ? basename($changeRequest->solutionPaper->file_path) : 'Tidak ada file yang dipilih') }}
                            </span>
                        </div>
                        @if($solutionPaperUrl)
                        <div style="margin-top: 6px;">
                            <a href="{{ $solutionPaperUrl }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.74rem; font-weight: 600; color: #0070D2; text-decoration: none;">
                                <i class="bi bi-box-arrow-up-right"></i> Lihat Solution Paper Terunggah
                            </a>
                        </div>
                        @elseif($changeRequest->solutionPaper?->solution_paper_file)
                        <div style="margin-top: 6px;">
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($changeRequest->solutionPaper->solution_paper_file) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.74rem; font-weight: 600; color: #0070D2; text-decoration: none;">
                                <i class="bi bi-file-earmark-pdf-fill" style="color: #EF4444;"></i> Unduh Berkas Solution Paper
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Section 4: Bottom Action Buttons (Far Right) --}}
                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 40px;">
                    @if($isVerifikasiCrDisabled)
                        <button type="button" disabled
                                style="background: #94A3B8; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: not-allowed; opacity: 0.65;">
                            <i class="bi bi-x-lg" style="font-size: 0.82rem;"></i>
                            Tolak (Nonaktif)
                        </button>
                        <button type="button" disabled
                                style="background: #94A3B8; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: not-allowed; opacity: 0.65;">
                            <i class="bi bi-lock-fill" style="font-size: 0.95rem;"></i>
                            Verifikasi CR (Terkunci)
                        </button>
                    @else
                        {{-- ✕ Tolak --}}
                        <button type="button" onclick="openRejectModal()"
                                style="background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.15s ease;"
                                onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">
                            <i class="bi bi-x-lg" style="font-size: 0.82rem;"></i>
                            Tolak
                        </button>

                        {{-- ✓ Verifikasi CR --}}
                        <button type="submit" name="action" value="approve"
                                style="background: #10B981; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.15s ease;"
                                onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                            <i class="bi bi-check-lg" style="font-size: 0.95rem;"></i>
                            Verifikasi CR
                        </button>
                    @endif
                </div>
            </form>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: TOLAK VERIFIKASI CR (ROLE PM SCREENSHOT EXACT)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="modalRejectCr" style="display: {{ request('reject') ? 'flex' : 'none' }}; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(2px); align-items: center; justify-content: center; padding: 16px;">
        <div style="background: #FFFFFF; border-radius: 16px; width: 100%; max-width: 580px; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25); padding: 26px 28px; box-sizing: border-box; position: relative; animation: modalFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
            
            {{-- Header: Warning Icon + Title/Subtitle + Close button --}}
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 20px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 4px 0; line-height: 1.3; font-family: 'Plus Jakarta Sans', sans-serif;">
                            Tolak Verifikasi Change Request
                        </h3>
                        <p style="font-size: 0.78rem; font-weight: 500; color: #64748B; margin: 0; line-height: 1.4;">
                            Tindakan ini akan mengembalikan status CR ke pemohon untuk revisi.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeRejectModal()" aria-label="Tutup"
                        style="background: transparent; border: none; color: #94A3B8; font-size: 1.25rem; line-height: 1; cursor: pointer; padding: 2px 6px; border-radius: 6px; transition: color 0.15s ease;"
                        onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94A3B8'">
                    ✕
                </button>
            </div>

            {{-- Form Tolak Verifikasi --}}
            <form method="POST" action="{{ route('change-requests.update', $changeRequest) }}" id="formRejectCr">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="reject">

                {{-- Summary Box: Nomor & Judul CR + Pemohon --}}
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                    <div>
                        <div style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-bottom: 4px;">
                            Nomor &amp; Judul CR:
                        </div>
                        <div style="font-size: 0.84rem; font-weight: 700; color: #1E293B; line-height: 1.35;">
                            {{ $modalCrCodeJudul }}
                        </div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-bottom: 4px;">
                            Pemohon:
                        </div>
                        <div style="font-size: 0.84rem; font-weight: 700; color: #1E293B; line-height: 1.35;">
                            {{ $modalPemohon }}
                        </div>
                    </div>
                </div>

                {{-- Field Alasan Penolakan --}}
                <div style="margin-bottom: 24px;">
                    <label for="modal_reject_reason" style="font-size: 0.78rem; font-weight: 700; color: #0F172A; margin-bottom: 8px; display: block;">
                        Alasan Penolakan <span style="color: #EF4444; font-weight: 700;">*</span>
                    </label>
                    <textarea id="modal_reject_reason" name="reject_reason" rows="4" required
                              placeholder="Jelaskan secara spesifik alasan penolakan dan poin-poin yang harus diperbaiki oleh pemohon agar proses revisi lebih cepat..."
                              style="width: 100%; height: 100px; background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 12px 14px; font-size: 0.82rem; font-family: 'Plus Jakarta Sans', sans-serif; color: #0F172A; outline: none; resize: vertical; box-sizing: border-box; transition: all 0.15s ease;"
                              onfocus="this.style.borderColor='#EF4444'; this.style.boxShadow='0 0 0 3px rgba(239, 68, 68, 0.08)';"
                              onblur="this.style.borderColor='#CBD5E1'; this.style.boxShadow='none';"></textarea>
                </div>

                {{-- Action Buttons: Batal & Konfirmasi Tolak CR --}}
                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
                    <button type="button" onclick="closeRejectModal()"
                            style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 8px 22px; font-size: 0.82rem; font-weight: 700; color: #334155; cursor: pointer; transition: all 0.15s ease;"
                            onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#94A3B8'"
                            onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#CBD5E1'">
                        Batal
                    </button>
                    <button type="submit"
                            style="background: #F87171; color: #FFFFFF; border: none; border-radius: 8px; padding: 8px 20px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.15s ease;"
                            onmouseover="this.style.background='#EF4444'"
                            onmouseout="this.style.background='#F87171'">
                        <i class="bi bi-x-lg" style="font-size: 0.80rem;"></i>
                        Konfirmasi Tolak CR
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB PANEL 3: STATUS CR (ROLE PM - FIGMA SCREENSHOT EXACT)
    ══════════════════════════════════════════════════════════════ --}}
    <div id="tabPanel-status" class="{{ $activeTab === 'status' ? '' : 'd-none' }}">
        <div style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; padding: 26px 28px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); width: 100%; box-sizing: border-box;">

            @if(!$isStatusCrUnlocked)
            {{-- Lock Warning Banner Sesuai Ketentuan 5: Tab Status CR belum bisa diisi jika belum melakukan pembayaran/quotation --}}
            <div style="background: #FEF2F2; border: 1.5px solid #FECACA; border-radius: 12px; padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #991B1B; margin: 0 0 4px 0;">
                        Tab Status CR Belum Bisa Diisi (Terkunci)
                    </h4>
                    <p style="font-size: 0.82rem; color: #B91C1C; margin: 0; line-height: 1.45;">
                        Sesuai alur Change Request, tab Status CR belum dapat diisi karena CR belum menyelesaikan seluruh tahapan:
                        <br><strong>1. Verifikasi CR oleh PM</strong> &bull; <strong>2. Validasi & Approval oleh PM Head</strong> &bull; <strong>3. Input Quotation & Konfirmasi Pembayaran oleh Presales</strong>.
                        <br>Setelah data Quotation disimpan dan dikonfirmasi, status CR akan otomatis terbuka ke tahap <strong>Development</strong> sehingga progress pengerjaan dapat diupdate.
                    </p>

                    @if($changeRequest->isPmhApproved())
                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #FECACA; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        <div style="font-size: 0.80rem; color: #991B1B; font-weight: 600;">
                            <i class="bi bi-info-circle-fill me-1"></i> CR telah disetujui PM Head. Anda dapat langsung mengonfirmasi Quotation untuk membuka tahap Development:
                        </div>
                        <form method="POST" action="{{ route('change-requests.quotation.confirm', $changeRequest) }}" style="margin: 0;">
                            @csrf
                            <button type="submit"
                                    style="background: #0284C7; color: #FFFFFF; border: none; border-radius: 8px; padding: 8px 18px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25); transition: background 0.15s ease;"
                                    onmouseover="this.style.background='#0369A1'" onmouseout="this.style.background='#0284C7'">
                                <i class="bi bi-check-circle-fill"></i>
                                Simpan &amp; Konfirmasi Quotation (Buka Tahap Development)
                                @if($changeRequest->saranHargaPenawaran() > 0)
                                    &bull; Rp {{ number_format($changeRequest->saranHargaPenawaran(), 0, ',', '.') }}
                                @endif
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @else
            {{-- Unlocked Success Banner --}}
            <div style="background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 12px; padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.88rem; font-weight: 800; color: #166534;">
                            Tahap Development Aktif (Telah Terbuka)
                        </div>
                        <div style="font-size: 0.78rem; color: #15803D;">
                            Quotation telah dikonfirmasi ({{ $changeRequest->harga_penawaran ? 'Rp ' . number_format($changeRequest->harga_penawaran, 0, ',', '.') : 'Selesai' }}). Anda dapat mengupdate status dan progres pengerjaan di bawah.
                        </div>
                    </div>
                </div>
                <span class="badge" style="background: #16A34A; color: #FFFFFF; font-size: 0.72rem; padding: 4px 12px; border-radius: 9999px;">
                    Tahap: {{ $pmStatusLabel }} ({{ $pmStatusPct }})
                </span>
            </div>
            @endif

            {{-- ── 1. Top Identity Row (Squircle + Title + Badge) ── --}}
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #0098EA; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; flex-shrink: 0;">
                    {{ $firstChar }}
                </div>
                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.01em;" data-i18n="cr_title_main">
                        {{ $crJudul }}
                    </h2>
                    <span style="background: #0098EA; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; padding: 3px 12px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
                        • {{ $statusDisplay }}
                    </span>
                </div>
            </div>

            {{-- ── 2. Card: PROGRESS PENGERJAAN ── --}}
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 28px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.72rem; font-weight: 800; color: #64748B; letter-spacing: 0.06em; text-transform: uppercase;">
                        PROGRESS PENGERJAAN
                    </span>
                    <span id="stepper_status_badge" style="background: #0098EA; color: #FFFFFF; font-size: 0.72rem; font-weight: 700; padding: 3px 14px; border-radius: 9999px;">
                        {{ $pmStatusPct }} — {{ $pmStatusLabel }}
                    </span>
                </div>

                {{-- Horizontal Continuous Stepper Line --}}
                <div style="position: relative; margin-top: 32px; margin-bottom: 8px;">
                    {{-- Constrained Track Container between circle 1 and circle 6 centers --}}
                    <div style="position: absolute; top: 15px; left: 30px; right: 30px; height: 3px; z-index: 1;">
                        {{-- Background Inactive Line --}}
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 100%; background: #E2E8F0;"></div>
                        {{-- Foreground Active Blue Line --}}
                        <div id="stepper_active_line" style="position: absolute; top: 0; left: 0; width: {{ $pmStatusLineWidth }}; height: 100%; background: #0098EA; transition: width 0.3s ease;"></div>
                    </div>

                    {{-- 6 Step Nodes --}}
                    <div style="display: flex; justify-content: space-between; position: relative; z-index: 3;">
                        {{-- Step 1: Analisa --}}
                        <div id="step_node_1" style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                            <div id="step_circle_1" style="width: 30px; height: 30px; border-radius: 50%; {{ $pmStatusStep >= 1 && $statusKey !== 'ditolak' ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #BAE6FD; color: #7DD3FC;' }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                1
                            </div>
                            <span id="step_label_1" style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $pmStatusStep >= 1 && $statusKey !== 'ditolak' ? '700' : '600' }}; color: {{ $pmStatusStep >= 1 && $statusKey !== 'ditolak' ? '#0098EA' : '#94A3B8' }}; text-align: center;">
                                Analisa
                            </span>
                        </div>

                        {{-- Step 2: Develop --}}
                        <div id="step_node_2" style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                            <div id="step_circle_2" style="width: 30px; height: 30px; border-radius: 50%; {{ $pmStatusStep >= 2 ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #BAE6FD; color: #7DD3FC;' }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                2
                            </div>
                            <span id="step_label_2" style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $pmStatusStep >= 2 ? '700' : '600' }}; color: {{ $pmStatusStep >= 2 ? '#0098EA' : '#94A3B8' }}; text-align: center;">
                                Develop
                            </span>
                        </div>

                        {{-- Step 3: SIT --}}
                        <div id="step_node_3" style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                            <div id="step_circle_3" style="width: 30px; height: 30px; border-radius: 50%; {{ $pmStatusStep >= 3 ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #BAE6FD; color: #7DD3FC;' }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                3
                            </div>
                            <span id="step_label_3" style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $pmStatusStep >= 3 ? '700' : '600' }}; color: {{ $pmStatusStep >= 3 ? '#0098EA' : '#94A3B8' }}; text-align: center;">
                                SIT
                            </span>
                        </div>

                        {{-- Step 4: UAT --}}
                        <div id="step_node_4" style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                            <div id="step_circle_4" style="width: 30px; height: 30px; border-radius: 50%; {{ $pmStatusStep >= 4 ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #BAE6FD; color: #7DD3FC;' }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                4
                            </div>
                            <span id="step_label_4" style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $pmStatusStep >= 4 ? '700' : '600' }}; color: {{ $pmStatusStep >= 4 ? '#0098EA' : '#94A3B8' }}; text-align: center;">
                                UAT
                            </span>
                        </div>

                        {{-- Step 5: Training --}}
                        <div id="step_node_5" style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                            <div id="step_circle_5" style="width: 30px; height: 30px; border-radius: 50%; {{ $pmStatusStep >= 5 ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #BAE6FD; color: #7DD3FC;' }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                5
                            </div>
                            <span id="step_label_5" style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $pmStatusStep >= 5 ? '700' : '600' }}; color: {{ $pmStatusStep >= 5 ? '#0098EA' : '#94A3B8' }}; text-align: center;">
                                Training
                            </span>
                        </div>

                        {{-- Step 6: Go Live --}}
                        <div id="step_node_6" style="display: flex; flex-direction: column; align-items: center; width: 60px;">
                            <div id="step_circle_6" style="width: 30px; height: 30px; border-radius: 50%; {{ $pmStatusStep >= 6 ? 'background: #0098EA; color: #FFFFFF; box-shadow: 0 0 0 4px #E0F2FE;' : 'background: #FFFFFF; border: 2px solid #BAE6FD; color: #7DD3FC;' }} font-weight: 700; font-size: 0.80rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                                6
                            </div>
                            <span id="step_label_6" style="margin-top: 8px; font-size: 0.72rem; font-weight: {{ $pmStatusStep >= 6 ? '700' : '600' }}; color: {{ $pmStatusStep >= 6 ? '#0098EA' : '#94A3B8' }}; text-align: center;">
                                Go Live
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Form Update Status & Berita Acara (2-Column Grid) ── --}}
            <form method="POST" action="{{ route('change-requests.update', $changeRequest) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_tab" value="status">

                <div class="cr-grid-status-ba">
                    {{-- Left Card: UPDATE STATUS CR --}}
                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 22px 24px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                            <span style="width: 3.5px; height: 16px; background: #0098EA; border-radius: 2px; display: inline-block;"></span>
                            <span style="font-size: 0.85rem; font-weight: 800; color: #0F172A; text-transform: uppercase;">
                                Update Status CR
                            </span>
                        </div>

                        {{-- STATUS BARU --}}
                        <div style="margin-bottom: 18px;">
                            <label for="select_status_cr" style="font-size: 0.72rem; font-weight: 800; color: #64748B; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 8px; display: block;">
                                STATUS BARU
                            </label>
                            <div style="position: relative;">
                                <select name="status" id="select_status_cr" onchange="updateStepperPreview(this.value)" {{ !$isStatusCrUnlocked ? 'disabled' : '' }}
                                        style="width: 100%; height: 42px; background: {{ !$isStatusCrUnlocked ? '#F1F5F9' : '#FFFFFF' }}; border: 1.5px solid {{ !$isStatusCrUnlocked ? '#CBD5E1' : '#0098EA' }}; border-radius: 8px; padding: 0 36px 0 14px; font-size: 0.84rem; font-weight: 600; color: {{ !$isStatusCrUnlocked ? '#94A3B8' : '#0F172A' }}; outline: none; appearance: none; -webkit-appearance: none; cursor: {{ !$isStatusCrUnlocked ? 'not-allowed' : 'pointer' }}; transition: border-color 0.15s ease;">
                                    <option value="diajukan" {{ in_array($statusKey, ['diajukan', 'awaiting_pm']) ? 'selected' : '' }}>Diajukan</option>
                                    <option value="analisa" {{ in_array($statusKey, ['analisa', 'dianalisis']) ? 'selected' : '' }}>Analysis</option>
                                    <option value="development" {{ in_array($statusKey, ['development', 'dikerjakan', 'validated', 'disetujui']) ? 'selected' : '' }}>Develop</option>
                                    <option value="sit" {{ $statusKey === 'sit' ? 'selected' : '' }}>SIT</option>
                                    <option value="uat" {{ $statusKey === 'uat' ? 'selected' : '' }}>UAT</option>
                                    <option value="training" {{ $statusKey === 'training' ? 'selected' : '' }}>Training</option>
                                    <option value="golive" {{ in_array($statusKey, ['golive', 'selesai', 'awaiting_golive_validation', 'invoicing']) ? 'selected' : '' }}>Go-Live</option>
                                    <option value="ditolak" {{ in_array($statusKey, ['ditolak', 'revision_needed']) ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 0.80rem; color: #64748B; pointer-events: none;"></i>
                            </div>
                        </div>

                        {{-- CR NOTES --}}
                        <div>
                            <label for="status_cr_notes" style="font-size: 0.72rem; font-weight: 800; color: #64748B; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 8px; display: block;">
                                CR NOTES
                            </label>
                            <textarea id="status_cr_notes" name="catatan_analisis" rows="4" {{ !$isStatusCrUnlocked ? 'disabled' : '' }}
                                      placeholder="Tuliskan catatan review, alasan perubahan status, atau instruksi tindak lanjut..."
                                      style="width: 100%; height: 95px; background: {{ !$isStatusCrUnlocked ? '#F1F5F9' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 12px 14px; font-size: 0.82rem; font-family: 'Plus Jakarta Sans', sans-serif; color: {{ !$isStatusCrUnlocked ? '#94A3B8' : '#0F172A' }}; outline: none; resize: vertical; box-sizing: border-box; transition: border-color 0.15s ease; cursor: {{ !$isStatusCrUnlocked ? 'not-allowed' : 'text' }};"
                                      onfocus="this.style.borderColor='#0098EA'" onblur="this.style.borderColor='#CBD5E1'">{{ $changeRequest->catatan_analisis ?: $changeRequest->catatan_admin }}</textarea>
                        </div>
                    </div>

                    {{-- Right Card: UPDATE BERITA ACARA --}}
                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 22px 24px; display: flex; flex-direction: column; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                            <span style="width: 3.5px; height: 16px; background: #0098EA; border-radius: 2px; display: inline-block;"></span>
                            <span style="font-size: 0.85rem; font-weight: 800; color: #0F172A; text-transform: uppercase;">
                                UPDATE BERITA ACARA
                            </span>
                        </div>

                        {{-- Upload Dropzone --}}
                        <input type="file" id="ba_file_input" name="berita_acara_file" accept=".pdf,application/pdf" style="display: none;" onchange="handleBaStatusFile(this)" {{ !$isStatusCrUnlocked ? 'disabled' : '' }}>
                        <div @if($isStatusCrUnlocked) onclick="document.getElementById('ba_file_input').click()" @endif
                             style="border: 1.5px dashed {{ !$isStatusCrUnlocked ? '#CBD5E1' : '#93C5FD' }}; background: {{ !$isStatusCrUnlocked ? '#F8FAFC' : '#FFFFFF' }}; border-radius: 10px; height: 172px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: {{ !$isStatusCrUnlocked ? 'not-allowed' : 'pointer' }}; opacity: {{ !$isStatusCrUnlocked ? '0.7' : '1' }}; transition: all 0.15s ease;"
                             @if($isStatusCrUnlocked)
                             onmouseover="this.style.borderColor='#0098EA'; this.style.background='#F0F9FF';"
                             onmouseout="this.style.borderColor='#93C5FD'; this.style.background='#FFFFFF';"
                             @endif>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="9" x2="12" y2="15"/>
                            </svg>
                            <span id="ba_file_status_label" style="font-size: 0.80rem; color: #64748B; font-weight: 500;">
                                {{ $changeRequest->berita_acara_file ? basename($changeRequest->berita_acara_file) : ($isStatusCrUnlocked ? 'Klik untuk upload PDF' : 'Terkunci (Belum bisa diunggah)') }}
                            </span>
                        </div>

                        {{-- Tanggal Berita Acara (BA) --}}
                        <div style="margin-top: 14px;">
                            <label for="status_ba_date" style="font-size: 0.72rem; font-weight: 800; color: #64748B; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 6px; display: block;">
                                Tanggal Berita Acara (BA)
                            </label>
                            <input type="date" id="status_ba_date" name="berita_acara_date"
                                   value="{{ $changeRequest->berita_acara_date ? $changeRequest->berita_acara_date->format('Y-m-d') : date('Y-m-d') }}"
                                   {{ !$isStatusCrUnlocked ? 'disabled' : '' }}
                                   style="width: 100%; height: 38px; background: {{ !$isStatusCrUnlocked ? '#F1F5F9' : '#FFFFFF' }}; border: 1.5px solid #CBD5E1; border-radius: 8px; padding: 0 12px; font-size: 0.82rem; color: #0F172A; outline: none;">
                        </div>
                    </div>
                </div>

                {{-- Action Button: Far Right --}}
                <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 24px;">
                    @if(!$isStatusCrUnlocked)
                        <button type="button" disabled
                                style="background: #94A3B8; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.84rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: not-allowed; opacity: 0.85;">
                            <i class="bi bi-lock-fill" style="font-size: 0.88rem;"></i>
                            Status CR Belum Bisa Diisi
                        </button>
                    @else
                        <button type="submit"
                                style="background: #10B981; color: #FFFFFF; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.84rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.15s ease;"
                                onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                            <i class="bi bi-check-lg" style="font-size: 0.95rem;"></i>
                            Update CR
                        </button>
                    @endif
                </div>
            </form>

        </div>
    </div>
    @endif

</div>

<script>
    @if($isClientUser)
    if (window.history && window.history.replaceState) {
        var url = new URL(window.location);
        if (url.searchParams.has('tab')) {
            url.searchParams.delete('tab');
            window.history.replaceState({}, '', url);
        }
    }
    @endif

    function switchDetailTab(tabName) {
        var tabs = ['detail', 'verifikasi', 'status'];
        tabs.forEach(function(t) {
            var btn = document.getElementById('tabBtn-' + t);
            var panel = document.getElementById('tabPanel-' + t);
            if (btn && panel) {
                if (t === tabName) {
                    btn.style.background = '#0070D2';
                    btn.style.color = '#FFFFFF';
                    btn.style.fontWeight = '700';
                    panel.classList.remove('d-none');
                } else {
                    btn.style.background = '#EEF2F6';
                    btn.style.color = '#1E293B';
                    btn.style.fontWeight = '600';
                    panel.classList.add('d-none');
                }
            }
        });

        if (window.history && window.history.replaceState) {
            var url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }
    }

    function calcVerifikasiTotal() {
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
        var uploadContainer = document.getElementById('sp_upload_container');
        var fileInput = document.getElementById('sp_file_input');

        if (input) input.value = val;

        if (val === 1) {
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
            if (uploadContainer) {
                uploadContainer.style.display = '';
            }
        } else {
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
            if (uploadContainer) {
                uploadContainer.style.display = 'none';
            }
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }

    function handleSolutionPaperFile(input) {
        var fileNameSpan = document.getElementById('sp_file_name');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var isPdf = file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf';
            if (!isPdf) {
                alert('Format file tidak didukung! Hanya file berformat PDF (.pdf) yang diperbolehkan.');
                input.value = '';
                if (fileNameSpan) {
                    fileNameSpan.textContent = 'Tidak ada file yang dipilih';
                    fileNameSpan.style.color = '#94A3B8';
                    fileNameSpan.style.fontWeight = '500';
                }
                return;
            }
            if (fileNameSpan) {
                fileNameSpan.textContent = file.name;
                fileNameSpan.style.color = '#0F172A';
                fileNameSpan.style.fontWeight = '600';
            }
        } else {
            if (fileNameSpan) {
                fileNameSpan.textContent = 'Tidak ada file yang dipilih';
                fileNameSpan.style.color = '#94A3B8';
                fileNameSpan.style.fontWeight = '500';
            }
        }
    }

    function handleBaStatusFile(input) {
        var label = document.getElementById('ba_file_status_label');
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var isPdf = file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf';
            if (!isPdf) {
                alert('Format file tidak didukung! Hanya file berformat PDF (.pdf) yang diperbolehkan.');
                input.value = '';
                if (label) {
                    label.textContent = 'Klik untuk upload PDF';
                    label.style.color = '#64748B';
                    label.style.fontWeight = '500';
                }
                return;
            }
            if (label) {
                label.textContent = file.name;
                label.style.color = '#0098EA';
                label.style.fontWeight = '700';
            }
        } else {
            if (label) {
                label.textContent = 'Klik untuk upload PDF';
                label.style.color = '#64748B';
                label.style.fontWeight = '500';
            }
        }
    }

    function updateStepperPreview(statusVal) {
        var stageMap = {
            'diajukan': { num: 1, pct: '0%', label: 'Diajukan', lineWidth: '0%' },
            'analisa': { num: 1, pct: '0%', label: 'Analisa', lineWidth: '0%' },
            'development': { num: 2, pct: '20%', label: 'Develop', lineWidth: '20%' },
            'dikerjakan': { num: 2, pct: '20%', label: 'Develop', lineWidth: '20%' },
            'sit': { num: 3, pct: '40%', label: 'SIT', lineWidth: '40%' },
            'uat': { num: 4, pct: '60%', label: 'UAT', lineWidth: '60%' },
            'training': { num: 5, pct: '80%', label: 'Training', lineWidth: '80%' },
            'golive': { num: 6, pct: '100%', label: 'Go Live', lineWidth: '100%' },
            'ditolak': { num: 1, pct: '0%', label: 'Ditolak', lineWidth: '0%' }
        };

        var stage = stageMap[statusVal] || stageMap['analisa'];
        var badge = document.getElementById('stepper_status_badge');
        if (badge) {
            badge.textContent = stage.pct + ' — ' + stage.label;
        }

        var line = document.getElementById('stepper_active_line');
        if (line) {
            line.style.width = stage.lineWidth;
        }

        for (var i = 1; i <= 6; i++) {
            var circle = document.getElementById('step_circle_' + i);
            var lbl = document.getElementById('step_label_' + i);
            if (!circle || !lbl) continue;

            if (i <= stage.num && statusVal !== 'ditolak') {
                circle.style.background = '#0098EA';
                circle.style.color = '#FFFFFF';
                circle.style.border = 'none';
                circle.style.boxShadow = (i === stage.num) ? '0 0 0 4px #E0F2FE' : 'none';
                lbl.style.color = '#0098EA';
                lbl.style.fontWeight = '700';
            } else {
                circle.style.background = '#FFFFFF';
                circle.style.color = '#7DD3FC';
                circle.style.border = '2px solid #BAE6FD';
                circle.style.boxShadow = 'none';
                lbl.style.color = '#94A3B8';
                lbl.style.fontWeight = '600';
            }
        }
    }

    function openRejectModal() {
        var modal = document.getElementById('modalRejectCr');
        if (modal) {
            modal.style.display = 'flex';
            var reasonInput = document.getElementById('modal_reject_reason');
            if (reasonInput) {
                setTimeout(function() { reasonInput.focus(); }, 120);
            }
        }
    }

    function closeRejectModal() {
        var modal = document.getElementById('modalRejectCr');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('modalRejectCr');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeRejectModal();
                }
            });
        }
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    });
</script>
@endsection
