@extends('layouts.app')

@section('title', 'Detail Klien ' . $client->company . ' • PT. ITPI Technology')

@section('content')
<div class="page-shell pb-5" style="width: 100%; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    {{-- ── 1. Page Header ── --}}
    <div class="d-flex justify-content-between align-items-start align-items-md-center mb-3 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('admin.clients.index') }}" class="text-decoration-none text-muted fw-semibold hover-primary" style="font-size: 0.82rem;">
                    Master Klien
                </a>
                <i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;"></i>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 text-uppercase px-2 py-1"
                      style="font-size: 0.72rem; letter-spacing: 0.05em; border-radius: 6px; font-weight: 700;">
                    Detail Klien
                </span>
            </div>

            <h1 class="fw-bold mb-2 d-flex align-items-center flex-wrap gap-2"
                style="color: #0F172A; font-size: 1.9rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
                <span>{{ $client->company }}</span>
                @if($client->nickname)
                    <span class="badge bg-light text-primary border" style="font-size: 0.82rem; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700;">
                        {{ $client->nickname }}
                    </span>
                @endif
            </h1>

            <div class="d-flex flex-wrap align-items-center gap-3 text-muted" style="font-size: 0.86rem; color: #64748B; line-height: 1.5;">
                <span class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-calendar3 text-primary"></i>
                    <span>Terdaftar sejak {{ $client->created_at ? $client->created_at->translatedFormat('d F Y') : '-' }}</span>
                </span>
                <span class="text-muted opacity-50">&bull;</span>
                <span class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt text-danger"></i>
                    <span>{{ $client->address ?: 'Alamat belum diatur' }}</span>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.projects.create', ['client_id' => $client->id]) }}"
               class="btn text-white fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
               style="background: #7C3AED; border-radius: 9999px; padding: 0.6rem 1.35rem; font-size: 0.85rem; border: none;">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Proyek</span>
            </a>
            <a href="{{ route('admin.clients.edit', $client) }}"
               class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center gap-2 shadow-sm"
               style="border-radius: 9999px; padding: 0.6rem 1.35rem; font-size: 0.85rem; border: none;">
                <i class="bi bi-pencil-square"></i>
                <span>Edit Klien &amp; Akun</span>
            </a>
        </div>
    </div>

    <div style="height: 2px; background: #BAE6FD; border-radius: 999px; margin: 1.25rem 0 1.5rem;"></div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 px-4 py-3" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div style="font-size: 0.88rem;">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── 2. Ringkasan Statistik Klien ── --}}
    @php
        $summaryCards = [
            [
                'label' => 'Akun PIC Terdaftar',
                'value' => $client->pics->count() . ' PIC',
                'caption' => 'Marketing, Procurement, IT',
                'accent' => '#0063D7',
                'iconBg' => '#EFF6FF',
                'icon' => 'bi-people-fill',
            ],
            [
                'label' => 'Proyek Klien Aktif',
                'value' => $client->projects->count() . ' Proyek',
                'caption' => 'Sistem atau aplikasi yang dikelola',
                'accent' => '#7C3AED',
                'iconBg' => '#F5F3FF',
                'icon' => 'bi-kanban-fill',
            ],
            [
                'label' => 'Change Request Diajukan',
                'value' => $client->changeRequests->count() . ' CR',
                'caption' => 'Total riwayat pengajuan klien',
                'accent' => '#059669',
                'iconBg' => '#ECFDF5',
                'icon' => 'bi-file-earmark-check-fill',
            ],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach($summaryCards as $card)
            <div class="col-md-4 col-12">
                <div class="bg-white h-100 overflow-hidden shadow-sm"
                     style="border: 1px solid #E2E8F0; border-radius: 16px;">
                    <div style="height: 4px; background: {{ $card['accent'] }};"></div>
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="text-uppercase fw-bold"
                                     style="font-size: 0.74rem; letter-spacing: 0.05em; color: #64748B; margin-bottom: 0.6rem;">
                                    {{ $card['label'] }}
                                </div>
                                <div class="fw-bold"
                                     style="font-size: 1.95rem; line-height: 1.15; color: #0F172A; letter-spacing: -0.02em; margin-bottom: 0.45rem;">
                                    {{ $card['value'] }}
                                </div>
                                <div style="font-size: 0.82rem; color: #64748B; line-height: 1.45;">
                                    {{ $card['caption'] }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; border-radius: 12px; background: {{ $card['iconBg'] }}; color: {{ $card['accent'] }};">
                                <i class="bi {{ $card['icon'] }} fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── 3. Akun Login PIC Resmi ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 18px; background: #FFFFFF; overflow: hidden;">
        <div class="card-header bg-white px-4 pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 42px; height: 42px; background: #EFF6FF; color: #0063D7;">
                    <i class="bi bi-person-badge-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem; line-height: 1.3;">Akun Login PIC Resmi (Role: Client)</h5>
                    <p class="text-muted mb-0" style="font-size: 0.83rem; line-height: 1.45;">
                        Akun berikut digunakan tim klien untuk login dan berkolaborasi dalam siklus Change Request.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.clients.edit', $client) }}"
               class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2"
               style="border-radius: 10px; font-size: 0.82rem;">
                <i class="bi bi-key-fill"></i>
                <span>Reset Password PIC</span>
            </a>
        </div>

        <div class="card-body p-4">
            <div class="row g-3 g-lg-4">
                @forelse($client->pics as $pic)
                    @php
                        $roleColor = match($pic->role_type) {
                            'marketing' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'border' => '#BFDBFE', 'badge' => 'PIC Marketing', 'icon' => 'bi-megaphone'],
                            'procurement' => ['bg' => '#F0FDF4', 'text' => '#15803D', 'border' => '#BBF7D0', 'badge' => 'PIC Procurement', 'icon' => 'bi-cart-check'],
                            'it' => ['bg' => '#FAF5FF', 'text' => '#7E22CE', 'border' => '#E9D5FF', 'badge' => 'PIC IT / Technical', 'icon' => 'bi-cpu'],
                            default => ['bg' => '#F8FAFC', 'text' => '#475569', 'border' => '#E2E8F0', 'badge' => ucfirst($pic->role_type), 'icon' => 'bi-person']
                        };
                    @endphp
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="p-4 h-100 d-flex flex-column"
                             style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px;">
                            <div class="d-flex align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background: {{ $roleColor['bg'] }}; color: {{ $roleColor['text'] }}; border: 1px solid {{ $roleColor['border'] }}; font-size: 0.74rem; font-weight: 700; padding: 0.4rem 0.75rem; border-radius: 8px;">
                                    <i class="bi {{ $roleColor['icon'] }}"></i>
                                    <span>{{ $roleColor['badge'] }}</span>
                                </span>
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background: #DCFCE7; color: #16A34A; font-size: 0.72rem; font-weight: 700; padding: 0.35rem 0.7rem; border-radius: 999px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Aktif</span>
                                </span>
                            </div>

                            <h6 class="fw-bold text-dark mb-3" style="font-size: 1.02rem; line-height: 1.35;">
                                {{ $pic->name }}
                            </h6>

                            <div class="d-flex flex-column gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2" style="font-size: 0.87rem; line-height: 1.45;">
                                    <i class="bi bi-envelope text-primary flex-shrink-0"></i>
                                    <span class="text-break" style="font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; font-size: 0.87rem; font-weight: 500; color: #0F172A; letter-spacing: 0.005em;">{{ $pic->email }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem; line-height: 1.45; color: #334155;">
                                    <i class="bi bi-whatsapp text-success flex-shrink-0"></i>
                                    <span>{{ $pic->phone ?: 'Nomor belum dicantumkan' }}</span>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.78rem;">
                                <span>Role Sistem: <strong class="text-dark">Client</strong></span>
                                <span>User ID: <strong class="text-dark">#{{ $pic->user_id ?: '-' }}</strong></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted" style="font-size: 0.88rem;">
                        Belum ada akun PIC terdaftar untuk klien ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── 4. Daftar Proyek Klien ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 18px; background: #FFFFFF; overflow: hidden;">
        <div class="card-header bg-white px-4 pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 42px; height: 42px; background: #F5F3FF; color: #7C3AED;">
                    <i class="bi bi-kanban-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem; line-height: 1.3;">Proyek Klien (Untuk Pengajuan Change Request)</h5>
                    <p class="text-muted mb-0" style="font-size: 0.83rem; line-height: 1.45;">
                        Klien hanya dapat membuat Change Request untuk proyek yang terdaftar di bawah ini.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.projects.create', ['client_id' => $client->id]) }}"
               class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
               style="background: #7C3AED; border-radius: 10px; font-size: 0.82rem; border: none;">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Proyek</span>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0; font-size: 0.86rem;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 170px; border-bottom: 1px solid #E2E8F0;">Kode Proyek</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; border-bottom: 1px solid #E2E8F0;">Nama Proyek &amp; Ruang Lingkup</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 130px; border-bottom: 1px solid #E2E8F0;">Jumlah CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 130px; border-bottom: 1px solid #E2E8F0;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->projects as $project)
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                <span class="badge bg-light text-primary border font-monospace px-3 py-2" style="font-size: 0.78rem; border-radius: 8px;">
                                    {{ $project->kode_proyek }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem; line-height: 1.4;">{{ $project->nama_proyek }}</div>
                                @if($project->deskripsi)
                                    <div class="text-muted text-truncate" style="max-width: 480px; font-size: 0.8rem; line-height: 1.45;" title="{{ $project->deskripsi }}">
                                        {{ $project->deskripsi }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2" style="font-size: 0.76rem;">
                                    {{ $project->changeRequests->count() }} CR
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-inline-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Edit Proyek" style="border-radius: 8px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="m-0"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek {{ $project->nama_proyek }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Proyek" style="border-radius: 8px;">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-2 d-block mb-2 text-muted opacity-75"></i>
                                <div class="fw-semibold text-dark mb-1" style="font-size: 0.92rem;">Klien ini belum memiliki proyek terdaftar.</div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.projects.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Tambahkan Proyek Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 5. Riwayat Change Request Klien ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 18px; background: #FFFFFF; overflow: hidden;">
        <div class="card-header bg-white px-4 pt-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 42px; height: 42px; background: #ECFDF5; color: #059669;">
                    <i class="bi bi-file-earmark-text-fill fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem; line-height: 1.3;">Riwayat Pengajuan Change Request</h5>
                    <p class="text-muted mb-0" style="font-size: 0.83rem; line-height: 1.45;">
                        Seluruh tiket CR yang diajukan oleh PIC dari {{ $client->company }}.
                    </p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle" style="border-collapse: separate; border-spacing: 0; font-size: 0.86rem;">
                <thead style="background-color: #ECF1F9 !important; --bs-table-bg: #ECF1F9 !important; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 150px; border-bottom: 1px solid #E2E8F0;">Kode CR</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; border-bottom: 1px solid #E2E8F0;">Judul Permintaan Perubahan</th>
                        <th class="py-3 px-4 fw-bold text-uppercase" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 180px; border-bottom: 1px solid #E2E8F0;">Proyek Terkait</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 160px; border-bottom: 1px solid #E2E8F0;">Tahapan Status</th>
                        <th class="py-3 px-4 fw-bold text-uppercase text-center" style="background-color: #ECF1F9 !important; font-size: 0.72rem; letter-spacing: 0.05em; color: #1E293B; width: 120px; border-bottom: 1px solid #E2E8F0;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->changeRequests as $cr)
                        @php
                            $badgeCfg = $cr->statusBadgeConfig();
                            $submittedDate = $cr->tanggal_pengajuan
                                ? \Carbon\Carbon::parse($cr->tanggal_pengajuan)->format('d M Y')
                                : ($cr->created_at ? $cr->created_at->format('d M Y') : '-');
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                            <td class="py-3 px-4" style="white-space: nowrap;">
                                <span class="badge bg-light text-dark border font-monospace px-3 py-2" style="font-size: 0.78rem; border-radius: 8px;">
                                    {{ $cr->kode_cr ?: '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem; line-height: 1.4;">{{ $cr->judul }}</div>
                                <div class="text-muted" style="font-size: 0.8rem; line-height: 1.4;">Diajukan pada {{ $submittedDate }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($cr->project)
                                    <span class="badge bg-light text-secondary border font-monospace px-2 py-1" style="font-size: 0.76rem; border-radius: 6px;">
                                        {{ $cr->project->kode_proyek }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center" style="white-space: nowrap;">
                                <span class="d-inline-flex align-items-center gap-1 fw-bold"
                                      style="background: {{ $badgeCfg['bg'] }}; color: {{ $badgeCfg['color'] }}; border-radius: 9999px; font-size: 0.74rem; padding: 0.35rem 0.85rem;">
                                    <span style="font-size: 0.6rem;">●</span>
                                    <span>{{ $badgeCfg['text'] }}</span>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('change-requests.show', $cr) }}"
                                   class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-1 px-3 py-1"
                                   style="background: #155DFC; border-radius: 6px; font-size: 0.76rem; border: none;">
                                    <span>Detail</span>
                                    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted" style="font-size: 0.88rem;">
                                Belum ada pengajuan Change Request dari klien ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
