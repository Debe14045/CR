<?php $__env->startSection('title', 'Notifikasi • PM Head Monitoring CR'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* Styling Notifikasi persis sesuai design */
    .notif-container {
        padding: 0.5rem 0.75rem 2rem 0.75rem;
    }

    /* Revisi 1: Garis divider abu-abu tipis di atas search bar dan filter */
    .notif-divider {
        height: 1px;
        background: #E2E8F0;
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
        border: none;
    }

    /* Revisi 2: Warna tombol filter tab aktif persis warna sidebar aktif (#0063D7) */
    .notif-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 1.15rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .notif-tab-btn.active {
        background: #0063D7 !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 6px rgba(0, 99, 215, 0.28);
        border-color: #0063D7;
    }

    .notif-tab-btn.inactive {
        background: #FFFFFF;
        color: #475569;
        border-color: #E2E8F0;
    }

    .notif-tab-btn.inactive:hover {
        background: #F8FAFC;
        color: #0F172A;
    }

    .notif-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 9999px;
        background: #FFFFFF;
        color: #0063D7;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .notif-search-input {
        height: 38px;
        border-radius: 9999px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        padding-left: 2.25rem;
        padding-right: 1rem;
        font-size: 0.82rem;
        color: #334155;
        transition: border-color 0.2s;
    }

    .notif-search-input:focus {
        border-color: #0063D7;
        box-shadow: 0 0 0 3px rgba(0, 99, 215, 0.12);
        outline: none;
    }

    .notif-list-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        overflow: hidden;
    }

    .notif-row {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #F1F5F9;
        transition: background 0.15s ease;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .notif-row:last-child {
        border-bottom: none;
    }

    .notif-row:hover {
        background: #FAFCFF;
    }

    .notif-avatar-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .notif-category-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.18rem 0.55rem;
        border-radius: 9999px;
        font-size: 0.68rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .notif-code-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.18rem 0.55rem;
        border-radius: 9999px;
        font-size: 0.68rem;
        font-weight: 600;
        background: #F0F9FF;
        color: #0284C7;
        border: 1px solid #BAE6FD;
        line-height: 1.2;
    }

    .btn-buka-detail {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.85rem;
        border-radius: 8px;
        background: #F0F9FF;
        border: 1px solid #BAE6FD;
        color: #0284C7;
        font-size: 0.74rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-buka-detail:hover {
        background: #0063D7;
        color: #FFFFFF;
        border-color: #0063D7;
    }

    .btn-action-trash {
        background: transparent;
        border: none;
        color: #CBD5E1;
        padding: 0.25rem;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: color 0.2s;
    }

    .btn-action-trash:hover {
        color: #EF4444;
    }

    .btn-bersihkan {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 10px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        color: #475569;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-bersihkan:hover {
        background: #F8FAFC;
        color: #0F172A;
        border-color: #CBD5E1;
    }
</style>

<div class="notif-container">

    
    <nav class="d-flex align-items-center gap-1.5 mb-2 text-muted" style="font-size: 0.78rem;">
        <a href="<?php echo e(route('change-requests.index')); ?>" class="text-decoration-none d-flex align-items-center gap-1" style="color: #64748B;">
            <i class="bi bi-house-door" style="font-size: 0.85rem;"></i>
            <span data-i18n="breadcrumb_dashboard">Dashboard</span>
        </a>
        <span style="color: #94A3B8;">/</span>
        <span style="color: #0063D7; font-weight: 600;" data-i18n="breadcrumb_notifikasi">Notifikasi</span>
    </nav>

    
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0F172A; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;" data-i18n="notif_page_title">
                Notifikasi
            </h1>
            <p class="mb-0" style="font-size: 0.85rem; color: #64748B;" data-i18n="notif_page_subtitle">
                Pemberitahuan pembaruan status, persetujuan, dan aktivitas Change Request Anda.
            </p>
        </div>
        <button type="button" class="btn-bersihkan" onclick="bersihkanNotifikasi()">
            <i class="bi bi-trash3 text-muted"></i>
            <span data-i18n="btn_bersihkan">Bersihkan</span>
        </button>
    </div>

    
    <div class="notif-divider"></div>

    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="tabSemua" class="notif-tab-btn active" onclick="switchTab('semua')">
                <span data-i18n="tab_semua">Semua</span>
                <span class="notif-count-badge" id="badgeSemuaCount"><?php echo e(count($notifications)); ?></span>
            </button>
            <button type="button" id="tabUnread" class="notif-tab-btn inactive" onclick="switchTab('unread')">
                <span data-i18n="tab_belum_dibaca">Belum Dibaca</span>
            </button>
        </div>

        <div class="position-relative" style="min-width: 260px; max-width: 320px; width: 100%;">
            <i class="bi bi-search position-absolute text-muted" style="left: 0.9rem; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
            <input type="text" id="notifSearchInput" class="form-control notif-search-input"
                   placeholder="Cari notifikasi / kode CR..."
                   data-i18n-placeholder="placeholder_search_notif"
                   onkeyup="filterNotifikasi()">
        </div>
    </div>

    
    <div class="notif-list-card shadow-sm mb-3" id="notifListContainer">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="notif-row notif-item" id="<?php echo e($n['id']); ?>" data-read="<?php echo e($n['is_read'] ? '1' : '0'); ?>"
                 data-text-id="<?php echo e(strtolower($n['title_id'] . ' ' . $n['code'] . ' ' . $n['desc_id'])); ?>"
                 data-text-en="<?php echo e(strtolower($n['title_en'] . ' ' . $n['code'] . ' ' . $n['desc_en'])); ?>">
                
                
                <div class="notif-avatar-icon" style="background: <?php echo e($n['icon_bg']); ?>; color: <?php echo e($n['icon_color']); ?>;">
                    <i class="bi <?php echo e($n['icon']); ?>"></i>
                </div>

                
                <div class="flex-grow-1">
                    
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1 flex-wrap">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-bold notif-trans-title" style="font-size: 0.92rem; color: #0F172A; font-weight: 800;"
                                  data-id="<?php echo e($n['title_id']); ?>" data-en="<?php echo e($n['title_en']); ?>">
                                <?php echo e($n['title_id']); ?>

                            </span>
                            <span class="notif-category-pill notif-trans-category"
                                  style="background: <?php echo e($n['badge_bg']); ?>; color: <?php echo e($n['badge_color']); ?>; border: 1px solid <?php echo e($n['badge_border']); ?>;"
                                  data-id="<?php echo e($n['category_id']); ?>" data-en="<?php echo e($n['category_en']); ?>">
                                <?php echo e($n['category_id']); ?>

                            </span>
                            <span class="notif-code-pill">
                                <?php echo e($n['code']); ?>

                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.74rem; color: #64748B;">
                            <i class="bi bi-clock"></i>
                            <span class="notif-trans-time" data-id="<?php echo e($n['time_id']); ?>" data-en="<?php echo e($n['time_en']); ?>">
                                <?php echo e($n['time_id']); ?>

                            </span>
                        </div>
                    </div>

                    
                    <p class="mb-2 notif-trans-desc" style="font-size: 0.81rem; line-height: 1.55; color: #64748B;"
                       data-id="<?php echo e($n['desc_id']); ?>" data-en="<?php echo e($n['desc_en']); ?>">
                        <?php echo e($n['desc_id']); ?>

                    </p>

                    
                    <div class="d-flex justify-content-between align-items-center pt-1">
                        <a href="<?php echo e($n['url']); ?>" class="btn-buka-detail">
                            <span class="notif-trans-btn" data-id="Buka Detail CR" data-en="Open CR Details">Buka Detail CR</span>
                            <i class="bi bi-arrow-right" style="font-size: 0.76rem;"></i>
                        </a>

                        <button type="button" class="btn-action-trash" title="Hapus Notifikasi" onclick="hapusSatuNotifikasi('<?php echo e($n['id']); ?>')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-2 d-block mb-2 text-secondary"></i>
                <div class="fw-bold text-dark notif-empty-title" data-id="Tidak ada notifikasi saat ini." data-en="No notifications at this time.">Tidak ada notifikasi saat ini.</div>
                <div class="small notif-empty-desc" data-id="Semua aktivitas Change Request Anda telah terupdate." data-en="All your Change Request activities are up to date.">Semua aktivitas Change Request Anda telah terupdate.</div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="text-muted" style="font-size: 0.75rem; color: #64748B;" id="notifFooterCount" data-total="<?php echo e(count($notifications)); ?>">
        <?php if(count($notifications) > 0): ?>
            Menampilkan 1-<?php echo e(count($notifications)); ?> dari <?php echo e(count($notifications)); ?> notifikasi
        <?php else: ?>
            Menampilkan 0 dari 0 notifikasi
        <?php endif; ?>
    </div>

</div>

<script>
    let currentFilter = 'semua';
    const totalNotifsInitial = <?php echo e(count($notifications)); ?>;

    function switchTab(tab) {
        currentFilter = tab;
        const tabSemua = document.getElementById('tabSemua');
        const tabUnread = document.getElementById('tabUnread');

        if (tab === 'semua') {
            tabSemua.classList.remove('inactive');
            tabSemua.classList.add('active');
            tabUnread.classList.remove('active');
            tabUnread.classList.add('inactive');
        } else {
            tabUnread.classList.remove('inactive');
            tabUnread.classList.add('active');
            tabSemua.classList.remove('active');
            tabSemua.classList.add('inactive');
        }

        filterNotifikasi();
    }

    function filterNotifikasi() {
        const query = (document.getElementById('notifSearchInput')?.value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.notif-item');
        let visibleCount = 0;

        items.forEach(item => {
            const isRead = item.getAttribute('data-read') === '1';
            const textId = item.getAttribute('data-text-id') || '';
            const textEn = item.getAttribute('data-text-en') || '';

            let matchesTab = true;
            if (currentFilter === 'unread' && isRead) {
                matchesTab = false;
            }

            let matchesQuery = true;
            if (query && !textId.includes(query) && !textEn.includes(query)) {
                matchesQuery = false;
            }

            if (matchesTab && matchesQuery) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        updateFooterText(visibleCount);
    }

    function updateFooterText(visibleCount) {
        const items = document.querySelectorAll('.notif-item');
        const total = items.length;
        const lang = (localStorage.getItem('itpi_lang') || 'id').toLowerCase();
        const footer = document.getElementById('notifFooterCount');

        if (!footer) return;

        if (lang === 'en') {
            if (visibleCount > 0) {
                footer.textContent = `Showing 1-${visibleCount} of ${total} notifications`;
            } else {
                footer.textContent = `Showing 0 of ${total} notifications`;
            }
        } else {
            if (visibleCount > 0) {
                footer.textContent = `Menampilkan 1-${visibleCount} dari ${total} notifikasi`;
            } else {
                footer.textContent = `Menampilkan 0 dari ${total} notifikasi`;
            }
        }
    }

    function hapusSatuNotifikasi(id) {
        const el = document.getElementById(id);
        if (el) {
            el.remove();
            updateCounts();
        }
    }

    function bersihkanNotifikasi() {
        const lang = (localStorage.getItem('itpi_lang') || 'id').toLowerCase();
        const confirmMsg = lang === 'en' ? 'Are you sure you want to clear all notifications?' : 'Apakah Anda yakin ingin membersihkan semua notifikasi?';
        
        if (confirm(confirmMsg)) {
            const container = document.getElementById('notifListContainer');
            if (container) {
                const emptyTitle = lang === 'en' ? 'No notifications at this time.' : 'Tidak ada notifikasi saat ini.';
                const emptyDesc = lang === 'en' ? 'All your Change Request activities are up to date.' : 'Semua notifikasi telah dibersihkan.';
                container.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-2 d-block mb-2 text-secondary"></i>
                        <div class="fw-bold text-dark notif-empty-title" data-id="Tidak ada notifikasi saat ini." data-en="No notifications at this time.">${emptyTitle}</div>
                        <div class="small notif-empty-desc" data-id="Semua aktivitas Change Request Anda telah terupdate." data-en="All your Change Request activities are up to date.">${emptyDesc}</div>
                    </div>
                `;
            }
            updateCounts();
        }
    }

    function updateCounts() {
        const items = document.querySelectorAll('.notif-item');
        const count = items.length;
        const badge = document.getElementById('badgeSemuaCount');
        if (badge) badge.textContent = count;
        filterNotifikasi();
    }

    // Revisi 3: Translation support matching user's selected language
    function applyNotificationLanguage(lang) {
        lang = (lang || 'id').toLowerCase();

        // 1. Titles
        document.querySelectorAll('.notif-trans-title').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });

        // 2. Categories
        document.querySelectorAll('.notif-trans-category').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });

        // 3. Times
        document.querySelectorAll('.notif-trans-time').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });

        // 4. Descriptions
        document.querySelectorAll('.notif-trans-desc').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });

        // 5. Button texts
        document.querySelectorAll('.notif-trans-btn').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });

        // 6. Empty placeholders
        document.querySelectorAll('.notif-empty-title').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });
        document.querySelectorAll('.notif-empty-desc').forEach(el => {
            el.textContent = lang === 'en' ? el.getAttribute('data-en') : el.getAttribute('data-id');
        });

        // 7. Footer counter text
        const visibleItems = Array.from(document.querySelectorAll('.notif-item')).filter(i => i.style.display !== 'none');
        updateFooterText(visibleItems.length);
    }

    // Hook to existing window.setAppLanguage if available
    document.addEventListener('DOMContentLoaded', function () {
        const savedLang = localStorage.getItem('itpi_lang') || 'id';
        applyNotificationLanguage(savedLang);

        if (typeof window.setAppLanguage === 'function') {
            const originalSetAppLanguage = window.setAppLanguage;
            window.setAppLanguage = function (lang) {
                originalSetAppLanguage(lang);
                applyNotificationLanguage(lang);
            };
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Magang_ITPI\CR\resources\views/pmhead/notifications.blade.php ENDPATH**/ ?>