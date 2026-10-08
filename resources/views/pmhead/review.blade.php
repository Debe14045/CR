@extends('layouts.app')

@section('title', 'Detail Change Request • ' . ($changeRequest->kode_cr ?: 'CR-2026-09-0042'))

@section('content')
@php
    $crTitle = $changeRequest->judul ?: 'Integrasi API Pembayaran OVO';
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
    $tglHeader = '04 Sep 2026';
    $targetSelesai = $changeRequest->target_selesai ? $changeRequest->target_selesai->format('d M Y') : '15 Jan 2024';

    $companyName = $changeRequest->client?->company ?: ($changeRequest->klien ?: 'PT Maju Bersama');
    $clientInitial = $changeRequest->client?->nickname ?: 'AF';
    $picName = $changeRequest->user?->name ?: ($changeRequest->owner_cr ?: 'Totok Antok');
    $projectName = $changeRequest->proyek_terkait ?: 'Sistem E-commerce';
    $crOwner = $changeRequest->pic_sales ?: ($changeRequest->main_desk ?: 'Andik Virmansyah');
    $priority = ucfirst($changeRequest->prioritas ?: 'Normal');

    $docUrl = $changeRequest->google_drive_url ?: 'https://drive.google.com/drive/folders/abc123';
    $solutionPaperUrl = $changeRequest->solution_paper_url ?: 'https://drive.google.com/file/sp001';

    $mandaysAnalisis = (int)($changeRequest->mindesk_analisis ?: 2);
    $mandaysDev = (int)($changeRequest->mindesk_development ?: ($changeRequest->estimasi_waktu ?: 8));
    $mandaysTest = (int)($changeRequest->mindesk_testing ?: 2);
    $totalMandays = $mandaysAnalisis + $mandaysDev + $mandaysTest;

    $catatanCR = $changeRequest->catatan_pengajuan ?: ($changeRequest->pesan_client ?: 'Mohon diprioritaskan untuk integrasi sandbox staging sebelum jadwal rilis agar tim QA dapat melakukan testing secara menyeluruh.');
    $pmVerifier = $changeRequest->nama_pm ?: ($changeRequest->user?->name ?: 'PM ITPI');
@endphp

