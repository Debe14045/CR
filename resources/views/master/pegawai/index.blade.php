@extends('layouts.app')

@section('title', 'Master Pegawai ITP &middot; Administrator')

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
            <h3 class="fw-bold mb-0 text-dark">Master Pegawai / Staff ITP</h3>
            <p class="text-muted small mb-0">Manajemen staf internal pelaksana CR (NIP, Nama, Alamat, dan Jabatan).</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('master.pegawai.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus"></i> Tambah Pegawai Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('master.pegawai.index') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari NIP, nama pegawai, jabatan (PM, Dev, QA, dll)..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
                @if (request('search'))
                    <a href="{{ route('master.pegawai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table cr-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan / Posisi</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pegawais as $pegawai)
                        <tr>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $pegawai->nip }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        {{ strtoupper(substr($pegawai->name, 0, 2)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $pegawai->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ $pegawai->position }}
                                </span>
                            </td>
                            <td>{{ $pegawai->address ?: '-' }}</td>
                            <td>
                                @if ($pegawai->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle me-1"></i> Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('master.pegawai.edit', $pegawai) }}" class="btn btn-sm btn-outline-primary" title="Edit Pegawai">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('master.pegawai.destroy', $pegawai) }}" onsubmit="return confirm('Hapus data pegawai ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Pegawai">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-muted"></i>
                                Belum ada data pegawai ITP. Silakan klik "Tambah Pegawai Baru".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pegawais->hasPages())
            <div class="p-3 border-top">
                {{ $pegawais->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
