@extends('layouts.app')

@section('title', 'DASHBOARD • Admin PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (DASHBOARD & Tombol Aksi) ── --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 2.15rem; font-weight: 800; letter-spacing: -0.02em;">DASHBOARD</h1>
            <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">Pantau data master sistem, klien, pegawai, dan proyek Anda</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary fw-semibold d-inline-flex align-items-center gap-2"
               style="border-radius: 9999px; padding: 0.58rem 1.35rem; font-size: 0.86rem; transition: transform 0.15s ease;">
                <i class="bi bi-folder-plus" style="font-size: 0.95rem;"></i>
                <span>Tambah Proyek</span>
            </a>
            <a href="{{ route('admin.pegawai.create') }}" class="btn btn-outline-success fw-semibold d-inline-flex align-items-center gap-2"
               style="border-radius: 9999px; padding: 0.58rem 1.35rem; font-size: 0.86rem; transition: transform 0.15s ease;">
                <i class="bi bi-person-plus-fill" style="font-size: 0.95rem;"></i>
                <span>Tambah Pegawai</span>
            </a>
            <a href="{{ route('admin.clients.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #50CD89; border-radius: 9999px; padding: 0.62rem 1.6rem; font-size: 0.88rem; border: none; box-shadow: 0 4px 14px rgba(80,205,137,0.35); transition: transform 0.15s ease;"
               onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='translateY(0)';">
                <i class="bi bi-plus-lg" style="font-size: 0.95rem;"></i>
                <span>Tambah Klien</span>
            </a>
        </div>
    </div>

    {{-- Horizontal Light Blue Line matching Figma --}}
    <div style="height: 2px; background: #BAE6FD; border-radius: 999px; margin-bottom: 1.5rem;"></div>

    {{-- ── 2. 4 Kartu Metrik Utama Admin (Master Klien, Pegawai Internal, Proyek Klien, Total CR) ── --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Master Klien (#014DA1) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #014DA1; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(1,77,161,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Data Perusahaan</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $totalClients }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-buildings" style="font-size: 0.85rem;"></i>
                        <span>Klien &amp; Akun PIC</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card 2: Pegawai Internal (#144272) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('admin.pegawai.index') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #144272; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(20,66,114,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Data Pegawai</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $totalPegawai }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-people-fill" style="font-size: 0.85rem;"></i>
                        <span>PM, Presales &amp; Staff</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card 3: Proyek Klien (#205295) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('admin.projects.index') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #205295; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(32,82,149,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Data Proyek</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $totalProjects }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-folder-fill" style="font-size: 0.85rem;"></i>
                        <span>Sistem &amp; Aplikasi Klien</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card 4: Total Change Request (#2C74B3) --}}
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('admin.clients.index') }}" class="text-decoration-none d-block h-100">
                <div class="p-4 text-white h-100 shadow-sm"
                     style="background: #2C74B3; border-radius: 18px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px rgba(44,116,179,0.35)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)';">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.95rem;">Total CR</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 32px; height: 32px; border: 1.5px solid rgba(255,255,255,0.7); background: transparent;">
                            <i class="bi bi-arrow-up-right text-white" style="font-size: 0.82rem; font-weight: bold;"></i>
                        </div>
                    </div>
                    <div class="fw-bold mb-3" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.02em;">
                        {{ $totalCrs }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white text-opacity-80" style="font-size: 0.76rem; font-weight: 500;">
                        <i class="bi bi-bar-chart-line-fill" style="font-size: 0.85rem;"></i>
                        <span>Seluruh Siklus Proyek</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── 3. Tabel Data Perusahaan Klien (Layout Seperti Semua CR) ── --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.01em;">Data Perusahaan Klien</h5>
            <p class="text-muted small mb-0" style="font-size: 0.80rem;">Daftar 5 perusahaan klien terbaru dan alamat operasional</p>
        </div>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px; font-size: 0.80rem;">
            Lihat Semua Klien <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="card overflow-hidden border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; --bs-table-accent-bg: transparent !important; border-bottom: 1px solid #E2E8F0;">
                    <tr style="background-color: #ECF1F9 !important;">
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 60px; border-bottom: 1px solid #E2E8F0; border-top-left-radius: 14px;">NO</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 25%; border-bottom: 1px solid #E2E8F0;">NAMA PERUSAHAAN</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 25%; border-bottom: 1px solid #E2E8F0;">ALAMAT</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 13%; border-bottom: 1px solid #E2E8F0;">AKUN PIC</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 13%; border-bottom: 1px solid #E2E8F0;">PROYEK</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 12%; border-bottom: 1px solid #E2E8F0;">TOTAL CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0; border-top-right-radius: 14px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentClients as $index => $client)
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            <td class="py-3 px-4" style="font-size: 0.82rem; font-weight: 600; color: #1E293B;">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-dark text-decoration-none hover-primary">
                                        {{ $client->company }}
                                    </a>
                                    @if($client->nickname)
                                        <span class="badge bg-light text-primary border ms-1" style="font-size: 0.70rem; border-radius: 6px;">{{ $client->nickname }}</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">
                                    Terdaftar {{ $client->created_at ? $client->created_at->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($client->address)
                                    <div class="d-flex align-items-center gap-2 text-muted small" style="font-size: 0.82rem; color: #475569 !important;">
                                        <i class="bi bi-geo-alt text-muted flex-shrink-0" style="font-size: 0.85rem;"></i>
                                        <span>{{ $client->address }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 0.78rem;">Alamat belum diisi</span>
                                @endif
                            </td>
                            @include('admin.clients._summary-cells', ['client' => $client])
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.clients.show', $client) }}"
                                   class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                   style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.95rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none;">
                                    <span>Detail</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.65rem; font-weight: 700;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Belum ada data klien terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 4. Tabel Data Pegawai (Layout Seperti Semua CR) ── --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.01em;">Data Pegawai Internal</h5>
            <p class="text-muted small mb-0" style="font-size: 0.80rem;">Daftar 5 pegawai internal PT. ITPI Technology terbaru</p>
        </div>
        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px; font-size: 0.80rem;">
            Lihat Semua Pegawai <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="card overflow-hidden border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; --bs-table-accent-bg: transparent !important; border-bottom: 1px solid #E2E8F0;">
                    <tr style="background-color: #ECF1F9 !important;">
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 60px; border-bottom: 1px solid #E2E8F0; border-top-left-radius: 14px;">NO</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 150px; border-bottom: 1px solid #E2E8F0;">NIP</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 25%; border-bottom: 1px solid #E2E8F0;">PEGAWAI &amp; JABATAN</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 24%; border-bottom: 1px solid #E2E8F0;">KONTAK</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 150px; border-bottom: 1px solid #E2E8F0;">ROLE</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0;">STATUS</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 100px; border-bottom: 1px solid #E2E8F0; border-top-right-radius: 14px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPegawais as $index => $pegawai)
                        @php
                            $roleMeta = $pegawai->roleBadgeMeta(true);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            <td class="py-3 px-4" style="font-size: 0.82rem; font-weight: 600; color: #1E293B;">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 0.78rem; letter-spacing: 0.03em;">
                                    {{ $pegawai->nip }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                        {{ $pegawai->name }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.74rem;">
                                        {{ $pegawai->organizationSummary() }}
                                    </div>
                                </div>
                            </td>
                            @include('admin.pegawai._kontak-cell', ['pegawai' => $pegawai])
                            <td class="py-3 px-4">
                                <span class="badge px-2 py-1" style="background: {{ $roleMeta['bg'] }}; color: {{ $roleMeta['text'] }}; border: 1px solid {{ $roleMeta['border'] }}; font-size: 0.72rem; font-weight: 700;">
                                    <i class="bi {{ $roleMeta['icon'] }} me-1"></i>{{ $roleMeta['label'] }}
                                </span>
                            </td>
                            @include('admin.pegawai._status-badge', ['pegawai' => $pegawai, 'interactive' => false])
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.pegawai.edit', $pegawai) }}"
                                   class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                   style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.85rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none;">
                                    <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                                    <span>Edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Belum ada data pegawai terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 5. Tabel Data Proyek (Layout Seperti Semua CR) ── --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <div>
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.15rem; font-weight: 800; letter-spacing: -0.01em;">Data Proyek Klien</h5>
            <p class="text-muted small mb-0" style="font-size: 0.80rem;">Daftar 5 proyek sistem/aplikasi klien terbaru</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px; font-size: 0.80rem;">
            Lihat Semua Proyek <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="card overflow-hidden border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; --bs-table-accent-bg: transparent !important; border-bottom: 1px solid #E2E8F0;">
                    <tr style="background-color: #ECF1F9 !important;">
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 60px; border-bottom: 1px solid #E2E8F0; border-top-left-radius: 14px;">NO</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 140px; border-bottom: 1px solid #E2E8F0;">KODE PROYEK</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 25%; border-bottom: 1px solid #E2E8F0;">NAMA PROYEK &amp; LINGKUP</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 19%; border-bottom: 1px solid #E2E8F0;">KLIEN PEMILIK</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 21%; border-bottom: 1px solid #E2E8F0;">PM PENANGGUNG JAWAB</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0;">JUMLAH CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 100px; border-bottom: 1px solid #E2E8F0; border-top-right-radius: 14px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentProjects as $index => $proj)
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            <td class="py-3 px-4" style="font-size: 0.82rem; font-weight: 600; color: #1E293B;">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.78rem; letter-spacing: 0.03em;">
                                    {{ $proj->kode_proyek }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                    {{ $proj->nama_proyek }}
                                </div>
                                @if($proj->deskripsi)
                                    <div class="text-muted small text-truncate" style="max-width: 300px; font-size: 0.74rem; margin-top: 1px;" title="{{ $proj->deskripsi }}">
                                        {{ $proj->deskripsi }}
                                    </div>
                                @else
                                    <div class="text-muted small fst-italic" style="font-size: 0.73rem;">Tidak ada deskripsi tambahan.</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($proj->client)
                                    <div class="fw-semibold text-dark" style="font-size: 0.84rem;">
                                        <a href="{{ route('admin.clients.show', $proj->client) }}" class="text-dark fw-bold text-decoration-none hover-primary">
                                            {{ $proj->client->company }}
                                        </a>
                                        @if($proj->client->nickname)
                                            <span class="badge bg-light text-primary border ms-1" style="font-size: 0.70rem; border-radius: 6px;">{{ $proj->client->nickname }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @include('admin.projects._pm-badges', ['project' => $proj])
                            </td>
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <span class="badge rounded-pill fw-bold" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; font-size: 0.75rem; padding: 0.28rem 0.75rem;">
                                    {{ $proj->changeRequests->count() }} CR
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.projects.edit', $proj) }}"
                                   class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                   style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.85rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none;">
                                    <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                                    <span>Edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Belum ada proyek terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
