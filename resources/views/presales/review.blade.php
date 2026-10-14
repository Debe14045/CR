@extends('layouts.app')

@section('title', 'Detail Change Request • ' . ($changeRequest->kode_cr ?: 'CR'))

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $crTitle = $changeRequest->judul ?: 'Detail Change Request';
    $firstChar = strtoupper(substr($crTitle, 0, 1)) ?: 'C';
    $crCode = $changeRequest->kode_cr ?: '-';
    $statusKey = strtolower($changeRequest->status ?? '');

    $statusText = match($statusKey) {
        'validated', 'disetujui' => 'Disetujui',
        'development', 'dikerjakan' => 'Sedang Dikerjakan',
        'golive', 'selesai' => 'Selesai Go-Live',
        'ditolak', 'revision_needed' => 'Revisi Dibutuhkan',
        default => $changeRequest->statusLabel(),
    };

    $tglPengajuan = $changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->format('d M Y') : ($changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '-');
    $tglHeader = $changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : $tglPengajuan;
    $targetSelesai = $changeRequest->target_selesai ? $changeRequest->target_selesai->format('d M Y') : '-';

    $companyName = $changeRequest->client?->company ?: ($changeRequest->klien ?: '-');
    $clientInitial = $changeRequest->client?->nickname ?: strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $companyName), 0, 2) ?: '-');
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
    $spFile = $changeRequest->solutionPaper?->solution_paper_file ?: null;
    $spFileUrl = $spFile && Storage::disk('public')->exists($spFile) ? Storage::url($spFile) : null;
    $solutionPaperUrl = $changeRequest->solution_paper_url ?: null;

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
        'validated', 'disetujui' => 4,
        'development', 'dikerjakan', 'sit', 'uat', 'training' => 5,
        'golive', 'selesai', 'invoicing', 'awaiting_golive_validation' => 6,
        default => 2,
    };
    $pctProgress = match($currentStep) {
        1 => 17, 2 => 17, 3 => 17, 4 => 35, 5 => 70, 6 => 100, default => 17,
    };
    $stepLabels = [
        1 => ['title' => 'Diajukan', 'desc' => 'Selesai'],
        2 => ['title' => 'Review PM', 'desc' => 'Selesai'],
        3 => ['title' => 'Approval PM Head', 'desc' => 'Sedang Berjalan'],
        4 => ['title' => 'Quotation', 'desc' => 'Menunggu'],
        5 => ['title' => 'Development', 'desc' => 'Menunggu'],
        6 => ['title' => 'Invoice', 'desc' => 'Menunggu'],
    ];

    // Quotation numbers (derived, not hardcoded)
    $createdYear = $changeRequest->created_at ? $changeRequest->created_at->format('Y') : date('Y');
    $nomorQuotation = 'QT-' . $createdYear . '-' . str_pad($changeRequest->id, 3, '0', STR_PAD_LEFT);
    $tglDibuat = $changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '-';
    $berlakuSampai = $changeRequest->created_at ? $changeRequest->created_at->copy()->addDays(30)->format('d M Y') : '-';

    $hargaPenawaran = $changeRequest->harga_penawaran !== null ? (float)$changeRequest->harga_penawaran : null;
    $totalPenawaranFinal = $hargaPenawaran ?? $estimasiNilai;

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
    .pm-filter-tab:not(.active):hover { background: rgba(11,110,189,0.08) !important; }
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
        <button type="button" class="pm-filter-tab" id="tabBtnPayment" onclick="switchCrTab('payment')" role="tab" aria-selected="false" style="border-top-right-radius: 11px; border-bottom-right-radius: 11px;">
            <span>Data Pembayaran &amp; GR</span>
        </button>
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

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-4 mb-4" style="border-bottom: 1.5px solid #F1F5F9;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="px-2 py-1" style="border: 1px solid #E2E8F0; border-radius: 6px; font-size: 0.76rem; font-weight: 700; color: #334155; font-family: monospace; background: #FAFAFA;">
                    ID:&nbsp;{{ $crCode }}
                </span>
                <span class="px-3 py-1 fw-semibold d-inline-flex align-items-center gap-2"
                      style="border: 1px solid #FDE68A; background: #FFFBEB; color: #B45309; border-radius: 6px; font-size: 0.76rem; font-weight: 700;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; flex-shrink: 0;"></span>
                    <span>{{ $statusText }}</span>
                </span>
                <span class="px-3 py-1 fw-semibold d-inline-flex align-items-center gap-2"
                      style="border: 1px solid #E2E8F0; background: #F8FAFC; color: #475569; border-radius: 6px; font-size: 0.76rem; font-weight: 700;">
                    Prioritas: {{ $priority }}
                </span>
            </div>
            <div class="text-muted small d-flex align-items-center gap-1.5" style="font-size: 0.8rem; color: #64748B;">
                <i class="bi bi-calendar3" style="font-size: 0.82rem; color: #94A3B8;"></i>
                <span>Tanggal Pengajuan: <strong style="color: #334155; font-weight: 700;">{{ $tglHeader }}</strong></span>
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em; color: #334155; font-weight: 800;">
                    STATUS ALUR CR
                </span>
                <span class="text-muted small" style="font-size: 0.74rem; font-weight: 600; color: #64748B;">
                    Total Progres Alur: <span style="font-weight: 700; color: #1E293B;">{{ $pctProgress }}%</span>
                </span>
            </div>
            <div class="d-flex align-items-center gap-1 mb-3" style="border-radius: 999px; overflow: hidden;">
                @for ($b = 1; $b <= 6; $b++)
                    <div class="flow-bar-item {{ $currentStep > $b ? 'completed' : ($currentStep == $b ? 'active' : '') }}" style="border-radius: 4px;"></div>
                @endfor
            </div>
            <div class="row g-2">
                @for ($step = 1; $step <= 6; $step++)
                    @php $isCompleted = $step < $currentStep; $isActive = $step === $currentStep; $stepInfo = $stepLabels[$step]; @endphp
                    <div class="col-lg-2 col-md-4 col-6">
                        @if ($isCompleted)
                            <div class="flow-step-box h-100" style="background: #F0FDF4; border: 1.5px solid #86EFAC;">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.74rem; color: #0F172A;">{{ $stepInfo['title'] }}</div>
                                    <div class="text-success small fw-semibold" style="font-size: 0.65rem;">Selesai</div>
                                </div>
                            </div>
                        @elseif ($isActive)
                            <div class="flow-step-box h-100" style="background: #FFFFFF; border: 2px solid #0284C7; box-shadow: 0 1px 3px rgba(2,132,199,0.15);">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 18px; height: 18px; background: #0284C7; font-size: 0.65rem; flex-shrink: 0;">{{ $step }}</span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-bold" style="font-size: 0.74rem; color: #0284C7;">{{ $stepInfo['title'] }}</div>
                                    <div class="small fw-semibold" style="font-size: 0.65rem; color: #0284C7;">Sedang Berjalan</div>
                                </div>
                            </div>
                        @else
                            <div class="flow-step-box h-100" style="background: #FAFAFA; border: 1.5px solid #E2E8F0;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 18px; height: 18px; background: #E2E8F0; font-size: 0.65rem; flex-shrink: 0; color: #94A3B8;">{{ $step }}</span>
                                <div style="line-height: 1.2;">
                                    <div class="fw-semibold" style="font-size: 0.74rem; color: #64748B;">{{ $stepInfo['title'] }}</div>
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
                                    <span class="text-truncate">{{ $docUrl }}</span>
                                </a>
                            @elseif($initialFileUrl)
                                <a href="{{ $initialFileUrl }}" class="doc-pill-box">
                                    <i class="bi bi-paperclip"></i>
                                    <span class="text-truncate">{{ $initialFileName }}</span>
                                </a>
                            @else
                                <div class="text-muted small" style="font-size: 0.82rem;">Belum ada dokumen CR terlampir.</div>
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
                                <div class="text-muted small" style="font-size: 0.82rem;">Belum ada Solution Paper terlampir.</div>
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
                <span class="text-muted small" style="font-size: 0.72rem; color: #94A3B8;">Data aktual CR</span>
            </div>
            <div class="text-dark" style="font-size: 0.84rem; line-height: 1.65;">
                <div class="fw-bold mb-1" style="color: #0F172A; font-weight: 800;">1. LATAR BELAKANG &amp; TUJUAN</div>
                @if($alasanBg)
                    <p class="text-secondary mb-3" style="color: #475569 !important;">{!! nl2br(e($alasanBg)) !!}</p>
                @elseif($deskripsiFull)
                    <p class="text-secondary mb-3" style="color: #475569 !important;">{!! nl2br(e($deskripsiFull)) !!}</p>
                @else
                    <p class="text-muted mb-3" style="font-size: 0.83rem;">Belum ada latar belakang &amp; tujuan yang diisi untuk CR ini.</p>
                @endif

                <div class="fw-bold mb-1" style="color: #0F172A; font-weight: 800;">2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)</div>
                @if($deskripsiFull && $deskripsiFull !== $alasanBg)
                    <div class="text-secondary mb-3" style="color: #475569 !important;">{!! nl2br(e($deskripsiFull)) !!}</div>
                @else
                    <p class="text-muted mb-3" style="font-size: 0.83rem;">Belum ada rincian scope of work untuk CR ini.</p>
                @endif

                <div class="fw-bold mb-2" style="color: #0F172A; font-weight: 800;">3. DAMPAK TEKNIS &amp; DEPENDENCIES</div>
                @if($dependensiNote)
                    <div class="p-3" style="background: #FFFBEB; border-left: 3px solid #F59E0B; border-radius: 6px;">
                        <div style="font-size: 0.82rem; color: #92400E; font-weight: 600; line-height: 1.5;">
                            <strong style="color: #78350F; font-weight: 800;">Catatan Dependensi:</strong> {{ $dependensiNote }}
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0" style="font-size: 0.83rem;">Belum ada catatan dampak teknis untuk CR ini.</p>
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

    {{-- ========================================================= --}}
    {{-- ============ TAB 2: QUOTATION (DESAIN FIGMA) ============ --}}
    {{-- ========================================================= --}}
    <div id="tabContentQuotation" class="cr-detail-container p-4 p-md-5 mb-4">

        {{-- Card Top Row: Header Badge & Quotation Dropdown --}}
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                     style="width: 42px; height: 42px; background: #0084FF; font-size: 1.35rem; flex-shrink: 0;">
                    I
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.35rem; font-weight: 800;">
                        Integrasi API Pembayaran OVO
                    </h3>
                    <span class="badge px-3 py-1.5 fw-bold text-white d-inline-flex align-items-center gap-1.5"
                          style="background: #00A3FF; border-radius: 999px; font-size: 0.72rem; letter-spacing: 0.02em;">
                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #FFFFFF;"></span>
                        Analisa
                    </span>
                </div>
            </div>
            <div>
                <label class="form-label small fw-semibold text-secondary mb-1 d-block text-md-end" style="font-size: 0.76rem;">
                    Quotation<span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-sm" style="min-width: 170px; border-radius: 8px; border: 1.5px solid #CBD5E1; font-size: 0.84rem; font-weight: 600; color: #334155; padding: 0.45rem 0.8rem; background-color: #FFFFFF;">
                    <option value="overall" selected>Overall</option>
                    <option value="tahap1">Tahap 1</option>
                    <option value="tahap2">Tahap 2</option>
                </select>
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
                        <input type="text" class="form-control form-control-disabled-gray" value="QT-2024-112" readonly style="padding-right: 2.3rem;">
                        <i class="bi bi-lock-fill position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 0.95rem;"></i>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="field-label-sm">Tanggal Dibuat</label>
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-white" value="20 Ags 2026" style="padding-right: 2.3rem;">
                        <i class="bi bi-calendar3 position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 0.95rem;"></i>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="field-label-sm">Berlaku Sampai</label>
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-white" value="20 Sep 2026" style="padding-right: 2.3rem;">
                        <i class="bi bi-calendar3 position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 0.95rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. INFORMASI PENGIRIMAN QUOTATION --}}
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="section-num-badge">2</span>
                    <span class="section-title-label">INFORMASI PENGIRIMAN QUOTATION</span>
                </div>
                <span class="text-muted small fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em;">D2CECE</span>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6 col-12">
                    <label class="field-label-sm">Nama Perusahaan<span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" style="background-color: #CBD5E1; font-weight: 600;">
                        <option selected>PT Maju Bersama</option>
                        <option>PT Teknologi Nusantara</option>
                        <option>PT Delta Solusi</option>
                        <option>PT Global Inovasi</option>
                    </select>
                </div>
                <div class="col-md-6 col-12">
                    <label class="field-label-sm">Nama PIC<span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" style="background-color: #FFFFFF;">
                        <option selected>Totok Antok</option>
                        <option>Andik Virmansyah</option>
                        <option>Rizky Pratama</option>
                        <option>Budi Santoso</option>
                    </select>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 col-12">
                    <label class="field-label-sm">Metode Pengiriman<span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom" style="background-color: #FFFFFF;">
                        <option selected>Email</option>
                        <option>WhatsApp Business</option>
                        <option>Client Portal</option>
                        <option>Kurir / Hardcopy</option>
                    </select>
                </div>
                <div class="col-md-6 col-12">
                    <label class="field-label-sm">Alamat Email Tujuan</label>
                    <input type="email" class="form-control form-control-white" value="totokantok@gmail.com">
                </div>
            </div>
        </div>

        {{-- 3. MANDAYS --}}
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="section-num-badge">3</span>
                <span class="section-title-label">MANDAYS</span>
            </div>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <label class="field-label-sm">Analisis (hari)<span class="text-danger">*</span></label>
                    <input type="number" id="inputMdAnalisis" class="form-control form-control-disabled-gray" value="0" oninput="recalcMandays()">
                </div>
                <div class="col-md-3 col-6">
                    <label class="field-label-sm">Development (hari)<span class="text-danger">*</span></label>
                    <input type="number" id="inputMdDev" class="form-control form-control-disabled-gray" value="0" oninput="recalcMandays()">
                </div>
                <div class="col-md-3 col-6">
                    <label class="field-label-sm">Testing (hari)<span class="text-danger">*</span></label>
                    <input type="number" id="inputMdTest" class="form-control form-control-disabled-gray" value="0" oninput="recalcMandays()">
                </div>
                <div class="col-md-3 col-6">
                    <label class="field-label-sm">Total Mandays</label>
                    <input type="number" id="inputMdTotal" class="form-control form-control-disabled-gray fw-bold" value="10" readonly>
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
                <button type="button" class="btn text-white fw-bold d-inline-flex align-items-center gap-1.5 px-3 py-1.5"
                        style="background: #22C55E; border-radius: 8px; font-size: 0.78rem; border: none; box-shadow: 0 2px 6px rgba(34,197,94,0.25);"
                        onclick="openAddTerminModal()">
                    <i class="bi bi-plus-lg"></i> Tambah Termin
                </button>
            </div>

            <div class="table-responsive" style="border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; background: #FFFFFF;">
                <table class="table mb-0 align-middle termin-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">NO</th>
                            <th style="min-width: 200px;">TERMIN OF PAYMENT</th>
                            <th style="min-width: 380px;">KETERANGAN / MILESTONE DELIVERABLE</th>
                            <th class="text-end" style="min-width: 150px;">NOMINAL (IDR)</th>
                            <th class="text-center" style="width: 170px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="terminTableBody">
                        <tr id="terminRow_0">
                            <td class="text-center">
                                <div class="termin-no-badge">1</div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #0F172A; font-size: 0.85rem;">Termin Of Payment 1</div>
                                <div class="d-flex align-items-center gap-1.5 mt-1">
                                    <span style="background: #DBEAFE; color: #2563EB; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">20% DP</span>
                                    <span style="font-size: 0.72rem; color: #94A3B8;">Tahap Awal</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.83rem; line-height: 1.35;">Kickoff meeting, spesifikasi arsitektur &amp; persetujuan analisa API OVO</div>
                                <div class="text-muted mt-0.5" style="font-size: 0.72rem; color: #94A3B8 !important;">Disertai Berita Acara Analisa (BAA) bertandatangan</div>
                            </td>
                            <td class="text-end fw-bold" style="color: #0F172A; font-size: 0.88rem;">
                                Rp 5.000.000
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px;">
                                    <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                            style="background: #F59E0B; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                            onclick="openEditTerminModal(0)">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                            style="background: #EF4444; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                            onclick="deleteTermin(0)">
                                        <i class="bi bi-trash3-fill"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="terminRow_1">
                            <td class="text-center">
                                <div class="termin-no-badge">2</div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #0F172A; font-size: 0.85rem;">Termin Of Payment 2</div>
                                <div class="d-flex align-items-center gap-1.5 mt-1">
                                    <span style="background: #EDE9FE; color: #7C3AED; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">35% Dev</span>
                                    <span style="font-size: 0.72rem; color: #94A3B8;">Implementasi Core</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.83rem; line-height: 1.35;">Penyelesaian integrasi endpoint OAuth 2.0, Create Order &amp; Callback Webhook</div>
                                <div class="text-muted mt-0.5" style="font-size: 0.72rem; color: #94A3B8 !important;">Deployment di staging environment &amp; verifikasi Sandbox OVO</div>
                            </td>
                            <td class="text-end fw-bold" style="color: #0F172A; font-size: 0.88rem;">
                                Rp 7.000.000
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px;">
                                    <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                            style="background: #F59E0B; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                            onclick="openEditTerminModal(1)">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                            style="background: #EF4444; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                            onclick="deleteTermin(1)">
                                        <i class="bi bi-trash3-fill"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="terminRow_2">
                            <td class="text-center">
                                <div class="termin-no-badge">3</div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #0F172A; font-size: 0.85rem;">Termin Of Payment 3</div>
                                <div class="d-flex align-items-center gap-1.5 mt-1">
                                    <span style="background: #D1FAE5; color: #059669; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">45% Pelunasan</span>
                                    <span style="font-size: 0.72rem; color: #94A3B8;">Final Release</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 0.83rem; line-height: 1.35;">Pelaksanaan UAT berhasil, serah terima source code, dan Go-Live Production</div>
                                <div class="text-muted mt-0.5" style="font-size: 0.72rem; color: #94A3B8 !important;">Penandatanganan BAST (Berita Acara Serah Terima)</div>
                            </td>
                            <td class="text-end fw-bold" style="color: #0F172A; font-size: 0.88rem;">
                                Rp 8.000.000
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px;">
                                    <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                            style="background: #F59E0B; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                            onclick="openEditTerminModal(2)">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                            style="background: #EF4444; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                            onclick="deleteTermin(2)">
                                        <i class="bi bi-trash3-fill"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end align-items-center px-4 py-3" style="background: #FFFFFF; border-top: 1px solid #E2E8F0;">
                    <div class="fw-bold me-4" style="font-size: 0.86rem; color: #334155;">
                        Subtotal Termin (<span id="milestoneCountText">3</span> Milestone):
                    </div>
                    <div class="fw-bold" id="milestoneSubtotalText" style="font-size: 0.96rem; color: #0F172A;">
                        Rp 20.000.000
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM SECTION: TOTAL HARGA, DOKUMEN & SUMMARY CARD --}}
        <div class="row g-4 align-items-end mb-4">
            <div class="col-lg-6 col-12">
                <div class="mb-3">
                    <label class="field-label-sm">Total Harga<span class="text-danger">*</span></label>
                    <input type="text" id="totalHargaField" class="form-control" value="Rp 39.500.000" oninput="onTotalHargaChange(this)"
                           style="background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 8px; font-size: 0.95rem; font-weight: 600; color: #0F172A; padding: 0.65rem 0.9rem;">
                </div>
                <div>
                    <label class="field-label-sm">Dokumen<span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center gap-2 p-1.5" style="border: 1.5px solid #CBD5E1; border-radius: 8px; background: #FFFFFF;">
                        <button type="button" class="btn text-white d-flex align-items-center gap-1.5 px-3 py-1.5"
                                style="background: #0B2545; border-radius: 6px; font-size: 0.8rem; font-weight: 700; border: none;"
                                onclick="document.getElementById('dokumenFileInput').click();">
                            <i class="bi bi-upload"></i> Pilih File
                        </button>
                        <input type="file" id="dokumenFileInput" style="display: none;" onchange="onFileSelected(this)">
                        <span id="dokumenFileName" class="text-muted small ms-1" style="font-size: 0.82rem;">Tidak ada file yang dipilih</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="p-3.5 p-md-4" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.86rem;">
                        <span style="color: #64748B;">Total Nilai Layanan (10 MD)</span>
                        <span class="fw-bold" id="nilaiLayananText" style="color: #0F172A;">Rp 35.585.586</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.86rem;">
                        <span style="color: #64748B;">PPN 11% (UU HPP No. 7/2021)</span>
                        <span class="fw-bold" id="ppnText" style="color: #0F172A;">Rp 3.914.414</span>
                    </div>
                    <div style="border-top: 1px solid #E2E8F0; margin: 0.65rem 0;"></div>
                    <div class="d-flex justify-content-between align-items-center pt-1">
                        <div>
                            <div class="fw-bold" style="font-size: 0.95rem; color: #0F172A;">Total Penawaran Final</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Nett Termasuk Pajak</div>
                        </div>
                        <div class="fw-bold" id="totalPenawaranFinalText" style="font-size: 1.35rem; color: #0072D2;">
                            Rp 39.500.000
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SIMPAN & SUBMIT BUTTON --}}
        <div class="d-flex justify-content-end">
            <button type="button" class="btn text-white fw-bold px-4 py-2.5 d-inline-flex align-items-center gap-2"
                    style="background: #22C55E; border-radius: 8px; font-size: 0.88rem; border: none; box-shadow: 0 4px 10px rgba(34,197,94,0.3);"
                    onclick="simpanDanSubmitQuotation()">
                <i class="bi bi-check2" style="font-size: 1.15rem;"></i> Simpan &amp; Submit
            </button>
        </div>

    </div>

    {{-- ============ TAB: DETAIL PEMBAYARAN & QR ============ --}}
    <div id="tabContentPayment" class="cr-detail-container p-4 p-md-5 mb-4" style="display: none;">

        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
            <h4 class="fw-bold mb-0" style="font-size: 1.1rem; color: #0F172A;">Detail Pembayaran &amp; QR</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('change-requests.invoice', $changeRequest) }}" class="btn btn-outline-primary btn-sm fw-bold px-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh Invoice PDF
                </a>
                @if($isReadyInvoicing)
                    <button type="button" class="btn btn-sm fw-bold text-white px-3" style="background: #0D9488; border: none; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#invoiceModalPresales">
                        <i class="bi bi-plus-lg me-1"></i> Catat Invoice
                    </button>
                @endif
            </div>
        </div>

        @if(!$isReadyInvoicing)
            <div class="p-3 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 0.83rem; color: #475569;">
                <i class="bi bi-info-circle me-1 text-primary"></i>
                Pencatatan invoice baru dapat dilakukan setelah proyek <strong>Go-Live dan divalidasi PM Head</strong> (status saat ini: <strong>{{ $changeRequest->statusLabel() }}</strong>). Data di bawah ini bersifat baca-dan-pantau untuk Presales.
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-12">
                <div class="cr-subcard p-3 text-center h-100">
                    <div class="text-muted small" style="font-size: 0.72rem; font-weight: 700;">TOTAL INVOICED</div>
                    <div class="fw-bold" style="font-size: 1.2rem; color: #0F172A;">{{ $fmtIdr($totalInvoiced) }}</div>
                    <div class="text-muted small">{{ $invoices->count() }} invoice</div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="cr-subcard p-3 text-center h-100">
                    <div class="text-muted small" style="font-size: 0.72rem; font-weight: 700;">LUNAS (PAID)</div>
                    <div class="fw-bold" style="font-size: 1.2rem; color: #15803D;">{{ $fmtIdr($totalPaid) }}</div>
                    <div class="text-muted small">{{ $invoices->where('payment_status', 'paid')->count() }} lunas</div>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="cr-subcard p-3 text-center h-100">
                    <div class="text-muted small" style="font-size: 0.72rem; font-weight: 700;">MENUNGGU (PENDING)</div>
                    <div class="fw-bold" style="font-size: 1.2rem; color: #B45309;">{{ $fmtIdr($totalPending) }}</div>
                    <div class="text-muted small">{{ $invoices->where('payment_status', '!=', 'paid')->count() }} pending</div>
                </div>
            </div>
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B;">DAFTAR INVOICE &amp; TERMIN</div>
            @forelse($invoices as $inv)
                <div class="p-3 mb-3" style="border: 1px solid #E2E8F0; border-radius: 12px; background: #FFFFFF;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="fw-bold" style="font-size: 0.9rem; color: #0F172A;">
                            {{ $inv->invoice_number ?: ($inv->term_name ?: 'Invoice #' . $inv->id) }}
                            <span class="badge ms-2 px-2 py-1" style="background: {{ $inv->payment_status === 'paid' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $inv->payment_status === 'paid' ? '#15803D' : '#B45309' }}; border-radius: 6px; font-size: 0.7rem;">
                                {{ strtoupper($inv->payment_status ?: 'pending') }}
                            </span>
                        </div>
                        <div class="fw-bold" style="color: #0063D7;">{{ $fmtIdr($inv->nominal) }}</div>
                    </div>
                    <div class="text-muted small mb-2" style="font-size: 0.8rem;">
                        Tgl Invoice: <strong>{{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : '-' }}</strong>
                        &bull; Plan Bayar: <strong>{{ $inv->planned_payment_date ? $inv->planned_payment_date->format('d M Y') : '-' }}</strong>
                        &bull; Aktual: <strong>{{ $inv->actual_payment_date ? $inv->actual_payment_date->format('d M Y') : '-' }}</strong>
                        @if($inv->term_name) &bull; ToP: <strong>{{ $inv->term_name }}</strong> @endif
                        @if($inv->percentage) ({{ $inv->percentage }}%) @endif
                    </div>
                    @if($inv->job_description)
                        <div class="small mb-2" style="font-size: 0.82rem; color: #334155;">{{ $inv->job_description }}</div>
                    @endif
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @if($inv->gr_file)<span class="badge px-2 py-1" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #475569; font-size: 0.7rem;"><i class="bi bi-paperclip me-1"></i>GR: {{ $inv->gr_number ?: basename($inv->gr_file) }}</span>@endif
                        @if($inv->journal_file)<span class="badge px-2 py-1" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #475569; font-size: 0.7rem;"><i class="bi bi-journal-text me-1"></i>Jurnal: {{ $inv->journal_number ?: basename($inv->journal_file) }}</span>@endif
                        @if($inv->ba_file)<span class="badge px-2 py-1" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #475569; font-size: 0.7rem;"><i class="bi bi-file-earmark-check me-1"></i>BA</span>@endif
                        @if($inv->quotation_file)<span class="badge px-2 py-1" style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1D4ED8; font-size: 0.7rem;"><i class="bi bi-file-earmark-pdf me-1"></i>Quotation</span>@endif
                        @if($inv->signed_quotation_file)<span class="badge px-2 py-1" style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; font-size: 0.7rem;"><i class="bi bi-patch-check me-1"></i>Signed Quotation</span>@endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                    <div class="fw-bold text-dark">Belum ada data invoice untuk CR ini.</div>
                    <div class="small">Invoice dapat dicatat setelah Go-Live divalidasi PM Head.</div>
                </div>
            @endforelse
        </div>

        <div class="cr-subcard p-4">
            <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B;">RINGKASAN NILAI CR</div>
            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <div class="text-muted small">Biaya Pengerjaan</div>
                    <div class="fw-bold">{{ $changeRequest->biaya_pengerjaan !== null ? $fmtIdr($changeRequest->biaya_pengerjaan) : '-' }}</div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="text-muted small">Harga Penawaran</div>
                    <div class="fw-bold">{{ $hargaPenawaran !== null ? $fmtIdr($hargaPenawaran) : '-' }}</div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="text-muted small">Status Invoicing</div>
                    <div class="fw-bold">{{ $changeRequest->invoicing_status ?: $changeRequest->statusLabel() }}</div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Modal Catat Invoice (Presales, hanya jika Go-Live validated) --}}
