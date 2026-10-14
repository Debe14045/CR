@extends('layouts.app')

@section('title', 'Dashboard • PM Head Executive Board')

@section('content')
<div class="page-shell pb-5">

    @php
          = \App\Models\ChangeRequest::whereIn('status', ['analisa', 'dianalisis', 'awaiting_pm', 'diajukan', 'awaiting_pmh'])->count();
          = \App\Models\ChangeRequest::whereIn('status', ['development', 'dikerjakan'])->count();
              = \App\Models\ChangeRequest::whereIn('status', ['sit'])->count();
              = \App\Models\ChangeRequest::whereIn('status', ['uat'])->count();
         = \App\Models\ChangeRequest::whereIn('status', ['training'])->count();
           = \App\Models\ChangeRequest::whereIn('status', ['golive', 'selesai', 'invoicing'])->count();
         =  +  +  +  +  + ;
        if ( == 0)  = 1;

         = ( / ) * 100;
         =  + (( / ) * 100);
         =  + (( / ) * 100);
         =  + (( / ) * 100);
         =  + (( / ) * 100);
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.01em;">DASHBOARD</h1>
            <p class="text-muted mb-0" style="font-size: 0.84rem; color: #64748B;">Pantau seluruh permintaan perubahan Anda (PM Head)</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.change-requests') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Semua CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ ['total'] ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.development') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Development</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ ['in_dev'] ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.golive') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">UAT</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ ['pending_golive'] ?? 0 }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('pmh.golive') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #134B8A; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(19,75,138,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">GO LIVE</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{  }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Aktif Bulan Ini</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Card Right (now visually right): NOTIFIKASI --}}
        <div class="col-lg-5 col-12 order-lg-2">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                        <h5 class="fw-bold mb-0 text-uppercase" style="color: #0F172A; font-size: 0.95rem; font-weight: 800; letter-spacing: 0.02em;">NOTIFIKASI</h5>
                    </div>
                    <a href="{{ route('pmh.notifications') }}" class="text-decoration-none fw-semibold" style="color: #0063D7; font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <p class="text-muted small mb-0" style="font-size: 0.74rem; line-height: 1.45; color: #64748B;">
                    Pemberitahuan persetujuan penting, batas waktu tinjauan dokumen, dan tindak lanjut status tahapan proyek.
                </p>
                <div style="height: 1px; background: #E2E8F0; margin: 0.85rem 0 1.25rem;"></div>

                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('pmh.persetujuan') }}" class="text-decoration-none">
                        <div class="p-3" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #E11D48; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #9F1239; font-size: 0.82rem;">
                                        {{ ['pending_pmh'] ?? 0 }} Perubahan Ruang Lingkup CR menunggu persetujuan > 14 hari
                                    </div>
                                    <div style="color: #E11D48; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Status : Menunggu Persetujuan PM Head
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('pmh.change-requests', ['status' => 'analisa']) }}" class="text-decoration-none">
                        <div class="p-3" style="background: #FEFCE8; border: 1px solid #FEF08A; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1" style="color: #D97706; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #854D0E; font-size: 0.82rem;">
                                        {{ ['pending_pmh'] ?? 0 }} CR Baru Menunggu Verifikasi Kebutuhan
                                    </div>
                                    <div style="color: #B45309; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 1. Analisis Kebutuhan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('pmh.development') }}" class="text-decoration-none">
                        <div class="p-3" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; transition: transform 0.15s ease;"
                             onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill mt-1" style="color: #2563EB; font-size: 1.05rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.35;">
                                    <div class="fw-bold" style="color: #1E40AF; font-size: 0.82rem;">
                                        {{ ['pending_golive'] ?? 0 }} Masukan Pengguna UAT butuh tindak lanjut
                                    </div>
                                    <div style="color: #2563EB; font-size: 0.74rem; font-weight: 500; margin-top: 3px;">
                                        Tahapan : 4. UAT & Pengujian
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card Left (now visually left): Chart / Grafik --}}
        <div class="col-lg-7 col-12 order-lg-1">
            <div class="card p-4 border-0 h-100 shadow-sm" style="border-radius: 20px; background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                <div>
                    <h5 class="fw-bold mb-1 text-uppercase" style="color: #0F172A; font-size: 1rem; font-weight: 800; letter-spacing: 0.02em;">STATUS</h5>
                    <p class="text-muted small mb-0" style="font-size: 0.78rem; color: #64748B;">Monitoring pergerakan CR melalui 6 tahapan siklus proyek TI</p>
                </div>
                <div style="height: 1px; background: #DCE1E7; margin: 0.85rem 0 1.25rem;"></div>

                <div class="row align-items-center g-3 pt-1">
                    {{-- Donut Chart Left --}}
                    <div class="col-sm-5 col-12 text-center">
                        <div style="position: relative; width: 155px; height: 155px; margin: 0 auto;">
                            <div style="width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(
                                #00A3FF 0% {{  }}%,
                                #4338CA {{  }}% {{  }}%,
                                #F59E0B {{  }}% {{  }}%,
                                #EC4899 {{  }}% {{  }}%,
                                #047857 {{  }}% {{  }}%,
                                #10B981 {{  }}% 100%
                            ); display: flex; align-items: center; justify-content: center;">
                                <div style="width: 96px; height: 96px; background: #FFFFFF; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span class="fw-bold" style="font-size: 2.2rem; line-height: 1; color: #0F172A; font-weight: 800;">{{  }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6 Status Progress Lines Right --}}
                    <div class="col-sm-7 col-12">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #00A3FF; flex-shrink: 0;"></span>
                                    <span>Analisa</span>
                                </span>
                                <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                    <div style="width: {{ ( / ) * 100 }}%; height: 100%; background: #00A3FF; border-radius: 999px;"></div>
                                </div>
                                <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{  }}</span>
                            </div>

                            <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #4338CA; flex-shrink: 0;"></span>
                                    <span>Develop</span>
                                </span>
                                <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                    <div style="width: {{ ( / ) * 100 }}%; height: 100%; background: #4338CA; border-radius: 999px;"></div>
                                </div>
                                <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{  }}</span>
                            </div>

                            <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; flex-shrink: 0;"></span>
                                    <span>SIT</span>
                                </span>
                                <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                    <div style="width: {{ ( / ) * 100 }}%; height: 100%; background: #F59E0B; border-radius: 999px;"></div>
                                </div>
                                <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{  }}</span>
                            </div>

                            <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #EC4899; flex-shrink: 0;"></span>
                                    <span>UAT</span>
                                </span>
                                <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                    <div style="width: {{ ( / ) * 100 }}%; height: 100%; background: #EC4899; border-radius: 999px;"></div>
                                </div>
                                <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{  }}</span>
                            </div>

                            <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #047857; flex-shrink: 0;"></span>
                                    <span>Pelatihan</span>
                                </span>
                                <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                    <div style="width: {{ ( / ) * 100 }}%; height: 100%; background: #047857; border-radius: 999px;"></div>
                                </div>
                                <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{  }}</span>
                            </div>

                            <div class="d-flex align-items-center" style="font-size: 0.78rem;">
                                <span class="d-inline-flex align-items-center gap-2" style="width: 76px; flex-shrink: 0; font-weight: 600; color: #475569;">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; flex-shrink: 0;"></span>
                                    <span>Go Live</span>
                                </span>
                                <div style="flex: 1; height: 4px; background: #DBD8CE; border-radius: 999px; margin: 0 12px; overflow: hidden;">
                                    <div style="width: {{ ( / ) * 100 }}%; height: 100%; background: #10B981; border-radius: 999px;"></div>
                                </div>
                                <span class="fw-bold" style="color: #0F172A; min-width: 14px; text-align: right;">{{  }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="card p-4 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 18px; background: #FFFFFF;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Antrean CR Perlu Keputusan PM Head</h5>
                <p class="text-muted small mb-0" style="font-size: 0.8rem;">Daftar CR yang telah diverifikasi PM dan menunggu validasi kelayakan atau go-live.</p>
            </div>
            <a href="{{ route('pmh.persetujuan') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px;">
                Lihat Semua Antrean <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="py-2 px-3 small text-muted">ID &amp; JUDUL CR</th>
                        <th class="py-2 px-3 small text-muted">PEMOHON</th>
                        <th class="py-2 px-3 small text-muted">NAMA PM</th>
                        <th class="py-2 px-3 small text-muted text-center">STATUS</th>
                        <th class="py-2 px-3 small text-muted text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestReviews as $cr)
                        <tr>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $cr->judul }}</div>
                                <div class="text-muted small font-monospace">{{ $cr->kode_cr }}</div>
                            </td>
                            <td class="py-3 px-3 fw-semibold text-dark small">
                                {{ $cr->client?->company ?? ($cr->klien ?: 'PT Maju Bersama') }}
                            </td>
                            <td class="py-3 px-3 small text-secondary">
                                {{ $cr->nama_pm }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="badge bg-warning-subtle text-warning fw-semibold px-2 py-1" style="border-radius: 999px;">
                                    {{ ucfirst(str_replace('_', ' ', $cr->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <a href="{{ route('pmh.review', $cr) }}" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 6px; font-size: 0.78rem;">
                                    Review &gt;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-muted small">
                                Tidak ada antrean persetujuan yang menunggu saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