<div class="page-shell pb-5">

    {{-- Page Header --}}
    <div class="mb-3">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">
            Detail Change Request
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.86rem; color: #64748B;">Melihat Data Lengkap CR</p>
    </div>

    {{-- Main Container Card --}}
    <div class="card p-4 p-md-5 shadow-sm border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 20px; background: #FFFFFF;">

        {{-- Top Header Item --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                 style="width: 44px; height: 44px; background: #007DFF; font-size: 1.35rem; flex-shrink: 0;">
                {{ $firstChar }}
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="fw-bold mb-0" style="color: #0F172A; font-size: 1.35rem; letter-spacing: -0.01em;">
                    {{ $crTitle }}
                </h3>
                <span class="badge px-3 py-1 fw-bold text-white" style="background: #00A3FF; border-radius: 999px; font-size: 0.72rem;">
                    {{ ucfirst(str_replace('_', ' ', $changeRequest->status ?: 'Analisa')) }}
                </span>
            </div>
        </div>

        {{-- Meta ID and Date bar --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-4 border-bottom" style="border-color: #F1F5F9 !important;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-secondary small fw-bold font-monospace" style="font-size: 0.82rem;">
                    ID: {{ $crCode }}
                </span>
                <span class="badge px-2 py-1 fw-semibold d-inline-flex align-items-center gap-1"
                      style="background: #FEF3C7; color: #D97706; border-radius: 999px; font-size: 0.74rem;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                    {{ $statusText }}
                </span>
            </div>
            <div class="text-muted small d-flex align-items-center gap-1" style="font-size: 0.8rem;">
                <i class="bi bi-calendar3"></i>
                <span>Tanggal Pengajuan: <strong class="text-dark">{{ $tglPengajuan }}</strong></span>
            </div>
        </div>

        @php
            // Calculate dynamic step progress based on actual status
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
                2 => 33,
                3 => 50,
                4 => 67,
                5 => 83,
                6 => 100,
                default => 50,
            };
            $stepLabels = [
                1 => ['title' => 'Diajukan', 'desc' => 'Pengajuan'],
                2 => ['title' => 'Review PM', 'desc' => 'Analisa'],
                3 => ['title' => 'Approval PM Head', 'desc' => 'Supervisi'],
                4 => ['title' => 'Quotation', 'desc' => 'Penawaran'],
                5 => ['title' => 'Development', 'desc' => 'Pengerjaan'],
                6 => ['title' => 'Invoice & Rilis', 'desc' => 'Selesai'],
            ];
        @endphp

        {{-- STATUS ALUR CR TRACKER (Dynamic Steps) --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.04em; color: #475569;">
                    STATUS ALUR CR
                </span>
                <span class="text-muted small" style="font-size: 0.74rem; font-weight: 600;">
                    Total Progres Alur: {{ $pctProgress }}%
                </span>
            </div>

            <div class="row g-2">
                @for ($step = 1; $step <= 6; $step++)
                    @php
                        $isCompleted = $step < $currentStep;
                        $isActive = $step === $currentStep;
                        $stepInfo = $stepLabels[$step];
                    @endphp
                    <div class="col-md-2 col-6">
                        @if ($isCompleted)
                            <div class="p-2 px-3 h-100 d-flex align-items-center gap-2 rounded-3"
                                 style="background: #ECFDF5; border: 1.5px solid #A7F3D0;">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1rem;"></i>
                                <div style="line-height: 1.15;">
                                    <div class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $stepInfo['title'] }}</div>
                                    <div class="text-success small fw-semibold" style="font-size: 0.68rem;">Selesai</div>
                                </div>
                            </div>
                        @elseif ($isActive)
                            <div class="p-2 px-3 h-100 d-flex align-items-center gap-2 rounded-3"
                                 style="background: #EFF6FF; border: 2px solid #3B82F6;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                      style="width: 20px; height: 20px; background: #0063D7; font-size: 0.68rem; flex-shrink: 0;">
                                    {{ $step }}
                                </span>
                                <div style="line-height: 1.15;">
                                    <div class="fw-bold text-primary" style="font-size: 0.78rem;">{{ $stepInfo['title'] }}</div>
                                    <div class="text-primary small fw-semibold" style="font-size: 0.68rem;">Sedang Berjalan</div>
                                </div>
                            </div>
                        @else
                            <div class="p-2 px-3 h-100 d-flex align-items-center gap-2 rounded-3"
                                 style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-muted"
                                      style="width: 20px; height: 20px; background: #E2E8F0; font-size: 0.68rem; flex-shrink: 0;">
                                    {{ $step }}
                                </span>
                                <div style="line-height: 1.15;">
                                    <div class="text-muted fw-semibold" style="font-size: 0.78rem;">{{ $stepInfo['title'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.68rem;">Menunggu</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        {{-- Row 1: INFORMASI CR (Kiri) & DOKUMEN (Kanan) --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7 col-12">
                <div class="card p-3 p-md-4 h-100 border-0" style="background: #F8FAFC; border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #475569;">
                        INFORMASI CR
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">NAMA PERUSAHAAN</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $companyName }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">INISIAL KLIEN</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $clientInitial }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">NAMA PIC</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $picName }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">NAMA PROJECT</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $projectName }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">CR OWNER</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $crOwner }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">TANGGAL PENGAJUAN</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $tglPengajuan }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">REQUEST GO-LIVE</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $targetSelesai }}</div>
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="text-muted small" style="font-size: 0.72rem;">PRIORITAS CR</div>
                            <div class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $priority }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-12">
                <div class="card p-3 p-md-4 h-100 border-0" style="background: #F8FAFC; border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #475569;">
                        DOKUMEN
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="text-muted small mb-1" style="font-size: 0.74rem;">DOKUMEN CR</div>
                            <a href="{{ $docUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center gap-2 small fw-semibold" style="color: #0284C7; word-break: break-all;">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                                <span>{{ $docUrl }}</span>
                            </a>
                        </div>
                        <div>
                            <div class="text-muted small mb-1" style="font-size: 0.74rem;">SOLUTION PAPER</div>
                            <a href="{{ $solutionPaperUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center gap-2 small fw-semibold" style="color: #0284C7; word-break: break-all;">
                                <i class="bi bi-file-earmark-code text-primary"></i>
                                <span>{{ $solutionPaperUrl }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Detail Deskripsi CR (Rendered WYSIWYG Content) --}}
        <div class="card p-4 border-0 mb-4" style="background: #FFFFFF; border: 1px solid #E2E8F0 !important; border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-text text-primary"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">Detail Deskripsi CR</h5>
                </div>
                <span class="text-muted small" style="font-size: 0.74rem;">Rendered WYSIWYG Content</span>
            </div>

            <div class="text-dark" style="font-size: 0.86rem; line-height: 1.65;">
                <div class="fw-bold mb-1">1. LATAR BELAKANG &amp; TUJUAN</div>
                <div class="text-secondary mb-3">
                    {!! nl2br(e($changeRequest->alasan ?: ($changeRequest->deskripsi ?: 'Implementasi penambahan modul atau fitur baru pada sistem sesuai kebutuhan operasional dan penyesuaian alur kerja terkini.'))) !!}
                </div>

                <div class="fw-bold mb-1">2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)</div>
                <div class="text-secondary mb-3">
                    @if ($changeRequest->deskripsi && $changeRequest->deskripsi !== $changeRequest->alasan)
                        {!! nl2br(e($changeRequest->deskripsi)) !!}
                    @else
                        <ul class="text-secondary ps-3 mb-0" style="list-style-type: disc;">
                            <li>Penyesuaian modul transaksi dan antarmuka pengguna pada sistem aplikasi.</li>
                            <li>Integrasi layanan backend, API endpoint, dan penyesuaian skema database.</li>
                            <li>Pengujian fungsional unit testing, staging, serta verifikasi keamanan data.</li>
                        </ul>
                    @endif
                </div>

                <div class="fw-bold mb-2">3. DAMPAK TEKNIS &amp; DEPENDENCIES</div>
                <div class="p-3 rounded-3" style="background: #FFFBEB; border: 1.5px solid #FDE68A;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 0.95rem; flex-shrink: 0;"></i>
                        <div style="font-size: 0.82rem; color: #92400E; font-weight: 500;">
                            <strong>Catatan Dependensi &amp; Solusi:</strong> {{ $changeRequest->solution_paper_note ?: ($changeRequest->pesan_client ?: 'Membutuhkan koordinasi kredensial staging/production dari PIC teknis serta pengujian menyeluruh sebelum rilis.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: ESTIMASI MANDAYS (Kiri) & CR Notes / Catatan (Kanan) --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-12">
                <div class="card p-4 h-100 border-0" style="background: #FFFFFF; border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="fw-bold mb-3 text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #475569;">
                        ESTIMASI MANDAYS
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Analisis</span>
                                <span class="fw-bold text-dark">{{ $mandaysAnalisis }} hari</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #F1F5F9;">
                                <div class="progress-bar" style="width: {{ ($mandaysAnalisis / ($totalMandays ?: 1)) * 100 }}%; background: #00A3FF; border-radius: 999px;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Development</span>
                                <span class="fw-bold text-dark">{{ $mandaysDev }} hari</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #F1F5F9;">
                                <div class="progress-bar" style="width: {{ ($mandaysDev / ($totalMandays ?: 1)) * 100 }}%; background: #0063D7; border-radius: 999px;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Testing</span>
                                <span class="fw-bold text-dark">{{ $mandaysTest }} hari</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #F1F5F9;">
                                <div class="progress-bar" style="width: {{ ($mandaysTest / ($totalMandays ?: 1)) * 100 }}%; background: #0284C7; border-radius: 999px;"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: #E2E8F0 !important;">
                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Total</span>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $totalMandays }} hari kerja</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card p-4 h-100 border-0" style="background: #FFFFFF; border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-chat-left-text text-primary"></i>
                        <span class="fw-bold text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.05em; color: #475569;">
                            CR Notes / Catatan
                        </span>
                    </div>

                    <div class="p-3 rounded-3 h-100" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <p class="mb-0 text-secondary" style="font-size: 0.84rem; font-style: italic; line-height: 1.6;">
                            &ldquo;{{ $catatanCR }}&rdquo;
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Keputusan Approval (Figma PM Head Action Box) --}}
        <div class="card p-4 border-0" style="background: #FFFFFF; border: 1.5px solid #BAE6FD !important; border-radius: 16px;">
            <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Keputusan Approval</h5>
            <p class="text-muted small mb-3" style="font-size: 0.82rem;">
                PM <strong class="text-dark">{{ $pmVerifier }}</strong> sudah memverifikasi Change Request. Setujui untuk diteruskan ke Marketing.
            </p>

            <form method="POST" action="{{ route('pmh.decision', $changeRequest) }}">
                @csrf
                <div class="mb-3">
                    <label class="fw-bold small text-dark mb-1" style="font-size: 0.78rem;">CR Notes</label>
                    <textarea name="catatan_approval" rows="3" class="form-control"
                              placeholder="Catatan tambahan, prioritas, atau informasi lain yang perlu diketahui PM..."
                              style="border-radius: 10px; border: 1px solid #CBD5E1; font-size: 0.88rem; padding: 0.75rem 1rem;"></textarea>
                    @error('catatan_approval')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="submit" name="action" value="reject" class="btn text-white fw-bold px-4 py-2 d-inline-flex align-items-center gap-1"
                            style="background: #DC2626; border-radius: 8px; font-size: 0.84rem; border: none; box-shadow: 0 4px 10px rgba(220,38,38,0.25);"
                            onclick="return confirm('Apakah Anda yakin ingin menolak / mengembalikan CR ini untuk revisi PM? Pastikan telah mengisi alasan di CR Notes.');">
                        <i class="bi bi-x-lg"></i> Tolak
                    </button>
                    <button type="submit" name="action" value="approve" class="btn text-white fw-bold px-4 py-2 d-inline-flex align-items-center gap-1"
                            style="background: #22C55E; border-radius: 8px; font-size: 0.84rem; border: none; box-shadow: 0 4px 10px rgba(34,197,94,0.3);"
                            onclick="return confirm('Setujui dan validasi Change Request ini untuk diteruskan ke Marketing?');">
                        <i class="bi bi-check-lg"></i> Setujui
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
