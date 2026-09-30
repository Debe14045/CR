@extends('cr.index')

@section('dashboard-intro')
    {{-- Role Header Banner --}}
    <div class="role-banner">
        <div class="role-banner__left">
            <div class="icon-wrap">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="eyebrow">Executive Review Board</div>
                <h4 class="fw-bold mb-1">Supervisi &amp; Final Approval PM Head</h4>
                <p class="text-muted small mb-0">Tinjau hasil kajian teknis dari PM, evaluasi kelayakan strategis &amp; resource, dan tentukan keputusan persetujuan resmi atau penolakan CR.</p>
            </div>
        </div>

        <div class="d-none d-md-flex gap-2">
            <a href="{{ route('change-requests.export.excel') }}" class="btn btn-outline-success btn-sm btn-export">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Excel/CSV
            </a>
            <a href="{{ route('change-requests.export.pdf') }}" class="btn btn-outline-danger btn-sm btn-export">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- PMH KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl col-md-4 col-6">
            <a href="{{ route('change-requests.index') }}" class="text-decoration-none">
                <div class="kpi-card card-lift">
                    <div>
                        <div class="kpi-icon" style="background: #FFF1F2; color: #E11D48;">
                            <i class="bi bi-shield-shaded"></i>
                        </div>
                        <div class="kpi-value">{{ $roleMetrics['total'] ?? 0 }}</div>
                        <div class="kpi-label">Total CR Masuk</div>
                    </div>
                    <div class="kpi-footer">
                        <span>Semua pengajuan</span>
                        <i class="bi bi-collection"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl col-md-4 col-6">
            <a href="{{ route('change-requests.index', ['status' => 'awaiting_pmh']) }}" class="text-decoration-none">
                <div class="kpi-card card-lift @if(($roleMetrics['pending_pmh'] ?? 0) > 0) pulse-active @endif" @if(($roleMetrics['pending_pmh'] ?? 0) > 0) style="border-color: #FECDD3; background: #FFF9FA;" @endif>
                    <div>
                        <div class="kpi-icon" style="background: #FFE4E6; color: #BE123C;">
                            <i class="bi bi-hourglass-top"></i>
                        </div>
                        <div class="kpi-value text-danger">{{ $roleMetrics['pending_pmh'] ?? 0 }}</div>
                        <div class="kpi-label">Review Awal (Solution Paper)</div>
                    </div>
                    <div class="kpi-footer">
                        <span class="{{ ($roleMetrics['pending_pmh'] ?? 0) > 0 ? 'text-danger fw-bold' : '' }}">
                            {{ ($roleMetrics['pending_pmh'] ?? 0) > 0 ? 'Butuh keputusan PMH' : 'Antrean bersih' }}
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl col-md-4 col-6">
            <a href="{{ route('change-requests.index', ['status' => 'awaiting_golive_validation']) }}" class="text-decoration-none">
                <div class="kpi-card card-lift @if(($roleMetrics['pending_golive'] ?? 0) > 0) pulse-active @endif" @if(($roleMetrics['pending_golive'] ?? 0) > 0) style="border-color: #FDE68A; background: #FFFBEB;" @endif>
                    <div>
                        <div class="kpi-icon" style="background: #FEF3C7; color: #D97706;">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <div class="kpi-value text-warning">{{ $roleMetrics['pending_golive'] ?? 0 }}</div>
                        <div class="kpi-label">Validasi Go-Live (BA Siap)</div>
                    </div>
                    <div class="kpi-footer">
                        <span class="{{ ($roleMetrics['pending_golive'] ?? 0) > 0 ? 'text-warning fw-bold' : '' }}">
                            {{ ($roleMetrics['pending_golive'] ?? 0) > 0 ? 'Lempar ke Marketing' : 'Tidak ada antrean' }}
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl col-md-6 col-6">
            <a href="{{ route('change-requests.index', ['status' => 'disetujui']) }}" class="text-decoration-none">
                <div class="kpi-card card-lift">
                    <div>
                        <div class="kpi-icon" style="background: #ECFDF5; color: #059669;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="kpi-value text-success">{{ $roleMetrics['approved'] ?? 0 }}</div>
                        <div class="kpi-label">CR Telah Divalidasi</div>
                    </div>
                    <div class="kpi-footer">
                        <span>Lolos review eksekutif</span>
                        <i class="bi bi-award"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl col-md-6 col-12">
            <a href="{{ route('change-requests.index', ['status' => 'ditolak']) }}" class="text-decoration-none">
                <div class="kpi-card card-lift">
                    <div>
                        <div class="kpi-icon" style="background: #F8FAFC; color: #64748B;">
                            <i class="bi bi-x-circle text-danger"></i>
                        </div>
                        <div class="kpi-value">{{ $roleMetrics['rejected'] ?? 0 }}</div>
                        <div class="kpi-label">CR Ditolak / Revisi</div>
                    </div>
                    <div class="kpi-footer">
                        <span>Perlu revisi PM</span>
                        <i class="bi bi-slash-circle"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
