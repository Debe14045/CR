@extends('layouts.app')

@section('title', 'Data Perusahaan Klien • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (Sesuai Gaya Semua CR) ── --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">DATA PERUSAHAAN</h1>
            <p class="text-muted mb-0" style="font-size: 0.85rem; color: #64748B;">Kelola data master perusahaan klien, alamat, dan akun PIC terdaftar</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #0063D7; border-radius: 9999px; padding: 0.55rem 1.35rem; font-size: 0.84rem; border: none; box-shadow: 0 4px 12px rgba(0,99,215,0.25);">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Klien &amp; Akun</span>
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mt-3 mb-2" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mt-3 mb-2" role="alert" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── 2. Search & Filter Bar (Sesuai Gaya Semua CR) ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 mb-3">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="m-0" style="width: 380px; max-width: 100%;">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted" style="left: 16px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama perusahaan atau alamat..."
                       class="form-control"
                       style="padding-left: 42px; border-radius: 9999px; border: 1px solid #CBD5E1; font-size: 0.85rem; height: 40px; background: #FFFFFF; box-shadow: none;">
            </div>
        </form>

        <div class="d-flex align-items-center gap-2">
            @if(request()->filled('search'))
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" style="border-radius: 8px; height: 38px;">
                    <i class="bi bi-x-circle"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Horizontal Light Blue Line matching Figma --}}
    <div style="height: 1.5px; background: #E0F2FE; margin: 0.75rem 0 1.5rem 0; width: 100%;"></div>

    {{-- ── 3. Table Card (Sesuai Gaya Semua CR) ── --}}
    <div class="card overflow-hidden border-0 mb-3" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
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
                    @forelse ($clients as $index => $client)
                        @php
                            $rowNumber = ($clients->currentPage() - 1) * $clients->perPage() + ($index + 1);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            {{-- 1. NO --}}
                            <td class="py-3 px-4" style="font-size: 0.82rem; font-weight: 600; color: #1E293B;">
                                {{ $rowNumber }}
                            </td>

                            {{-- 2. NAMA PERUSAHAAN --}}
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

                            {{-- 3. ALAMAT --}}
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

                            {{-- 4-6. AKUN PIC, PROYEK, TOTAL CR --}}
                            @include('admin.clients._summary-cells', ['client' => $client])

                            {{-- 7. AKSI (Detail > button) --}}
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.clients.show', $client) }}"
                                   class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                   style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.95rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none; transition: opacity 0.15s ease;"
                                   onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                                    <span>Detail</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.65rem; font-weight: 700;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-buildings fs-2 text-muted"></i></div>
                                <div class="fw-bold">Belum Ada Data Klien</div>
                                <div class="small mt-1">Daftarkan perusahaan klien mitra beserta akun login PIC.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 4. Bottom Pagination matching Figma Semua CR ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 px-1 mb-4">
        <div class="fw-bold text-uppercase" style="font-size: 0.72rem; color: #0F172A; letter-spacing: 0.05em;">
            TOTAL DATA: {{ $clients->total() }}
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">ROWS PER PAGE</span>
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm bg-white border d-flex align-items-center gap-2 px-2 py-1"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: #CBD5E1 !important; border-radius: 4px; font-size: 0.74rem; font-weight: 600; color: #0F172A;">
                        <span>{{ $clients->perPage() }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.65rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="font-size: 0.76rem; min-width: 65px; border-radius: 6px;">
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 5, 'page' => 1]) }}">5</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}">10</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 20, 'page' => 1]) }}">20</a></li>
                        <li><a class="dropdown-item px-2 py-1 text-center" href="{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}">50</a></li>
                    </ul>
                </div>
            </div>
            <div class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">
                PAGE {{ $clients->currentPage() }} OF {{ max(1, $clients->lastPage()) }}
            </div>
            <div class="d-flex align-items-center gap-1">
                {{-- First Page << --}}
                <a href="{{ $clients->url(1) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $clients->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Previous Page < --}}
                <a href="{{ $clients->previousPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $clients->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Next Page > --}}
                <a href="{{ $clients->nextPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$clients->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Last Page >> --}}
                <a href="{{ $clients->url($clients->lastPage()) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$clients->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-right" style="font-size: 0.65rem;"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
