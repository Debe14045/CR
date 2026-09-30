@extends('layouts.app')

@section('title', 'Master Status Transaksi &middot; Administrator')

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
            <h3 class="fw-bold mb-0 text-dark">Master Status Transaksi CR</h3>
            <p class="text-muted small mb-0">Kelola status dan tahapan alur pelacakan Change Request.</p>
        </div>

        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStatusModal">
            <i class="bi bi-plus-circle"></i> Tambah Status Baru
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table cr-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">Urutan</th>
                        <th>Kode Status</th>
                        <th>Label Status</th>
                        <th>Warna Badge</th>
                        <th>Keterangan Alur</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statuses as $st)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $st->sort_order }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $st->code }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $st->badge_color }}-subtle text-{{ $st->badge_color }} border border-{{ $st->badge_color }}-subtle px-2 py-1 fs-6">
                                    {{ $st->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $st->badge_color }} text-white">{{ $st->badge_color }}</span>
                            </td>
                            <td class="small text-muted">{{ $st->description ?: '-' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $st->id }}" title="Edit Status">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

                                {{-- Modal Edit Status --}}
                                <div class="modal fade text-start" id="editModal{{ $st->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('master.status.update', $st) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Edit Status: {{ $st->code }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Status</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $st->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Warna Badge</label>
                                                        <select name="badge_color" class="form-select">
                                                            @foreach (['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'] as $color)
                                                                <option value="{{ $color }}" {{ $st->badge_color == $color ? 'selected' : '' }}>{{ ucfirst($color) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Urutan Tampil (Sort Order)</label>
                                                        <input type="number" name="sort_order" class="form-control" value="{{ $st->sort_order }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="description" rows="2" class="form-control">{{ $st->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada status.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Add Status --}}
<div class="modal fade" id="addStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('master.status.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Status Transaksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Status (Identifier Unik) <span class="required-mark">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="Contoh: pending_review" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama / Label Status <span class="required-mark">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Pending Review" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warna Badge</label>
                        <select name="badge_color" class="form-select">
                            <option value="primary">Primary (Biru)</option>
                            <option value="info">Info (Biru Muda)</option>
                            <option value="success">Success (Hijau)</option>
                            <option value="warning">Warning (Kuning/Oranye)</option>
                            <option value="danger">Danger (Merah)</option>
                            <option value="secondary">Secondary (Abu-abu)</option>
                            <option value="dark">Dark (Hitam)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" placeholder="Angka urutan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Keterangan alur kerja status ini"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
