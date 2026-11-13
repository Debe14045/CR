@extends('layouts.app')

@section('title', 'Data Pegawai & Akun • PT. ITPI Technology')

@section('content')
<div class="page-shell" style="width: 100%; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header (Sesuai Gaya Semua CR) ── --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000000; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em;">DATA PEGAWAI</h1>
            <p class="text-muted mb-0" style="font-size: 0.85rem; color: #64748B;">Manajemen pegawai internal PT. ITPI Technology (PM, PM Head, Presales, Admin, serta Programmer / Tim Non-Login)</p>
        </div>
        <div>
            <a href="{{ route('admin.pegawai.create') }}" class="btn text-white fw-bold d-inline-flex align-items-center gap-2"
               style="background: #059669; border-radius: 9999px; padding: 0.55rem 1.35rem; font-size: 0.84rem; border: none; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Pegawai &amp; Akun</span>
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

    {{-- ── 2. Search & Role Filter Bar (Sesuai Gaya Semua CR) ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 mb-3">
        <form method="GET" action="{{ route('admin.pegawai.index') }}" class="m-0" style="width: 380px; max-width: 100%;">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div class="position-relative">
                <i class="bi bi-search position-absolute text-muted" style="left: 16px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari NIP, nama lengkap, atau email..."
                       class="form-control"
                       style="padding-left: 42px; border-radius: 9999px; border: 1px solid #CBD5E1; font-size: 0.85rem; height: 40px; background: #FFFFFF; box-shadow: none;">
            </div>
        </form>

        {{-- Role Filter Dropdown matching Figma --}}
        @php
            $roleParam = request('role', '');
            $roleLabelMap = [
                'pm' => 'Project Manager (PM)',
                'pmh' => 'PM Head (PMH)',
                'presales' => 'Presales',
                'admin' => 'Administrator',
                'programmer' => 'Programmer (Non-Login)',
                'staff' => 'Tim Teknis / Staff',
            ];
            $currentRoleLabel = $roleLabelMap[$roleParam] ?? 'Semua Role Sistem';
        @endphp
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn bg-white d-flex align-items-center justify-content-between gap-3 px-3 shadow-none"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="border: 1px solid #CBD5E1; border-radius: 10px; font-size: 0.85rem; height: 40px; color: #334155; min-width: 160px;">
                    <span>{{ $currentRoleLabel }}</span>
                    <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border py-1" style="border-radius: 8px; font-size: 0.85rem; min-width: 210px;">
                    <li><a class="dropdown-item py-2 {{ empty($roleParam) ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => null, 'page' => 1]) }}">Semua Role Sistem</a></li>
                    <li><a class="dropdown-item py-2 {{ $roleParam === 'pm' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => 'pm', 'page' => 1]) }}">Project Manager (PM)</a></li>
                    <li><a class="dropdown-item py-2 {{ $roleParam === 'pmh' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => 'pmh', 'page' => 1]) }}">PM Head (PMH)</a></li>
                    <li><a class="dropdown-item py-2 {{ $roleParam === 'presales' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => 'presales', 'page' => 1]) }}">Presales</a></li>
                    <li><a class="dropdown-item py-2 {{ $roleParam === 'admin' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => 'admin', 'page' => 1]) }}">Administrator</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item py-2 {{ $roleParam === 'programmer' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => 'programmer', 'page' => 1]) }}">Programmer (Non-Login)</a></li>
                    <li><a class="dropdown-item py-2 {{ $roleParam === 'staff' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['role' => 'staff', 'page' => 1]) }}">Tim Teknis / Staff</a></li>
                </ul>
            </div>
            @if(request()->filled('search') || request()->filled('role'))
                <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" style="border-radius: 8px; height: 40px;" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
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
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 150px; border-bottom: 1px solid #E2E8F0;">NIP</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 25%; border-bottom: 1px solid #E2E8F0;">PEGAWAI &amp; JABATAN</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 190px; border-bottom: 1px solid #E2E8F0;">ROLE</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 24%; border-bottom: 1px solid #E2E8F0;">KONTAK</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0;">STATUS</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 110px; border-bottom: 1px solid #E2E8F0; border-top-right-radius: 14px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawais as $index => $pegawai)
                        @php
                            $roleMeta = $pegawai->roleBadgeMeta(false);
                            $rowNumber = ($pegawais->currentPage() - 1) * $pegawais->perPage() + ($index + 1);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            {{-- 1. NO --}}
                            <td class="py-3 px-4" style="font-size: 0.82rem; font-weight: 600; color: #1E293B;">
                                {{ $rowNumber }}
                            </td>

                            {{-- 2. NIP --}}
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 0.78rem; letter-spacing: 0.03em;">
                                    {{ $pegawai->nip }}
                                </span>
                            </td>

                            {{-- 3. PEGAWAI & JABATAN --}}
                            <td class="py-3 px-4">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                        {{ $pegawai->name }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.76rem;">
                                        <i class="bi bi-briefcase me-1"></i>{{ $pegawai->organizationSummary() }}
                                    </div>
                                </div>
                            </td>

                            {{-- 4. ROLE --}}
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="badge px-2 py-1" style="background: {{ $roleMeta['bg'] }}; color: {{ $roleMeta['text'] }}; border: 1px solid {{ $roleMeta['border'] }}; font-size: 0.72rem; font-weight: 700;">
                                        <i class="bi {{ $roleMeta['icon'] }} me-1"></i>{{ $roleMeta['label'] }}
                                    </span>
                                    @if($pegawai->role === 'pmh' && $pegawai->user?->can_act_as_pm)
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25" style="font-size: 0.62rem;" title="Bisa switch ke dashboard PM">
                                            <i class="bi bi-arrow-repeat me-1"></i>Can Act as PM
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. KONTAK --}}
                            @include('admin.pegawai._kontak-cell', ['pegawai' => $pegawai])

                            {{-- 6. STATUS --}}
                            @include('admin.pegawai._status-badge', ['pegawai' => $pegawai, 'interactive' => true])

                            {{-- 7. AKSI --}}
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="{{ route('admin.pegawai.edit', $pegawai) }}"
                                       class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1"
                                       style="background: #155DFC; border-radius: 6px; font-size: 0.74rem; padding: 0.35rem 0.85rem; border: none; box-shadow: 0 1px 2px rgba(21,93,252,0.25); text-decoration: none;">
                                        <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form action="{{ route('admin.pegawai.destroy', $pegawai) }}" method="POST" class="m-0"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $pegawai->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-0 d-inline-flex align-items-center justify-content-center"
                                                style="width: 28px; height: 28px; border-radius: 6px;" title="Hapus Pegawai">
                                            <i class="bi bi-trash3" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="bi bi-people fs-2 text-muted"></i></div>
                                <div class="fw-bold">Belum Ada Data Pegawai</div>
                                <div class="small mt-1">Daftarkan pegawai internal PT. ITPI Technology.</div>
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
            TOTAL DATA: {{ $pegawais->total() }}
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-uppercase" style="font-size: 0.70rem; color: #0F172A; letter-spacing: 0.04em;">ROWS PER PAGE</span>
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm bg-white border d-flex align-items-center gap-2 px-2 py-1"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-color: #CBD5E1 !important; border-radius: 4px; font-size: 0.74rem; font-weight: 600; color: #0F172A;">
                        <span>{{ $pegawais->perPage() }}</span>
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
                PAGE {{ $pegawais->currentPage() }} OF {{ max(1, $pegawais->lastPage()) }}
            </div>
            <div class="d-flex align-items-center gap-1">
                {{-- First Page << --}}
                <a href="{{ $pegawais->url(1) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $pegawais->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Previous Page < --}}
                <a href="{{ $pegawais->previousPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ $pegawais->onFirstPage() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-left" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Next Page > --}}
                <a href="{{ $pegawais->nextPageUrl() }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$pegawais->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
                </a>
                {{-- Last Page >> --}}
                <a href="{{ $pegawais->url($pegawais->lastPage()) }}" class="btn btn-sm p-0 d-flex align-items-center justify-content-center {{ !$pegawais->hasMorePages() ? 'disabled opacity-50' : '' }}"
                   style="width: 24px; height: 24px; border: 1px solid #CBD5E1; border-radius: 4px; background: #FFFFFF; font-size: 0.70rem; color: #334155; text-decoration: none;">
                    <i class="bi bi-chevron-double-right" style="font-size: 0.65rem;"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
