@extends('cr.index')

@section('dashboard-intro')
    {{-- Role Header Banner --}}
    <div class="role-banner">
        <div class="role-banner__left">
            <div class="icon-wrap" style="background: linear-gradient(135deg, #0D9488, #0F766E);">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div>
                <div class="eyebrow" style="color: #0D9488;">Marketing &amp; Finance Workspace</div>
                <h4 class="fw-bold mb-1">Pencatatan Quotation &amp; Invoicing Internal</h4>
                <p class="text-muted small mb-0">Kelola draft invoice untuk CR yang telah Go-Live &amp; tervalidasi PM Head, pantau Term of Payment (ToP), serta arsipkan dokumen GR, Jurnal (J), Berita Acara (BA), dan Quotation resmi.</p>
            </div>
        </div>

        <div class="d-none d-md-flex gap-2">
            <a href="{{ route('change-requests.export.excel') }}" class="btn btn-outline-success btn-sm btn-export">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Invoicing Excel
            </a>
            <a href="{{ route('change-requests.export.pdf') }}" class="btn btn-outline-danger btn-sm btn-export">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export Laporan PDF
            </a>
        </div>
    </div>

    {{-- Finance KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-6">
            <a href="{{ route('change-requests.index', ['status' => 'golive']) }}" class="text-decoration-none">
                <div class="kpi-card card-lift @if(($roleMetrics['ready_invoicing'] ?? 0) > 0) pulse-active @endif" @if(($roleMetrics['ready_invoicing'] ?? 0) > 0) style="border-color: #99F6E4; background: #F0FDFA;" @endif>
                    <div>
                        <div class="kpi-icon" style="background: #CCFBF1; color: #0D9488;">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <div class="kpi-value text-teal" style="color: #0F766E;">{{ $roleMetrics['ready_invoicing'] ?? 0 }}</div>
                        <div class="kpi-label">Siap Diterbitkan Invoice</div>
                    </div>
                    <div class="kpi-footer">
                        <span class="{{ ($roleMetrics['ready_invoicing'] ?? 0) > 0 ? 'text-teal fw-bold' : '' }}" style="color: #0F766E;">
                            {{ ($roleMetrics['ready_invoicing'] ?? 0) > 0 ? 'Lolos Validasi Go-Live' : 'Tidak ada antrean' }}
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <div class="kpi-card card-lift">
                <div>
                    <div class="kpi-icon" style="background: #EFF6FF; color: #2563EB;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="kpi-value text-primary" style="font-size: 1.4rem;">
                        Rp {{ number_format(($roleMetrics['total_invoiced'] ?? 0) / 1000000, 1) }}M
                    </div>
                    <div class="kpi-label">Total Tagihan Invoiced</div>
                </div>
                <div class="kpi-footer">
                    <span>Semua tagihan terbit</span>
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="kpi-card card-lift">
                <div>
                    <div class="kpi-icon" style="background: #ECFDF5; color: #059669;">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="kpi-value text-success" style="font-size: 1.4rem;">
                        Rp {{ number_format(($roleMetrics['paid_invoiced'] ?? 0) / 1000000, 1) }}M
                    </div>
                    <div class="kpi-label">Tagihan Lunas (Paid)</div>
                </div>
                <div class="kpi-footer">
                    <span>Kas masuk terkonfirmasi</span>
                    <i class="bi bi-check-all"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="kpi-card card-lift">
                <div>
                    <div class="kpi-icon" style="background: #FFFBEB; color: #D97706;">
                        <i class="bi bi-hourglass-bottom"></i>
                    </div>
                    <div class="kpi-value text-warning" style="font-size: 1.4rem;">
                        Rp {{ number_format(($roleMetrics['pending_payment'] ?? 0) / 1000000, 1) }}M
                    </div>
                    <div class="kpi-label">Piutang Menunggu (Pending)</div>
                </div>
                <div class="kpi-footer">
                    <span>Belum terbayar</span>
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Finance Action Quick Trigger Section --}}
    <div class="card p-3 mb-4 border-0 shadow-sm" style="border: 1px solid #CCFBF1 !important; background: #F0FDFA; border-radius: 14px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-teal fs-5" style="color: #0D9488;"></i>
                <div class="small" style="color: #115E59;">
                    <strong>Petunjuk Invoicing &amp; Dokumen:</strong>
                    Klik tombol <strong>"Catat Invoice"</strong> pada CR yang berstatus Go-Live untuk melengkapi 3 Data Utama (No/Tgl Invoice, Nominal &amp; Deskripsi, Plan/Aktual Bayar) dan 4 Berkas Unggahan (GR, Jurnal, BA, Quotation) beserta skema Term of Payment (ToP).
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pencatatan Invoice Internal Terpadu --}}
    @foreach($changeRequests as $cr)
        @if(in_array($cr->status, ['golive', 'selesai', 'invoicing', 'awaiting_golive_validation']))
            <div class="modal fade" id="invoiceModal{{ $cr->id }}" tabindex="-1" aria-labelledby="invoiceModalLabel{{ $cr->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="modal-header border-0 pb-0 px-4 pt-4">
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" id="invoiceModalLabel{{ $cr->id }}">
                                    <i class="bi bi-receipt-cutoff text-teal me-2" style="color: #0D9488;"></i>Pencatatan Invoice Internal ({{ $cr->kode_cr }})
                                </h5>
                                <p class="text-muted small mb-0 mt-1">{{ $cr->judul }} &bull; Klien: {{ $cr->klien }}</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('change-requests.invoices.store', $cr) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body px-4 py-3">
                                <div class="row g-3">
                                    {{-- ── 3 INPUT DATA UTAMA ── --}}
                                    <div class="col-12">
                                        <div class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #0D9488;">
                                            1. TIGA INPUT DATA UTAMA INVOICE
                                        </div>
                                    </div>

                                    {{-- Input 1: Nomor & Tanggal Invoice --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">Nomor Invoice <span class="text-danger">*</span></label>
                                        <input type="text" name="invoice_number" class="form-control" placeholder="Contoh: INV/ITP/2026/09/001" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">Tanggal Invoice <span class="text-danger">*</span></label>
                                        <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    {{-- Input 2: Nominal & Deskripsi Pekerjaan --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">Nominal Invoice (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" step="1" min="0" name="nominal" class="form-control"
                                               value="{{ $cr->harga_penawaran ?: ($cr->biaya_pengerjaan ?: 0) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark mb-1">Term of Payment (ToP)</label>
                                        <select name="term_name" class="form-select">
                                            <option value="Pelunasan 100%">Pelunasan 100% (Single Payment)</option>
                                            <option value="DP 30% - Pelunasan 70%">DP 30% - Pelunasan 70%</option>
                                            <option value="DP 20% - Pelunasan 80%">DP 20% - Pelunasan 80%</option>
                                            <option value="DP 10% - Pelunasan 90%">DP 10% - Pelunasan 90%</option>
                                            <option value="Termin Progresif">Termin Progresif (Custom)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark mb-1">Deskripsi / Ruang Lingkup Pekerjaan <span class="text-danger">*</span></label>
                                        <textarea name="job_description" rows="2" class="form-control" required>{{ $cr->judul }} - {{ $cr->proyek_terkait }}</textarea>
                                    </div>

                                    {{-- Input 3: Plan & Aktual Tanggal Bayar --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-dark mb-1">Plan Tanggal Bayar <span class="text-danger">*</span></label>
                                        <input type="date" name="planned_payment_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-dark mb-1">Aktual Tanggal Bayar</label>
                                        <input type="date" name="actual_payment_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-dark mb-1">Status Pembayaran <span class="text-danger">*</span></label>
                                        <select name="payment_status" class="form-select" required>
                                            <option value="pending" selected>Pending (Menunggu Bayar)</option>
                                            <option value="paid">Paid (Lunas)</option>
                                        </select>
                                    </div>

                                    {{-- ── 3-4 BERKAS UNGGAHAN WAJIB ── --}}
                                    <div class="col-12 mt-3">
                                        <div class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.08em; color: #0D9488;">
                                            2. BERKAS DOKUMEN PENDUKUNG (GR, JURNAL, BA, QUOTATION)
                                        </div>
                                    </div>

                                    {{-- Berkas 1: File GR & Nomor GR --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">File Goods Receipt (GR)</label>
                                        <input type="file" name="gr_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">Nomor GR</label>
                                        <input type="text" name="gr_number" class="form-control form-control-sm" placeholder="Nomor GR dari Klien">
                                    </div>

                                    {{-- Berkas 2: File Jurnal & Nomor Jurnal --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">File Jurnal / Voucher</label>
                                        <input type="file" name="journal_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">Nomor Jurnal</label>
                                        <input type="text" name="journal_number" class="form-control form-control-sm" placeholder="Nomor Bukti Jurnal Akuntansi">
                                    </div>

                                    {{-- Berkas 3: File BA (Otomatis mewarisi BA Go-Live jika ada) --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">
                                            File Berita Acara (BA)
                                            @if($cr->berita_acara_file)
                                                <span class="badge bg-success-subtle text-success">Tersedia dari Go-Live</span>
                                            @endif
                                        </label>
                                        <input type="file" name="ba_file" class="form-control form-control-sm" accept=".pdf,.png,.jpg,.jpeg,.zip">
                                        @if($cr->berita_acara_file)
                                            <div class="form-text text-success small" style="font-size: 0.72rem;">
                                                <i class="bi bi-check2-circle me-1"></i> Menggunakan arsip BA Go-Live secara otomatis jika tidak diganti.
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Berkas 4: File Quotation --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-dark mb-1">File Quotation Resmi (PDF)</label>
                                        <input type="file" name="quotation_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.zip">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm px-4 fw-semibold text-white" style="background: #0D9488; border-color: #0D9488;">
                                    <i class="bi bi-save me-1"></i> Simpan Invoice Internal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
