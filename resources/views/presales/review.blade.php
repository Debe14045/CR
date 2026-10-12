@extends('layouts.app')

@section('title', 'Detail Change Request • ' . ($changeRequest->kode_cr ?: 'CR-2026-09-0042'))

@section('content')
@php
    $crTitle = $changeRequest->judul ?: 'Integrasi API Pembayaran';
    $firstChar = strtoupper(substr($crTitle, 0, 1)) ?: 'I';
    $crCode = $changeRequest->kode_cr ?: 'CR-2026-09-0042';

    $statusKey = strtolower($changeRequest->status);
    $statusText = match($statusKey) {
        'validated', 'disetujui' => 'Disetujui',
        'development', 'dikerjakan' => 'Sedang Dikerjakan',
        'golive', 'selesai' => 'Selesai Go-Live',
        'ditolak', 'revision_needed' => 'Revisi Dibutuhkan',
        default => 'Menunggu Review (In Progress)',
    };

    $tglPengajuan = $changeRequest->tanggal_pengajuan ? $changeRequest->tanggal_pengajuan->format('d M Y') : '15 Jan 2024';
    $tglHeader = $changeRequest->created_at ? $changeRequest->created_at->format('d M Y') : '04 Sep 2026';
    $targetSelesai = $changeRequest->target_selesai ? $changeRequest->target_selesai->format('d M Y') : '15 Jan 2024';

    $companyName = $changeRequest->client?->company ?: ($changeRequest->klien ?: 'PT Maju Bersama');
    $clientInitial = $changeRequest->client?->nickname ?: 'AF';
    $picName = $changeRequest->user?->name ?: ($changeRequest->owner_cr ?: 'Totok Antok');
    $projectName = $changeRequest->proyek_terkait ?: 'Sistem E-commerce';
    $crOwner = $changeRequest->pic_sales ?: ($changeRequest->main_desk ?: 'Andik Virmansyah');
    $priority = ucfirst($changeRequest->prioritas ?: 'Normal');

    $docUrl = $changeRequest->google_drive_url ?: 'https://drive.google.com/drive/folders/abc123';
    $solutionPaperUrl = $changeRequest->solution_paper_url ?: 'https://drive.google.com/file/sp001';

    $rawAnalisis = (float)($changeRequest->mindesk_analisis ?? 0);
    $rawDev = (float)($changeRequest->mindesk_development ?? 0);
    $rawTest = (float)($changeRequest->mindesk_testing ?? 0);

    $mandaysAnalisis = $rawAnalisis > 0 ? (int)$rawAnalisis : 3;
    $mandaysDev = $rawDev > 0 ? (int)$rawDev : 10;
    $mandaysTest = $rawTest > 0 ? (int)$rawTest : 2;
    $totalMandays = $mandaysAnalisis + $mandaysDev + $mandaysTest;

    $catatanCR = $changeRequest->catatan_pengajuan ?: ($changeRequest->pesan_client ?: 'Mohon diprioritaskan untuk integrasi sandbox staging sebelum tanggal 15 September agar tim QA dapat melakukan testing payment gateway secara menyeluruh.');
    $pmVerifier = $changeRequest->nama_pm ?: ($changeRequest->user?->name ?: 'Rizky Pratama');
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
    .flow-bar-item {
        flex: 1;
        height: 4px;
        background: #E2E8F0;
    }
    .flow-bar-item.active {
        background: #0284C7;
    }
    .flow-bar-item.completed {
        background: #10B981;
    }
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
    .doc-pill-box:hover {
        background: #E0F2FE;
        color: #0369A1;
    }
</style>

