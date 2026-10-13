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
        position: relative; cursor: pointer; border: none; background: transparent; color: #1E3A8A;
    }
    .pm-filter-tab.active { background: #0B6EBD !important; color: #FFFFFF !important; box-shadow: none !important; }
    .pm-filter-tab:not(.active):hover { background: rgba(11,110,189,0.08) !important; }
    .pm-filter-divider { width: 1px; background: #CBDCEE; flex-shrink: 0; }
    .quot-input {
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;
        padding: 0.5rem 0.8rem; font-size: 0.84rem; color: #0F172A; width: 100%;
    }
    .quot-label { font-size: 0.7rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.3rem; display: block; }
    .top-table th { background: #F8FAFC; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B; padding: 0.7rem 0.9rem; border-bottom: 1px solid #E2E8F0; }
    .top-table td { padding: 0.8rem 0.9rem; font-size: 0.83rem; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
</style>

<div class="page-shell pb-5">

    <div class="mb-3">
        <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1.2;">
            Detail Change Request
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Melihat Data Lengkap CR</p>
    </div>

    <div style="height: 3px; background: #E0F2FE; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    <div class="pm-filter-container" role="tablist" aria-label="Tab Detail CR">
        <button type="button" class="pm-filter-tab active" id="tabBtnDetail" onclick="switchCrTab('detail')" role="tab" aria-selected="true" style="border-top-left-radius: 11px; border-bottom-left-radius: 11px;">
            Detail Change Request
        </button>
        <div class="pm-filter-divider" id="divDetailQuot"></div>
        <button type="button" class="pm-filter-tab" id="tabBtnQuotation" onclick="switchCrTab('quotation')" role="tab" aria-selected="false">
            Quotation <span style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;margin-left:4px;"></span>
        </button>
        <div class="pm-filter-divider" id="divQuotPay"></div>
        <button type="button" class="pm-filter-tab" id="tabBtnPayment" onclick="switchCrTab('payment')" role="tab" aria-selected="false" style="border-top-right-radius: 11px; border-bottom-right-radius: 11px;">
            Detail Pembayaran &amp; QR
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

    {{-- ============ TAB: QUOTATION ============ --}}
    <div id="tabContentQuotation" class="cr-detail-container p-4 p-md-5 mb-4" style="display: none;">

        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: #007DFF; font-size: 1.35rem; border-radius: 12px; flex-shrink: 0;">
                    {{ $firstChar }}
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.25rem; font-weight: 800;">{{ $crTitle }}</h3>
                    <span class="badge px-3 py-1 fw-bold text-white" style="background: #00A3FF; border-radius: 999px; font-size: 0.72rem;">{{ ucfirst(str_replace('_', ' ', $changeRequest->status ?: '-')) }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small" style="font-size: 0.78rem;">Quotation</span>
                <span class="badge px-3 py-2" style="background: #F1F5F9; border: 1px solid #E2E8F0; color: #334155; border-radius: 8px; font-size: 0.78rem; font-weight: 700;">Overall</span>
            </div>
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B;">INFORMASI QUOTATION</div>
            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <label class="quot-label">Nomor Quotation <span class="text-danger">*</span></label>
                    <input class="quot-input" value="{{ $nomorQuotation }}" readonly>
                </div>
                <div class="col-md-4 col-12">
                    <label class="quot-label">Tanggal Dibuat <span class="text-danger">*</span></label>
                    <input class="quot-input" value="{{ $tglDibuat }}" readonly>
                </div>
                <div class="col-md-4 col-12">
                    <label class="quot-label">Berlaku Sampai <span class="text-danger">*</span></label>
                    <input class="quot-input" value="{{ $berlakuSampai }}" readonly>
                </div>
            </div>
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B;">INFORMASI PENAWARAN QUOTATION</div>
            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <label class="quot-label">Nama Penawaran <span class="text-danger">*</span></label>
                    <input class="quot-input" value="{{ $companyName }}" readonly>
                </div>
                <div class="col-md-4 col-12">
                    <label class="quot-label">Kode CR</label>
                    <input class="quot-input" value="{{ $crCode }}" readonly>
                </div>
                <div class="col-md-4 col-12">
                    <label class="quot-label">Nama PIC <span class="text-danger">*</span></label>
                    <input class="quot-input" value="{{ $picName }}" readonly>
                </div>
                <div class="col-md-6 col-12">
                    <label class="quot-label">Email <span class="text-danger">*</span></label>
                    <input class="quot-input" value="{{ $clientEmail }}" readonly>
                </div>
                <div class="col-md-6 col-12">
                    <label class="quot-label">Alamat / Proyek Terkait</label>
                    <input class="quot-input" value="{{ $projectName }}{{ $changeRequest->client?->address ? ' — ' . $changeRequest->client->address : '' }}" readonly>
                </div>
            </div>
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B;">PEKERJAAN (RINCIAN MAN-DAYS &amp; TARIF)</div>
            <div class="table-responsive">
                <table class="table top-table mb-0">
                    <thead>
                        <tr>
                            <th style="min-width: 160px;">Pekerjaan</th>
                            <th class="text-center" style="min-width: 110px;">Estimasi (md)</th>
                            <th class="text-end" style="min-width: 150px;">Tarif / md</th>
                            <th class="text-end" style="min-width: 160px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Analisis</td>
                            <td class="text-center fw-bold">{{ rtrim(rtrim(number_format($mandaysAnalisis, 2, ',', '.'), '0'), ',') }}</td>
                            <td class="text-end">{{ $fmtIdr($ratePerMandays) }}</td>
                            <td class="text-end fw-bold">{{ $fmtIdr($subAnalisis) }}</td>
                        </tr>
                        <tr>
                            <td>Development</td>
                            <td class="text-center fw-bold">{{ rtrim(rtrim(number_format($mandaysDev, 2, ',', '.'), '0'), ',') }}</td>
                            <td class="text-end">{{ $fmtIdr($ratePerMandays) }}</td>
                            <td class="text-end fw-bold">{{ $fmtIdr($subDev) }}</td>
                        </tr>
                        <tr>
                            <td>Testing</td>
                            <td class="text-center fw-bold">{{ rtrim(rtrim(number_format($mandaysTest, 2, ',', '.'), '0'), ',') }}</td>
                            <td class="text-end">{{ $fmtIdr($ratePerMandays) }}</td>
                            <td class="text-end fw-bold">{{ $fmtIdr($subTest) }}</td>
                        </tr>
                        <tr style="background: #F8FAFC;">
                            <td class="fw-bold">Total Mandays</td>
                            <td class="text-center fw-bold">{{ rtrim(rtrim(number_format($totalMandays, 2, ',', '.'), '0'), ',') }} hari kerja</td>
                            <td></td>
                            <td class="text-end fw-bold" style="color: #0063D7;">{{ $fmtIdr($estimasiNilai) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($totalMandays == 0)
                <div class="text-muted small mt-2" style="font-size: 0.78rem;">Estimasi man-days belum diinput. Nilai dihitung otomatis setelah PM mengisi man-days.</div>
            @endif
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="fw-bold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B;">TERMS OF PAYMENT (TERMIN PEMBAYARAN)</div>
                <button type="button" class="btn btn-sm fw-bold" style="background: #10B981; color: #fff; border-radius: 8px; font-size: 0.76rem;" onclick="switchCrTab('payment')">
                    <i class="bi bi-plus-lg me-1"></i> Kelola Termin
                </button>
            </div>
            <div class="table-responsive">
                <table class="table top-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">NO</th>
                            <th style="min-width: 170px;">Terms of Payment</th>
                            <th style="min-width: 240px;">Keterangan / Activities Deliverables</th>
                            <th class="text-end" style="min-width: 150px;">Nominal (IDR)</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $idx => $inv)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td class="fw-bold">{{ $inv->term_name ?: ($inv->invoice_number ?: 'Termin ' . ($idx + 1)) }}</td>
                                <td class="text-muted">{{ $inv->job_description ?: $crTitle }}</td>
                                <td class="text-end fw-bold">{{ $fmtIdr($inv->nominal) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm px-2 py-1" style="background: #FEF3C7; color: #D97706; border-radius: 6px; font-size: 0.72rem;" onclick="switchCrTab('payment')" title="Kelola di tab pembayaran">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm px-2 py-1" style="background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 0.72rem;" onclick="switchCrTab('payment')" title="Kelola di tab pembayaran">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center">1</td>
                                <td class="fw-bold">Pelunasan 100%</td>
                                <td class="text-muted">{{ $crTitle }} — {{ $projectName }}</td>
                                <td class="text-end fw-bold">{{ $fmtIdr($totalPenawaranFinal) }}</td>
                                <td class="text-center text-muted small">—</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 pt-3" style="border-top: 1px solid #E2E8F0;">
                <div class="d-flex justify-content-between small mb-1" style="font-size: 0.84rem;">
                    <span class="text-muted">Subtotal ({{ $invoices->count() > 0 ? $invoices->count() . ' termin' : '1 termin' }})</span>
                    <span class="fw-bold">{{ $fmtIdr($invoices->count() > 0 ? $totalInvoiced : $totalPenawaranFinal) }}</span>
                </div>
                <div class="d-flex justify-content-between small mb-1" style="font-size: 0.84rem;">
                    <span class="text-muted">Total Net</span>
                    <span class="fw-bold">{{ $fmtIdr($invoices->count() > 0 ? $totalInvoiced : $totalPenawaranFinal) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="fw-bold" style="font-size: 0.9rem;">Total Penawaran Final</span>
                    <span class="fw-bold" style="font-size: 1.05rem; color: #0063D7;">{{ $fmtIdr($invoices->count() > 0 ? $totalInvoiced : $totalPenawaranFinal) }}</span>
                </div>
            </div>
        </div>

        <div class="cr-subcard p-4 mb-4">
            <div class="fw-bold mb-2" style="font-size: 0.9rem; color: #0C4A6E;">Status Quotation</div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if($changeRequest->isPmhApproved())
                    <span class="badge px-3 py-2" style="background: #DCFCE7; color: #15803D; border-radius: 999px; font-size: 0.75rem;">Disetujui PM Head</span>
                @else
                    <span class="badge px-3 py-2" style="background: #FEF3C7; color: #B45309; border-radius: 999px; font-size: 0.75rem;">Menunggu Persetujuan PM Head ({{ $changeRequest->statusLabel() }})</span>
                @endif
                @if($changeRequest->quotation_approved_client_at)
                    <span class="badge px-3 py-2" style="background: #DCFCE7; color: #15803D; border-radius: 999px; font-size: 0.75rem;">Disetujui Klien: {{ $changeRequest->quotation_approved_client_at->format('d M Y') }}</span>
                @endif
                @if($changeRequest->quotation_approved_tp_at)
                    <span class="badge px-3 py-2" style="background: #DCFCE7; color: #15803D; border-radius: 999px; font-size: 0.75rem;">Disetujui TP: {{ $changeRequest->quotation_approved_tp_at->format('d M Y') }}</span>
                @endif
                @if($hargaPenawaran !== null)
                    <span class="badge px-3 py-2" style="background: #EFF6FF; color: #1D4ED8; border-radius: 999px; font-size: 0.75rem;">Tersimpan: {{ $fmtIdr($hargaPenawaran) }}</span>
                @else
                    <span class="badge px-3 py-2" style="background: #F1F5F9; color: #64748B; border-radius: 999px; font-size: 0.75rem;">Belum ada harga penawaran tersimpan</span>
                @endif
            </div>

            @if(!$canBeQuoted)
                <div class="p-3 mb-0" style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; font-size: 0.82rem; color: #92400E;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    CR berstatus <strong>{{ $changeRequest->statusLabel() }}</strong> dan belum disetujui PM Head. Harga penawaran hanya dapat disimpan setelah PM Head menyetujui CR. Aturan approval tetap dipertahankan.
                </div>
            @else
                <form method="POST" action="{{ route('change-requests.quotation.save', $changeRequest) }}" enctype="multipart/form-data" id="quotationForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label">Total Harga Penawaran (Rp) <span class="text-danger">*</span></label>
                            <input type="text" id="hargaPenawaranDisplay" class="form-control" value="{{ $hargaPenawaran !== null ? number_format($hargaPenawaran, 0, ',', '.') : number_format($estimasiNilai, 0, ',', '.') }}" inputmode="numeric" placeholder="Contoh: 30.500.000">
                            <input type="hidden" name="harga_penawaran" id="hargaPenawaranValue" value="{{ $hargaPenawaran ?? $estimasiNilai }}">
                            @error('harga_penawaran')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <div class="form-text small">Dihitung otomatis dari {{ rtrim(rtrim(number_format($totalMandays, 2, ',', '.'), '0'), ',') }} md × {{ $fmtIdr($ratePerMandays) }}. Dapat disesuaikan manual.</div>
                        </div>
                        <div class="col-md-3 col-12">
                            <label class="form-label">File Quotation (PDF)</label>
                            <input type="file" name="quotation_file" class="form-control" accept=".pdf,.doc,.docx,.zip">
                            @if($invoices->firstWhere('quotation_file', '!=', null))
                                <div class="form-text small text-success"><i class="bi bi-check-circle me-1"></i>Sudah ada berkas tersimpan.</div>
                            @endif
                        </div>
                        <div class="col-md-3 col-12">
                            <label class="form-label">File Signed Quotation</label>
                            <input type="file" name="signed_quotation_file" class="form-control" accept=".pdf,.doc,.docx,.zip">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6 col-12">
                            <label class="quot-label">Total Harga (Preview)</label>
                            <input class="quot-input" id="previewTotalHarga" value="{{ $fmtIdr($hargaPenawaran ?? $estimasiNilai) }}" readonly>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="quot-label">Total Penawaran Final</label>
                            <input class="quot-input fw-bold" id="previewTotalFinal" style="color: #0063D7; font-weight: 800;" value="{{ $fmtIdr($hargaPenawaran ?? $estimasiNilai) }}" readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn text-white fw-bold px-4 py-2" style="background: #22C55E; border-radius: 10px; font-size: 0.86rem; border: none; box-shadow: 0 4px 10px rgba(34,197,94,0.3);">
                            <i class="bi bi-save me-1"></i> Simpan &amp; Submit
                        </button>
                    </div>
                </form>
            @endif
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

<script>
    function switchCrTab(tab) {
        const tabs = { detail: 'Detail', quotation: 'Quotation', payment: 'Payment' };
        Object.keys(tabs).forEach(k => {
            const btn = document.getElementById('tabBtn' + tabs[k]);
            const content = document.getElementById('tabContent' + tabs[k]);
            if (!btn || !content) return;
            if (k === tab) {
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
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url.toString());
            localStorage.setItem('presales_cr_tab_{{ $changeRequest->id }}', tab);
        } catch (e) {}
    }

    function formatIdr(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function () {
        let initialTab = 'detail';
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

        const display = document.getElementById('hargaPenawaranDisplay');
        const hidden = document.getElementById('hargaPenawaranValue');
        const prevHarga = document.getElementById('previewTotalHarga');
        const prevFinal = document.getElementById('previewTotalFinal');
        if (display && hidden) {
            display.addEventListener('input', function () {
                const raw = display.value.replace(/[^0-9]/g, '');
                const num = raw === '' ? 0 : parseInt(raw, 10);
                hidden.value = num;
                display.value = num === 0 && raw === '' ? '' : num.toLocaleString('id-ID');
                if (prevHarga) prevHarga.value = formatIdr(num);
                if (prevFinal) prevFinal.value = formatIdr(num);
            });
            display.addEventListener('blur', function () {
                if (display.value === '') {
                    display.value = '0';
                    hidden.value = 0;
                    if (prevHarga) prevHarga.value = formatIdr(0);
                    if (prevFinal) prevFinal.value = formatIdr(0);
                }
            });
        }
    });
</script>
@endsection