@if($isReadyInvoicing)
<div class="modal fade" id="invoiceModalPresales" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="bi bi-receipt-cutoff me-2" style="color: #0D9488;"></i>Pencatatan Invoice ({{ $crCode }})
                    </h5>
                    <p class="text-muted small mb-0 mt-1">{{ $crTitle }} &bull; {{ $companyName }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('change-requests.invoices.store', $changeRequest) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Nomor Invoice <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_number" class="form-control" placeholder="Contoh: INV/ITP/2026/09/001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Tanggal Invoice <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Nominal Invoice (Rp) <span class="text-danger">*</span></label>
                            <input type="number" step="1" min="0" name="nominal" class="form-control" value="{{ $totalPenawaranFinal }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">Term of Payment (ToP)</label>
                            <select name="term_name" class="form-select">
                                <option value="Pelunasan 100%">Pelunasan 100% (Single Payment)</option>
                                <option value="DP 30% - Pelunasan 70%">DP 30% - Pelunasan 70%</option>
                                <option value="DP 20% - Pelunasan 80%">DP 20% - Pelunasan 80%</option>
                                <option value="Termin Progresif">Termin Progresif (Custom)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                            <textarea name="job_description" rows="2" class="form-control" required>{{ $crTitle }} - {{ $projectName }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Plan Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="planned_payment_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Aktual Tanggal Bayar</label>
                            <input type="date" name="actual_payment_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="pending" selected>Pending</option>
                                <option value="paid">Paid (Lunas)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-dark mb-1">File GR</label>
                            <input type="file" name="gr_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-dark mb-1">Nomor GR</label>
                            <input type="text" name="gr_number" class="form-control form-control-sm" placeholder="Nomor GR dari Klien">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-dark mb-1">File Jurnal</label>
                            <input type="file" name="journal_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-dark mb-1">Nomor Jurnal</label>
                            <input type="text" name="journal_number" class="form-control form-control-sm" placeholder="Nomor Bukti Jurnal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-dark mb-1">File BA</label>
                            <input type="file" name="ba_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-dark mb-1">File Quotation</label>
                            <input type="file" name="quotation_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.zip">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm px-4 fw-semibold text-white" style="background: #0D9488; border-color: #0D9488;">
                        <i class="bi bi-save me-1"></i> Simpan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Tambah / Edit Termin --}}
