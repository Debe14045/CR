@extends('layouts.app')

@section('title', 'Detail Change Request • ' . ($changeRequest->kode_cr ?: 'CR'))

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $crTitle = $changeRequest->judul ?: 'Detail Change Request';
    $firstChar = strtoupper(substr($crTitle, 0, 1)) ?: 'C';
    $crCode = $changeRequest->kode_cr ?: '-';
    $statusKey = strtolower($changeRequest->status ?? '');

    $statusBadgeCfg = $changeRequest->statusBadgeConfig();

    $statusText = match($statusKey) {
        'validated', 'disetujui', 'quotation' => 'Menunggu Quotation',
        'development', 'dikerjakan' => 'Sedang Dikerjakan',
        'golive', 'selesai' => 'Selesai Go-Live',
        'ditolak', 'revision_needed' => 'Revisi Dibutuhkan',
        default => $statusBadgeCfg['text'],
    };

    $tglPengajuan = $changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->format('d M Y') : ($changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '-');
    $tglHeader = $changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : $tglPengajuan;
    $targetSelesai = $changeRequest->target_selesai ? $changeRequest->target_selesai->format('d M Y') : '-';

    // Ambil nama perusahaan secara konsisten
    $companyName = $changeRequest->klien ?: ($changeRequest->client?->company ?: '-');

    // Standardized Initial Generator (Sama persis dengan form Ajukan CR)
    $cleanName = trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $companyName));
    $words = preg_split('/\s+/', $cleanName);
    
    if (count($words) >= 2) {
        $clientInitial = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } elseif (!empty($words[0])) {
        $clientInitial = strtoupper(substr($words[0], 0, min(2, strlen($words[0]))));
    } else {
        $clientInitial = 'CR';
    }
    $clientEmail = $changeRequest->client?->email ?: '-';
    $picName = $changeRequest->user?->name ?: ($changeRequest->owner_cr ?: '-');
    $projectName = $changeRequest->proyek_terkait ?: '-';
    $crOwner = $changeRequest->pic_sales ?: ($changeRequest->main_desk ?: ($changeRequest->nama_pm ?: '-'));
    $priority = $changeRequest->prioritas ? ucfirst($changeRequest->prioritas) : '-';

    $docUrl = $changeRequest->google_drive_url ?: null;
    if ($changeRequest->lampiran_pengajuan) {
        $initialFileUrl = route('change-requests.initial-file', $changeRequest);
        $initialFileName = basename($changeRequest->lampiran_pengajuan);
    } else {
        $initialFileUrl = null;
        $initialFileName = null;
    }
    $docName = $changeRequest->dokumen_cr_name ?: ($initialFileName ?: ($docUrl ? basename($docUrl) : null));
    $spFile = $changeRequest->solutionPaper?->solution_paper_file ?: null;
    $spFileUrl = $spFile && Storage::disk('public')->exists($spFile) ? Storage::url($spFile) : null;
    $solutionPaperUrl = $changeRequest->solution_paper_url ?: null;
    $spName = $changeRequest->solution_paper_name ?: ($spFile ? basename($spFile) : ($solutionPaperUrl ? basename($solutionPaperUrl) : null));

    $mandaysAnalisis = (float)($changeRequest->mindesk_analisis ?? 0);
    $mandaysDev = (float)($changeRequest->mindesk_development ?? 0);
    $mandaysTest = (float)($changeRequest->mindesk_testing ?? 0);
    $totalMandays = $mandaysAnalisis + $mandaysDev + $mandaysTest;
    $ratePerMandays = \App\Models\ChangeRequest::RATE_PER_MINDESK;
    $estimasiNilai = $changeRequest->saranHargaPenawaran();

    $catatanCR = $changeRequest->catatan_pengajuan ?: ($changeRequest->pesan_client ?: null);
    $alasanBg = $changeRequest->alasan ?: null;
    $deskripsiFull = $changeRequest->deskripsi ?: null;
    $dependensiNote = $changeRequest->solution_paper_note ?: ($changeRequest->solutionPaper?->notes ?: ($changeRequest->pesan_client ?: null));

    $currentStep = match($statusKey) {
        'diajukan' => 1,
        'awaiting_pm', 'analisa', 'dianalisis' => 2,
        'awaiting_pmh' => 3,
        'validated', 'disetujui', 'quotation' => 4,
        'development', 'dikerjakan', 'sit', 'uat', 'training' => 5,
        'golive', 'selesai', 'invoicing', 'awaiting_golive_validation' => 6,
        default => 2,
    };
    $pctProgress = match($currentStep) {
        1 => 17, 2 => 34, 3 => 50, 4 => 67, 5 => 83, 6 => 100, default => 17,
    };
    $stepLabels = [
        1 => 'Diajukan',
        2 => 'Review PM',
        3 => 'Approval PM Head',
        4 => 'Quotation',
        5 => 'Development',
        6 => 'Invoice',
    ];

    // Quotation numbers (derived, not hardcoded)
    $createdYear = $changeRequest->created_at ? $changeRequest->created_at->format('Y') : date('Y');
    $nomorQuotation = 'QT-' . $createdYear . '-' . str_pad($changeRequest->id, 3, '0', STR_PAD_LEFT);
    $tglDibuat = $changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '-';
    $berlakuSampai = $changeRequest->created_at ? $changeRequest->created_at->copy()->addDays(30)->format('d M Y') : '-';

    $hargaPenawaran = $changeRequest->harga_penawaran !== null ? (float)$changeRequest->harga_penawaran : null;
    $totalPenawaranFinal = $hargaPenawaran ?? $estimasiNilai;
    $nilaiLayananAwal = $totalPenawaranFinal ? round($totalPenawaranFinal / 1.11) : 0;
    $ppnAwal = $totalPenawaranFinal ? ($totalPenawaranFinal - $nilaiLayananAwal) : 0;

    $subAnalisis = $mandaysAnalisis * $ratePerMandays;
    $subDev = $mandaysDev * $ratePerMandays;
    $subTest = $mandaysTest * $ratePerMandays;

    $invoices = $changeRequest->invoices ?? collect();
    $totalInvoiced = $invoices->sum('nominal');
    $totalPaid = $invoices->where('payment_status', 'paid')->sum('nominal');
    $totalPending = $totalInvoiced - $totalPaid;

    $canBeQuoted = $changeRequest->canBeQuoted();
    $isReadyInvoicing = $changeRequest->isReadyForInvoicing();

    $fmtIdr = fn($v) => 'Rp ' . number_format((float)$v, 0, ',', '.');
@endphp

