@extends('layouts.app')

@section('title', 'Master Data Client &middot; Administrator')

@section('content')
<div class="page-shell">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('change-requests.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <span class="text-muted small">&bull;</span>
                <span class="text-primary small fw-semibold text-uppercase">Administrator</span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">Master Client &amp; Rekanan</h3>
            <p class="text-muted small mb-0">Kelola profil instansi dan 3 PIC khusus (Marketing, IT, Procurement).</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('master.clients.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Client Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('master.clients.index') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama perusahaan, inisial/nickname, email, atau nama PIC..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
                @if (request('search'))
                    <a href="{{ route('master.clients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table cr-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Perusahaan &amp; Inisial</th>
                        <th>Kontak Utama</th>
                        <th>Alamat</th>
                        <th>3 PIC Khusus (Marketing, IT, Procurement)</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 0.95rem;">
                                        {{ $client->nickname ? strtoupper(substr($client->nickname, 0, 4)) : strtoupper(substr($client->company, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $client->company }}</div>
                                        <div class="small text-muted">Inisial: <span class="badge bg-secondary-subtle text-secondary">{{ $client->nickname ?: '-' }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1 small"></i> {{ $client->phone ?: '-' }}</div>
                                <div class="small text-muted"><i class="bi bi-envelope text-muted me-1 small"></i> {{ $client->email ?: '-' }}</div>
                            </td>
                            <td style="max-width: 200px;">
                                <span class="small text-muted">{{ Str::limit($client->address ?: '-', 50) }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle text-start p-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-megaphone me-1"></i> Mkt: <strong>{{ $client->pic_marketing_name ?: '-' }}</strong> ({{ $client->pic_marketing_phone ?: '-' }})
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-start p-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-code-slash me-1"></i> IT: <strong>{{ $client->pic_it_name ?: '-' }}</strong> ({{ $client->pic_it_phone ?: '-' }})
                                    </span>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-start p-1 px-2" style="font-size: 0.75rem;">
                                        <i class="bi bi-cart me-1"></i> Proc: <strong>{{ $client->pic_procurement_name ?: '-' }}</strong> ({{ $client->pic_procurement_phone ?: '-' }})
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if ($client->active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i> Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#picModal{{ $client->id }}" title="Lihat Detail PIC">
                                        <i class="bi bi-people"></i>
                                    </button>
                                    <a href="{{ route('master.clients.edit', $client) }}" class="btn btn-sm btn-outline-primary" title="Edit Data Client">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('master.clients.destroy', $client) }}" onsubmit="return confirm('Hapus master data client ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Client">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Modal PIC Detail --}}
                                <div class="modal fade text-start" id="picModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-person-lines-fill text-primary me-2"></i> Detail PIC: {{ $client->company }} ({{ $client->nickname }})
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    {{-- PIC Marketing --}}
                                                    <div class="col-md-4">
                                                        <div class="card h-100 p-3 bg-light border-0 shadow-none">
                                                            <div class="fw-bold text-info mb-2"><i class="bi bi-megaphone-fill me-1"></i> PIC Marketing</div>
                                                            <div class="fw-bold fs-6">{{ $client->pic_marketing_name ?: '-' }}</div>
                                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $client->pic_marketing_phone ?: '-' }}</div>
                                                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $client->pic_marketing_email ?: '-' }}</div>
                                                            <hr class="my-2">
                                                            <div class="small text-secondary">{{ $client->pic_marketing_desc ?: 'Tidak ada keterangan peran.' }}</div>
                                                        </div>
                                                    </div>

                                                    {{-- PIC IT / Programmer --}}
                                                    <div class="col-md-4">
                                                        <div class="card h-100 p-3 bg-light border-0 shadow-none">
                                                            <div class="fw-bold text-primary mb-2"><i class="bi bi-code-square me-1"></i> PIC IT / Programmer</div>
                                                            <div class="fw-bold fs-6">{{ $client->pic_it_name ?: '-' }}</div>
                                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $client->pic_it_phone ?: '-' }}</div>
                                                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $client->pic_it_email ?: '-' }}</div>
                                                            <hr class="my-2">
                                                            <div class="small text-secondary">{{ $client->pic_it_desc ?: 'Tidak ada keterangan peran.' }}</div>
                                                        </div>
                                                    </div>

                                                    {{-- PIC Procurement --}}
                                                    <div class="col-md-4">
                                                        <div class="card h-100 p-3 bg-light border-0 shadow-none">
                                                            <div class="fw-bold text-warning mb-2"><i class="bi bi-cart-check-fill me-1"></i> PIC Procurement</div>
                                                            <div class="fw-bold fs-6">{{ $client->pic_procurement_name ?: '-' }}</div>
                                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $client->pic_procurement_phone ?: '-' }}</div>
                                                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $client->pic_procurement_email ?: '-' }}</div>
                                                            <hr class="my-2">
                                                            <div class="small text-secondary">{{ $client->pic_procurement_desc ?: 'Tidak ada keterangan peran.' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ route('master.clients.edit', $client) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-pencil"></i> Edit Profil PIC
                                                </a>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-building-slash fs-1 d-block mb-2 text-muted"></i>
                                Belum ada data master client yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="p-3 border-top">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
