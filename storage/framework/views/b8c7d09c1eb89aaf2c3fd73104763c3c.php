<?php $__env->startSection('title', 'Ajukan Change Request Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-shell" style="width: 100%; padding: 0.25rem 0.5rem 3rem; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;">

    <?php if($errors->any()): ?>
        <div class="alert alert-danger mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid #FECACA; background: #FEF2F2;">
            <div class="fw-bold small mb-1 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Periksa kembali isian formulir:</div>
            <ul class="mb-0 small text-danger ps-3">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.68rem; font-weight: 800; letter-spacing: -0.02em; line-height: 1.2;">
            <?php echo e(!empty($isEdit) ? 'Edit Draft Change Request' : 'Ajukan Change Request Baru'); ?>

        </h1>
        <div style="color: #64748B; font-size: 0.82rem; font-weight: 400;">
            <?php echo e(!empty($isEdit) ? 'Perbarui draft pengajuan Change Request sebelum disubmit' : 'Pembuatan Pengajuan CR'); ?>

        </div>
    </div>

    <form method="POST" action="<?php echo e(!empty($isEdit) ? route('change-requests.update', $changeRequest) : route('change-requests.store')); ?>" enctype="multipart/form-data" id="figmaCrForm">
        <?php echo csrf_field(); ?>
        <?php if(!empty($isEdit)): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        
        <input type="hidden" name="nama_perusahaan" id="nama_perusahaan_input" value="<?php echo e(old('nama_perusahaan', !empty($changeRequest) ? ($changeRequest->klien ?: 'PT Maju Bersama') : 'PT Maju Bersama')); ?>">
        <input type="hidden" name="nama_project" id="nama_project_input" value="<?php echo e(old('nama_project', !empty($changeRequest) ? ($changeRequest->proyek_terkait ?: 'Eprocurement PT eagle Hight plantanions TBK') : 'Eprocurement PT eagle Hight plantanions TBK')); ?>">
        <input type="hidden" name="prioritas" id="prioritas_input" value="<?php echo e(old('prioritas', !empty($changeRequest) ? ($changeRequest->prioritas ?: 'normal') : 'normal')); ?>">

        
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important; position: relative; z-index: 50;">
            
            
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

            
            <div class="p-4" style="background: #FFFFFF;">
                <div class="figma-grid-3col">
                    
                    
                    <div class="grid-col-1">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Perusahaan <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="companyDropdownWrapper" style="position: relative; z-index: 60;">
                            <button type="button" class="btn w-100 d-flex align-items-center justify-content-between text-start"
                                    id="companyDropdownBtn"
                                    onclick="toggleCustomDropdown('companyDropdownMenu', event)"
                                    style="background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B; transition: all 0.15s ease;">
                                <span id="selectedCompanyName"><?php echo e(old('nama_perusahaan', 'PT Maju Bersama')); ?></span>
                                <svg id="companyDropdownChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ms-2 flex-shrink-0" style="transition: transform 0.2s ease;">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div class="position-absolute w-100 py-1" id="companyDropdownMenu"
                                 style="display: none; top: calc(100% + 4px); left: 0; z-index: 9999; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18), 0 4px 8px -2px rgba(15, 23, 42, 0.08); max-height: 240px; overflow-y: auto;">
                                <?php
                                    $companies = [
                                        ['name' => 'PT Maju Bersama', 'initial' => 'MB'],
                                        ['name' => 'PT Megah Jaya', 'initial' => 'MJ'],
                                        ['name' => 'PT Harian Bersama', 'initial' => 'HB'],
                                        ['name' => 'PT Sumber Makmur', 'initial' => 'SM'],
                                        ['name' => 'PT Nahkoda Biru', 'initial' => 'NB'],
                                    ];
                                ?>
                                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="dropdown-item py-2 px-3 cursor-pointer d-flex align-items-center justify-content-between"
                                         onclick="selectCompany('<?php echo e($comp['name']); ?>', '<?php echo e($comp['initial']); ?>')"
                                         style="font-size: 0.84rem; color: #1E293B; cursor: pointer; transition: background 0.15s ease;"
                                         onmouseover="this.style.background='#F0F9FF'; this.style.color='#0098EA';"
                                         onmouseout="this.style.background='transparent'; this.style.color='#1E293B';">
                                        <span style="font-weight: 600;"><?php echo e($comp['name']); ?></span>
                                        <span style="background: #E0F2FE; color: #0284C7; font-size: 0.70rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;"><?php echo e($comp['initial']); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="grid-col-2">
                        <div class="d-flex align-items-center gap-2 w-100">
                            
                            <div style="width: 72px; flex-shrink: 0;">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                    Inisial Klien
                                </label>
                                <input type="text" name="inisial_klien" id="inisial_klien"
                                       value="<?php echo e(old('inisial_klien', 'MB')); ?>" readonly
                                       style="background-color: #E2E8F0; color: #475569; font-weight: 600; border: none; border-radius: 8px; height: 42px; padding: 0 8px; font-size: 0.84rem; width: 100%; text-align: center;">
                            </div>

                            
                            <div style="flex: 1 1 auto; min-width: 0;">
                                <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                                    Nama PIC
                                </label>
                                <input type="text" name="nama_pic" id="nama_pic"
                                       value="<?php echo e(old('nama_pic', Auth::user()->name ?? 'Totok Antok')); ?>" readonly
                                       style="background-color: #E2E8F0; color: #475569; font-weight: 500; border: none; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            </div>
                        </div>
                    </div>

                    
                    <div class="grid-col-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            CR Owner <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" name="cr_owner" id="cr_owner"
                               value="<?php echo e(old('cr_owner', !empty($changeRequest) ? ($changeRequest->owner_cr ?: 'Andik Vermansyah') : 'Andik Vermansyah')); ?>" required
                               style="background-color: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%;">
                    </div>

                </div>
            </div>
        </div>

        
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important; position: relative; z-index: 30;">
            
            
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #DCFCE7; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 30px; height: 30px; background: #16A34A; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">Informasi Change Request</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Lengkapi detail informasi perubahan yang akan diajukan
                </div>
            </div>

            
            <div class="p-4" style="background: #FFFFFF;">
                
                
                <div class="figma-grid-3col mb-3">
                    
                    
                    <div class="grid-col-1">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Nama Project <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative" id="projectDropdownWrapper" style="position: relative; z-index: 40;">
                            <button type="button" class="btn w-100 d-flex align-items-center justify-content-between text-start"
                                    id="projectDropdownBtn"
                                    onclick="toggleCustomDropdown('projectDropdownMenu', event)"
                                    style="background: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; font-weight: 500; color: #1E293B; transition: all 0.15s ease;">
                                <span id="selectedProjectName" class="text-truncate"><?php echo e(old('nama_project', 'Eprocurement PT eagle Hight plantanions TBK')); ?></span>
                                <svg id="projectDropdownChevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="ms-2 flex-shrink-0" style="transition: transform 0.2s ease;">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div class="position-absolute w-100 py-1" id="projectDropdownMenu"
                                 style="display: none; top: calc(100% + 4px); left: 0; z-index: 9999; background: #FFFFFF; border: 1.5px solid #0098EA; border-radius: 8px; box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.18), 0 4px 8px -2px rgba(15, 23, 42, 0.08); max-height: 240px; overflow-y: auto;">
                                <?php
                                    $projects = [
                                        'Eprocurement PT eagle Hight plantanions TBK',
                                        'Sistem E-commerce',
                                        'E-Procurement',
                                        'Sistem Enterprise',
                                        'E-Cooper',
                                        'Data Integration',
                                    ];
                                ?>
                                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="dropdown-item py-2 px-3 cursor-pointer text-truncate"
                                         onclick="selectProject('<?php echo e($proj); ?>')"
                                         style="font-size: 0.84rem; color: #1E293B; cursor: pointer; transition: background 0.15s ease;"
                                         onmouseover="this.style.background='#F0F9FF'; this.style.color='#0098EA';"
                                         onmouseout="this.style.background='transparent'; this.style.color='#1E293B';">
                                        <?php echo e($proj); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="grid-col-2">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Tanggal
                        </label>
                        <div class="position-relative cursor-pointer" onclick="openDatePicker('date_input_1', event)">
                            <input type="text" name="date" id="date_input_1"
                                   value="<?php echo e(old('date', '04 Sep 2026')); ?>" readonly
                                   onclick="openDatePicker('date_input_1', event)"
                                   style="background-color: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 42px; padding: 0 38px 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%; cursor: pointer;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>

                    
                    <div class="grid-col-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Request Date <span style="color: #EF4444;">*</span>
                        </label>
                        <div class="position-relative cursor-pointer" onclick="openDatePicker('date_input_2', event)">
                            <input type="text" name="request_date" id="date_input_2"
                                   value="<?php echo e(old('request_date', old('target_golive', '04 Sep 2026'))); ?>" readonly
                                   onclick="openDatePicker('date_input_2', event)"
                                   style="background-color: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 42px; padding: 0 38px 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%; cursor: pointer;">
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

                
                <div class="mb-3">
                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                        Nama CR <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" name="cr_name" id="cr_name"
                           placeholder="Contoh: Integrasi API Pembayaran OVO"
                           value="<?php echo e(old('cr_name', !empty($changeRequest) ? $changeRequest->judul : '')); ?>" required
                           style="background-color: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; height: 42px; padding: 0 14px; font-size: 0.84rem; color: #1E293B; width: 100%;">
                </div>

                
                <div>
                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                        Detail CR <span style="color: #EF4444;">*</span>
                    </label>
                    
                    <div style="border: 1px solid #CBD5E1; border-radius: 8px; overflow: hidden; background: #FFFFFF;" id="editorWrapper">
                        
                        
                        <div class="d-flex align-items-center gap-1 px-2 py-1 bg-white border-bottom position-relative"
                             style="font-size: 0.78rem; user-select: none; color: #475569; border-color: #F1F5F9 !important;">
                            
                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">File</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="editorAction('new')"><i class="bi bi-file-earmark me-2"></i> Dokumen Baru</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="window.print()"><i class="bi bi-printer me-2"></i> Cetak (Print)</a></li>
                                </ul>
                            </div>

                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Edit</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('undo')"><span><i class="bi bi-arrow-counterclockwise me-2"></i> Undo</span> <small class="text-muted">Ctrl+Z</small></a></li>
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('redo')"><span><i class="bi bi-arrow-clockwise me-2"></i> Redo</span> <small class="text-muted">Ctrl+Y</small></a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><a class="dropdown-item py-1 px-3 d-flex justify-content-between" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('selectAll')"><span><i class="bi bi-check-all me-2"></i> Pilih Semua</span> <small class="text-muted">Ctrl+A</small></a></li>
                                </ul>
                            </div>

                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">View</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openPreviewModal()"><i class="bi bi-eye me-2"></i> Preview CR</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="toggleEditorFullscreen()"><i class="bi bi-arrows-fullscreen me-2"></i> Layar Penuh</a></li>
                                </ul>
                            </div>

                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Insert</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 160px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertLinkPrompt()"><i class="bi bi-link-45deg me-2"></i> Tautan Link</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertImagePrompt()"><i class="bi bi-image me-2"></i> Gambar / Mockup</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onmousedown="event.preventDefault();" onclick="execCmd('insertHorizontalRule')"><i class="bi bi-dash-lg me-2"></i> Garis Horizontal</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertCurrentDate()"><i class="bi bi-calendar-event me-2"></i> Tanggal Hari Ini</a></li>
                                </ul>
                            </div>

                            
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

                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Tools</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 150px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="showWordCount()"><i class="bi bi-calculator me-2"></i> Hitung Kata</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openSourceModal()"><i class="bi bi-code-slash me-2"></i> Kode Sumber HTML</a></li>
                                </ul>
                            </div>

                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Table</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 140px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertTable(2, 2)"><i class="bi bi-table me-2"></i> Tabel 2 x 2</a></li>
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="insertTable(3, 3)"><i class="bi bi-table me-2"></i> Tabel 3 x 3</a></li>
                                </ul>
                            </div>

                            
                            <div class="dropdown d-inline-block">
                                <span class="cursor-pointer py-1 px-2 rounded menubar-btn" data-bs-toggle="dropdown" aria-expanded="false" role="button">Help</span>
                                <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 0.8rem; border-radius: 8px; min-width: 160px;">
                                    <li><a class="dropdown-item py-1 px-3" href="javascript:void(0)" onclick="openHelpModal()"><i class="bi bi-question-circle me-2"></i> Bantuan & Shortcut</a></li>
                                </ul>
                            </div>

                        </div>

                        
                        <div class="d-flex align-items-center gap-1 px-3 py-1 border-bottom bg-white flex-wrap" style="font-size: 0.82rem; color: #334155; border-color: #F1F5F9 !important;">
                            
                            
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('undo')" title="Undo">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('redo')" title="Redo">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            
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

                            
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 fw-bold text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('bold')" title="Bold (Ctrl+B)">B</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 fst-italic text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('italic')" title="Italic (Ctrl+I)">I</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-decoration-underline text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('underline')" title="Underline (Ctrl+U)">U</button>
                            <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 text-decoration-line-through text-dark"
                                    onmousedown="event.preventDefault();" onclick="execCmd('strikeThrough')" title="Strikethrough">S</button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('justifyFull')" title="Justify"><i class="bi bi-justify"></i></button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('outdent')" title="Kurangi Inden"><i class="bi bi-text-indent-right"></i></button>
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="execCmd('indent')" title="Tambah Inden"><i class="bi bi-text-indent-left"></i></button>

                            <div class="vr my-1 mx-1 text-muted" style="opacity: 0.25;"></div>

                            
                            <button type="button" class="btn btn-sm btn-light border-0 p-1 text-secondary"
                                    onmousedown="event.preventDefault();" onclick="insertLinkPrompt()" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                        </div>

                        
                        <div id="editorBody" contenteditable="true"
                             style="min-height: 220px; padding: 14px 16px; outline: none; font-size: 0.86rem; line-height: 1.6; color: #1E293B;"
                             oninput="syncEditor()"
                             data-placeholder="Tuliskan rincian kebutuhan perubahan sistem, spesifikasi fitur baru, atau alur kerja yang diinginkan...">
                            <?php echo old('detail_cr', old('deskripsi_cr', old('deskripsi', !empty($changeRequest) ? $changeRequest->deskripsi : ''))); ?>

                        </div>
                        <textarea name="detail_cr" id="deskripsi_cr_hidden" class="d-none"><?php echo e(old('detail_cr', old('deskripsi_cr', old('deskripsi', !empty($changeRequest) ? $changeRequest->deskripsi : '')))); ?></textarea>

                        
                        <div class="px-3 py-1 border-top d-flex justify-content-between align-items-center text-muted"
                             style="background: #F8FAFC; font-size: 0.72rem; user-select: none; border-color: #F1F5F9 !important;">
                            <span style="color: #64748B;">p</span>
                            <span style="color: #94A3B8; cursor: pointer;" onclick="openHelpModal()" title="Klik untuk bantuan shortcut">Press ⌥0 for help</span>
                            <div class="d-flex align-items-center gap-1" style="color: #64748B;">
                                <span>Build with</span>
                                <span class="fw-bold" style="color: #1E293B;">tinyMCE</span>
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" style="margin-left: 4px; opacity: 0.5;">
                                    <path d="M9 1L1 9M9 5L5 9M9 9H9.01" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        
        <div class="card border-0 mb-4 shadow-xs" style="border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0 !important; position: relative; z-index: 10;">
            
            
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background: #ECEAFD; border-bottom: 1px solid rgba(0,0,0,0.03); border-top-left-radius: 13px; border-top-right-radius: 13px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center text-white rounded-circle shadow-xs"
                         style="width: 30px; height: 30px; background: #7C3AED; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-text-fill" style="font-size: 1rem;"></i>
                    </div>
                    <span class="fw-bold" style="color: #0F172A; font-size: 0.95rem; letter-spacing: -0.01em;">Dokumen Dan Lainnya</span>
                </div>
                <div class="d-none d-sm-block" style="font-size: 0.78rem; color: #64748B; font-weight: 400;">
                    Lengkapi detail informasi perubahan yang akan diajukan
                </div>
            </div>

            
            <div class="p-4" style="background: #FFFFFF;">
                
                
                <div class="row g-3 mb-3 align-items-stretch">
                    
                    
                    <div class="col-lg-6 col-12 d-flex flex-column">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Dokumen CR
                        </label>
                        
                        <div id="dropzoneBox"
                             class="p-2 text-center d-flex flex-column align-items-center justify-content-center cursor-pointer flex-grow-1"
                             onclick="triggerFileInput()"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleFileDrop(event)"
                             style="border: 1.5px dashed #93C5FD; border-radius: 10px; background: #FFFFFF; min-height: 104px; transition: all 0.2s ease;">
                            
                            
                            <div id="dropzonePlaceholder">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mb-1">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <div id="dropzoneText" style="font-size: 0.82rem; color: #64748B; font-weight: 500;">
                                    Klik untuk upload PDF
                                </div>
                            </div>

                            
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
                                   accept=".pdf,.doc,.docx,.zip"
                                   onchange="handleFileChange(this)">
                        </div>
                    </div>

                    
                    <div class="col-lg-6 col-12 d-flex flex-column">
                        <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                            Prioritas CR <span style="color: #EF4444;">*</span>
                        </label>

                        <div class="d-flex flex-column gap-2 flex-grow-1 justify-content-between">
                            
                            
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

                
                <div>
                    <label class="form-label fw-bold mb-1" style="font-size: 0.78rem; color: #0F172A;">
                        CR Notes
                    </label>
                    <textarea name="cr_notes" id="cr_notes" rows="3" class="form-control"
                              style="background-color: #FFFFFF; border: 1.5px solid #BAE6FD; border-radius: 8px; font-size: 0.84rem; padding: 12px 14px; width: 100%; height: 90px; outline: none;"
                              placeholder="Catatan tambahan, prioritas, atau informasi lain yang perlu diketahui PM..."><?php echo e(old('cr_notes', !empty($changeRequest) ? $changeRequest->catatan_pengajuan : '')); ?></textarea>
                </div>

            </div>
        </div>

        
        <div class="d-flex align-items-center justify-content-end gap-3 mt-4 mb-4">
            
            
            <a href="<?php echo e(route('change-requests.index')); ?>"
               class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center"
               style="background: #DC2626; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.7rem; text-decoration: none; transition: transform 0.15s ease, opacity 0.15s ease;"
               onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                Kembali
            </a>

            
            <button type="submit" name="action" value="draft" formnovalidate
                    onclick="prepareDraftSubmit()"
                    class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                    style="background: #FFC107; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.7rem; transition: transform 0.15s ease, opacity 0.15s ease;"
                    onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                <i class="bi bi-check-lg" style="font-size: 1rem; stroke-width: 1px;"></i>
                <span>Simpan Draft</span>
            </button>

            
            <button type="submit" name="action" value="submit"
                    onclick="syncEditor()"
                    class="btn text-white fw-semibold d-inline-flex align-items-center justify-content-center gap-1"
                    style="background: #50CD89; border-radius: 8px; font-size: 0.84rem; border: none; padding: 0.55rem 1.7rem; transition: transform 0.15s ease, opacity 0.15s ease;"
                    onmouseover="this.style.opacity='0.9';" onmouseout="this.style.opacity='1';">
                <i class="bi bi-check-lg" style="font-size: 1rem; stroke-width: 1px;"></i>
                <span>Submit CR</span>
            </button>

        </div>

    </form>

</div>


<div id="customDatePickerModal" class="shadow-lg position-fixed"
     style="display: none; z-index: 2000; background: #FFFFFF; border-radius: 12px; border: 1px solid #E2E8F0; width: 290px; padding: 14px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);">
    
    
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
        <div class="fw-bold text-dark" id="calendarMonthYear" style="font-size: 0.85rem; color: #18181B;">September 2026</div>
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

    
    <div class="d-grid mb-2 text-center text-muted" style="grid-template-columns: repeat(7, 1fr); font-size: 0.72rem; font-weight: 600; color: #94A3B8;">
        <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
    </div>

    
    <div id="calendarDaysGrid" class="d-grid text-center" style="grid-template-columns: repeat(7, 1fr); gap: 2px; font-size: 0.78rem;">
        
    </div>

    
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
    });

    // 2. Priority Selection Handler
    function selectPriority(val) {
        document.getElementById('prioritas_input').value = val;
        
        const cardNormal = document.getElementById('priorityCardNormal');
        const cardUrgent = document.getElementById('priorityCardUrgent');
        const labelNormal = document.getElementById('labelNormal');
        const labelUrgent = document.getElementById('labelUrgent');
        const radioNormal = document.getElementById('radioIndicatorNormal');
        const radioUrgent = document.getElementById('radioIndicatorUrgent');

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

    // 3. TinyMCE Editor Logic
    function execCmd(command, value = null) {
        document.execCommand(command, false, value);
        syncEditor();
    }

    function applyFormatBlock(tag, label) {
        document.execCommand('formatBlock', false, '<' + tag + '>');
        document.getElementById('currentParagraphLabel').innerText = label;
        syncEditor();
    }

    function syncEditor() {
        const content = document.getElementById('editorBody').innerHTML;
        document.getElementById('deskripsi_cr_hidden').value = content;
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
                document.getElementById('editorBody').innerHTML = '';
                syncEditor();
            }
        }
    }

    function showWordCount() {
        const text = document.getElementById('editorBody').innerText || '';
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const chars = text.length;
        alert('Statistik Teks:\n- Jumlah Kata: ' + words + '\n- Jumlah Karakter: ' + chars);
    }

    function openSourceModal() {
        document.getElementById('rawHtmlTextarea').value = document.getElementById('editorBody').innerHTML;
        new bootstrap.Modal(document.getElementById('sourceCodeModal')).show();
    }

    function saveSourceHtml() {
        document.getElementById('editorBody').innerHTML = document.getElementById('rawHtmlTextarea').value;
        syncEditor();
        bootstrap.Modal.getInstance(document.getElementById('sourceCodeModal')).hide();
    }

    function openPreviewModal() {
        document.getElementById('previewModalBody').innerHTML = document.getElementById('editorBody').innerHTML || '<div class="text-muted fst-italic">Belum ada konten.</div>';
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    }

    function openHelpModal() {
        new bootstrap.Modal(document.getElementById('helpModal')).show();
    }

    function toggleEditorFullscreen() {
        const wrap = document.getElementById('editorWrapper');
        if (!document.fullscreenElement) {
            wrap.requestFullscreen().catch(err => {
                alert(`Gagal layar penuh: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // 4. File Upload & Dropzone Handling
    function triggerFileInput() {
        document.getElementById('dokumen_fsd_file').click();
    }

    function handleFileChange(input) {
        if (input.files && input.files[0]) {
            displaySelectedFile(input.files[0]);
        }
    }

    function displaySelectedFile(file) {
        document.getElementById('dropzonePlaceholder').style.display = 'none';
        document.getElementById('dropzoneFilePreview').style.display = 'block';
        document.getElementById('fileNameDisplay').innerText = file.name;
        document.getElementById('fileSizeDisplay').innerText = (file.size / 1024).toFixed(1) + ' KB';
        
        const box = document.getElementById('dropzoneBox');
        box.style.borderColor = '#50CD89';
        box.style.background = '#F0FDF4';
    }

    function removeSelectedFile(event) {
        event.stopPropagation();
        const input = document.getElementById('dokumen_fsd_file');
        input.value = '';
        
        document.getElementById('dropzonePlaceholder').style.display = 'block';
        document.getElementById('dropzoneFilePreview').style.display = 'none';
        
        const box = document.getElementById('dropzoneBox');
        box.style.borderColor = '#93C5FD';
        box.style.background = '#FFFFFF';
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        const box = document.getElementById('dropzoneBox');
        box.style.borderColor = '#0063D7';
        box.style.background = '#EFF6FF';
    }

    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        const box = document.getElementById('dropzoneBox');
        const input = document.getElementById('dokumen_fsd_file');
        if (input.files && input.files.length > 0) {
            box.style.borderColor = '#50CD89';
            box.style.background = '#F0FDF4';
        } else {
            box.style.borderColor = '#93C5FD';
            box.style.background = '#FFFFFF';
        }
    }

    function handleFileDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            const fileInput = document.getElementById('dokumen_fsd_file');
            
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            
            displaySelectedFile(file);
        }
    }

    // 5. Draft submission helper
    function prepareDraftSubmit() {
        syncEditor();
        const crNameInput = document.getElementById('cr_name');
        if (!crNameInput.value.trim()) {
            crNameInput.value = 'Draft Pengajuan CR';
        }
    }

    // 6. Custom Date Picker Logic matching Figma
    let currentActiveDateInputId = null;
    let pickerDate = new Date(2026, 8, 4); // Default Sept 2026
    let selectedDay = 4;

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    const monthShortNames = [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
    ];

    function openDatePicker(inputId, event) {
        if (event) event.stopPropagation();
        currentActiveDateInputId = inputId;
        const targetInput = document.getElementById(inputId);
        const modal = document.getElementById('customDatePickerModal');
        
        // Parse current input date if available
        if (targetInput && targetInput.value) {
            const parts = targetInput.value.split(' ');
            if (parts.length === 3) {
                const day = parseInt(parts[0]);
                const mIdx = monthShortNames.findIndex(m => m.toLowerCase() === parts[1].toLowerCase());
                const yr = parseInt(parts[2]);
                if (!isNaN(day) && mIdx !== -1 && !isNaN(yr)) {
                    pickerDate = new Date(yr, mIdx, day);
                    selectedDay = day;
                }
            }
        }

        const rect = targetInput.getBoundingClientRect();
        // Position right below the input box
        modal.style.top = (rect.bottom + window.scrollY + 6) + 'px';
        modal.style.left = Math.max(16, (rect.right + window.scrollX - 290)) + 'px';
        modal.style.display = 'block';

        renderCalendar();
    }

    function closeDatePicker() {
        document.getElementById('customDatePickerModal').style.display = 'none';
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
        document.getElementById('calendarMonthYear').innerText = monthNames[month] + " " + year;

        const firstDay = new Date(year, month, 1).getDay(); // 0 is Sun
        const startOffset = (firstDay === 0 ? 6 : firstDay - 1); // Monday is 0
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();

        const grid = document.getElementById('calendarDaysGrid');
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
        document.getElementById(currentActiveDateInputId).value = formatted;
        closeDatePicker();
    }

    // Close calendar on outside click
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('customDatePickerModal');
        if (modal && modal.style.display === 'block') {
            if (!e.target.closest('#customDatePickerModal') && !e.target.closest('#date_input_1') && !e.target.closest('#date_input_2')) {
                closeDatePicker();
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\tyoda\Downloads\cr-monitoring-updated\resources\views/cr/create.blade.php ENDPATH**/ ?>