<style>
    .cr-detail-container {
        border: 1px solid #CBD5E1;
        border-radius: 20px;
        background: #FFFFFF;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .cr-subcard {
        border: 1px solid #BAE6FD !important;
        border-radius: 16px !important;
        background: #FFFFFF;
        overflow: hidden;
    }
    .flow-bar-item { flex: 1; height: 4px; background: #E2E8F0; }
    .flow-bar-item.active { background: #0284C7; }
    .flow-bar-item.completed { background: #10B981; }
    .flow-step-box {
        border-radius: 10px;
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
        font-size: 0.84rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s ease;
        max-width: 100%;
    }
    .doc-pill-box:hover { background: #E0F2FE; color: #0369A1; }
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
    
    /* Quotation Components Styling */
    .section-num-badge {
        width: 18px; height: 18px; border-radius: 4px;
        background: #0084FF; color: #FFFFFF;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 800; flex-shrink: 0;
    }
    .section-title-label {
        font-size: 0.76rem; letter-spacing: 0.05em; color: #334155; font-weight: 800; text-transform: uppercase;
    }
    .field-label-sm {
        font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem; display: block;
    }
    .form-control-disabled-gray {
        background-color: #CBD5E1 !important; border: 1.5px solid #CBD5E1 !important;
        border-radius: 8px !important; font-size: 0.85rem !important; color: #1E293B !important;
        font-weight: 600 !important; height: 42px !important;
    }
    .form-control-white {
        background-color: #FFFFFF !important; border: 1.5px solid #CBD5E1 !important;
        border-radius: 8px !important; font-size: 0.85rem !important; color: #1E293B !important;
        height: 42px !important;
    }
    .form-select-custom {
        border: 1.5px solid #CBD5E1 !important; border-radius: 8px !important;
        font-size: 0.85rem !important; color: #1E293B !important; height: 42px !important;
    }
    .termin-table th {
        background: #F8FAFC !important; font-size: 0.72rem !important; text-transform: uppercase !important;
        letter-spacing: 0.04em !important; color: #64748B !important; padding: 0.85rem 1rem !important;
        border-bottom: 1.5px solid #E2E8F0 !important; vertical-align: middle;
    }
    .termin-table td {
        padding: 1rem !important; font-size: 0.84rem !important;
        border-bottom: 1px solid #F1F5F9 !important; vertical-align: middle; background: #FFFFFF;
    }
    .termin-no-badge {
        width: 28px; height: 28px; border-radius: 50%; border: 1px solid #CBD5E1;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 600; color: #64748B; background: #FFFFFF;
    }
</style>

<div class="page-shell pb-5">

    <div class="mb-3">
        <h1 class="fw-bold mb-1" id="pageHeaderTitle" style="color: #0F172A; font-size: 2rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            Quotation
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Melihat Data Lengkap CR</p>
    </div>

    <div class="pm-filter-container" role="tablist" aria-label="Tab Detail CR">
        <button type="button" class="pm-filter-tab" id="tabBtnDetail" onclick="switchCrTab('detail')" role="tab" aria-selected="false" style="border-top-left-radius: 11px; border-bottom-left-radius: 11px;">
            <span>Detail Change Request</span>
            <i class="bi bi-check2 ms-1" style="font-size: 1.15rem; color: #0072D2; font-weight: bold;"></i>
        </button>
        <div class="pm-filter-divider" id="divDetailQuot"></div>
        <button type="button" class="pm-filter-tab active" id="tabBtnQuotation" onclick="switchCrTab('quotation')" role="tab" aria-selected="true">
            <span>Quotation</span>
            <span style="color: #EF4444; font-weight: 800; margin-left: 2px;">*</span>
        </button>
        <div class="pm-filter-divider" id="divQuotPay"></div>
        @if(!$isReadyInvoicing)
            <button type="button" class="pm-filter-tab is-locked" id="tabBtnPayment" onclick="notifyPaymentLocked()" role="tab"
                    title="Halaman belum bisa dibuka, baru dapat diakses setelah CR selesai atau setelah Go-Live"
                    style="border-top-right-radius: 11px; border-bottom-right-radius: 11px;">
                <i class="bi bi-lock-fill" style="font-size: 0.84rem; margin-right: 5px;"></i>
                <span>Data Pembayaran &amp; GR <small style="font-weight: 700;">(Terkunci)</small></span>
            </button>
        @else
            <button type="button" class="pm-filter-tab" id="tabBtnPayment" onclick="switchCrTab('payment')" role="tab" aria-selected="false" style="border-top-right-radius: 11px; border-bottom-right-radius: 11px;">
                <span>Data Pembayaran &amp; GR</span>
            </button>
        @endif
    </div>

    {{-- ============ TAB: DETAIL ============ --}}
    <div id="tabContentDetail" class="cr-detail-container p-4 p-md-5 mb-4">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                 style="width: 44px; height: 44px; background: #007DFF; font-size: 1.35rem; border-radius: 12px; flex-shrink: 0;">
                {{ $firstChar }}
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.35rem; font-weight: 800; letter-spacing: -0.015em;">
                    {{ $crTitle }}
                </h3>
                <span class="badge px-3 py-1 fw-bold text-white d-inline-flex align-items-center gap-1"
                      style="background: #00A3FF; border-radius: 999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.02em;">
                    <span style="width: 4px; height: 4px; border-radius: 50%; background: #FFFFFF;"></span>
                    {{ ucfirst(str_replace('_', ' ', $changeRequest->status ?: '-')) }}
                </span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span style="font-size: 0.78rem; font-weight: 700; color: #475569; font-family: 'SF Mono', Monaco, Consolas, monospace;">
                    ID: &nbsp;<strong style="color: #0F172A;">{{ $crCode }}</strong>
                </span>
                <span class="px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5"
                      style="border: 1px solid {{ $statusBadgeCfg['bg'] }}; background: {{ $statusBadgeCfg['bg'] }}; color: {{ $statusBadgeCfg['color'] }}; border-radius: 9999px; font-size: 0.74rem;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $statusBadgeCfg['color'] }}; flex-shrink: 0;"></span>
                    <span>{{ $statusBadgeCfg['text'] }}</span>
                </span>
                <span class="px-3 py-1 fw-semibold d-inline-flex align-items-center gap-2"
                      style="border: 1px solid #E2E8F0; background: #F8FAFC; color: #475569; border-radius: 6px; font-size: 0.76rem; font-weight: 700;">
                    Prioritas: {{ $priority }}
                </span>
            </div>
            <div class="text-muted small d-flex align-items-center gap-1.5" style="font-size: 0.8rem; color: #64748B;">
                <i class="bi bi-calendar3" style="font-size: 0.82rem; color: #94A3B8;"></i>
                <span>Tanggal Pengajuan: <strong style="color: #0F172A; font-weight: 800;">{{ $tglHeader }}</strong></span>
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em; color: #334155; font-weight: 800;">
                    STATUS ALUR CR
                </span>
                <span class="text-muted small" style="font-size: 0.74rem; font-weight: 600; color: #64748B;">
                    Total Progres Alur: <strong style="font-weight: 800; color: #1E293B;">{{ $pctProgress }}%</strong>
                </span>
            </div>
            {{-- Continuous Track Bar --}}
            <div class="position-relative mb-3" style="height: 5px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                <div style="height: 100%; width: {{ $pctProgress }}%; background: #007DFF; border-radius: 9999px; transition: width 0.35s ease;"></div>
            </div>
            <div class="row g-2">
                @for ($step = 1; $step <= 6; $step++)
                    @php $isCompleted = $step < $currentStep; $isActive = $step === $currentStep; $title = $stepLabels[$step]; @endphp
                    <div class="col-lg-2 col-md-4 col-6">
                        @if ($isCompleted)
                            <div class="flow-step-box h-100" style="background: #F0FDF4; border: 1.5px solid #86EFAC;">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.74rem; color: #0F172A;">{{ $title }}</div>
                                    <div class="text-success small fw-semibold" style="font-size: 0.65rem;">Selesai</div>
                                </div>
                            </div>
                        @elseif ($isActive)
                            <div class="flow-step-box h-100" style="background: #FFFFFF; border: 2px solid #0284C7; box-shadow: 0 1px 3px rgba(2,132,199,0.15);">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 18px; height: 18px; background: #0284C7; font-size: 0.65rem; flex-shrink: 0;">{{ $step }}</span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.74rem; color: #0284C7;">{{ $title }}</div>
                                    <div class="small fw-semibold" style="font-size: 0.65rem; color: #0284C7;">Sedang Berjalan</div>
                                </div>
                            </div>
                        @else
                            <div class="flow-step-box h-100" style="background: #FAFAFA; border: 1.5px solid #E2E8F0;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 18px; height: 18px; background: #E2E8F0; font-size: 0.65rem; flex-shrink: 0; color: #94A3B8;">{{ $step }}</span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-semibold" style="font-size: 0.74rem; color: #64748B;">{{ $title }}</div>
                                    <div class="text-muted small" style="font-size: 0.65rem;">Menunggu</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                        INFORMASI CR
                    </div>
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">NAMA PERUSAHAAN</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $companyName }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">INISIAL KLIEN</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $clientInitial }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">NAMA PIC</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $picName }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">NAMA PROJECT</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $projectName }}</div>
                        </div>
                        <div class="col-8">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">CR OWNER</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $crOwner }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">TANGGAL PENGAJUAN</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $tglPengajuan }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">REQUEST GO-LIVE</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $targetSelesai }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-uppercase" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">PRIORITAS CR</div>
                            <div class="fw-bold mt-1" style="font-size: 0.9rem; color: #0C4A6E; font-weight: 800;">{{ $priority }}</div>
                        </div>
                    </div>
                    @if($changeRequest->client)
                        <div class="mt-3 pt-3" style="border-top: 1px solid #F1F5F9; font-size: 0.8rem; color: #64748B;">
                            <span class="fw-bold" style="color:#0C4A6E;">Kontak klien:</span>
                            {{ $clientEmail }}
                            @if($changeRequest->client->phone) &bull; {{ $changeRequest->client->phone }} @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                        DOKUMEN
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">DOKUMEN CR</div>
                            @if($docUrl)
                                <a href="{{ $docUrl }}" target="_blank" rel="noopener" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span class="text-truncate">{{ $docName ?: $docUrl }}</span>
                                </a>
                            @elseif($initialFileUrl)
                                <a href="{{ $initialFileUrl }}" class="doc-pill-box">
                                    <i class="bi bi-paperclip"></i>
                                    <span class="text-truncate">{{ $initialFileName }}</span>
                                </a>
                            @else
                                <div class="fw-bold mt-1" style="font-size: 0.95rem; color: #64748B;">-</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">SOLUTION PAPER</div>
                            @if($spFileUrl)
                                <a href="{{ $spFileUrl }}" target="_blank" rel="noopener" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-code"></i>
                                    <span class="text-truncate">{{ basename($spFile) }}</span>
                                </a>
                            @elseif($solutionPaperUrl)
                                <a href="{{ $solutionPaperUrl }}" target="_blank" rel="noopener" class="doc-pill-box">
                                    <i class="bi bi-file-earmark-code"></i>
                                    <span class="text-truncate">{{ $solutionPaperUrl }}</span>
                                </a>
                            @else
                                <div class="fw-bold mt-1" style="font-size: 0.95rem; color: #64748B;">-</div>
                            @endif
                        </div>
                        @if($changeRequest->berita_acara_file)
                            <div>
                                <div class="text-uppercase mb-1" style="font-size: 0.68rem; font-weight: 700; color: #64748B;">BERITA ACARA (BA)</div>
                                <div class="text-muted small" style="font-size: 0.82rem;">
                                    No: <strong>{{ $changeRequest->berita_acara_no ?: '-' }}</strong>
                                    &bull; Tgl: <strong>{{ $changeRequest->berita_acara_date ? $changeRequest->berita_acara_date->format('d M Y') : '-' }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text" style="color: #0284C7; font-size: 1.1rem;"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; font-weight: 800;">Detail Deskripsi CR</h5>
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


        <div class="row g-4">
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                            ESTIMASI MANDAYS
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <div class="d-flex justify-content-between small mb-1" style="font-size: 0.8rem;">
                                    <span style="color: #475569; font-weight: 500;">Analisis</span>
                                    <span style="color: #0F172A; font-weight: 800;">{{ rtrim(rtrim(number_format($mandaysAnalisis, 2, ',', '.'), '0'), ',') }} hari</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 999px; background: #E0F2FE;">
                                    <div class="progress-bar" style="width: {{ $totalMandays > 0 ? ($mandaysAnalisis / $totalMandays) * 100 : 0 }}%; background: #0284C7; border-radius: 999px;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small mb-1" style="font-size: 0.8rem;">
                                    <span style="color: #475569; font-weight: 500;">Development</span>
                                    <span style="color: #0F172A; font-weight: 800;">{{ rtrim(rtrim(number_format($mandaysDev, 2, ',', '.'), '0'), ',') }} hari</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 999px; background: #E0F2FE;">
                                    <div class="progress-bar" style="width: {{ $totalMandays > 0 ? ($mandaysDev / $totalMandays) * 100 : 0 }}%; background: #0284C7; border-radius: 999px;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between small mb-1" style="font-size: 0.8rem;">
                                    <span style="color: #475569; font-weight: 500;">Testing</span>
                                    <span style="color: #0F172A; font-weight: 800;">{{ rtrim(rtrim(number_format($mandaysTest, 2, ',', '.'), '0'), ',') }} hari</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 999px; background: #E0F2FE;">
                                    <div class="progress-bar" style="width: {{ $totalMandays > 0 ? ($mandaysTest / $totalMandays) * 100 : 0 }}%; background: #2563EB; border-radius: 999px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3" style="border-top: 1px solid #F1F5F9;">
                        <span style="font-size: 0.88rem; color: #475569; font-weight: 500;">Total</span>
                        <span style="font-size: 0.95rem; color: #0F172A; font-weight: 800;">{{ rtrim(rtrim(number_format($totalMandays, 2, ',', '.'), '0'), ',') }} hari kerja</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2" style="border-top: 1px solid #F1F5F9;">
                        <span style="font-size: 0.88rem; color: #475569; font-weight: 500;">Estimasi Nilai ({{ $fmtIdr($ratePerMandays) }}/md)</span>
                        <span style="font-size: 1rem; color: #16A34A; font-weight: 800;">{{ $fmtIdr($estimasiNilai) }}</span>
                    </div>
                    @if($totalMandays == 0)
                        <div class="text-muted small mt-2" style="font-size: 0.76rem;">Belum ada estimasi man-days yang diinput PM.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-chat-square-text" style="color: #0C4A6E; font-size: 0.95rem;"></i>
                        <span class="fw-bold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #0C4A6E; font-weight: 800;">
                            CR Notes / Catatan
                        </span>
                    </div>
                    <div class="p-4 flex-grow-1 d-flex align-items-center justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;">
                        @if($catatanCR)
                            <p class="mb-0" style="font-size: 0.85rem; line-height: 1.65; color: #0C4A6E;">&ldquo;{{ $catatanCR }}&rdquo;</p>
                        @else
                            <p class="mb-0 text-muted" style="font-size: 0.83rem;">Belum ada catatan untuk CR ini.</p>
                        @endif
                    </div>
                    @if($changeRequest->catatan_revisi)
                        <div class="mt-3 p-3" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; font-size: 0.82rem; color: #991B1B;">
                            <strong>Catatan Revisi:</strong> {{ $changeRequest->catatan_revisi }}
                        </div>
                    @endif
                    @if($changeRequest->catatan_approval)
                        <div class="mt-2 p-3" style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; font-size: 0.82rem; color: #065F46;">
                            <strong>Catatan Approval:</strong> {{ $changeRequest->catatan_approval }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ============ TAB 2: QUOTATION ============ --}}
    <div id="tabContentQuotation" class="cr-detail-container p-4 p-md-5 mb-4">

        <form method="POST" action="{{ route('change-requests.quotation.save', $changeRequest->id ?: 1) }}" enctype="multipart/form-data" id="presalesQuotationForm">
            @csrf
            <input type="hidden" name="harga_penawaran" id="formHargaPenawaran" value="{{ $hargaPenawaran }}">
            <input type="hidden" name="biaya_pengerjaan" id="formBiayaPengerjaan" value="0">
            <input type="hidden" name="termins_json" id="formTerminsJson" value="">

            <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                         style="width: 42px; height: 42px; background: #0084FF; font-size: 1.35rem; flex-shrink: 0;">
                        {{ $firstChar }}
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.35rem; font-weight: 800;">
                            {{ $crTitle }}
                        </h3>
                    </div>
                </div>
            </div>

            {{-- 1. INFORMASI QUOTATION --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="section-num-badge">1</span>
                    <span class="section-title-label">INFORMASI QUOTATION</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4 col-12">
                        <label class="field-label-sm">Nomor Quotation</label>
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-disabled-gray" value="{{ $nomorQuotation }}" readonly style="padding-right: 2.3rem;">
                            <i class="bi bi-lock-fill position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #64748B;"></i>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="field-label-sm">Tanggal Dibuat</label>
                        <input type="date" class="form-control form-control-white" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="field-label-sm">Berlaku Sampai</label>
                        <input type="date" class="form-control form-control-white" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                    </div>
                </div>
            </div>

            {{-- 2. INFORMASI PENGIRIMAN QUOTATION --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="section-num-badge">2</span>
                    <span class="section-title-label">INFORMASI PENGIRIMAN QUOTATION</span>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-12">
                        <label class="field-label-sm">Nama Perusahaan<span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-disabled-gray" value="{{ $companyName }}" readonly>
                    </div>
                    <div class="col-md-6 col-12">
                        <label class="field-label-sm">Nama PIC<span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-white" name="pic_name" value="{{ $picName }}">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label class="field-label-sm">Metode Pengiriman<span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom bg-white">
                            <option selected>Email</option>
                            <option>WhatsApp Business</option>
                            <option>Client Portal</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-12">
                        <label class="field-label-sm">Alamat Email Tujuan</label>
                        <input type="email" class="form-control form-control-white" value="{{ $clientEmail }}">
                    </div>
                </div>
            </div>

            {{-- 3. MANDAYS & TOTAL HARGA PENAWARAN --}}
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="section-num-badge">3</span>
                    <span class="section-title-label">MANDAYS &amp; PENAWARAN HARGA</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <label class="field-label-sm">Analisis (hari)<span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-disabled-gray" value="{{ $mandaysAnalisis }}" readonly>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="field-label-sm">Development (hari)<span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-disabled-gray" value="{{ $mandaysDev }}" readonly>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="field-label-sm">Testing (hari)<span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-disabled-gray" value="{{ $mandaysTest }}" readonly>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="field-label-sm">Total Mandays</label>
                        <input type="number" class="form-control form-control-disabled-gray fw-bold" value="{{ $totalMandays }}" readonly>
                    </div>
                </div>

                {{-- Total Harga Penawaran Tetap Disini (Sebagai Acuan Termin) --}}
                <div class="mb-2">
                    <label class="field-label-sm fw-bold" style="font-size: 0.85rem; color: #0F172A;">
                        Total Harga Penawaran <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="totalHargaField" class="form-control form-control-white fw-bold"
                           value="{{ $hargaPenawaran ? $fmtIdr($hargaPenawaran) : 'Rp 0' }}"
                           onfocus="if(this.value === 'Rp 0' || this.value === '0') this.value = '';"
                           onblur="if(!this.value.trim()) this.value = 'Rp 0';"
                           oninput="onTotalHargaChange(this)"
                           placeholder="Contoh: 200000000"
                           style="font-size: 1.15rem; color: #0072D2; padding: 0.75rem 1rem; border: 2px solid #0072D2; border-radius: 10px;">
                    <div class="form-text text-muted small mt-1.5" style="font-size: 0.76rem;">
                        <i class="bi bi-info-circle me-1 text-primary"></i>Ketik total penawaran (misal: <strong>200000000</strong>). Nominal ini menjadi batas maksimal dan acuan persentase termin.
                    </div>
                </div>
            </div>

            {{-- 4. TERMIN OF PAYMENT (SKEMA TAHAPAN PEMBAYARAN) --}}
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="section-num-badge">4</span>
                        <span class="section-title-label">TERMIN OF PAYMENT (SKEMA TAHAPAN PEMBAYARAN)</span>
                    </div>
                    <button type="button" class="btn text-white fw-bold px-3 py-1.5 shadow-sm" style="background: #22C55E; border-radius: 8px; font-size: 0.82rem; border: none;" onclick="openAddTerminModal()">
                        <i class="bi bi-plus-lg"></i> Tambah Termin
                    </button>
                </div>
                <div class="table-responsive" style="border: 1px solid #E2E8F0; border-radius: 12px; background: #FFFFFF;">
                    <table class="table mb-0 align-middle termin-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">NO</th>
                                <th>TERMIN OF PAYMENT</th>
                                <th>KETERANGAN / MILESTONE DELIVERABLE</th>
                                <th class="text-end">NOMINAL (IDR)</th>
                                <th class="text-center" style="width: 170px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="terminTableBody">
                            {{-- Diisi dinamis via JavaScript --}}
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2" style="background: #FFFFFF; border-top: 1px solid #E2E8F0;">
                        <div id="terminAllocationStatus"></div>
                        <div class="d-flex align-items-center">
                            <div class="fw-bold me-3" style="font-size: 0.86rem; color: #334155;">Subtotal Termin (<span id="milestoneCountText">0</span> Milestone):</div>
                            <div class="fw-bold" id="milestoneSubtotalText" style="font-size: 1.05rem; color: #0F172A;">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DOKUMEN QUOTATION & HARGA FINAL (Di bawah Termin) --}}
            <div class="row g-4 align-items-end mb-4">
                <div class="col-lg-6 col-12">
                    <label class="field-label-sm fw-bold">Dokumen Quotation<span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center gap-2 p-1.5" style="border: 1.5px solid #CBD5E1; border-radius: 8px; background: #FFFFFF;">
                        <button type="button" class="btn text-white d-flex align-items-center gap-1.5 px-3 py-1.5"
                                style="background: #0B2545; border-radius: 6px; font-size: 0.8rem; font-weight: 700; border: none;"
                                onclick="document.getElementById('dokumenFileInput').click();">
                            <i class="bi bi-upload"></i> Pilih File
                        </button>
                        <input type="file" id="dokumenFileInput" name="quotation_file" style="display: none;" onchange="onFileSelected(this)">
                        <span id="dokumenFileName" class="text-muted small ms-1" style="font-size: 0.82rem;">Tidak ada file yang dipilih</span>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="p-4" style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.86rem;">
                            <span style="color: #64748B;">Total Nilai Layanan ({{ $totalMandays }} MD)</span>
                            <span class="fw-bold" id="nilaiLayananText">{{ $fmtIdr($nilaiLayananAwal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.86rem;">
                            <span style="color: #64748B;">PPN 11% (UU HPP No. 7/2021)</span>
                            <span class="fw-bold" id="ppnText">{{ $fmtIdr($ppnAwal) }}</span>
                        </div>
                        <div style="border-top: 1.5px solid #E2E8F0; margin: 0.65rem 0;"></div>
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <div>
                                <div class="fw-bold" style="font-size: 0.95rem; color: #0F172A;">Total Penawaran Final</div>
                                <div class="text-muted small" style="font-size: 0.72rem;">Nett Termasuk Pajak</div>
                            </div>
                            <div class="fw-bold" id="totalPenawaranFinalText" style="font-size: 1.35rem; color: #0072D2;">
                                {{ $totalPenawaranFinal ? $fmtIdr($totalPenawaranFinal) : 'Rp 0' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn text-white fw-bold px-4 py-2.5 d-inline-flex align-items-center gap-2"
                        style="background: #22C55E; border-radius: 8px; font-size: 0.88rem; border: none;"
                        onclick="simpanDanSubmitQuotation()">
                    <i class="bi bi-check2" style="font-size: 1.15rem;"></i> Simpan Quotation
                </button>
            </div>

        </form>

    </div>

    {{-- ============ TAB 3: PAYMENT & GR ============ --}}
    <div id="tabContentPayment" class="cr-detail-container p-4 p-md-5 mb-4" style="display: none;">
        <h4 class="fw-bold mb-3" style="font-size: 1.1rem; color: #0F172A;">Detail Pembayaran &amp; GR</h4>
        <p class="text-muted small">Data pembayaran aktif.</p>
    </div>

</div>

{{-- Modal Tambah / Edit Termin --}}
<div class="modal fade" id="terminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTerminLabel"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Termin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="modalTerminIndex" value="-1">

                {{-- Alert Total Harga Acuan & Sisa Alokasi --}}
                <div class="alert alert-primary p-3 mb-3 d-flex align-items-center justify-content-between" id="modalTotalHargaAlert"
                     style="background: #EFF6FF; border: 1.5px solid #93C5FD; border-radius: 12px; color: #1E40AF;">
                    <div class="d-flex align-items-center gap-2.5">
                        <i class="bi bi-info-circle-fill text-primary" style="font-size: 1.3rem;"></i>
                        <div>
                            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #3B82F6; letter-spacing: 0.04em;">Total Harga Acuan</div>
                            <div class="fw-bold" style="font-size: 1.1rem; color: #1E3A8A;" id="modalAlertTotalHarga">Rp 0</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size: 0.72rem; color: #64748B; font-weight: 700; text-transform: uppercase;">Sisa Alokasi</div>
                        <div class="fw-bold" style="font-size: 0.98rem; color: #059669;" id="modalAlertSisaAlokasi">Rp 0</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Termin <span class="text-danger">*</span></label>
                    <input type="text" id="modalTerminName" class="form-control" placeholder="Contoh: Termin 1" required>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">Persentase (%) <span class="text-danger">*</span></label>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-xs py-0 px-2 fw-semibold" style="font-size: 0.72rem; background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; border-radius: 5px;" onclick="applyQuickPercent(20)">20%</button>
                            <button type="button" class="btn btn-xs py-0 px-2 fw-semibold" style="font-size: 0.72rem; background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; border-radius: 5px;" onclick="applyQuickPercent(30)">30%</button>
                            <button type="button" class="btn btn-xs py-0 px-2 fw-semibold" style="font-size: 0.72rem; background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; border-radius: 5px;" onclick="applyQuickPercent(50)">50%</button>
                            <button type="button" class="btn btn-xs py-0 px-2 fw-semibold" style="font-size: 0.72rem; background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; border-radius: 5px;" onclick="applyQuickPercent(100)">100%</button>
                            <button type="button" class="btn btn-xs py-0 px-2 fw-bold" style="font-size: 0.72rem; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; border-radius: 5px;" onclick="applyQuickRemainingPercent()">Sisa</button>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" id="modalTerminBadge" class="form-control" placeholder="Contoh: 50"
                               inputmode="decimal"
                               onkeydown="filterPercentageKeys(event)"
                               oninput="onTerminBadgeInput(this)"
                               maxlength="5">
                        <span class="input-group-text fw-bold text-primary" style="background: #F1F5F9; border-color: #CBD5E1;">%</span>
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.74rem;">Ketik angka persentase (hanya angka, misal: <strong>50</strong>), nominal terhitung otomatis dari Total Harga.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Milestone Deliverable <span class="text-danger">*</span></label>
                    <textarea id="modalTerminDeliverable" class="form-control" rows="2" placeholder="Deskripsi milestone deliverable termin ini" required></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Nominal (IDR) <span class="text-danger">*</span></label>
                    <input type="text" id="modalTerminNominal" class="form-control fw-bold" placeholder="Contoh: 100.000.000"
                           oninput="onTerminNominalInput(this)" required>
                    <div id="modalCalcHelperText" class="mt-1.5 small fw-semibold" style="display: none; font-size: 0.82rem; color: #0284C7;"></div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm px-4 fw-bold text-white shadow-sm" style="background: #22C55E; border: none; border-radius: 8px;" onclick="saveTerminModal()">Simpan Termin</button>
            </div>
        </div>
    </div>
</div>

<script>
    let terminList = [
        @if(isset($changeRequest->invoices) && $changeRequest->invoices->count() > 0)
            @foreach($changeRequest->invoices as $inv)
            {
                name: "{{ addslashes($inv->term_name) }}",
                badge: "{{ $inv->percentage > 0 ? (float)$inv->percentage . '%' : '' }}",
                deliverable: "{{ addslashes($inv->job_description ?? '') }}",
                nominal: {{ (int)$inv->nominal }}
            },
            @endforeach
        @endif
    ];

    function formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    function getTotalHargaNumber() {
        const input = document.getElementById('totalHargaField');
        if (!input) return 0;
        const raw = input.value.replace(/[^0-9]/g, '');
        return raw === '' ? 0 : parseInt(raw, 10);
    }

    function renderTerminTable() {
        const tbody = document.getElementById('terminTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';
        let total = 0;

        if (terminList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small fst-italic">Belum ada termin pembayaran yang ditambahkan. Silakan klik Tambah Termin.</td></tr>`;
        }

        terminList.forEach((item, idx) => {
            total += Number(item.nominal || 0);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center"><div class="termin-no-badge">${idx + 1}</div></td>
                <td>
                    <div class="fw-bold" style="color: #0F172A; font-size: 0.85rem;">${item.name}</div>
                    <div class="d-flex align-items-center gap-1.5 mt-1">
                        <span style="background: #DBEAFE; color: #2563EB; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">${item.badge || 'Termin'}</span>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold text-dark" style="font-size: 0.83rem;">${item.deliverable}</div>
                </td>
                <td class="text-end fw-bold" style="color: #0F172A; font-size: 0.88rem;">${formatRupiah(item.nominal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 me-1" style="font-size: 0.75rem;" onclick="editTermin(${idx})">Edit</button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.75rem;" onclick="deleteTermin(${idx})">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('milestoneCountText').innerText = terminList.length;
        document.getElementById('milestoneSubtotalText').innerText = formatRupiah(total);

        // Update status alokasi termin terhadap Total Harga
        const totalHarga = getTotalHargaNumber();
        const statusEl = document.getElementById('terminAllocationStatus');
        if (statusEl) {
            if (totalHarga > 0) {
                const diff = totalHarga - total;
                if (diff === 0) {
                    statusEl.innerHTML = `<span class="badge bg-success-subtle text-success fw-bold px-3 py-1.5" style="border: 1px solid #86EFAC; font-size: 0.8rem;"><i class="bi bi-check-circle-fill me-1"></i>Termin 100% Sesuai Total Harga</span>`;
                } else if (diff > 0) {
                    const pctDiff = ((diff / totalHarga) * 100).toFixed(1).replace('.0', '');
                    statusEl.innerHTML = `<span class="badge bg-warning-subtle text-warning-emphasis fw-bold px-3 py-1.5" style="border: 1px solid #FDE68A; font-size: 0.8rem;"><i class="bi bi-exclamation-circle-fill me-1"></i>Sisa Belum Dialokasikan: ${formatRupiah(diff)} (${pctDiff}%)</span>`;
                } else {
                    statusEl.innerHTML = `<span class="badge bg-danger-subtle text-danger fw-bold px-3 py-1.5" style="border: 1px solid #FCA5A5; font-size: 0.8rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Melebihi Total Harga: ${formatRupiah(Math.abs(diff))}</span>`;
                }
            } else {
                statusEl.innerHTML = '';
            }
        }
    }

    function getMaxAllowedNominalForModal() {
        const totalHarga = getTotalHargaNumber();
        const idx = parseInt(document.getElementById('modalTerminIndex').value, 10);
        const otherAllocated = terminList.reduce((sum, it, i) => i === idx ? sum : sum + Number(it.nominal || 0), 0);
        return Math.max(0, totalHarga - otherAllocated);
    }

    function openAddTerminModal() {
        const totalHarga = getTotalHargaNumber();
        if (totalHarga <= 0) {
            alert('Silakan masukkan Total Harga terlebih dahulu sebelum menambahkan Termin of Payment.');
            const field = document.getElementById('totalHargaField');
            if (field) {
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                field.focus();
            }
            return;
        }

        const currentAllocated = terminList.reduce((sum, item) => sum + Number(item.nominal || 0), 0);
        const sisaAlokasi = Math.max(0, totalHarga - currentAllocated);
        if (sisaAlokasi <= 0) {
            alert('Total alokasi termin sudah mencapai 100% dari Total Harga Penawaran (' + formatRupiah(totalHarga) + '). Anda tidak dapat menambah termin baru kecuali mengubah atau menghapus termin yang sudah ada.');
            return;
        }

        const sisaPct = ((sisaAlokasi / totalHarga) * 100).toFixed(1).replace('.0', '');

        document.getElementById('modalTerminIndex').value = '-1';
        document.getElementById('modalTerminLabel').innerHTML = '<i class="bi bi-plus-circle text-primary me-2"></i>Tambah Termin';
        document.getElementById('modalTerminName').value = 'Termin ' + (terminList.length + 1);
        document.getElementById('modalTerminBadge').value = '';
        document.getElementById('modalTerminDeliverable').value = '';
        document.getElementById('modalTerminNominal').value = '';

        // Tampilkan alert box di dalam modal dengan total harga acuan dan sisa alokasi
        document.getElementById('modalAlertTotalHarga').innerText = formatRupiah(totalHarga);
        document.getElementById('modalAlertSisaAlokasi').innerText = `${formatRupiah(sisaAlokasi)} (${sisaPct}%)`;

        const helper = document.getElementById('modalCalcHelperText');
        if (helper) helper.style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('terminModal'));
        modal.show();
    }

    function editTermin(idx) {
        if (idx < 0 || idx >= terminList.length) return;
        const item = terminList[idx];
        const totalHarga = getTotalHargaNumber();

        const otherAllocated = terminList.reduce((sum, it, i) => i === idx ? sum : sum + Number(it.nominal || 0), 0);
        const sisaAlokasi = Math.max(0, totalHarga - otherAllocated);
        const sisaPct = totalHarga > 0 ? ((sisaAlokasi / totalHarga) * 100).toFixed(1).replace('.0', '') : 0;

        document.getElementById('modalTerminIndex').value = idx;
        document.getElementById('modalTerminLabel').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit ' + item.name;
        document.getElementById('modalTerminName').value = item.name || '';
        document.getElementById('modalTerminBadge').value = (item.badge || '').replace(/[^0-9.,]/g, '');
        document.getElementById('modalTerminDeliverable').value = item.deliverable || '';
        document.getElementById('modalTerminNominal').value = item.nominal ? Number(item.nominal).toLocaleString('id-ID') : '';

        document.getElementById('modalAlertTotalHarga').innerText = formatRupiah(totalHarga);
        document.getElementById('modalAlertSisaAlokasi').innerText = `${formatRupiah(sisaAlokasi)} (${sisaPct}%)`;

        const helper = document.getElementById('modalCalcHelperText');
        if (helper && item.nominal && totalHarga > 0) {
            const pct = ((item.nominal / totalHarga) * 100).toFixed(1).replace('.0', '');
            helper.innerHTML = `<i class="bi bi-calculator me-1"></i>Terhitung: <strong style="color: #0072D2;">${formatRupiah(item.nominal)}</strong> (${pct}% dari Total Harga ${formatRupiah(totalHarga)})`;
            helper.style.display = 'block';
        } else if (helper) {
            helper.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('terminModal'));
        modal.show();
    }

    function filterPercentageKeys(e) {
        // Izinkan: backspace (8), tab (9), enter (13), escape (27), delete (46), panah (37-40), home (36), end (35)
        if ([8, 9, 13, 27, 46, 35, 36, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
            // Izinkan kombinasi shortcut: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.ctrlKey === true || e.metaKey === true)) {
            return;
        }
        // Izinkan titik (.) atau koma (,) untuk desimal maksimal 1 kali
        if (e.key === '.' || e.key === ',' || e.keyCode === 190 || e.keyCode === 188 || e.keyCode === 110) {
            const val = e.target.value;
            if (val.indexOf('.') === -1 && val.indexOf(',') === -1) {
                return;
            } else {
                e.preventDefault();
                return;
            }
        }
        // Cegah huruf (seperti 'a', 'e', dll) dan karakter lain (hanya angka 0-9)
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    }

    function onTerminBadgeInput(input) {
        // Mencegah dan membersihkan paste huruf seperti 'aaaaaa' atau '10e'
        input.value = input.value.replace(/[^0-9.,]/g, '');

        const totalHarga = getTotalHargaNumber();
        if (totalHarga <= 0) return;

        const maxAllowed = getMaxAllowedNominalForModal();
        const maxPercent = totalHarga > 0 ? (maxAllowed / totalHarga) * 100 : 0;

        let val = input.value.trim().replace(',', '.');
        let percent = parseFloat(val);

        const nominalInput = document.getElementById('modalTerminNominal');
        const helper = document.getElementById('modalCalcHelperText');

        if (!isNaN(percent) && percent >= 0) {
            let isClamped = false;
            if (percent > maxPercent) {
                percent = Math.floor(maxPercent * 10) / 10;
                input.value = percent;
                isClamped = true;
            }

            let nominal = Math.round(totalHarga * (percent / 100));
            if (nominal > maxAllowed) {
                nominal = maxAllowed;
                isClamped = true;
            }

            if (nominalInput) {
                nominalInput.value = Number(nominal).toLocaleString('id-ID');
            }
            if (helper) {
                const warn = isClamped ? `<div class="text-danger small mt-0.5"><i class="bi bi-shield-exclamation me-1"></i>Dibatasi otomatis agar tidak melebihi sisa alokasi maksimal (${formatRupiah(maxAllowed)}).</div>` : '';
                helper.innerHTML = `<i class="bi bi-calculator me-1"></i>Terhitung: <strong style="color: #0072D2;">${formatRupiah(nominal)}</strong> (${percent}% dari Total Harga ${formatRupiah(totalHarga)})${warn}`;
                helper.style.display = 'block';
            }
        } else {
            if (helper) helper.style.display = 'none';
        }
    }

    function applyQuickPercent(percent) {
        const totalHarga = getTotalHargaNumber();
        const maxAllowed = getMaxAllowedNominalForModal();
        const maxPercent = totalHarga > 0 ? Math.floor((maxAllowed / totalHarga) * 100 * 10) / 10 : 0;

        if (percent > maxPercent) {
            percent = maxPercent;
        }

        const badgeInput = document.getElementById('modalTerminBadge');
        if (badgeInput) {
            badgeInput.value = percent;
            onTerminBadgeInput(badgeInput);
        }
    }

    function applyQuickRemainingPercent() {
        const totalHarga = getTotalHargaNumber();
        const maxAllowed = getMaxAllowedNominalForModal();
        if (totalHarga > 0 && maxAllowed > 0) {
            const pct = Math.floor(((maxAllowed / totalHarga) * 100) * 10) / 10;
            applyQuickPercent(pct);
        }
    }

    function onTerminNominalInput(input) {
        const totalHarga = getTotalHargaNumber();
        const raw = input.value.replace(/[^0-9]/g, '');
        let nominal = raw === '' ? 0 : parseInt(raw, 10);
        const maxAllowed = getMaxAllowedNominalForModal();
        let isClamped = false;

        // Cegah input melebihi sisa alokasi / total harga
        if (maxAllowed >= 0 && nominal > maxAllowed) {
            nominal = maxAllowed;
            isClamped = true;
        }

        input.value = (nominal === 0 && raw === '') ? '' : Number(nominal).toLocaleString('id-ID');

        const helper = document.getElementById('modalCalcHelperText');
        const badgeInput = document.getElementById('modalTerminBadge');

        if (totalHarga > 0 && nominal > 0) {
            const pct = ((nominal / totalHarga) * 100).toFixed(1).replace('.0', '');
            if (badgeInput) {
                badgeInput.value = pct;
            }
            if (helper) {
                const warn = isClamped ? `<div class="text-danger small mt-0.5"><i class="bi bi-shield-exclamation me-1"></i>Dibatasi otomatis agar tidak melebihi sisa alokasi (${formatRupiah(maxAllowed)}).</div>` : '';
                helper.innerHTML = `<i class="bi bi-calculator me-1"></i>Terhitung: <strong style="color: #0072D2;">${formatRupiah(nominal)}</strong> (${pct}% dari Total Harga ${formatRupiah(totalHarga)})${warn}`;
                helper.style.display = 'block';
            }
        } else {
            if (helper) helper.style.display = 'none';
        }
    }

    function saveTerminModal() {
        const idx = parseInt(document.getElementById('modalTerminIndex').value, 10);
        const name = document.getElementById('modalTerminName').value.trim();
        const deliverable = document.getElementById('modalTerminDeliverable').value.trim();
        const nominalRaw = document.getElementById('modalTerminNominal').value.replace(/[^0-9]/g, '');
        const nominal = parseInt(nominalRaw, 10) || 0;
        const totalHarga = getTotalHargaNumber();
        const maxAllowed = getMaxAllowedNominalForModal();

        if (!name || !deliverable) {
            alert('Mohon lengkapi Nama Termin dan Milestone Deliverable.');
            return;
        }

        if (nominal <= 0) {
            alert('Mohon masukkan nominal atau persentase termin yang valid (lebih dari 0).');
            return;
        }

        if (nominal > maxAllowed) {
            alert('Nominal termin (' + formatRupiah(nominal) + ') melebihi sisa alokasi yang tersedia (' + formatRupiah(maxAllowed) + ').');
            return;
        }

        // Format badge selalu berupa angka persentase bersih dengan simbol '%'
        const rawPercent = document.getElementById('modalTerminBadge').value.replace(/[^0-9.,]/g, '').replace(',', '.');
        const pctNum = parseFloat(rawPercent) || 0;
        let badge = '';

        if (pctNum > 0) {
            badge = pctNum + '%';
        } else if (totalHarga > 0 && nominal > 0) {
            badge = ((nominal / totalHarga) * 100).toFixed(1).replace('.0', '') + '%';
        } else {
            badge = 'Termin';
        }

        const dataObj = { name, badge, deliverable, nominal };

        if (idx >= 0 && idx < terminList.length) {
            terminList[idx] = dataObj;
        } else {
            terminList.push(dataObj);
        }

        renderTerminTable();
        const modalEl = document.getElementById('terminModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    }

    function deleteTermin(idx) {
        terminList.splice(idx, 1);
        renderTerminTable();
    }

    function onTotalHargaChange(input) {
        const raw = input.value.replace(/[^0-9]/g, '');
        const num = raw === '' ? 0 : parseInt(raw, 10);
        input.value = num === 0 && raw === '' ? '' : formatRupiah(num);

        // Perhitungan PPN Hitung Mundur UU HPP No. 7/2021 (11%)
        const nilaiLayanan = Math.round(num / 1.11);
        const ppn = num - nilaiLayanan;

        document.getElementById('nilaiLayananText').innerText = formatRupiah(nilaiLayanan);
        document.getElementById('ppnText').innerText = formatRupiah(ppn);
        document.getElementById('totalPenawaranFinalText').innerText = formatRupiah(num);

        renderTerminTable();
    }

    function onFileSelected(input) {
        const nameEl = document.getElementById('dokumenFileName');
        if (input.files && input.files[0]) {
            nameEl.innerText = input.files[0].name;
            nameEl.style.color = '#0F172A';
        }
    }

    function simpanDanSubmitQuotation() {
        const totalHargaInput = document.getElementById('totalHargaField');
        const formHarga = document.getElementById('formHargaPenawaran');
        const formBiaya = document.getElementById('formBiayaPengerjaan');
        const formTermins = document.getElementById('formTerminsJson');
        const form = document.getElementById('presalesQuotationForm');

        const totalNum = getTotalHargaNumber();
        if (totalNum <= 0) {
            alert('Mohon masukkan Total Harga Penawaran terlebih dahulu.');
            if (totalHargaInput) {
                totalHargaInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                totalHargaInput.focus();
            }
            return;
        }

        const totalTermin = terminList.reduce((sum, item) => sum + Number(item.nominal || 0), 0);
        if (terminList.length > 0 && totalTermin > totalNum) {
            alert('Total nominal termin pembayaran (' + formatRupiah(totalTermin) + ') melebihi Total Harga Penawaran (' + formatRupiah(totalNum) + '). Mohon sesuaikan kembali nominal termin.');
            return;
        }

        if (terminList.length > 0 && totalTermin < totalNum) {
            const sisa = totalNum - totalTermin;
            if (!confirm('Total termin pembayaran belum mencapai 100% dari Total Harga Penawaran (masih ada sisa alokasi ' + formatRupiah(sisa) + '). Apakah Anda yakin ingin melanjutkan penyimpanan?')) {
                return;
            }
        }

        if (totalHargaInput && formHarga) {
            formHarga.value = totalNum.toString();
            if (formBiaya) {
                formBiaya.value = Math.round(totalNum / 1.11).toString();
            }
        }

        if (formTermins) {
            formTermins.value = JSON.stringify(terminList);
        }

        if (form) {
            form.submit();
        }
    }

    function notifyPaymentLocked() {
        const msg = 'Halaman Data Pembayaran & GR belum bisa dibuka. Halaman ini baru terbuka ketika CR sudah selesai atau setelah mencapai tahap Go-Live.';
        alert(msg);
        if (typeof window.showAppToast === 'function') {
            window.showAppToast(msg, false);
        }
    }

    function switchCrTab(tab) {
        if (tab === 'payment' && !{{ $isReadyInvoicing ? 'true' : 'false' }}) {
            notifyPaymentLocked();
            return;
        }

        const tabConfigs = {
            detail: { title: 'Detail Change Request', btnId: 'tabBtnDetail', contentId: 'tabContentDetail' },
            quotation: { title: 'Quotation', btnId: 'tabBtnQuotation', contentId: 'tabContentQuotation' },
            payment: { title: 'Data Pembayaran & GR', btnId: 'tabBtnPayment', contentId: 'tabContentPayment' }
        };

        const activeKey = tabConfigs[tab] ? tab : 'quotation';
        document.getElementById('pageHeaderTitle').innerText = tabConfigs[activeKey].title;

        Object.keys(tabConfigs).forEach(k => {
            const btn = document.getElementById(tabConfigs[k].btnId);
            const content = document.getElementById(tabConfigs[k].contentId);
            if (!btn || !content) return;
            if (k === activeKey) {
                btn.classList.add('active');
                content.style.display = 'block';
            } else {
                btn.classList.remove('active');
                content.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        let initialTab = 'quotation';
        const url = new URL(window.location.href);
        const q = url.searchParams.get('tab');
        if (['detail', 'quotation', 'payment'].includes(q)) {
            if (q === 'payment' && !{{ $isReadyInvoicing ? 'true' : 'false' }}) {
                initialTab = 'quotation';
            } else {
                initialTab = q;
            }
        }
        switchCrTab(initialTab);
        const thField = document.getElementById('totalHargaField');
        if (thField && thField.value) {
            onTotalHargaChange(thField);
        }
        renderTerminTable();
    });
</script>
@endsection