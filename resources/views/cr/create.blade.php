@extends('layouts.app')

@section('title', 'Ajukan Change Request Baru')

@section('content')
<div class="page-shell" style="width: 100%; padding: 0.25rem 0.5rem 3rem; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    @if ($errors->any())
        <div class="alert alert-danger mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #FECACA; background: #FEF2F2;">
            <div class="fw-bold small mb-1 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Periksa kembali isian formulir:</div>
            <ul class="mb-0 small text-danger ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── 1. Page Header (Figma Exact: Outside & Above the Cards) ── --}}
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.68rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            {{ !empty($isEdit) ? 'Edit Draft Change Request' : 'Ajukan Change Request Baru' }}
        </h1>
        <div style="color: #64748B; font-size: 0.82rem; font-weight: 400;">
            {{ !empty($isEdit) ? 'Perbarui draft pengajuan Change Request sebelum disubmit' : 'Pembuatan Pengajuan CR' }}
        </div>
    </div>

    <form method="POST" action="{{ !empty($isEdit) ? route('change-requests.update', $changeRequest) : route('change-requests.store') }}" enctype="multipart/form-data" id="figmaCrForm">
        @csrf
        @if (!empty($isEdit))
            @method('PUT')
        @endif

        {{-- Hidden inputs for backend compatibility --}}
        @php
            $isPmUser = auth()->user()?->isPm() || auth()->user()?->isAdmin() || (!empty($isPm) && empty($isClient));
            
            $companies = [];
            if (isset($clients) && count($clients) > 0) {
                foreach ($clients as $cl) {
                    $clName = is_object($cl) ? $cl->company : $cl;
                    $clClean = trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $clName));
                    $clInit = strtoupper(substr($clClean, 0, 2)) ?: 'CL';
                    $companies[] = ['name' => $clName, 'initial' => $clInit];
                }
            } elseif (Auth::user()?->client?->company) {
                $clName = Auth::user()->client->company;
                $companies[] = ['name' => $clName, 'initial' => strtoupper(substr($clName, 0, 2))];
            } else {
                $companies[] = ['name' => 'PT Maju Bersama', 'initial' => 'MB'];
            }

            $defaultComp = old('nama_perusahaan', !empty($changeRequest) ? ($changeRequest->klien ?: '') : (Auth::user()?->client?->company ?? ''));
            if (empty($defaultComp) && count($companies) > 0) {
                $defaultComp = $companies[0]['name'];
            }
            $defaultProj = old('nama_project', !empty($changeRequest) ? ($changeRequest->proyek_terkait ?: '') : '');
            if (empty($defaultProj)) {
                $defaultProj = 'Eprocurement PT eagle Hight plantanions TBK';
            }
            $defaultInitial = !empty($defaultComp) ? strtoupper(substr(trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $defaultComp)), 0, 2)) : '';
            if (!$defaultInitial && !empty($defaultComp)) $defaultInitial = strtoupper(substr($defaultComp, 0, 2));
            if (!$defaultInitial) $defaultInitial = 'MB';

            $selectedProject = old('nama_project', !empty($changeRequest) ? ($changeRequest->proyek_terkait ?: '') : 'Eprocurement PT eagle Hight plantanions TBK');
        @endphp
        <input type="hidden" name="nama_perusahaan" id="nama_perusahaan_input" value="{{ $defaultComp }}">
        <input type="hidden" name="nama_project" id="nama_project_input" value="{{ $defaultProj }}">
        <input type="hidden" name="prioritas" id="prioritas_input" value="{{ old('prioritas', !empty($changeRequest) ? ($changeRequest->prioritas ?: 'normal') : 'normal') }}">

        @if ($isPmUser)
            {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                 PM VIEW: AJUKAN CR BARU (Unified Layout matching screenshot)
            ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}

            {{-- Thin Blue Divider --}}
            <div style="height: 1.5px; background: #CDE7FE; margin: -0.5rem 0 1.5rem 0;"></div>

            {{-- Single Unified Gray Container (#F4F6F9) --}}
            <div class="p-4 mb-4" style="background: #F4F6F9; border: 1.5px solid #DCE6F1; border-radius: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">

                {{-- ── ROW 1: Nama Perusahaan*, Inisial Klien, Nama PIC, CR Owner* ── --}}
                <div class="row g-3 mb-3 align-items-end">
                    {{-- Col 1: Nama Perusahaan* --}}
                    <div class="col-lg-5 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Perusahaan <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="pmCompanyDropdownWrapper">
                            <button type="button" class="btn w-100 d-flex align-items-center justify-content-between text-start"
                                    id="pmCompanyDropdownBtn"
                                    onclick="togglePmDropdown('pmCompanyDropdownMenu', event)"
                                    style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B; transition: all 0.15s ease;">
                                <span id="pmSelectedCompanyName">{{ $defaultComp ?: 'PT Maju Bersama' }}</span>
                                <svg id="pmCompanyChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ms-2 flex-shrink-0" style="transition: transform 0.2s ease;">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div class="position-absolute w-100 py-1" id="pmCompanyDropdownMenu"
                                 style="display: none; top: calc(100% + 4px); left: 0; z-index: 9999; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18); max-height: 240px; overflow-y: auto;">
                                @foreach ($companies as $comp)
                                    <div class="dropdown-item py-2 px-3 cursor-pointer d-flex align-items-center justify-content-between"
                                         onclick="selectPmCompany('{{ $comp['name'] }}', '{{ $comp['initial'] }}')"
                                         style="font-size: 0.84rem; color: #1E293B; cursor: pointer;"
                                         onmouseover="this.style.background='#F0F9FF'; this.style.color='#0098EA';"
                                         onmouseout="this.style.background='transparent'; this.style.color='#1E293B';">
                                        <span style="font-weight: 600;">{{ $comp['name'] }}</span>
                                        <span style="background: #E0F2FE; color: #0284C7; font-size: 0.70rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;">{{ $comp['initial'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Col 2: Inisial Klien --}}
                    <div class="col-lg-1 col-3" style="min-width: 65px; max-width: 85px;">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Inisial Klien
                        </label>
                        <div id="pm_inisial_klien_box" class="d-flex align-items-center justify-content-center fw-bold"
                             style="background: #CBD5E1; color: #334155; border-radius: 8px; height: 42px; font-size: 0.84rem;">
                            {{ $defaultInitial ?: 'MB' }}
                        </div>
                        <input type="hidden" name="inisial_klien" id="pm_inisial_klien_input" value="{{ $defaultInitial ?: 'MB' }}">
                    </div>

                    {{-- Col 3: Nama PIC --}}
                    <div class="col-lg-3 col-9">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama PIC
                        </label>
                        <input type="text" name="nama_pic" id="pm_nama_pic"
                               value="{{ old('nama_pic', !empty($changeRequest) ? ($changeRequest->pic_sales ?: 'Totok Antok') : 'Totok Antok') }}"
                               class="form-control"
                               style="background: #CBD5E1; color: #334155; border: none; border-radius: 8px; height: 42px; font-size: 0.84rem; font-weight: 500; padding: 0 14px;">
                    </div>

                    {{-- Col 4: CR Owner* --}}
                    <div class="col-lg-3 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            CR Owner <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="cr_owner" id="pm_cr_owner" required
                               value="{{ old('cr_owner', !empty($changeRequest) ? ($changeRequest->owner_cr ?: 'Andik Vermansyah') : 'Andik Vermansyah') }}"
                               class="form-control"
                               style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; font-weight: 500; padding: 0 14px; color: #1E293B;">
                    </div>
                </div>

                {{-- ── ROW 2: Nama Project*, Tanggal, Request Date* ── --}}
                <div class="row g-3 mb-3 align-items-end">
                    {{-- Col 1: Nama Project* --}}
                    <div class="col-lg-6 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Project <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="pmProjectDropdownWrapper">
                            <button type="button" class="btn w-100 d-flex align-items-center justify-content-between text-start"
                                    id="pmProjectDropdownBtn"
                                    onclick="togglePmDropdown('pmProjectDropdownMenu', event)"
                                    style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B; transition: all 0.15s ease;">
                                <span id="pmSelectedProjectName" class="text-truncate">{{ $selectedProject ?: 'Eprocurement PT eagle Hight plantanions TBK' }}</span>
                                <svg id="pmProjectChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ms-2 flex-shrink-0" style="transition: transform 0.2s ease;">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>

                            <div class="position-absolute w-100 py-1" id="pmProjectDropdownMenu"
                                 style="display: none; top: calc(100% + 4px); left: 0; z-index: 9999; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18); max-height: 240px; overflow-y: auto;">
                                @php
                                    $projOptions = [
                                        'Eprocurement PT eagle Hight plantanions TBK',
                                        'Enterprise HR Management System',
                                        'Mobile Banking Platform App',
                                        'Core ERP Migration & Integration',
                                        'Single Sign-On & Authentication Gateway',
                                    ];
                                @endphp
                                @foreach ($projOptions as $proj)
                                    <div class="dropdown-item py-2 px-3 cursor-pointer d-flex align-items-center justify-content-between"
                                         onclick="selectPmProject('{{ $proj }}')"
                                         style="font-size: 0.84rem; color: #1E293B; cursor: pointer;"
                                         onmouseover="this.style.background='#F0F9FF'; this.style.color='#0098EA';"
                                         onmouseout="this.style.background='transparent'; this.style.color='#1E293B';">
                                        <span style="font-weight: 500;">{{ $proj }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Col 2: Tanggal --}}
                    <div class="col-lg-3 col-6">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Tanggal
                        </label>
                        <div class="position-relative" id="pm_date_input_wrap">
                            <input type="text" name="date" id="pm_date_input"
                                   value="{{ old('date', !empty($changeRequest) && $changeRequest->tanggal_pengajuan ? date('d M Y', strtotime($changeRequest->tanggal_pengajuan)) : now()->format('d M Y')) }}"
                                   readonly onclick="openDatePicker('pm_date_input', event)"
                                   class="form-control"
                                   style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 38px 0 14px; cursor: pointer; color: #1E293B; font-weight: 500;">
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" style="cursor: pointer;" onclick="openDatePicker('pm_date_input', event)">
                                <i class="bi bi-calendar3" style="font-size: 0.95rem; color: #334155;"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Col 3: Request Date* --}}
                    <div class="col-lg-3 col-6">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Request Date <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="pm_request_date_input_wrap">
                            <input type="text" name="request_date" id="pm_request_date_input"
                                   value="{{ old('request_date', !empty($changeRequest) && $changeRequest->target_selesai ? date('d M Y', strtotime($changeRequest->target_selesai)) : now()->format('d M Y')) }}"
                                   readonly onclick="openDatePicker('pm_request_date_input', event)"
                                   class="form-control"
                                   style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 38px 0 14px; cursor: pointer; color: #1E293B; font-weight: 500;">
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" style="cursor: pointer;" onclick="openDatePicker('pm_request_date_input', event)">
                                <i class="bi bi-calendar3" style="font-size: 0.95rem; color: #334155;"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ── ROW 3: Nama CR* ── --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size: 0.78rem; color: #0F172A;">
                            Nama CR <span style="color: #EF4444;">*</span>
                        </label>
                        <span id="pm_cr_name_counter" class="text-muted" style="font-size: 0.70rem;">0/255</span>
                    </div>
                    <input type="text" name="cr_name" id="pm_cr_name" class="form-control"
                           placeholder="Contoh: Integrasi API Pembayaran OVO"
                           maxlength="255"
                           value="{{ old('cr_name', old('judul', !empty($changeRequest) ? $changeRequest->judul : '')) }}"
                           required
                           style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 14px; color: #1E293B;">
                    <div class="form-text text-muted" style="font-size: 0.70rem;">Maksimal 255 kata/karakter.</div>
                </div>

                {{-- ── ROW 4: Detail CR* (TinyMCE WYSIWYG Editor) ── --}}
                <div class="mb-3">
                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                        Detail CR <span style="color: #EF4444;">*</span>
                    </label>
                    
                    <div style="border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden; background: #FFFFFF;" id="pmEditorWrapper">
                        {{-- Menubar with Working Dropdowns --}}
                        <div class="d-flex align-items-center gap-1 px-2 py-1 bg-white border-bottom position-relative"
                             style="font-size: 0.78rem; user-select: none; color: #475569; border-color: #F1F5F9 !important;">
                            
                            {{-- File Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">File</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="editorAction('new')"><i class="bi bi-file-earmark me-2"></i> Dokumen Baru</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="window.print()"><i class="bi bi-printer me-2"></i> Cetak (Print)</a></li>
                                </ul>
                            </div>

                            {{-- Edit Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Edit</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('undo')"><span><i class="bi bi-arrow-counterclockwise me-2"></i> Undo</span> <small class="text-muted">Ctrl+Z</small></a></li>
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('redo')"><span><i class="bi bi-arrow-clockwise me-2"></i> Redo</span> <small class="text-muted">Ctrl+Y</small></a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('selectAll')"><span><i class="bi bi-check-all me-2"></i> Pilih Semua</span> <small class="text-muted">Ctrl+A</small></a></li>
                                </ul>
                            </div>

                            {{-- View Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">View</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openPreviewModal()"><i class="bi bi-eye me-2"></i> Preview CR</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="toggleEditorFullscreen()"><i class="bi bi-arrows-fullscreen me-2"></i> Layar Penuh</a></li>
                                </ul>
                            </div>

                            {{-- Insert Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Insert</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 160px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertLinkPrompt()"><i class="bi bi-link-45deg me-2"></i> Tautan Link</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertImagePrompt()"><i class="bi bi-image me-2"></i> Gambar / Mockup</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('insertHorizontalRule')"><i class="bi bi-dash-lg me-2"></i> Garis Horizontal</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertCurrentDate()"><i class="bi bi-calendar-event me-2"></i> Tanggal Hari Ini</a></li>
                                </ul>
                            </div>

                            {{-- Format Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Format</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('bold')"><strong>Tebal (Bold)</strong></a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('italic')"><em>Miring (Italic)</em></a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('underline')"><u>Garis Bawah</u></a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('strikeThrough')"><del>Coret</del></a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><a class="dropdown-item py-1 px-3 text-danger" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('removeFormat')"><i class="bi bi-eraser me-2"></i> Hapus Format</a></li>
                                </ul>
                            </div>

                            {{-- Tools Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Tools</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="showWordCount()"><i class="bi bi-calculator me-2"></i> Hitung Kata</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openSourceModal()"><i class="bi bi-code-slash me-2"></i> Kode Sumber HTML</a></li>
                                </ul>
                            </div>

                            {{-- Table Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Table</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertTable(2, 2)"><i class="bi bi-table me-2"></i> Tabel 2 x 2</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertTable(3, 3)"><i class="bi bi-table me-2"></i> Tabel 3 x 3</a></li>
                                </ul>
                            </div>

                            {{-- Help Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Help</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 160px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openHelpModal()"><i class="bi bi-question-circle me-2"></i> Bantuan & Shortcut</a></li>
                                </ul>
                            </div>

                        </div>

                        {{-- Toolbar Icons --}}
                        <div class="d-flex align-items-center gap-1 px-3 py-1 border-bottom bg-white flex-wrap" style="font-size: 0.82rem; color: #334155; border-color: #F1F5F9 !important;">
                            
                            {{-- Undo / Redo --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('undo')" title="Undo">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('redo')" title="Redo">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Paragraph Dropdown --}}
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-dark d-flex align-items-center gap-1"
                                        id="pmParagraphDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="font-size: 0.78rem; font-weight: 500; height: 26px;">
                                    <span id="pmCurrentParagraphLabel">Paragraph</span>
                                    <i class="bi bi-chevron-down" style="font-size: 0.65rem;"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('p', 'Paragraph')">Paragraph</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fw-bold" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('h1', 'Heading 1')">Heading 1</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fw-bold" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('h2', 'Heading 2')">Heading 2</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fw-bold" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('h3', 'Heading 3')">Heading 3</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fst-italic" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('blockquote', 'Quote')">Quote</a></li>
                                    <li><a class="dropdown-item py-1 px-3 font-monospace" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('pre', 'Code')">Code Block</a></li>
                                </ul>
                            </div>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Bold, Italic, Underline, Strikethrough --}}
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 fw-bold text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('bold')" title="Bold (Ctrl+B)">B</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 fst-italic text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('italic')" title="Italic (Ctrl+I)">I</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-decoration-underline text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('underline')" title="Underline (Ctrl+U)">U</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-decoration-line-through text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('strikeThrough')" title="Strikethrough">S</button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Alignments --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyFull')" title="Justify"><i class="bi bi-justify"></i></button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Lists & Indents --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('outdent')" title="Kurangi Inden"><i class="bi bi-text-indent-right"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('indent')" title="Tambah Inden"><i class="bi bi-text-indent-left"></i></button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Link & Image --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="insertLinkPrompt()" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="insertImagePrompt()" title="Insert Image"><i class="bi bi-image"></i></button>
                        </div>

                        {{-- Contenteditable Area --}}
                        <div id="pmEditorBody" contenteditable="true"
                             style="min-height: 250px; padding: 14px 16px; outline: none; font-size: 0.86rem; line-height: 1.6; color: #1E293B;"
                             oninput="syncEditor()"
                             data-placeholder="Tuliskan rincian kebutuhan perubahan sistem, spesifikasi fitur baru, atau alur kerja yang diinginkan...">
                            {!! old('detail_cr', old('deskripsi_cr', old('deskripsi', !empty($changeRequest) ? $changeRequest->deskripsi : ''))) !!}
                        </div>
                        <textarea name="detail_cr" id="pm_deskripsi_cr_hidden" class="d-none">{{ old('detail_cr', old('deskripsi_cr', old('deskripsi', !empty($changeRequest) ? $changeRequest->deskripsi : ''))) }}</textarea>

                        {{-- TinyMCE Statusbar at bottom --}}
                        <div class="px-3 py-1 border-top d-flex justify-content-between align-items-center text-muted"
                             style="background: #F8FAFC; font-size: 0.72rem; user-select: none; border-color: #F1F5F9 !important;">
                            <span style="color: #64748B;">p</span>
                            <span id="pm_detail_cr_counter" style="color: #64748B; font-weight: 500;">0/5000 karakter</span>
                            <div class="d-flex align-items-center gap-1" style="color: #64748B;">
                                <span>Build with</span>
                                <span class="fw-bold" style="color: #1E293B;">tinyMCE</span>
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="margin-left: 4px; opacity: 0.5;">
                                    <path d="M9 1L1 9M9 5L5 9M9 9H9.01" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="form-text text-muted mt-1" style="font-size: 0.70rem;">Maksimal 5000 kata/karakter.</div>
                </div>

                {{-- ── ROW 5: Dokumen Lainnya* (50%) & Prioritas CR* (50%) ── --}}
                <div class="row g-3 mb-3 align-items-end">
                    {{-- Dokumen Lainnya* --}}
                    <div class="col-lg-6 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Dokumen Lainnya <span class="text-muted fw-normal" style="font-size: 0.72rem;">(Hanya format .pdf)</span> <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="d-flex align-items-center" style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; overflow: hidden;">
                            <button type="button" class="btn text-white d-flex align-items-center justify-content-center gap-2 px-3 h-100 flex-shrink-0"
                                    onclick="document.getElementById('pm_dokumen_lainnya').click()"
                                    style="background: #0F2A4A; border: none; border-radius: 0; font-size: 0.82rem; font-weight: 600;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span>Pilih File</span>
                            </button>
                            <span id="pm_dokumen_lainnya_label" class="px-3 text-truncate flex-grow-1" style="font-size: 0.82rem; color: #64748B;">
                                Tidak ada file yang dipilih
                            </span>
                            <input type="file" name="dokumen_lainnya" id="pm_dokumen_lainnya" accept=".pdf,application/pdf" class="d-none" onchange="handlePmFileInput(this, 'pm_dokumen_lainnya_label')">
                        </div>
                    </div>

                    {{-- Prioritas CR* --}}
                    <div class="col-lg-6 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Prioritas CR <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="d-flex gap-3">
                            {{-- Normal Pill --}}
                            <div id="pmPriorityNormalCard" class="flex-grow-1 d-flex align-items-center justify-content-between px-3 cursor-pointer"
                                 onclick="selectPmPriority('normal')"
                                 style="background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; height: 42px; transition: all 0.15s ease;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #22C55E; box-shadow: 0 0 6px rgba(34, 197, 94, 0.6); display: inline-block;"></span>
                                    <span id="pmLabelNormal" class="fw-semibold" style="font-size: 0.84rem; color: #0284C7;">Normal</span>
                                </div>
                                <span id="pmRadioNormal">
                                    <span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span></span>
                                </span>
                            </div>

                            {{-- Urgent Pill --}}
                            <div id="pmPriorityUrgentCard" class="flex-grow-1 d-flex align-items-center justify-content-between px-3 cursor-pointer"
                                 onclick="selectPmPriority('urgent')"
                                 style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; transition: all 0.15s ease;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #EF4444; box-shadow: 0 0 6px rgba(239, 68, 68, 0.6); display: inline-block;"></span>
                                    <span id="pmLabelUrgent" class="fw-semibold" style="font-size: 0.84rem; color: #64748B;">Urgent</span>
                                </div>
                                <span id="pmRadioUrgent">
                                    <span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── ROW 6: Estimasi Mandays (Analisis*, Development*, Testing*, Total Mandays) ── --}}
                @php
                    $initA = old('mindesk_analisis', !empty($changeRequest) && $changeRequest->mindesk_analisis !== null ? $changeRequest->mindesk_analisis : '');
                    $initD = old('mindesk_development', !empty($changeRequest) && $changeRequest->mindesk_development !== null ? $changeRequest->mindesk_development : '');
                    $initT = old('mindesk_testing', !empty($changeRequest) && $changeRequest->mindesk_testing !== null ? $changeRequest->mindesk_testing : '');
                    $hasAllThree = ($initA !== '' && $initA !== null && $initD !== '' && $initD !== null && $initT !== '' && $initT !== null);
                    $initTotal = '';
                    if ($hasAllThree) {
                        $calcVal = (float)$initA + (float)$initD + (float)$initT;
                        $initTotal = ($calcVal == (int)$calcVal) ? (int)$calcVal : $calcVal;
                    }
                @endphp
                <div class="mb-3">
                    <div class="fw-bold mb-2" style="font-size: 0.8rem; color: #0F172A;">Estimasi Mandays</div>
                    <div class="row g-3">
                        <div class="col-lg-3 col-6">
                            <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Analisis (hari) <span style="color: #EF4444;">*</span>
                            </label>
                            <input type="number" step="0.5" min="0" name="mindesk_analisis" id="pm_mindesk_analisis"
                                   value="{{ $initA }}"
                                   placeholder="0"
                                   oninput="calcPmTotalMandays();"
                                   onchange="calcPmTotalMandays();"
                                   class="form-control"
                                   style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 14px; color: #1E293B;">
                        </div>
                        <div class="col-lg-3 col-6">
                            <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Development (hari) <span style="color: #EF4444;">*</span>
                            </label>
                            <input type="number" step="0.5" min="0" name="mindesk_development" id="pm_mindesk_development"
                                   value="{{ $initD }}"
                                   placeholder="0"
                                   oninput="calcPmTotalMandays();"
                                   onchange="calcPmTotalMandays();"
                                   class="form-control"
                                   style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 14px; color: #1E293B;">
                        </div>
                        <div class="col-lg-3 col-6">
                            <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Testing (hari) <span style="color: #EF4444;">*</span>
                            </label>
                            <input type="number" step="0.5" min="0" name="mindesk_testing" id="pm_mindesk_testing"
                                   value="{{ $initT }}"
                                   placeholder="0"
                                   oninput="calcPmTotalMandays();"
                                   onchange="calcPmTotalMandays();"
                                   class="form-control"
                                   style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 14px; color: #1E293B;">
                        </div>
                        <div class="col-lg-3 col-6">
                            <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                Total Mandays
                            </label>
                            <input type="text" id="pm_total_mandays" readonly
                                   value="{{ $initTotal }}"
                                   placeholder="0"
                                   class="form-control fw-bold"
                                   style="background: #CBD5E1; color: #1E293B; border: none; border-radius: 8px; height: 42px; font-size: 0.84rem; padding: 0 14px;">
                        </div>
                    </div>
                </div>

                {{-- ── ROW 7: CR Notes ── --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size: 0.78rem; color: #0F172A;">
                            CR Notes
                        </label>
                        <span id="pm_cr_notes_counter" class="text-muted" style="font-size: 0.70rem;">0/5000</span>
                    </div>
                    <textarea name="cr_notes" id="pm_cr_notes" rows="3" class="form-control"
                              maxlength="5000"
                              style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.84rem; padding: 12px 14px; width: 100%; height: 90px; outline: none; color: #1E293B;"
                              placeholder="Catatan tambahan atau konteks...">{{ old('cr_notes', !empty($changeRequest) ? ($changeRequest->catatan_pengajuan ?: $changeRequest->catatan_analisis) : '') }}</textarea>
                    <div class="form-text text-muted" style="font-size: 0.70rem;">Maksimal 5000 kata/karakter.</div>
                </div>

                {{-- ── ROW 8: Butuh Solution Paper?* & Upload Solution Paper (PDF)* ── --}}
                @php
                    $initSpRequired = old('solution_paper_required', !empty($changeRequest) && isset($changeRequest->solution_paper_required) ? ($changeRequest->solution_paper_required ? '1' : '0') : '1');
                    $existingSpFile = !empty($changeRequest) && $changeRequest->solutionPaper ? $changeRequest->solutionPaper->solution_paper_file : null;
                    $existingSpFileName = $existingSpFile ? basename($existingSpFile) : 'Tidak ada file yang dipilih';
                @endphp
                <div class="row g-3 align-items-end">
                    {{-- Butuh Solution Paper?* --}}
                    <div class="col-lg-4 col-12">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Butuh Solution Paper? <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="d-flex gap-2">
                            {{-- Ya Pill --}}
                            <div id="pmSpYaCard" class="d-flex align-items-center gap-2 px-3 cursor-pointer"
                                 onclick="selectPmSolutionPaper(1)"
                                 style="background: #FFFFFF; border: {{ $initSpRequired == '1' ? '1.5px solid #0098EA' : '1px solid #CBD5E1' }}; border-radius: 8px; height: 42px; min-width: 95px; transition: all 0.15s ease;">
                                <span id="pmSpYaRadio">
                                    @if($initSpRequired == '1')
                                        <span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span></span>
                                    @else
                                        <span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>
                                    @endif
                                </span>
                                <span id="pmSpYaLabel" class="fw-semibold" style="font-size: 0.84rem; color: {{ $initSpRequired == '1' ? '#0284C7' : '#64748B' }};">Ya</span>
                            </div>

                            {{-- Tidak Pill --}}
                            <div id="pmSpTidakCard" class="d-flex align-items-center gap-2 px-3 cursor-pointer"
                                 onclick="selectPmSolutionPaper(0)"
                                 style="background: #FFFFFF; border: {{ $initSpRequired == '0' ? '1.5px solid #0098EA' : '1px solid #CBD5E1' }}; border-radius: 8px; height: 42px; min-width: 95px; transition: all 0.15s ease;">
                                <span id="pmSpTidakRadio">
                                    @if($initSpRequired == '0')
                                        <span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span></span>
                                    @else
                                        <span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>
                                    @endif
                                </span>
                                <span id="pmSpTidakLabel" class="fw-semibold" style="font-size: 0.84rem; color: {{ $initSpRequired == '0' ? '#0284C7' : '#64748B' }};">Tidak</span>
                            </div>
                        </div>
                        <input type="hidden" name="solution_paper_required" id="pm_solution_paper_required" value="{{ $initSpRequired }}">
                    </div>

                    {{-- Upload Solution Paper (PDF)* --}}
                    <div class="col-lg-8 col-12" id="pm_solution_paper_upload_col" style="{{ $initSpRequired == '0' ? 'display: none;' : '' }}">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Upload Solution Paper (PDF) <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="d-flex align-items-center" style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; overflow: hidden;">
                            <button type="button" class="btn text-white d-flex align-items-center justify-content-center gap-2 px-3 h-100 flex-shrink-0"
                                    onclick="document.getElementById('pm_solution_paper_file').click()"
                                    style="background: #0F2A4A; border: none; border-radius: 0; font-size: 0.82rem; font-weight: 600;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span>Pilih File</span>
                            </button>
                            <span id="pm_solution_paper_label" class="px-3 text-truncate flex-grow-1" style="font-size: 0.82rem; color: {{ $existingSpFile ? '#1E293B' : '#64748B' }}; font-weight: {{ $existingSpFile ? '600' : 'normal' }};">
                                {{ $existingSpFileName }}
                            </span>
                            <input type="file" name="solution_paper_file" id="pm_solution_paper_file" accept=".pdf,application/pdf" class="d-none" onchange="handlePmFileInput(this, 'pm_solution_paper_label')">
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── ROW 9: Bottom Actions (Outside container, bottom right) ── --}}
            <div class="d-flex align-items-center justify-content-end gap-3 mt-4 mb-4">
                {{-- Kembali (Red) --}}
                <a href="{{ route('change-requests.index') }}"
                   class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center"
                   style="background: #DC2626; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.8rem; text-decoration: none; transition: all 0.15s ease;"
                   onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                    Kembali
                </a>

                {{-- Simpan Draft (Yellow/Amber) --}}
                <button type="submit" name="action" value="draft" formnovalidate
                        onclick="preparePmDraftSubmit()"
                        class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                        style="background: #F59E0B; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.8rem; transition: all 0.15s ease;"
                        onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                    <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                    <span>Simpan Draft</span>
                </button>

                {{-- Submit CR (Green) --}}
                <button type="submit" name="action" value="submit"
                        onclick="syncEditor()"
                        class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                        style="background: #10B981; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.8rem; transition: all 0.15s ease;"
                        onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                    <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                    <span>Ajukan CR</span>
                </button>
            </div>

        @else
            {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                 CLIENT VIEW: 3-CARD FIGMA LAYOUT
            ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}

            {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                 CARD 1: INFORMASI KLIEN (#DFEDFD Header Strip)
            ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important; position: relative; z-index: 50;">
            
            {{-- Header Strip --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DFEDFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 30px; height: 30px; background: #156BE9; flex-shrink: 0;">
                        <i class="bi bi-person-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">Informasi Klien</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Lengkapi detail informasi perubahan yang akan diajukan
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-4" style="background: #FFFFFF;">
                <div class="figma-grid-3col">
                    
                    {{-- Col 1: Nama Perusahaan (Dropdown) --}}
                    <div class="grid-col-1">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Perusahaan <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="companyDropdownWrapper" style="position: relative; z-index: 60;">
                            <button type="button" class="btn w-100 d-flex align-items-center justify-content-between text-start"
                                    id="companyDropdownBtn"
                                    onclick="toggleCustomDropdown('companyDropdownMenu', event)"
                                    style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B; transition: all 0.15s ease;">
                                <span id="selectedCompanyName">{{ $defaultComp ?: 'Pilih Nama Perusahaan' }}</span>
                                <svg id="companyDropdownChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ms-2 flex-shrink-0" style="transition: transform 0.2s ease;">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div class="position-absolute w-100 py-1" id="companyDropdownMenu"
                                 style="display: none; top: calc(100% + 4px); left: 0; z-index: 9999; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18), 0 4px 8px -2px rgba(15, 23, 42, 0.08); max-height: 240px; overflow-y: auto;">
                                @php
                                    $companies = [];
                                    if (isset($clients) && count($clients) > 0) {
                                        foreach ($clients as $cl) {
                                            $clName = is_object($cl) ? $cl->company : $cl;
                                            $clClean = trim(preg_replace('/^(pt\.?|cv\.?)\s+/i', '', $clName));
                                            $clInit = strtoupper(substr($clClean, 0, 2)) ?: 'CL';
                                            $companies[] = ['name' => $clName, 'initial' => $clInit];
                                        }
                                    } elseif (Auth::user()?->client?->company) {
                                        $clName = Auth::user()->client->company;
                                        $companies[] = ['name' => $clName, 'initial' => strtoupper(substr($clName, 0, 2))];
                                    } else {
                                        $companies[] = ['name' => 'PT Maju Bersama', 'initial' => 'MB'];
                                    }
                                @endphp
                                @forelse ($companies as $comp)
                                    <div class="dropdown-item py-2 px-3 cursor-pointer d-flex align-items-center justify-content-between"
                                         onclick="selectCompany('{{ $comp['name'] }}', '{{ $comp['initial'] }}')"
                                         style="font-size: 0.84rem; color: #1E293B; cursor: pointer; transition: background 0.15s ease;"
                                         onmouseover="this.style.background='#F0F9FF'; this.style.color='#0098EA';"
                                         onmouseout="this.style.background='transparent'; this.style.color='#1E293B';">
                                        <span style="font-weight: 600;">{{ $comp['name'] }}</span>
                                        <span style="background: #E0F2FE; color: #0284C7; font-size: 0.70rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;">{{ $comp['initial'] }}</span>
                                    </div>
                                @empty
                                    <div class="px-3 py-2 text-muted small">
                                        Perusahaan Pengguna:
                                        <div class="fw-bold text-dark mt-1" onclick="selectCompany('{{ Auth::user()?->name ?? 'Klien' }}', '{{ strtoupper(substr(Auth::user()?->name ?? 'KL', 0, 2)) }}')" style="cursor: pointer;">
                                            {{ Auth::user()?->name ?? 'Klien Utama' }} (Pilih)
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Col 2: Inisial Klien (Small box) + Nama PIC (Readonly box) --}}
                    <div class="grid-col-2">
                        <div class="d-flex align-items-center gap-2 w-100">
                            {{-- Inisial Klien --}}
                            <div style="width: 72px; flex-shrink: 0;">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                    Inisial Klien
                                </label>
                                <input type="text" name="inisial_klien" id="inisial_klien"
                                       value="{{ old('inisial_klien', $defaultInitial) }}" readonly
                                       style="background-color: #E9EDF1; color: #475569; font-weight: 600; border: none; border-radius: 8px; height: 42px; padding: 0 8px; font-size: 0.84rem; width: 100%; text-align: center;">
                            </div>

                            {{-- Nama PIC --}}
                            <div style="flex: 1 1 auto; min-width: 0;">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                    Nama PIC
                                </label>
                                <input type="text" name="nama_pic" id="nama_pic"
                                       value="{{ old('nama_pic', Auth::user()?->name ?? '') }}" readonly
                                       style="background-color: #E9EDF1; color: #475569; font-weight: 500; border: none; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            </div>
                        </div>
                    </div>

                    {{-- Col 3: CR Owner* --}}
                    <div class="grid-col-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            CR Owner <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="cr_owner" id="cr_owner"
                               value="{{ old('cr_owner', !empty($changeRequest) ? ($changeRequest->owner_cr ?: (Auth::user()?->name ?? '')) : (Auth::user()?->name ?? '')) }}" required
                               style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%;">
                    </div>

                </div>
            </div>
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             CARD 2: INFORMASI CHANGE REQUEST (#DCFCE7 Header Strip)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important; position: relative; z-index: 30;">
            
            {{-- Header Strip --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #E1F6F1; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 30px; height: 30px; background: #03A086; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">Informasi Change Request</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Lengkapi detail informasi perubahan yang akan diajukan
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-4" style="background: #FFFFFF;">
                
                {{-- Row 1: Nama Project (Dropdown), Request Date, Tanggal Go Live --}}
                <div class="figma-grid-3col mb-3">
                    
                    {{-- Col 1: Nama Project (Dropdown) --}}
                    <div class="grid-col-1">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Project <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="projectDropdownWrapper" style="position: relative; z-index: 40;">
                            <button type="button" class="btn w-100 d-flex align-items-center justify-content-between text-start"
                                    id="projectDropdownBtn"
                                    onclick="toggleCustomDropdown('projectDropdownMenu', event)"
                                    style="background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B; transition: all 0.15s ease;">
                                <span id="selectedProjectName" class="text-truncate">{{ $defaultProj ?: 'Pilih atau Masukkan Project' }}</span>
                                <svg id="projectDropdownChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ms-2 flex-shrink-0" style="transition: transform 0.2s ease;">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div class="position-absolute w-100 py-1" id="projectDropdownMenu"
                                 style="display: none; top: calc(100% + 4px); left: 0; z-index: 9999; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18), 0 4px 8px -2px rgba(15, 23, 42, 0.08); max-height: 240px; overflow-y: auto;">
                                @php
                                    $projects = [
                                        'Core System Enhancement',
                                        'Sistem E-commerce',
                                        'E-Procurement',
                                        'Sistem Enterprise',
                                        'E-Cooper',
                                        'Data Integration',
                                        'Payment Gateway Integration',
                                        'Enterprise Portal',
                                        'Mobile Application',
                                    ];
                                @endphp
                                @foreach ($projects as $proj)
                                    <div class="dropdown-item py-2 px-3 cursor-pointer text-truncate"
                                         onclick="selectProject('{{ $proj }}')"
                                         style="font-size: 0.84rem; color: #1E293B; cursor: pointer; transition: background 0.15s ease;"
                                         onmouseover="this.style.background='#F0F9FF'; this.style.color='#0098EA';"
                                         onmouseout="this.style.background='transparent'; this.style.color='#1E293B';">
                                        {{ $proj }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Col 2: Tanggal Pengajuan --}}
                    <div class="grid-col-2">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Tanggal Pengajuan
                        </label>
                        <div class="position-relative cursor-pointer" id="client_date_input_1_wrap" onclick="openDatePicker('date_input_1', event)">
                            <input type="text" name="date" id="date_input_1"
                                    value="{{ old('date', !empty($changeRequest?->tanggal_pengajuan) ? $changeRequest->tanggal_pengajuan->format('d M Y') : now()->format('d M Y')) }}" readonly
                                    onclick="openDatePicker('date_input_1', event)"
                                    style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 38px 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%; cursor: pointer;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>

                    {{-- Col 3: Tanggal Go Live* --}}
                    <div class="grid-col-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Tanggal Go Live <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative cursor-pointer" id="client_date_input_2_wrap" onclick="openDatePicker('date_input_2', event)">
                            <input type="text" name="request_date" id="date_input_2"
                                    value="{{ old('request_date', old('target_golive', !empty($changeRequest?->target_selesai) ? $changeRequest->target_selesai->format('d M Y') : now()->format('d M Y'))) }}" readonly
                                    onclick="openDatePicker('date_input_2', event)"
                                    style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 38px 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%; cursor: pointer;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>

                </div>

                {{-- Row 2: Nama CR* --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size: 0.78rem; color: #0F172A;">
                            Nama CR <span style="color: #EF4444;">*</span>
                        </label>
                        <span id="cr_name_counter" class="text-muted" style="font-size: 0.70rem;">0/255</span>
                    </div>
                    <input type="text" name="cr_name" id="cr_name"
                           placeholder="Contoh: Integrasi API Pembayaran OVO"
                           maxlength="255"
                           value="{{ old('cr_name', !empty($changeRequest) ? $changeRequest->judul : '') }}" required
                           style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%;">
                    <div class="form-text text-muted" style="font-size: 0.70rem;">Maksimal 255 kata/karakter.</div>
                </div>

                {{-- Row 3: Detail CR* (Interactive TinyMCE Style Editor) --}}
                <div>
                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                        Detail CR <span style="color: #EF4444;">*</span>
                    </label>
                    
                    <div style="border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden; background: #FFFFFF;" id="editorWrapper">
                        
                        {{-- ── Menubar with Working Dropdowns ── --}}
                        <div class="d-flex align-items-center gap-1 px-2 py-1 bg-white border-bottom position-relative"
                             style="font-size: 0.78rem; user-select: none; color: #475569; border-color: #F1F5F9 !important;">
                            
                            {{-- File Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">File</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="editorAction('new')"><i class="bi bi-file-earmark me-2"></i> Dokumen Baru</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="window.print()"><i class="bi bi-printer me-2"></i> Cetak (Print)</a></li>
                                </ul>
                            </div>

                            {{-- Edit Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Edit</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('undo')"><span><i class="bi bi-arrow-counterclockwise me-2"></i> Undo</span> <small class="text-muted">Ctrl+Z</small></a></li>
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('redo')"><span><i class="bi bi-arrow-clockwise me-2"></i> Redo</span> <small class="text-muted">Ctrl+Y</small></a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('selectAll')"><span><i class="bi bi-check-all me-2"></i> Pilih Semua</span> <small class="text-muted">Ctrl+A</small></a></li>
                                </ul>
                            </div>

                            {{-- View Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">View</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openPreviewModal()"><i class="bi bi-eye me-2"></i> Preview CR</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="toggleEditorFullscreen()"><i class="bi bi-arrows-fullscreen me-2"></i> Layar Penuh</a></li>
                                </ul>
                            </div>

                            {{-- Insert Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Insert</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 160px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertLinkPrompt()"><i class="bi bi-link-45deg me-2"></i> Tautan Link</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertImagePrompt()"><i class="bi bi-image me-2"></i> Gambar / Mockup</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('insertHorizontalRule')"><i class="bi bi-dash-lg me-2"></i> Garis Horizontal</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertCurrentDate()"><i class="bi bi-calendar-event me-2"></i> Tanggal Hari Ini</a></li>
                                </ul>
                            </div>

                            {{-- Format Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Format</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('bold')"><strong>Tebal (Bold)</strong></a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('italic')"><em>Miring (Italic)</em></a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('underline')"><u>Garis Bawah</u></a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('strikeThrough')"><del>Coret</del></a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><a class="dropdown-item py-1 px-3 text-danger" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('removeFormat')"><i class="bi bi-eraser me-2"></i> Hapus Format</a></li>
                                </ul>
                            </div>

                            {{-- Tools Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Tools</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="showWordCount()"><i class="bi bi-calculator me-2"></i> Hitung Kata</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openSourceModal()"><i class="bi bi-code-slash me-2"></i> Kode Sumber HTML</a></li>
                                </ul>
                            </div>

                            {{-- Table Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Table</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertTable(2, 2)"><i class="bi bi-table me-2"></i> Tabel 2 x 2</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertTable(3, 3)"><i class="bi bi-table me-2"></i> Tabel 3 x 3</a></li>
                                </ul>
                            </div>

                            {{-- Help Menu --}}
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Help</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 160px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openHelpModal()"><i class="bi bi-question-circle me-2"></i> Bantuan & Shortcut</a></li>
                                </ul>
                            </div>

                        </div>

                        {{-- ── Toolbar Icons (with onmousedown preventDefault to maintain selection) ── --}}
                        <div class="d-flex align-items-center gap-1 px-3 py-1 border-bottom bg-white flex-wrap" style="font-size: 0.82rem; color: #334155; border-color: #F1F5F9 !important;">
                            
                            {{-- Undo / Redo --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('undo')" title="Undo">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('redo')" title="Redo">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Paragraph Dropdown (Functional with tags) --}}
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-dark d-flex align-items-center gap-1"
                                        id="paragraphDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="font-size: 0.78rem; font-weight: 500; height: 26px;">
                                    <span id="currentParagraphLabel">Paragraph</span>
                                    <i class="bi bi-chevron-down" style="font-size: 0.65rem;"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('p', 'Paragraph')">Paragraph</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fw-bold" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('h1', 'Heading 1')">Heading 1</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fw-bold" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('h2', 'Heading 2')">Heading 2</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fw-bold" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('h3', 'Heading 3')">Heading 3</a></li>
                                    <li><a class="dropdown-item py-1 px-3 fst-italic" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('blockquote', 'Quote')">Quote</a></li>
                                    <li><a class="dropdown-item py-1 px-3 font-monospace" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="applyFormatBlock('pre', 'Code')">Code Block</a></li>
                                </ul>
                            </div>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Bold, Italic, Underline, Strikethrough --}}
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 fw-bold text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('bold')" title="Bold (Ctrl+B)">B</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 fst-italic text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('italic')" title="Italic (Ctrl+I)">I</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-decoration-underline text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('underline')" title="Underline (Ctrl+U)">U</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-decoration-line-through text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('strikeThrough')" title="Strikethrough">S</button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Alignments --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyFull')" title="Justify"><i class="bi bi-justify"></i></button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Lists & Indents --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('outdent')" title="Kurangi Inden"><i class="bi bi-text-indent-right"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('indent')" title="Tambah Inden"><i class="bi bi-text-indent-left"></i></button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            {{-- Link --}}
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="insertLinkPrompt()" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                        </div>

                        {{-- Contenteditable Area --}}
                        <div id="editorBody" contenteditable="true"
                             style="min-height: 220px; padding: 14px 16px; outline: none; font-size: 0.86rem; line-height: 1.6; color: #1E293B;"
                             oninput="syncEditor()"
                             data-placeholder="Tuliskan rincian kebutuhan perubahan sistem, spesifikasi fitur baru, atau alur kerja yang diinginkan...">
                            {!! old('detail_cr', old('deskripsi_cr', old('deskripsi', !empty($changeRequest) ? $changeRequest->deskripsi : ''))) !!}
                        </div>
                        <textarea name="detail_cr" id="deskripsi_cr_hidden" class="d-none">{{ old('detail_cr', old('deskripsi_cr', old('deskripsi', !empty($changeRequest) ? $changeRequest->deskripsi : ''))) }}</textarea>

                        {{-- TinyMCE Statusbar at bottom --}}
                        <div class="px-3 py-1 border-top d-flex justify-content-between align-items-center text-muted"
                             style="background: #F8FAFC; font-size: 0.72rem; user-select: none; border-color: #F1F5F9 !important;">
                            <span style="color: #64748B;">p</span>
                            <span id="detail_cr_counter" style="color: #64748B; font-weight: 500;">0/5000 karakter</span>
                            <div class="d-flex align-items-center gap-1" style="color: #64748B;">
                                <span>Build with</span>
                                <span class="fw-bold" style="color: #1E293B;">tinyMCE</span>
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="margin-left: 4px; opacity: 0.5;">
                                    <path d="M9 1L1 9M9 5L5 9M9 9H9.01" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>

                    </div>
                    <div class="form-text text-muted mt-1" style="font-size: 0.70rem;">Maksimal 5000 kata/karakter.</div>
                </div>

            </div>
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
             CARD 3: DOKUMEN DAN LAINNYA (#ECEAFD Header Strip)
        ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important; position: relative; z-index: 10;">
            
            {{-- Header Strip --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #ECEAFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 30px; height: 30px; background: #907CF8; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">Dokumen Dan Lainnya</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Lengkapi detail informasi perubahan yang akan diajukan
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-4" style="background: #FFFFFF;">
                
                {{-- Row 1: Dokumen CR Upload (50%) & Prioritas CR (50%) --}}
                <div class="row g-3 mb-3 align-items-stretch">
                    
                    {{-- Left: Dokumen CR Upload Dropzone --}}
                    <div class="col-lg-6 col-12 d-flex flex-column">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Dokumen CR <span class="text-muted fw-normal" style="font-size: 0.72rem;">(Hanya format .pdf)</span>
                        </label>
                        
                        <div id="dropzoneBox"
                             class="p-2 text-center d-flex flex-column align-items-center justify-content-center cursor-pointer flex-grow-1"
                             onclick="triggerFileInput()"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleFileDrop(event)"
                             style="border: 1.5px dashed #CBD5E1; border-radius: 10px; background: #FFFFFF; min-height: 104px; transition: all 0.2s ease;">
                            
                            {{-- Upload Tray Icon matching Figma --}}
                            <div id="dropzonePlaceholder">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mb-1">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <div id="dropzoneText" style="font-size: 0.82rem; color: #64748B; font-weight: 500;">
                                    Klik untuk upload PDF
                                </div>
                                <div style="font-size: 0.70rem; color: #94A3B8; margin-top: 2px;">
                                    Format: .pdf (Maks. 20MB)
                                </div>
                            </div>

                            {{-- Active File Display State (Hidden initially) --}}
                            <div id="dropzoneFilePreview" style="display: none;" class="w-100 px-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2 text-start text-truncate">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                        <div class="text-truncate">
                                            <div class="fw-bold text-dark text-truncate" id="fileNameDisplay" style="font-size: 0.82rem;">nama_dokumen.pdf</div>
                                            <div class="text-muted" id="fileSizeDisplay" style="font-size: 0.72rem;">0 KB</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2 border-0"
                                            onclick="removeSelectedFile(event)" title="Hapus file" style="font-size: 0.8rem;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="file" name="dokumen_fsd" id="dokumen_fsd_file" class="d-none"
                                   accept=".pdf,application/pdf"
                                   onchange="handleFileChange(this)">
                        </div>
                        @error('dokumen_fsd')
                            <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Right: Prioritas CR (Normal & Urgent Cards) --}}
                    <div class="col-lg-6 col-12 d-flex flex-column">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Prioritas CR <span style="color: #EF4444;">*</span>
                        </label>

                        <div class="d-flex flex-column gap-2 flex-grow-1 justify-content-between">
                            
                            {{-- Normal Option Card (Active by Default) --}}
                            <div id="priorityCardNormal"
                                 class="p-2 px-3 d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectPriority('normal')"
                                 style="border: 1.5px solid #00A3FF; background: #FFFFFF; height: 48px; border-radius: 8px; transition: all 0.15s ease;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width: 11px; height: 11px; border-radius: 50%; background: radial-gradient(circle at 35% 35%, #4ADE80, #16A34A); display: inline-block; box-shadow: 0 1px 3px rgba(22,163,74,0.4);"></span>
                                    <span class="fw-semibold" id="labelNormal" style="font-size: 0.85rem; color: #0284C7;">Normal</span>
                                </div>
                                <span id="radioIndicatorNormal">
                                    <span style="width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #00A3FF; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 9px; height: 9px; border-radius: 50%; background: #00A3FF; display: inline-block;"></span></span>
                                </span>
                            </div>

                            {{-- Urgent Option Card --}}
                            <div id="priorityCardUrgent"
                                 class="p-2 px-3 d-flex align-items-center justify-content-between cursor-pointer"
                                 onclick="selectPriority('urgent')"
                                 style="border: 1px solid #CBD5E1; background: #FFFFFF; height: 48px; border-radius: 8px; transition: all 0.15s ease;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width: 11px; height: 11px; border-radius: 50%; background: radial-gradient(circle at 35% 35%, #F87171, #DC2626); display: inline-block; box-shadow: 0 1px 3px rgba(220,38,38,0.4);"></span>
                                    <span class="fw-semibold" id="labelUrgent" style="font-size: 0.85rem; color: #64748B;">Urgent</span>
                                </div>
                                <span id="radioIndicatorUrgent">
                                    <span style="width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>
                                </span>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Row 2: CR Notes --}}
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size: 0.78rem; color: #0F172A;">
                            CR Notes
                        </label>
                        <span id="cr_notes_counter" class="text-muted" style="font-size: 0.70rem;">0/5000</span>
                    </div>
                    <textarea name="cr_notes" id="cr_notes" rows="3" class="form-control"
                              maxlength="5000"
                              style="background-color: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.84rem; padding: 12px 14px; width: 100%; height: 90px; outline: none;"
                              placeholder="Catatan tambahan, prioritas, atau informasi lain yang perlu diketahui PM...">{{ old('cr_notes', !empty($changeRequest) ? $changeRequest->catatan_pengajuan : '') }}</textarea>
                    <div class="form-text text-muted" style="font-size: 0.70rem;">Maksimal 5000 kata/karakter.</div>
                </div>

            </div>
        </div>

        {{-- ── 4. Bottom Action Buttons (Figma Exact: Outside Cards at Bottom Right) ── --}}
        <div class="d-flex align-items-center justify-content-end gap-3 mt-4 mb-4">
            
            {{-- Button 1: Kembali (Red Pill) --}}
            <a href="{{ route('change-requests.index') }}"
               class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center"
               style="background: #DC2626; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.7rem; text-decoration: none; transition: transform 0.15s ease, opacity 0.15s ease;"
               onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                Kembali
            </a>

            {{-- Button 2: Simpan Draft (Yellow/Amber Pill with Checkmark) --}}
            <button type="submit" name="action" value="draft" formnovalidate
                    onclick="prepareDraftSubmit()"
                    class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                    style="background: #FFC107; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.7rem; transition: transform 0.15s ease, opacity 0.15s ease;"
                    onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                <i class="bi bi-check-lg" style="font-size: 1rem; stroke-width: 1px;"></i>
                <span>Simpan Draft</span>
            </button>

            {{-- Button 3: Submit CR (Emerald Green Pill with Checkmark) --}}
            <button type="submit" name="action" value="submit"
                    onclick="syncEditor()"
                    class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                    style="background: #50CD89; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.7rem; transition: transform 0.15s ease, opacity 0.15s ease;"
                    onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                <i class="bi bi-check-lg" style="font-size: 1rem; stroke-width: 1px;"></i>
                <span>Ajukan CR</span>
            </button>

        </div>

        @endif

    </form>

</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     DATE PICKER POPUP (Instant Click-to-Select & Close)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<div id="customDatePickerModal" class="shadow-lg position-fixed"
     style="display: none; z-index: 2000; background: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0; width: 290px; padding: 14px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);">
    
    {{-- Header: [<<] [<] Month Year [>] [>>] --}}
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="btn btn-sm p-0 text-muted border d-flex align-items-center justify-content-center"
                    onclick="prevYear(event)" title="Tahun Sebelumnya"
                    style="width: 24px; height: 24px; border-radius: 6px; border-color: #E2E8F0 !important; background: #FFFFFF; font-size: 0.75rem; font-weight: bold;">
                &laquo;
            </button>
            <button type="button" class="btn btn-sm p-0 text-muted border d-flex align-items-center justify-content-center"
                    onclick="prevMonth(event)" title="Bulan Sebelumnya"
                    style="width: 24px; height: 24px; border-radius: 6px; border-color: #E2E8F0 !important; background: #FFFFFF; font-size: 0.75rem; font-weight: bold;">
                &lsaquo;
            </button>
        </div>
        <div class="fw-bold text-dark" id="calendarMonthYear" style="font-size: 0.85rem; color: #18181B;">{{ now()->format('F Y') }}</div>
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="btn btn-sm p-0 text-muted border d-flex align-items-center justify-content-center"
                    onclick="nextMonth(event)" title="Bulan Berikutnya"
                    style="width: 24px; height: 24px; border-radius: 6px; border-color: #E2E8F0 !important; background: #FFFFFF; font-size: 0.75rem; font-weight: bold;">
                &rsaquo;
            </button>
            <button type="button" class="btn btn-sm p-0 text-muted border d-flex align-items-center justify-content-center"
                    onclick="nextYear(event)" title="Tahun Berikutnya"
                    style="width: 24px; height: 24px; border-radius: 6px; border-color: #E2E8F0 !important; background: #FFFFFF; font-size: 0.75rem; font-weight: bold;">
                &raquo;
            </button>
        </div>
    </div>

    {{-- Day names header (Mo, Tu, We, Th, Fr, Sa, Su) --}}
    <div class="d-grid mb-2 text-center text-muted" style="grid-template-columns: repeat(7, 1fr); font-size: 0.72rem; font-weight: 600; color: #94A3B8;">
        <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
    </div>

    {{-- Days grid --}}
    <div id="calendarDaysGrid" class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 0.78rem;">
        {{-- Filled by JS --}}
    </div>

    {{-- Footer Actions: Cancel and Apply --}}
    <div class="d-flex align-items-center justify-content-end gap-2 mt-3 pt-2 border-top" style="border-color: #F1F5F9 !important;">
        <button type="button" class="btn btn-sm px-3" onclick="closeDatePicker()"
                style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 6px; font-size: 0.76rem; color: #475569; font-weight: 500; height: 28px;">
            Cancel
        </button>
        <button type="button" class="btn btn-sm px-3 text-white" onclick="applyDatePicker()"
                style="background: #18181B; border: none; border-radius: 6px; font-size: 0.76rem; font-weight: 600; height: 28px;">
            Apply
        </button>
    </div>

</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     MODALS FOR TINYMCE ACTIONS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}

{{-- 1. Source Code Modal --}}
<div class="modal fade" id="sourceCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-code-slash me-2"></i> Kode Sumber HTML</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea id="rawHtmlTextarea" class="form-control font-monospace" rows="10" style="font-size: 0.82rem;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="saveSourceHtml()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-eye me-2"></i> Pratinjau Detail CR</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="previewModalBody" style="font-size: 0.88rem; line-height: 1.6;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- 3. Help / Shortcuts Modal --}}
<div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-keyboard me-2"></i> Pintasan Keyboard</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <table class="table table-sm table-borderless mb-0" style="font-size: 0.82rem;">
                    <tr><td class="fw-bold">Ctrl + B</td><td>Teks Tebal (Bold)</td></tr>
                    <tr><td class="fw-bold">Ctrl + I</td><td>Teks Miring (Italic)</td></tr>
                    <tr><td class="fw-bold">Ctrl + U</td><td>Garis Bawah (Underline)</td></tr>
                    <tr><td class="fw-bold">Ctrl + Z</td><td>Undo (Batalkan aksi)</td></tr>
                    <tr><td class="fw-bold">Ctrl + Y</td><td>Redo (Ulangi aksi)</td></tr>
                    <tr><td class="fw-bold">Ctrl + A</td><td>Pilih Semua Teks</td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<style>
/* 3-Column Unified CSS Grid matching Figma layout */
.figma-grid-3col {
    display: grid;
    grid-template-columns: minmax(0, 3.8fr) minmax(0, 3fr) minmax(0, 3.2fr);
    gap: 16px;
    align-items: start;
}

@media (max-width: 991.98px) {
    .figma-grid-3col {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}

.menubar-btn {
    transition: background 0.15s ease;
}
.menubar-btn:hover {
    background: #F1F5F9;
    color: #0F172A;
}

#editorBody:empty::before {
    content: attr(data-placeholder);
    color: #94A3B8;
    pointer-events: none;
    display: block;
}
</style>

{{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     SCRIPTS & MICRO-INTERACTION LOGIC
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
<script>
    // 1. Dropdown Toggle Helper
    function toggleCustomDropdown(menuId, event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById(menuId);
        const isOpen = menu && menu.style.display === 'block';
        
        // Close both custom dropdowns first & reset chevrons
        const m1 = document.getElementById('companyDropdownMenu');
        const m2 = document.getElementById('projectDropdownMenu');
        const c1 = document.getElementById('companyDropdownChevron');
        const c2 = document.getElementById('projectDropdownChevron');

        if (m1) m1.style.display = 'none';
        if (m2) m2.style.display = 'none';
        if (c1) c1.style.transform = 'rotate(0deg)';
        if (c2) c2.style.transform = 'rotate(0deg)';
        
        if (!isOpen && menu) {
            menu.style.display = 'block';
            if (menuId === 'companyDropdownMenu' && c1) c1.style.transform = 'rotate(180deg)';
            if (menuId === 'projectDropdownMenu' && c2) c2.style.transform = 'rotate(180deg)';
        }
    }

    function selectCompany(companyName, initial) {
        document.getElementById('selectedCompanyName').innerText = companyName;
        document.getElementById('nama_perusahaan_input').value = companyName;
        document.getElementById('inisial_klien').value = initial;
        const m1 = document.getElementById('companyDropdownMenu');
        const c1 = document.getElementById('companyDropdownChevron');
        if (m1) m1.style.display = 'none';
        if (c1) c1.style.transform = 'rotate(0deg)';
    }

    function selectProject(projectName) {
        document.getElementById('selectedProjectName').innerText = projectName;
        document.getElementById('nama_project_input').value = projectName;
        const m2 = document.getElementById('projectDropdownMenu');
        const c2 = document.getElementById('projectDropdownChevron');
        if (m2) m2.style.display = 'none';
        if (c2) c2.style.transform = 'rotate(0deg)';
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#companyDropdownWrapper')) {
            const m1 = document.getElementById('companyDropdownMenu');
            const c1 = document.getElementById('companyDropdownChevron');
            if (m1) m1.style.display = 'none';
            if (c1) c1.style.transform = 'rotate(0deg)';
        }
        if (!e.target.closest('#projectDropdownWrapper')) {
            const m2 = document.getElementById('projectDropdownMenu');
            const c2 = document.getElementById('projectDropdownChevron');
            if (m2) m2.style.display = 'none';
            if (c2) c2.style.transform = 'rotate(0deg)';
        }
        if (!e.target.closest('#pmCompanyDropdownWrapper')) {
            const pm1 = document.getElementById('pmCompanyDropdownMenu');
            const pc1 = document.getElementById('pmCompanyChevron');
            if (pm1) pm1.style.display = 'none';
            if (pc1) pc1.style.transform = 'rotate(0deg)';
        }
        if (!e.target.closest('#pmProjectDropdownWrapper')) {
            const pm2 = document.getElementById('pmProjectDropdownMenu');
            const pc2 = document.getElementById('pmProjectChevron');
            if (pm2) pm2.style.display = 'none';
            if (pc2) pc2.style.transform = 'rotate(0deg)';
        }
    });

    // PM Dropdown Handlers
    function togglePmDropdown(menuId, event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById(menuId);
        const isOpen = menu && menu.style.display === 'block';

        const pm1 = document.getElementById('pmCompanyDropdownMenu');
        const pm2 = document.getElementById('pmProjectDropdownMenu');
        const pc1 = document.getElementById('pmCompanyChevron');
        const pc2 = document.getElementById('pmProjectChevron');

        if (pm1) pm1.style.display = 'none';
        if (pm2) pm2.style.display = 'none';
        if (pc1) pc1.style.transform = 'rotate(0deg)';
        if (pc2) pc2.style.transform = 'rotate(0deg)';

        if (!isOpen && menu) {
            menu.style.display = 'block';
            if (menuId === 'pmCompanyDropdownMenu' && pc1) pc1.style.transform = 'rotate(180deg)';
            if (menuId === 'pmProjectDropdownMenu' && pc2) pc2.style.transform = 'rotate(180deg)';
        }
    }

    function selectPmCompany(companyName, initial) {
        const nameEl = document.getElementById('pmSelectedCompanyName');
        if (nameEl) nameEl.innerText = companyName;
        const inputComp = document.getElementById('nama_perusahaan_input');
        if (inputComp) inputComp.value = companyName;
        const initBox = document.getElementById('pm_inisial_klien_box');
        if (initBox) initBox.innerText = initial;
        const initInput = document.getElementById('pm_inisial_klien_input');
        if (initInput) initInput.value = initial;

        const pm1 = document.getElementById('pmCompanyDropdownMenu');
        const pc1 = document.getElementById('pmCompanyChevron');
        if (pm1) pm1.style.display = 'none';
        if (pc1) pc1.style.transform = 'rotate(0deg)';
    }

    function selectPmProject(projectName) {
        const projEl = document.getElementById('pmSelectedProjectName');
        if (projEl) projEl.innerText = projectName;
        const inputProj = document.getElementById('nama_project_input');
        if (inputProj) inputProj.value = projectName;

        const pm2 = document.getElementById('pmProjectDropdownMenu');
        const pc2 = document.getElementById('pmProjectChevron');
        if (pm2) pm2.style.display = 'none';
        if (pc2) pc2.style.transform = 'rotate(0deg)';
    }

    // 2. Priority Selection Handler
    function selectPriority(val) {
        const prioInput = document.getElementById('prioritas_input');
        if (prioInput) prioInput.value = val;
        
        const cardNormal = document.getElementById('priorityCardNormal');
        const cardUrgent = document.getElementById('priorityCardUrgent');
        const labelNormal = document.getElementById('labelNormal');
        const labelUrgent = document.getElementById('labelUrgent');
        const radioNormal = document.getElementById('radioIndicatorNormal');
        const radioUrgent = document.getElementById('radioIndicatorUrgent');

        if (cardNormal && cardUrgent) {
            if (val === 'normal') {
                cardNormal.style.borderColor = '#00A3FF';
                cardNormal.style.borderWidth = '1.5px';
                labelNormal.style.color = '#0284C7';
                radioNormal.innerHTML = '<span style="width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #00A3FF; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 9px; height: 9px; border-radius: 50%; background: #00A3FF; display: inline-block;"></span></span>';

                cardUrgent.style.borderColor = '#CBD5E1';
                cardUrgent.style.borderWidth = '1px';
                labelUrgent.style.color = '#64748B';
                radioUrgent.innerHTML = '<span style="width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>';
            } else {
                cardUrgent.style.borderColor = '#EF4444';
                cardUrgent.style.borderWidth = '1.5px';
                labelUrgent.style.color = '#DC2626';
                radioUrgent.innerHTML = '<span style="width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #EF4444; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 9px; height: 9px; border-radius: 50%; background: #EF4444; display: inline-block;"></span></span>';

                cardNormal.style.borderColor = '#CBD5E1';
                cardNormal.style.borderWidth = '1px';
                labelNormal.style.color = '#64748B';
                radioNormal.innerHTML = '<span style="width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>';
            }
        }
    }

    function selectPmPriority(val) {
        selectPriority(val);
        const cardNormal = document.getElementById('pmPriorityNormalCard');
        const cardUrgent = document.getElementById('pmPriorityUrgentCard');
        const labelNormal = document.getElementById('pmLabelNormal');
        const labelUrgent = document.getElementById('pmLabelUrgent');
        const radioNormal = document.getElementById('pmRadioNormal');
        const radioUrgent = document.getElementById('pmRadioUrgent');

        if (cardNormal && cardUrgent) {
            if (val === 'normal') {
                cardNormal.style.borderColor = '#0098EA';
                cardNormal.style.borderWidth = '1.5px';
                if (labelNormal) labelNormal.style.color = '#0284C7';
                if (radioNormal) radioNormal.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span></span>';

                cardUrgent.style.borderColor = '#CBD5E1';
                cardUrgent.style.borderWidth = '1px';
                if (labelUrgent) labelUrgent.style.color = '#64748B';
                if (radioUrgent) radioUrgent.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>';
            } else {
                cardUrgent.style.borderColor = '#EF4444';
                cardUrgent.style.borderWidth = '1.5px';
                if (labelUrgent) labelUrgent.style.color = '#DC2626';
                if (radioUrgent) radioUrgent.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #EF4444; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444; display: inline-block;"></span></span>';

                cardNormal.style.borderColor = '#CBD5E1';
                cardNormal.style.borderWidth = '1px';
                if (labelNormal) labelNormal.style.color = '#64748B';
                if (radioNormal) radioNormal.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>';
            }
        }
    }

    // Solution Paper Option
    function selectPmSolutionPaper(val) {
        const inputSp = document.getElementById('pm_solution_paper_required');
        if (inputSp) inputSp.value = val;

        const cardYa = document.getElementById('pmSpYaCard');
        const cardTidak = document.getElementById('pmSpTidakCard');
        const radioYa = document.getElementById('pmSpYaRadio');
        const radioTidak = document.getElementById('pmSpTidakRadio');
        const labelYa = document.getElementById('pmSpYaLabel');
        const labelTidak = document.getElementById('pmSpTidakLabel');
        const uploadCol = document.getElementById('pm_solution_paper_upload_col');
        const fileInput = document.getElementById('pm_solution_paper_file');
        const fileLabel = document.getElementById('pm_solution_paper_label');

        if (cardYa && cardTidak) {
            if (val === 1) {
                cardYa.style.borderColor = '#0098EA';
                cardYa.style.borderWidth = '1.5px';
                if (labelYa) labelYa.style.color = '#0284C7';
                if (radioYa) radioYa.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span></span>';

                cardTidak.style.borderColor = '#CBD5E1';
                cardTidak.style.borderWidth = '1px';
                if (labelTidak) labelTidak.style.color = '#64748B';
                if (radioTidak) radioTidak.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>';

                if (uploadCol) {
                    uploadCol.style.display = '';
                }
            } else {
                cardTidak.style.borderColor = '#0098EA';
                cardTidak.style.borderWidth = '1.5px';
                if (labelTidak) labelTidak.style.color = '#0284C7';
                if (radioTidak) radioTidak.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #0098EA; display: inline-flex; align-items: center; justify-content: center;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #0098EA;"></span></span>';

                cardYa.style.borderColor = '#CBD5E1';
                cardYa.style.borderWidth = '1px';
                if (labelYa) labelYa.style.color = '#64748B';
                if (radioYa) radioYa.innerHTML = '<span style="width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #CBD5E1; display: inline-block;"></span>';

                if (uploadCol) {
                    uploadCol.style.display = 'none';
                }
                if (fileInput) {
                    fileInput.value = '';
                }
                if (fileLabel) {
                    fileLabel.innerText = 'Tidak ada file yang dipilih';
                    fileLabel.style.color = '#64748B';
                    fileLabel.style.fontWeight = 'normal';
                }
            }
        }
    }

    // PM File Input Handler
    function handlePmFileInput(input, labelId) {
        const label = document.getElementById(labelId);
        if (!label) return;
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const isPdf = file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf';
            if (!isPdf) {
                alert('Format file tidak didukung! Hanya file berformat PDF (.pdf) yang diperbolehkan.');
                input.value = '';
                label.innerText = 'Tidak ada file yang dipilih';
                label.style.color = '#64748B';
                label.style.fontWeight = 'normal';
                return;
            }
            label.innerText = file.name;
            label.style.color = '#1E293B';
            label.style.fontWeight = '600';
        } else {
            label.innerText = 'Tidak ada file yang dipilih';
            label.style.color = '#64748B';
            label.style.fontWeight = 'normal';
        }
    }

    // Mandays Calculation: baru terisi setelah analisis, development, dan testing diinput
    function calcPmTotalMandays() {
        const aInput = document.getElementById('pm_mindesk_analisis');
        const dInput = document.getElementById('pm_mindesk_development');
        const tInput = document.getElementById('pm_mindesk_testing');
        const totalBox = document.getElementById('pm_total_mandays');
        if (!aInput || !dInput || !tInput || !totalBox) return;

        const aVal = aInput.value.trim();
        const dVal = dInput.value.trim();
        const tVal = tInput.value.trim();

        // Hanya isi total mandays jika ketiga input telah diisi
        if (aVal === '' || dVal === '' || tVal === '') {
            totalBox.value = '';
            return;
        }

        const a = parseFloat(aVal) || 0;
        const d = parseFloat(dVal) || 0;
        const t = parseFloat(tVal) || 0;
        const total = a + d + t;
        totalBox.value = total % 1 === 0 ? total.toFixed(0) : total.toFixed(1);
    }

    function preparePmDraftSubmit() {
        syncEditor();
        const crNameInput = document.getElementById('pm_cr_name');
        if (crNameInput && !crNameInput.value.trim()) {
            crNameInput.value = 'Draft Pengajuan CR';
        }
    }

    // 3. TinyMCE Editor Logic
    function execCmd(command, value = null) {
        const editor = document.getElementById('pmEditorBody') || document.getElementById('editorBody');
        if (editor) {
            editor.focus();
        }
        document.execCommand(command, false, value);
        syncEditor();
    }

    function applyFormatBlock(tag, label) {
        document.execCommand('formatBlock', false, '<' + tag + '>');
        const lbl = document.getElementById('pmCurrentParagraphLabel') || document.getElementById('currentParagraphLabel');
        if (lbl) lbl.innerText = label;
        syncEditor();
    }

    let hasAlertedPmDetail = false;
    let hasAlertedClientDetail = false;

    function syncEditor() {
        const pmBody = document.getElementById('pmEditorBody');
        const pmHidden = document.getElementById('pm_deskripsi_cr_hidden');
        if (pmBody && pmHidden) {
            pmHidden.value = pmBody.innerHTML;
            const text = pmBody.innerText || '';
            const chars = text.length;
            const words = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
            const counter = document.getElementById('pm_detail_cr_counter');
            if (counter) {
                counter.innerText = `${chars}/5000 karakter`;
                counter.style.color = (chars > 5000 || words > 5000) ? '#EF4444' : '#64748B';
            }
            if ((chars > 5000 || words > 5000) && !hasAlertedPmDetail) {
                alert('Detail CR maksimal 5000 kata/karakter!');
                hasAlertedPmDetail = true;
            } else if (chars <= 5000 && words <= 5000) {
                hasAlertedPmDetail = false;
            }
        }

        const clientBody = document.getElementById('editorBody');
        const clientHidden = document.getElementById('deskripsi_cr_hidden');
        if (clientBody && clientHidden) {
            clientHidden.value = clientBody.innerHTML;
            const text = clientBody.innerText || '';
            const chars = text.length;
            const words = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
            const counter = document.getElementById('detail_cr_counter');
            if (counter) {
                counter.innerText = `${chars}/5000 karakter`;
                counter.style.color = (chars > 5000 || words > 5000) ? '#EF4444' : '#64748B';
            }
            if ((chars > 5000 || words > 5000) && !hasAlertedClientDetail) {
                alert('Detail CR maksimal 5000 kata/karakter!');
                hasAlertedClientDetail = true;
            } else if (chars <= 5000 && words <= 5000) {
                hasAlertedClientDetail = false;
            }
        }
    }

    function insertLinkPrompt() {
        const url = prompt('Masukkan URL Link:', 'https://');
        if (url) {
            execCmd('createLink', url);
        }
    }

    function insertImagePrompt() {
        const url = prompt('Masukkan URL Gambar/Mockup:', 'https://');
        if (url) {
            execCmd('insertImage', url);
        }
    }

    function insertCurrentDate() {
        const now = new Date();
        const str = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        execCmd('insertHTML', '<strong>' + str + '</strong>');
    }

    function insertTable(rows, cols) {
        let html = '<table style="width:100%; border-collapse: collapse; margin: 10px 0;" border="1">';
        for (let r = 0; r < rows; r++) {
            html += '<tr>';
            for (let c = 0; c < cols; c++) {
                html += '<td style="padding: 8px; border: 1px solid #CBD5E1;">Sel ' + (r + 1) + ',' + (c + 1) + '</td>';
            }
            html += '</tr>';
        }
        html += '</table><p></p>';
        execCmd('insertHTML', html);
    }

    function editorAction(act) {
        if (act === 'new') {
            if (confirm('Bersihkan konten editor ini?')) {
                const pmBody = document.getElementById('pmEditorBody');
                if (pmBody) pmBody.innerHTML = '';
                const clientBody = document.getElementById('editorBody');
                if (clientBody) clientBody.innerHTML = '';
                syncEditor();
            }
        }
    }

    function showWordCount() {
        const editor = document.getElementById('pmEditorBody') || document.getElementById('editorBody');
        const text = editor ? (editor.innerText || '') : '';
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const chars = text.length;
        alert('Statistik Teks:\n- Jumlah Kata: ' + words + '\n- Jumlah Karakter: ' + chars);
    }

    function openSourceModal() {
        const editor = document.getElementById('pmEditorBody') || document.getElementById('editorBody');
        document.getElementById('rawHtmlTextarea').value = editor ? editor.innerHTML : '';
        new bootstrap.Modal(document.getElementById('sourceCodeModal')).show();
    }

    function saveSourceHtml() {
        const val = document.getElementById('rawHtmlTextarea').value;
        const pmBody = document.getElementById('pmEditorBody');
        if (pmBody) pmBody.innerHTML = val;
        const clientBody = document.getElementById('editorBody');
        if (clientBody) clientBody.innerHTML = val;
        syncEditor();
        bootstrap.Modal.getInstance(document.getElementById('sourceCodeModal')).hide();
    }

    function openPreviewModal() {
        const editor = document.getElementById('pmEditorBody') || document.getElementById('editorBody');
        document.getElementById('previewModalBody').innerHTML = (editor && editor.innerHTML.trim()) ? editor.innerHTML : '<div class="text-muted fst-italic">Belum ada konten.</div>';
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    }

    function openHelpModal() {
        new bootstrap.Modal(document.getElementById('helpModal')).show();
    }

    function toggleEditorFullscreen() {
        const wrap = document.getElementById('pmEditorWrapper') || document.getElementById('editorWrapper');
        if (wrap) {
            if (!document.fullscreenElement) {
                wrap.requestFullscreen().catch(err => {
                    alert(`Gagal layar penuh: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
    }

    // 4. File Upload & Dropzone Handling (Client)
    function triggerFileInput() {
        const input = document.getElementById('dokumen_fsd_file');
        if (input) input.click();
    }

    function validatePdfFile(file) {
        if (!file) return false;
        const fileName = (file.name || '').toLowerCase();
        const fileType = (file.type || '').toLowerCase();
        const isPdf = fileName.endsWith('.pdf') || fileType === 'application/pdf';
        
        if (!isPdf) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format File Ditolak',
                    text: 'Dokumen CR hanya dapat menerima file berformat PDF (.pdf). Format file selain PDF tidak diperbolehkan.',
                    confirmButtonColor: '#0098EA',
                    confirmButtonText: 'Mengerti'
                });
            } else {
                alert('Dokumen CR hanya dapat menerima file berformat PDF (.pdf). Format file selain PDF tidak diperbolehkan.');
            }
            return false;
        }
        return true;
    }

    function handleFileChange(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!validatePdfFile(file)) {
                input.value = '';
                return;
            }
            displaySelectedFile(file);
        }
    }

    function displaySelectedFile(file) {
        const pl = document.getElementById('dropzonePlaceholder');
        const pr = document.getElementById('dropzoneFilePreview');
        const fn = document.getElementById('fileNameDisplay');
        const fs = document.getElementById('fileSizeDisplay');
        const box = document.getElementById('dropzoneBox');
        if (pl) pl.style.display = 'none';
        if (pr) pr.style.display = 'block';
        if (fn) fn.innerText = file.name;
        if (fs) fs.innerText = (file.size / 1024).toFixed(1) + ' KB';
        if (box) {
            box.style.borderColor = '#50CD89';
            box.style.background = '#F0FDF4';
        }
    }

    function removeSelectedFile(event) {
        event.stopPropagation();
        const input = document.getElementById('dokumen_fsd_file');
        if (input) input.value = '';
        
        const pl = document.getElementById('dropzonePlaceholder');
        const pr = document.getElementById('dropzoneFilePreview');
        const box = document.getElementById('dropzoneBox');
        if (pl) pl.style.display = 'block';
        if (pr) pr.style.display = 'none';
        if (box) {
            box.style.borderColor = '#93C5FD';
            box.style.background = '#FFFFFF';
        }
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        const box = document.getElementById('dropzoneBox');
        if (box) {
            box.style.borderColor = '#0063D7';
            box.style.background = '#EFF6FF';
        }
    }

    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        const box = document.getElementById('dropzoneBox');
        const input = document.getElementById('dokumen_fsd_file');
        if (box) {
            if (input && input.files && input.files.length > 0) {
                box.style.borderColor = '#50CD89';
                box.style.background = '#F0FDF4';
            } else {
                box.style.borderColor = '#93C5FD';
                box.style.background = '#FFFFFF';
            }
        }
    }

    function handleFileDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        const box = document.getElementById('dropzoneBox');
        const input = document.getElementById('dokumen_fsd_file');
        if (box) {
            if (input && input.files && input.files.length > 0) {
                box.style.borderColor = '#50CD89';
                box.style.background = '#F0FDF4';
            } else {
                box.style.borderColor = '#93C5FD';
                box.style.background = '#FFFFFF';
            }
        }
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            if (!validatePdfFile(file)) {
                return;
            }
            if (input) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }
            displaySelectedFile(file);
        }
    }

    // 5. Draft submission helper
    function prepareDraftSubmit() {
        syncEditor();
        const crNameInput = document.getElementById('cr_name');
        if (crNameInput && !crNameInput.value.trim()) {
            crNameInput.value = 'Draft Pengajuan CR';
        }
    }

    // 6. Custom Date Picker Logic matching Figma
    let currentActiveDateInputId = null;
    let pickerDate = new Date(); // Default today
    let selectedDay = pickerDate.getDate();

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const monthShortNames = [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
    ];
    const monthLookup = {
        'jan': 0, 'january': 0, 'januari': 0,
        'feb': 1, 'february': 1, 'februari': 1,
        'mar': 2, 'march': 2, 'maret': 2,
        'apr': 3, 'april': 3,
        'may': 4, 'mei': 4,
        'jun': 5, 'june': 5, 'juni': 5,
        'jul': 6, 'july': 6, 'juli': 6,
        'aug': 7, 'agu': 7, 'august': 7, 'agustus': 7,
        'sep': 8, 'september': 8,
        'oct': 9, 'okt': 9, 'october': 9, 'oktober': 9,
        'nov': 10, 'november': 10,
        'dec': 11, 'des': 11, 'december': 11, 'desember': 11
    };

    function positionDatePicker(targetInput, modal) {
        if (!targetInput || !modal) return;
        const rect = targetInput.getBoundingClientRect();
        const modalHeight = 310;
        const modalWidth = 290;

        // customDatePickerModal has position: fixed, so top & left must be relative to viewport (NO window.scrollY / window.scrollX)
        let topPos = rect.bottom + 6;
        if (topPos + modalHeight > window.innerHeight && rect.top > modalHeight + 10) {
            topPos = rect.top - modalHeight - 6;
        }

        let leftPos = rect.right - modalWidth;
        if (leftPos < 16) leftPos = rect.left;
        if (leftPos + modalWidth > window.innerWidth) {
            leftPos = window.innerWidth - modalWidth - 16;
        }

        modal.style.top = Math.max(10, Math.round(topPos)) + 'px';
        modal.style.left = Math.max(10, Math.round(leftPos)) + 'px';
    }

    function openDatePicker(inputId, event) {
        if (event) event.stopPropagation();
        currentActiveDateInputId = inputId;
        const targetInput = document.getElementById(inputId);
        const modal = document.getElementById('customDatePickerModal');
        if (!targetInput || !modal) return;
        
        // Parse current input date if available
        let parsed = false;
        if (targetInput.value) {
            const parts = targetInput.value.trim().split(/\s+/);
            if (parts.length === 3) {
                const day = parseInt(parts[0], 10);
                const mStr = parts[1].toLowerCase();
                const yr = parseInt(parts[2], 10);
                const mIdx = monthLookup[mStr] !== undefined ? monthLookup[mStr] : monthShortNames.findIndex(m => m.toLowerCase() === mStr);
                if (!isNaN(day) && mIdx !== -1 && !isNaN(yr)) {
                    pickerDate = new Date(yr, mIdx, day);
                    selectedDay = day;
                    parsed = true;
                }
            }
        }
        if (!parsed) {
            pickerDate = new Date();
            selectedDay = pickerDate.getDate();
        }

        positionDatePicker(targetInput, modal);
        modal.style.display = 'block';

        renderCalendar();
    }

    function closeDatePicker() {
        const modal = document.getElementById('customDatePickerModal');
        if (modal) modal.style.display = 'none';
        currentActiveDateInputId = null;
    }

    function prevMonth(e) {
        if (e) e.stopPropagation();
        pickerDate.setMonth(pickerDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth(e) {
        if (e) e.stopPropagation();
        pickerDate.setMonth(pickerDate.getMonth() + 1);
        renderCalendar();
    }

    function prevYear(e) {
        if (e) e.stopPropagation();
        pickerDate.setFullYear(pickerDate.getFullYear() - 1);
        renderCalendar();
    }

    function nextYear(e) {
        if (e) e.stopPropagation();
        pickerDate.setFullYear(pickerDate.getFullYear() + 1);
        renderCalendar();
    }

    function renderCalendar() {
        const year = pickerDate.getFullYear();
        const month = pickerDate.getMonth();
        const myEl = document.getElementById('calendarMonthYear');
        if (myEl) myEl.innerText = monthNames[month] + " " + year;

        const firstDay = new Date(year, month, 1).getDay(); // 0 is Sun
        const startOffset = (firstDay === 0 ? 6 : firstDay - 1); // Monday is 0
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();

        const grid = document.getElementById('calendarDaysGrid');
        if (!grid) return;
        grid.innerHTML = '';

        // Trailing days from previous month (grayed out)
        for (let i = startOffset - 1; i >= 0; i--) {
            const prevCell = document.createElement('div');
            prevCell.innerText = prevMonthDays - i;
            prevCell.className = 'py-1 text-muted d-flex align-items-center justify-content-center';
            prevCell.style.height = '28px';
            prevCell.style.width = '28px';
            prevCell.style.margin = '0 auto';
            prevCell.style.color = '#CBD5E1';
            prevCell.style.fontSize = '0.78rem';
            grid.appendChild(prevCell);
        }

        // Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('div');
            cell.innerText = day;
            cell.className = 'py-1 rounded cursor-pointer d-flex align-items-center justify-content-center';
            cell.style.transition = 'all 0.15s ease';
            cell.style.height = '28px';
            cell.style.width = '28px';
            cell.style.margin = '0 auto';
            cell.style.fontSize = '0.78rem';
            
            if (day === selectedDay) {
                // Black circular pill matching Figma #18181B
                cell.style.background = '#18181B';
                cell.style.color = '#FFFFFF';
                cell.style.fontWeight = 'bold';
                cell.style.borderRadius = '50%';
            } else {
                cell.style.color = '#1E293B';
                cell.onmouseover = function() { this.style.background = '#F1F5F9'; this.style.borderRadius = '50%'; };
                cell.onmouseout = function() { if (day !== selectedDay) this.style.background = 'transparent'; };
            }

            cell.onclick = function(e) {
                e.stopPropagation();
                selectedDay = day;
                renderCalendar();
            };

            grid.appendChild(cell);
        }

        // Remaining cells for neat grid
        const totalRendered = startOffset + daysInMonth;
        const remainder = (7 - (totalRendered % 7)) % 7;
        for (let nextDay = 1; nextDay <= remainder; nextDay++) {
            const nextCell = document.createElement('div');
            nextCell.innerText = nextDay;
            nextCell.className = 'py-1 text-muted d-flex align-items-center justify-content-center';
            nextCell.style.height = '28px';
            nextCell.style.width = '28px';
            nextCell.style.margin = '0 auto';
            nextCell.style.color = '#CBD5E1';
            nextCell.style.fontSize = '0.78rem';
            grid.appendChild(nextCell);
        }
    }

    function applyDatePicker() {
        if (!currentActiveDateInputId) return;
        const dayStr = String(selectedDay).padStart(2, '0');
        const monthStr = monthShortNames[pickerDate.getMonth()];
        const yearStr = pickerDate.getFullYear();
        
        const formatted = dayStr + " " + monthStr + " " + yearStr;
        const input = document.getElementById(currentActiveDateInputId);
        if (input) input.value = formatted;
        closeDatePicker();
    }

    // Close calendar on outside click
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('customDatePickerModal');
        if (modal && modal.style.display === 'block') {
            if (!e.target.closest('#customDatePickerModal') &&
                !e.target.closest('#date_input_1') &&
                !e.target.closest('#date_input_2') &&
                !e.target.closest('#client_date_input_1_wrap') &&
                !e.target.closest('#client_date_input_2_wrap') &&
                !e.target.closest('#pm_date_input_wrap') &&
                !e.target.closest('#pm_request_date_input_wrap')) {
                closeDatePicker();
            }
        }
    });

    // Keep datepicker positioned accurately on scroll & resize
    window.addEventListener('scroll', function() {
        const modal = document.getElementById('customDatePickerModal');
        if (modal && modal.style.display === 'block' && currentActiveDateInputId) {
            const targetInput = document.getElementById(currentActiveDateInputId);
            if (targetInput) positionDatePicker(targetInput, modal);
        }
    }, { passive: true });

    window.addEventListener('resize', function() {
        const modal = document.getElementById('customDatePickerModal');
        if (modal && modal.style.display === 'block' && currentActiveDateInputId) {
            const targetInput = document.getElementById(currentActiveDateInputId);
            if (targetInput) positionDatePicker(targetInput, modal);
        }
    });

    function setupInputLimit(inputId, counterId, maxLimit, fieldLabel) {
        const el = document.getElementById(inputId);
        const counter = document.getElementById(counterId);
        if (!el) return;

        function check() {
            const val = el.value || '';
            const chars = val.length;
            const words = val.trim() ? val.trim().split(/\s+/).filter(Boolean).length : 0;
            if (counter) {
                counter.innerText = `${chars}/${maxLimit}`;
                counter.style.color = (chars > maxLimit || words > maxLimit) ? '#EF4444' : '#64748B';
            }
            if (chars > maxLimit || words > maxLimit) {
                alert(`${fieldLabel} maksimal ${maxLimit} kata/karakter!`);
                if (chars > maxLimit) {
                    el.value = val.substring(0, maxLimit);
                    if (counter) counter.innerText = `${el.value.length}/${maxLimit}`;
                }
            }
        }

        el.addEventListener('input', check);
        el.addEventListener('paste', () => setTimeout(check, 10));
        check();
    }

    document.addEventListener('DOMContentLoaded', function() {
        calcPmTotalMandays();
        syncEditor();

        // 1. Nama CR (maksimal 255)
        setupInputLimit('pm_cr_name', 'pm_cr_name_counter', 255, 'Nama CR');
        setupInputLimit('cr_name', 'cr_name_counter', 255, 'Nama CR');

        // 2. CR Notes (maksimal 5000)
        setupInputLimit('pm_cr_notes', 'pm_cr_notes_counter', 5000, 'CR Notes');
        setupInputLimit('cr_notes', 'cr_notes_counter', 5000, 'CR Notes');

        // 3. Form submit validation (PM & Client)
        const form = document.getElementById('figmaCrForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                syncEditor();

                // Validate Nama CR
                const crNameEl = document.getElementById('pm_cr_name') || document.getElementById('cr_name');
                if (crNameEl) {
                    const val = crNameEl.value || '';
                    const chars = val.length;
                    const words = val.trim() ? val.trim().split(/\s+/).filter(Boolean).length : 0;
                    if (chars > 255 || words > 255) {
                        e.preventDefault();
                        alert('Nama CR maksimal 255 kata/karakter!');
                        crNameEl.focus();
                        return false;
                    }
                }

                // Validate Detail CR
                const pmBody = document.getElementById('pmEditorBody');
                const clientBody = document.getElementById('editorBody');
                const activeEditor = pmBody || clientBody;
                if (activeEditor) {
                    const text = activeEditor.innerText || '';
                    const chars = text.length;
                    const words = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
                    if (chars > 5000 || words > 5000) {
                        e.preventDefault();
                        alert('Detail CR maksimal 5000 kata/karakter!');
                        activeEditor.focus();
                        return false;
                    }
                }

                // Validate CR Notes
                const notesEl = document.getElementById('pm_cr_notes') || document.getElementById('cr_notes');
                if (notesEl) {
                    const val = notesEl.value || '';
                    const chars = val.length;
                    const words = val.trim() ? val.trim().split(/\s+/).filter(Boolean).length : 0;
                    if (chars > 5000 || words > 5000) {
                        e.preventDefault();
                        alert('CR Notes maksimal 5000 kata/karakter!');
                        notesEl.focus();
                        return false;
                    }
                }
            });
        }
    });
</script>
@endsection