<div class="modal fade" id="terminModal" tabindex="-1" aria-labelledby="modalTerminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTerminLabel">
                    <i class="bi bi-calendar2-check text-primary me-2"></i>Tambah Termin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="modalTerminIndex" value="-1">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Termin <span class="text-danger">*</span></label>
                    <input type="text" id="modalTerminName" class="form-control" placeholder="Contoh: Termin Of Payment 1" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Label / Persentase</label>
                        <input type="text" id="modalTerminBadge" class="form-control" placeholder="Contoh: 20% DP">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Sub-label Tahap</label>
                        <input type="text" id="modalTerminSublabel" class="form-control" placeholder="Contoh: Tahap Awal">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Milestone Deliverable <span class="text-danger">*</span></label>
                    <textarea id="modalTerminDeliverable" class="form-control" rows="2" placeholder="Deskripsi milestone deliverable" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Keterangan Tambahan / Dokumen</label>
                    <input type="text" id="modalTerminSubtext" class="form-control" placeholder="Contoh: Disertai Berita Acara Analisa (BAA)">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Nominal (IDR) <span class="text-danger">*</span></label>
                    <input type="number" id="modalTerminNominal" class="form-control" placeholder="Contoh: 5000000" min="0" required>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm px-4 fw-bold text-white" style="background: #22C55E; border: none; border-radius: 8px;" onclick="saveTerminModal()">
                    <i class="bi bi-check2 me-1"></i>Simpan Termin
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Feedback Sukses --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="quotationSuccessToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 font-weight-semibold" style="font-size: 0.9rem;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span id="quotationToastMessage">Quotation berhasil disimpan &amp; disubmit!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    // Data list termin
    let terminList = [
        {
            name: 'Termin Of Payment 1',
            badge: '20% DP',
            badgeColor: '#DBEAFE',
            badgeTextColor: '#2563EB',
            sublabel: 'Tahap Awal',
            deliverable: 'Kickoff meeting, spesifikasi arsitektur & persetujuan analisa API OVO',
            subtext: 'Disertai Berita Acara Analisa (BAA) bertandatangan',
            nominal: 5000000
        },
        {
            name: 'Termin Of Payment 2',
            badge: '35% Dev',
            badgeColor: '#EDE9FE',
            badgeTextColor: '#7C3AED',
            sublabel: 'Implementasi Core',
            deliverable: 'Penyelesaian integrasi endpoint OAuth 2.0, Create Order & Callback Webhook',
            subtext: 'Deployment di staging environment & verifikasi Sandbox OVO',
            nominal: 7000000
        },
        {
            name: 'Termin Of Payment 3',
            badge: '45% Pelunasan',
            badgeColor: '#D1FAE5',
            badgeTextColor: '#059669',
            sublabel: 'Final Release',
            deliverable: 'Pelaksanaan UAT berhasil, serah terima source code, dan Go-Live Production',
            subtext: 'Penandatanganan BAST (Berita Acara Serah Terima)',
            nominal: 8000000
        }
    ];

    function formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    function renderTerminTable() {
        const tbody = document.getElementById('terminTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';
        let total = 0;

        terminList.forEach((item, idx) => {
            total += Number(item.nominal || 0);
            const tr = document.createElement('tr');
            tr.id = 'terminRow_' + idx;
            tr.innerHTML = `
                <td class="text-center">
                    <div class="termin-no-badge">${idx + 1}</div>
                </td>
                <td>
                    <div class="fw-bold" style="color: #0F172A; font-size: 0.85rem;">${escapeHtml(item.name)}</div>
                    <div class="d-flex align-items-center gap-1.5 mt-1">
                        <span style="background: ${item.badgeColor || '#DBEAFE'}; color: ${item.badgeTextColor || '#2563EB'}; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">${escapeHtml(item.badge || '')}</span>
                        <span style="font-size: 0.72rem; color: #94A3B8;">${escapeHtml(item.sublabel || '')}</span>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold text-dark" style="font-size: 0.83rem; line-height: 1.35;">${escapeHtml(item.deliverable)}</div>
                    <div class="text-muted mt-0.5" style="font-size: 0.72rem; color: #94A3B8 !important;">${escapeHtml(item.subtext || '')}</div>
                </td>
                <td class="text-end fw-bold" style="color: #0F172A; font-size: 0.88rem;">
                    ${formatRupiah(item.nominal)}
                </td>
                <td class="text-center">
                    <div class="d-inline-flex align-items-center justify-content-center" style="gap: 8px;">
                        <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                style="background: #F59E0B; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                onclick="openEditTerminModal(${idx})">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                style="background: #EF4444; border-radius: 6px; font-size: 0.75rem; border: none; padding: 5px 11px;"
                                onclick="deleteTermin(${idx})">
                            <i class="bi bi-trash3-fill"></i> Hapus
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        const countEl = document.getElementById('milestoneCountText');
        const subtotalEl = document.getElementById('milestoneSubtotalText');
        if (countEl) countEl.innerText = terminList.length;
        if (subtotalEl) subtotalEl.innerText = formatRupiah(total);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    function openAddTerminModal() {
        document.getElementById('modalTerminIndex').value = '-1';
        document.getElementById('modalTerminLabel').innerHTML = '<i class="bi bi-plus-circle text-primary me-2"></i>Tambah Termin';
        document.getElementById('modalTerminName').value = 'Termin Of Payment ' + (terminList.length + 1);
        document.getElementById('modalTerminBadge').value = '';
        document.getElementById('modalTerminSublabel').value = '';
        document.getElementById('modalTerminDeliverable').value = '';
        document.getElementById('modalTerminSubtext').value = '';
        document.getElementById('modalTerminNominal').value = '';
        const modal = new bootstrap.Modal(document.getElementById('terminModal'));
        modal.show();
    }

    function openEditTerminModal(idx) {
        const item = terminList[idx];
        if (!item) return;
        document.getElementById('modalTerminIndex').value = idx;
        document.getElementById('modalTerminLabel').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i>Edit Termin ' + (idx + 1);
        document.getElementById('modalTerminName').value = item.name;
        document.getElementById('modalTerminBadge').value = item.badge || '';
        document.getElementById('modalTerminSublabel').value = item.sublabel || '';
        document.getElementById('modalTerminDeliverable').value = item.deliverable;
        document.getElementById('modalTerminSubtext').value = item.subtext || '';
        document.getElementById('modalTerminNominal').value = item.nominal;
        const modal = new bootstrap.Modal(document.getElementById('terminModal'));
        modal.show();
    }

    function saveTerminModal() {
        const idx = parseInt(document.getElementById('modalTerminIndex').value, 10);
        const name = document.getElementById('modalTerminName').value.trim();
        const badge = document.getElementById('modalTerminBadge').value.trim();
        const sublabel = document.getElementById('modalTerminSublabel').value.trim();
        const deliverable = document.getElementById('modalTerminDeliverable').value.trim();
        const subtext = document.getElementById('modalTerminSubtext').value.trim();
        const nominal = parseInt(document.getElementById('modalTerminNominal').value, 10) || 0;

        if (!name || !deliverable) {
            alert('Mohon lengkapi Nama Termin dan Milestone Deliverable.');
            return;
        }

        const colors = [
            { bg: '#DBEAFE', text: '#2563EB' },
            { bg: '#EDE9FE', text: '#7C3AED' },
            { bg: '#D1FAE5', text: '#059669' },
            { bg: '#FEF3C7', text: '#D97706' },
            { bg: '#FCE7F3', text: '#DB2777' }
        ];
        const assignedColor = colors[(idx >= 0 ? idx : terminList.length) % colors.length];

        const dataObj = {
            name: name,
            badge: badge,
            badgeColor: assignedColor.bg,
            badgeTextColor: assignedColor.text,
            sublabel: sublabel,
            deliverable: deliverable,
            subtext: subtext,
            nominal: nominal
        };

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
        if (!confirm('Hapus termin ini?')) return;
        terminList.splice(idx, 1);
        renderTerminTable();
    }

    function onTotalHargaChange(input) {
        const raw = input.value.replace(/[^0-9]/g, '');
        const num = raw === '' ? 0 : parseInt(raw, 10);
        input.value = num === 0 && raw === '' ? '' : 'Rp ' + num.toLocaleString('id-ID');

        // Kalkulasi DPP dan PPN 11% (UU HPP No. 7/2021)
        const total = num;
        const nilaiLayanan = Math.round(total / 1.11);
        const ppn = total - nilaiLayanan;

        const nlEl = document.getElementById('nilaiLayananText');
        const ppnEl = document.getElementById('ppnText');
        const finalEl = document.getElementById('totalPenawaranFinalText');

        if (nlEl) nlEl.innerText = formatRupiah(nilaiLayanan);
        if (ppnEl) ppnEl.innerText = formatRupiah(ppn);
        if (finalEl) finalEl.innerText = formatRupiah(total);
    }

    function recalcMandays() {
        const a = parseInt(document.getElementById('inputMdAnalisis')?.value || 0, 10) || 0;
        const d = parseInt(document.getElementById('inputMdDev')?.value || 0, 10) || 0;
        const t = parseInt(document.getElementById('inputMdTest')?.value || 0, 10) || 0;
        const sum = a + d + t;
        const totInput = document.getElementById('inputMdTotal');
        if (totInput && sum > 0) {
            totInput.value = sum;
        }
    }

    function onFileSelected(input) {
        const nameEl = document.getElementById('dokumenFileName');
        if (!nameEl) return;
        if (input.files && input.files[0]) {
            nameEl.innerText = input.files[0].name;
            nameEl.style.color = '#0F172A';
            nameEl.style.fontWeight = '600';
        } else {
            nameEl.innerText = 'Tidak ada file yang dipilih';
            nameEl.style.color = '#64748B';
            nameEl.style.fontWeight = 'normal';
        }
    }

    function simpanDanSubmitQuotation() {
        const toastEl = document.getElementById('quotationSuccessToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
            toast.show();
        } else {
            alert('Quotation berhasil disimpan & disubmit!');
        }
    }

    function switchCrTab(tab) {
        const tabConfigs = {
            detail: { title: 'Detail Change Request', btnId: 'tabBtnDetail', contentId: 'tabContentDetail' },
            quotation: { title: 'Quotation', btnId: 'tabBtnQuotation', contentId: 'tabContentQuotation' },
            payment: { title: 'Data Pembayaran & GR', btnId: 'tabBtnPayment', contentId: 'tabContentPayment' }
        };

        const activeKey = tabConfigs[tab] ? tab : 'quotation';
        const pageHeader = document.getElementById('pageHeaderTitle');
        if (pageHeader) {
            pageHeader.innerText = tabConfigs[activeKey].title;
        }

        Object.keys(tabConfigs).forEach(k => {
            const btn = document.getElementById(tabConfigs[k].btnId);
            const content = document.getElementById(tabConfigs[k].contentId);
            if (!btn || !content) return;
            if (k === activeKey) {
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');
                content.style.display = 'block';
            } else {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
                content.style.display = 'none';
            }
        });

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', activeKey);
            window.history.replaceState({}, '', url.toString());
            localStorage.setItem('presales_cr_tab_{{ $changeRequest->id }}', activeKey);
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        let initialTab = 'quotation';
        try {
            const url = new URL(window.location.href);
            const q = url.searchParams.get('tab');
            if (['detail', 'quotation', 'payment'].includes(q)) initialTab = q;
            else {
                const saved = localStorage.getItem('presales_cr_tab_{{ $changeRequest->id }}');
                if (['detail', 'quotation', 'payment'].includes(saved)) initialTab = saved;
            }
        } catch (e) {}
        switchCrTab(initialTab);
        renderTerminTable();
    });
</script>
@endsection