<div class="page-shell pb-5">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <a href="{{ route('presales.change-requests') }}" class="btn btn-outline-secondary btn-sm px-3 fw-bold d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar CR
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-2 fw-bold" style="background: #E0F2FE; color: #0284C7; border-radius: 999px; font-size: 0.8rem;">
                {{ $crCode }}
            </span>
            <span class="badge px-3 py-2 fw-bold" style="background: #DCFCE7; color: #16A34A; border-radius: 999px; font-size: 0.8rem;">
                {{ $statusText }}
            </span>
        </div>
    </div>

    <div class="cr-detail-container p-4 p-md-5">

        {{-- Header Detail CR --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                 style="width: 52px; height: 52px; background: #0063D7; font-size: 1.45rem; flex-shrink: 0;">
                {{ $firstChar }}
            </div>
            <div>
                <h2 class="fw-bold mb-0" style="font-size: 1.45rem; color: #0F172A; font-weight: 800; letter-spacing: -0.01em;">
                    {{ $crTitle }}
                </h2>
                <div class="text-muted small" style="font-size: 0.82rem;">
                    {{ $crCode }} &bull; Tanggal Masuk: {{ $tglHeader }}
                </div>
            </div>
        </div>

        {{-- Informasi Ringkas CR --}}
        <div class="cr-subcard p-4 mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 0.95rem; font-weight: 800; color: #0C4A6E; text-transform: uppercase; letter-spacing: 0.04em;">
                INFORMASI CR
            </h5>

            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="text-muted small" style="font-size: 0.74rem; font-weight: 600;">PERUSAHAAN</div>
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $companyName }}</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-muted small" style="font-size: 0.74rem; font-weight: 600;">PROYEK TERKAIT</div>
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $projectName }}</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-muted small" style="font-size: 0.74rem; font-weight: 600;">PEMOHON / PIC</div>
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $picName }}</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-muted small" style="font-size: 0.74rem; font-weight: 600;">TARGET SELESAI</div>
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $targetSelesai }}</div>
                </div>
            </div>
        </div>

        {{-- Mandays & Notes --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-12">
                <div class="cr-subcard p-4 h-100">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #64748B; font-weight: 700;">
                        ESTIMASI MANDAYS
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="d-flex justify-content-between small mb-1" style="font-size: 0.8rem;">
                                <span style="color: #475569; font-weight: 500;">Analisis</span>
                                <span style="color: #0F172A; font-weight: 800;">{{ $mandaysAnalisis }} hari</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #E0F2FE;">
                                <div class="progress-bar" style="width: {{ ($mandaysAnalisis / ($totalMandays ?: 1)) * 100 }}%; background: #0284C7; border-radius: 999px;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between small mb-1" style="font-size: 0.8rem;">
                                <span style="color: #475569; font-weight: 500;">Development</span>
                                <span style="color: #0F172A; font-weight: 800;">{{ $mandaysDev }} hari</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #E0F2FE;">
                                <div class="progress-bar" style="width: {{ ($mandaysDev / ($totalMandays ?: 1)) * 100 }}%; background: #0284C7; border-radius: 999px;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between small mb-1" style="font-size: 0.8rem;">
                                <span style="color: #475569; font-weight: 500;">Testing</span>
                                <span style="color: #0F172A; font-weight: 800;">{{ $mandaysTest }} hari</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #E0F2FE;">
                                <div class="progress-bar" style="width: {{ ($mandaysTest / ($totalMandays ?: 1)) * 100 }}%; background: #2563EB; border-radius: 999px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                        <span style="font-size: 0.88rem; color: #475569; font-weight: 500;">Total Mandays</span>
                        <span style="font-size: 1rem; color: #0063D7; font-weight: 800;">{{ $totalMandays }} hari kerja</span>
                    </div>

                    @if ($changeRequest->saranHargaPenawaran() > 0)
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                            <span style="font-size: 0.88rem; color: #475569; font-weight: 500;">Estimasi Nilai Proyek</span>
                            <span style="font-size: 1.05rem; color: #16A34A; font-weight: 800;">Rp {{ number_format($changeRequest->saranHargaPenawaran(), 0, ',', '.') }}</span>
                        </div>
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
                        <p class="mb-0" style="font-size: 0.85rem; line-height: 1.65; color: #0C4A6E;">
                            &ldquo;{{ $catatanCR }}&rdquo;
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Deskripsi CR --}}
        <div class="cr-subcard p-4 mb-4">
            <h5 class="fw-bold mb-3" style="font-size: 0.95rem; font-weight: 800; color: #0C4A6E; text-transform: uppercase; letter-spacing: 0.04em;">
                DETAIL DESKRIPSI CR
            </h5>
            <div class="p-3 bg-light rounded-3" style="font-size: 0.88rem; line-height: 1.6; color: #334155;">
                {!! nl2br(e($changeRequest->deskripsi)) !!}
            </div>
        </div>

    </div>

</div>
@endsection
