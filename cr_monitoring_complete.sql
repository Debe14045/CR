-- ============================================================
-- ITPI ENTERPRISE CR MONITORING SYSTEM
-- Complete PostgreSQL Database Schema + Seed Data
-- Database : cr_monitoring
-- Generated: 2026-09-17
-- ============================================================
-- HOW TO IMPORT (pgAdmin):
--   1. Buka pgAdmin > Database "cr_monitoring" > Query Tool
--   2. File > Open File > pilih cr_monitoring_complete.sql
--   3. Tekan F5 atau tombol Run
-- ============================================================

-- TABLES IN THIS FILE:
--  1. users                  |  9. clients
--  2. password_reset_tokens  | 10. pegawais
--  3. sessions              | 11. master_statuses
--  4. cache                 | 12. email_verifications
--  5. cache_locks           | 13. change_requests (MAIN)
--  6. jobs                  | 14. solution_papers
--  7. job_batches           | 15. cr_invoices
--  8. failed_jobs           | 16. migrations
-- ============================================================

-- ==========================================================
-- STEP 0: DROP TABLES (safe re-run)
-- ==========================================================
DROP TABLE IF EXISTS cr_invoices CASCADE;
DROP TABLE IF EXISTS solution_papers CASCADE;
DROP TABLE IF EXISTS change_requests CASCADE;
DROP TABLE IF EXISTS email_verifications CASCADE;
DROP TABLE IF EXISTS master_statuses CASCADE;
DROP TABLE IF EXISTS pegawais CASCADE;
DROP TABLE IF EXISTS clients CASCADE;
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS job_batches CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS migrations CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ==========================================================
-- TABLE 1: users
-- ==========================================================
CREATE TABLE users (
    id                BIGSERIAL    PRIMARY KEY,
    name              VARCHAR(255) NOT NULL,
    email             VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP(0) NULL,
    password          VARCHAR(255) NOT NULL,
    role              VARCHAR(50)  NOT NULL DEFAULT 'client',
    google_id         VARCHAR(255) NULL,
    avatar            VARCHAR(500) NULL,
    remember_token    VARCHAR(100) NULL,
    created_at        TIMESTAMP(0) NULL,
    updated_at        TIMESTAMP(0) NULL
);
COMMENT ON TABLE  users      IS 'Semua pengguna sistem CR Monitoring';
COMMENT ON COLUMN users.role IS 'Enum: client | pm | pmh | presales | finance | admin';

-- TABLE 2: password_reset_tokens
CREATE TABLE password_reset_tokens (
    email      VARCHAR(255) NOT NULL PRIMARY KEY,
    token      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP(0) NULL
);

-- TABLE 3: sessions
CREATE TABLE sessions (
    id            VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id       BIGINT       NULL REFERENCES users(id) ON DELETE SET NULL,
    ip_address    VARCHAR(45)  NULL,
    user_agent    TEXT         NULL,
    payload       TEXT         NOT NULL,
    last_activity INTEGER      NOT NULL
);
CREATE INDEX idx_sessions_user_id       ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- TABLE 4 & 5: cache, cache_locks
CREATE TABLE cache (
    key        VARCHAR(255) NOT NULL PRIMARY KEY,
    value      TEXT         NOT NULL,
    expiration INTEGER      NOT NULL
);
CREATE TABLE cache_locks (
    key        VARCHAR(255) NOT NULL PRIMARY KEY,
    owner      VARCHAR(255) NOT NULL,
    expiration INTEGER      NOT NULL
);

-- TABLE 6: jobs (Laravel Queue)
CREATE TABLE jobs (
    id           BIGSERIAL    PRIMARY KEY,
    queue        VARCHAR(255) NOT NULL,
    payload      TEXT         NOT NULL,
    attempts     SMALLINT     NOT NULL DEFAULT 0,
    reserved_at  INTEGER      NULL,
    available_at INTEGER      NOT NULL,
    created_at   INTEGER      NOT NULL
);
CREATE INDEX idx_jobs_queue ON jobs(queue);

-- TABLE 7: job_batches
CREATE TABLE job_batches (
    id             VARCHAR(255) NOT NULL PRIMARY KEY,
    name           VARCHAR(255) NOT NULL,
    total_jobs     INTEGER      NOT NULL,
    pending_jobs   INTEGER      NOT NULL,
    failed_jobs    INTEGER      NOT NULL,
    failed_job_ids TEXT         NOT NULL,
    options        TEXT         NULL,
    cancelled_at   INTEGER      NULL,
    created_at     INTEGER      NOT NULL,
    finished_at    INTEGER      NULL
);

-- TABLE 8: failed_jobs
CREATE TABLE failed_jobs (
    id         BIGSERIAL    PRIMARY KEY,
    uuid       VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT         NOT NULL,
    queue      TEXT         NOT NULL,
    payload    TEXT         NOT NULL,
    exception  TEXT         NOT NULL,
    failed_at  TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================================
-- TABLE 9: clients
-- ==========================================================
CREATE TABLE clients (
    id                    BIGSERIAL    PRIMARY KEY,
    name                  VARCHAR(255) NOT NULL,
    nickname              VARCHAR(50)  NULL,
    company               VARCHAR(255) NOT NULL,
    email                 VARCHAR(255) NULL,
    phone                 VARCHAR(50)  NULL,
    address               TEXT         NULL,
    active                BOOLEAN      NOT NULL DEFAULT TRUE,
    -- PIC Marketing
    pic_marketing_name    VARCHAR(255) NULL,
    pic_marketing_phone   VARCHAR(50)  NULL,
    pic_marketing_email   VARCHAR(255) NULL,
    pic_marketing_desc    TEXT         NULL,
    -- PIC IT / Technical Lead
    pic_it_name           VARCHAR(255) NULL,
    pic_it_phone          VARCHAR(50)  NULL,
    pic_it_email          VARCHAR(255) NULL,
    pic_it_desc           TEXT         NULL,
    -- PIC Procurement
    pic_procurement_name  VARCHAR(255) NULL,
    pic_procurement_phone VARCHAR(50)  NULL,
    pic_procurement_email VARCHAR(255) NULL,
    pic_procurement_desc  TEXT         NULL,
    created_at            TIMESTAMP(0) NULL,
    updated_at            TIMESTAMP(0) NULL
);
COMMENT ON TABLE clients IS 'Master data perusahaan klien beserta 3 PIC (Marketing, IT, Procurement)';

-- TABLE 10: pegawais
CREATE TABLE pegawais (
    id         BIGSERIAL    PRIMARY KEY,
    nip        VARCHAR(50)  NOT NULL UNIQUE,
    name       VARCHAR(255) NOT NULL,
    address    TEXT         NULL,
    position   VARCHAR(100) NOT NULL,
    is_active  BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP(0) NULL,
    updated_at TIMESTAMP(0) NULL
);
COMMENT ON TABLE pegawais IS 'Master karyawan internal ITPI (PM, Developer, QA, Analyst, DevOps)';

-- TABLE 11: master_statuses
CREATE TABLE master_statuses (
    id          BIGSERIAL    PRIMARY KEY,
    code        VARCHAR(100) NOT NULL UNIQUE,
    name        VARCHAR(255) NOT NULL,
    badge_color VARCHAR(50)  NOT NULL DEFAULT 'secondary',
    sort_order  INTEGER      NOT NULL DEFAULT 0,
    description TEXT         NULL,
    created_at  TIMESTAMP(0) NULL,
    updated_at  TIMESTAMP(0) NULL
);
COMMENT ON TABLE  master_statuses            IS 'Status alur CR yang dikonfigurasi Admin';
COMMENT ON COLUMN master_statuses.code       IS 'awaiting_pm|awaiting_pmh|revision_needed|validated|analisa|development|sit|uat|training|awaiting_golive_validation|golive|invoicing|ditolak';
COMMENT ON COLUMN master_statuses.badge_color IS 'Bootstrap badge: warning|info|primary|success|danger|secondary';

-- TABLE 12: email_verifications
CREATE TABLE email_verifications (
    id          BIGSERIAL    PRIMARY KEY,
    email       VARCHAR(255) NOT NULL,
    code        VARCHAR(10)  NOT NULL,
    purpose     VARCHAR(30)  NOT NULL DEFAULT 'login',
    attempts    SMALLINT     NOT NULL DEFAULT 0,
    expires_at  TIMESTAMP(0) NOT NULL,
    verified_at TIMESTAMP(0) NULL,
    created_at  TIMESTAMP(0) NULL,
    updated_at  TIMESTAMP(0) NULL
);
CREATE INDEX idx_email_ver_email ON email_verifications(email);
COMMENT ON TABLE  email_verifications         IS 'OTP kode verifikasi email: login tanpa password & reset password';
COMMENT ON COLUMN email_verifications.purpose IS 'Tujuan: login | reset_password';

-- ==========================================================
-- TABLE 13: change_requests  ** TABEL UTAMA **
-- ==========================================================
CREATE TABLE change_requests (
    id                           BIGSERIAL     PRIMARY KEY,
    kode_cr                      VARCHAR(50)   NOT NULL UNIQUE,
    judul                        VARCHAR(500)  NOT NULL,
    -- Relasi
    user_id                      BIGINT        NULL REFERENCES users(id)   ON DELETE SET NULL,
    client_id                    BIGINT        NULL REFERENCES clients(id) ON DELETE SET NULL,
    -- Data Pengajuan
    klien                        VARCHAR(255)  NOT NULL DEFAULT '',
    proyek_terkait               VARCHAR(255)  NOT NULL DEFAULT '',
    owner_cr                     VARCHAR(255)  NULL,
    pic_sales                    VARCHAR(255)  NULL,
    bap_date                     DATE          NULL,
    -- Konten CR
    deskripsi                    TEXT          NOT NULL,
    alasan                       TEXT          NULL,
    reject_reason                TEXT          NULL,
    catatan_pengajuan            TEXT          NULL,
    pesan_client                 TEXT          NULL,
    -- Dokumen & Lampiran
    google_drive_url             VARCHAR(500)  NULL,
    lampiran_pengajuan           VARCHAR(500)  NULL,
    solution_paper_required      BOOLEAN       NOT NULL DEFAULT FALSE,
    solution_paper_note          TEXT          NULL,
    solution_paper_url           VARCHAR(500)  NULL,
    berita_acara_file            VARCHAR(500)  NULL,
    berita_acara_date            DATE          NULL,
    berita_acara_no              VARCHAR(100)  NULL,
    -- Status & Prioritas
    status                       VARCHAR(50)   NOT NULL DEFAULT 'awaiting_pm',
    prioritas                    VARCHAR(20)   NULL     DEFAULT 'normal',
    -- Tanggal
    tanggal_pengajuan            DATE          NOT NULL DEFAULT CURRENT_DATE,
    target_selesai               DATE          NULL,
    target_start_date            DATE          NULL,
    actual_completion_date       DATE          NULL,
    -- Estimasi & Pelaksanaan
    estimasi_waktu               VARCHAR(100)  NULL,
    estimasi_resource            VARCHAR(255)  NULL,
    main_desk                    VARCHAR(255)  NULL,
    mulai_dikerjakan_pada        DATE          NULL,
    selesai_dikerjakan_pada      DATE          NULL,
    -- Man-Day Estimasi (Mindesk)
    mindesk_analisis             NUMERIC(8,2)  NULL,
    mindesk_development          NUMERIC(8,2)  NULL,
    mindesk_testing              NUMERIC(8,2)  NULL,
    -- Keuangan
    biaya_pengerjaan             NUMERIC(15,2) NULL,
    harga_penawaran              NUMERIC(15,2) NULL,
    potensi_penjualan            TEXT          NULL,
    invoicing_status             VARCHAR(50)   NOT NULL DEFAULT 'pending',
    -- Catatan Internal
    catatan_analisis             TEXT          NULL,
    catatan_approval             TEXT          NULL,
    catatan_revisi               TEXT          NULL,
    lampiran_revisi              VARCHAR(500)  NULL,
    -- Approval Tracking
    approved_by                  VARCHAR(255)  NULL,
    pmh_approved_by              VARCHAR(255)  NULL,
    pmh_approved_at              TIMESTAMP(0)  NULL,
    -- Go-Live Validation
    golive_validated_at          TIMESTAMP(0)  NULL,
    golive_validated_by          VARCHAR(255)  NULL,
    -- Quotation Approval
    quotation_approved_client_at TIMESTAMP(0)  NULL,
    quotation_approved_tp_at     TIMESTAMP(0)  NULL,
    direvisi_pada                TIMESTAMP(0)  NULL,
    created_at                   TIMESTAMP(0)  NULL,
    updated_at                   TIMESTAMP(0)  NULL
);
CREATE INDEX idx_cr_status     ON change_requests(status);
CREATE INDEX idx_cr_user_id    ON change_requests(user_id);
CREATE INDEX idx_cr_client_id  ON change_requests(client_id);
CREATE INDEX idx_cr_kode_cr    ON change_requests(kode_cr);
CREATE INDEX idx_cr_prioritas  ON change_requests(prioritas);
CREATE INDEX idx_cr_created_at ON change_requests(created_at);
COMMENT ON TABLE  change_requests                  IS 'Tabel utama CR dari pengajuan sampai go-live & invoicing';
COMMENT ON COLUMN change_requests.status           IS 'Alur: awaiting_pm->awaiting_pmh->validated->analisa->development->sit->uat->training->awaiting_golive_validation->golive->invoicing | ditolak';
COMMENT ON COLUMN change_requests.mindesk_analisis IS 'Man-Day estimasi fase analisis';
COMMENT ON COLUMN change_requests.harga_penawaran  IS 'Harga penawaran ke klien (Rp)';
COMMENT ON COLUMN change_requests.biaya_pengerjaan IS 'Biaya aktual pengerjaan internal (Rp)';

-- TABLE 14: solution_papers
CREATE TABLE solution_papers (
    id                     BIGSERIAL    PRIMARY KEY,
    change_request_id      BIGINT       NOT NULL REFERENCES change_requests(id) ON DELETE CASCADE,
    status                 VARCHAR(50)  NOT NULL DEFAULT 'Draft',
    pegawai_id             BIGINT       NULL REFERENCES pegawais(id) ON DELETE SET NULL,
    creator_name           VARCHAR(255) NULL,
    target_date_start      DATE         NULL,
    target_date_end        DATE         NULL,
    actual_completion_date DATE         NULL,
    solution_paper_file    VARCHAR(500) NULL,
    notes                  TEXT         NULL,
    created_at             TIMESTAMP(0) NULL,
    updated_at             TIMESTAMP(0) NULL
);
CREATE INDEX idx_sp_cr_id ON solution_papers(change_request_id);
COMMENT ON TABLE  solution_papers        IS 'Dokumen Solution Paper / FSD / Analisis Teknis per CR';
COMMENT ON COLUMN solution_papers.status IS 'Draft | Completed | Needs Revision';

-- TABLE 15: cr_invoices
CREATE TABLE cr_invoices (
    id                    BIGSERIAL     PRIMARY KEY,
    change_request_id     BIGINT        NOT NULL REFERENCES change_requests(id) ON DELETE CASCADE,
    -- Invoice Info
    invoice_number        VARCHAR(100)  NULL,
    invoice_date          DATE          NULL,
    -- Nominal & Deskripsi
    nominal               NUMERIC(15,2) NOT NULL DEFAULT 0,
    job_description       TEXT          NULL,
    -- Rencana & Aktual Pembayaran
    planned_payment_date  DATE          NULL,
    actual_payment_date   DATE          NULL,
    -- Term of Payment
    term_name             VARCHAR(100)  NULL,
    percentage            NUMERIC(5,2)  NULL,
    -- Dokumen GR (Good Receipt)
    gr_number             VARCHAR(100)  NULL,
    gr_file               VARCHAR(500)  NULL,
    -- Dokumen Journal (J)
    journal_number        VARCHAR(100)  NULL,
    journal_file          VARCHAR(500)  NULL,
    -- Berita Acara
    ba_date               DATE          NULL,
    ba_file               VARCHAR(500)  NULL,
    -- Quotation Documents
    quotation_file        VARCHAR(500)  NULL,
    signed_quotation_file VARCHAR(500)  NULL,
    -- Status Pembayaran
    payment_status        VARCHAR(20)   NOT NULL DEFAULT 'pending',
    created_by            BIGINT        NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at            TIMESTAMP(0)  NULL,
    updated_at            TIMESTAMP(0)  NULL
);
CREATE INDEX idx_inv_cr_id          ON cr_invoices(change_request_id);
CREATE INDEX idx_inv_payment_status ON cr_invoices(payment_status);
COMMENT ON TABLE  cr_invoices                IS 'Invoice & dokumen pembayaran per CR (GR, Jurnal, BA, Quotation)';
COMMENT ON COLUMN cr_invoices.payment_status IS 'Status: pending | paid';
COMMENT ON COLUMN cr_invoices.term_name      IS 'Term of Payment: DP 30%, Pelunasan 70%, Full Payment';

-- TABLE 16: migrations (Laravel internal tracking)
CREATE TABLE migrations (
    id        SERIAL       PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch     INTEGER      NOT NULL
);

-- ==========================================================
-- SEED DATA
-- ==========================================================

-- SEED 1: master_statuses
INSERT INTO master_statuses (code, name, badge_color, sort_order, description, created_at, updated_at) VALUES
('awaiting_pm', 'Awaiting PM Verification', 'warning', 1, 'CR baru diajukan Client, menunggu verifikasi PM.', NOW(), NOW()),
('awaiting_pmh', 'Awaiting PMH Validation', 'info', 2, 'CR diverifikasi PM, menunggu validasi PM Head.', NOW(), NOW()),
('revision_needed', 'Revision Needed', 'danger', 3, 'CR dikembalikan ke PM untuk revisi oleh PM Head.', NOW(), NOW()),
('validated', 'Validated', 'primary', 4, 'CR divalidasi PM Head, siap dikerjakan.', NOW(), NOW()),
('analisa', 'Analisa', 'info', 5, 'Tahap analisis kebutuhan teknis dan proses.', NOW(), NOW()),
('development', 'Development', 'primary', 6, 'Tahap pengembangan dan koding.', NOW(), NOW()),
('sit', 'SIT (System Integration Testing)', 'warning', 7, 'Tahap pengujian integrasi sistem oleh tim internal.', NOW(), NOW()),
('uat', 'UAT (User Acceptance Testing)', 'warning', 8, 'Tahap pengujian penerimaan bersama pengguna/klien.', NOW(), NOW()),
('training', 'Training', 'info', 9, 'Tahap pelatihan penggunaan sistem kepada klien.', NOW(), NOW()),
('awaiting_golive_validation', 'Awaiting Go-Live Validation', 'secondary', 10, 'Berita Acara sudah diunggah PM, menunggu validasi PM Head.', NOW(), NOW()),
('golive', 'Go-Live', 'success', 11, 'Proyek resmi Go-Live, divalidasi PM Head.', NOW(), NOW()),
('invoicing', 'Invoicing', 'success', 12, 'Masuk antrean Finance untuk penerbitan Invoice.', NOW(), NOW()),
('ditolak', 'Ditolak', 'danger', 13, 'Pengajuan ditolak permanen oleh PM Head.', NOW(), NOW());

-- SEED 2: users (password semua = "password")
INSERT INTO users (name, email, email_verified_at, password, role, created_at, updated_at) VALUES
('Client Demo', 'client@itpi.test', NOW(), '$2y$12$b4mR4.bChBrdgFLqrHZhiuJHhnO1lXH8IJGxwVSivm1dOICGh4EYK', 'client', NOW(), NOW()),
('PM Demo', 'pm@itpi.test', NOW(), '$2y$12$b4mR4.bChBrdgFLqrHZhiuJHhnO1lXH8IJGxwVSivm1dOICGh4EYK', 'pm', NOW(), NOW()),
('PM Head Demo', 'pmh@itpi.test', NOW(), '$2y$12$b4mR4.bChBrdgFLqrHZhiuJHhnO1lXH8IJGxwVSivm1dOICGh4EYK', 'pmh', NOW(), NOW()),
('Presales Demo', 'presales@itpi.test', NOW(), '$2y$12$b4mR4.bChBrdgFLqrHZhiuJHhnO1lXH8IJGxwVSivm1dOICGh4EYK', 'presales', NOW(), NOW()),
('Finance Demo', 'finance@itpi.test', NOW(), '$2y$12$b4mR4.bChBrdgFLqrHZhiuJHhnO1lXH8IJGxwVSivm1dOICGh4EYK', 'finance', NOW(), NOW()),
('Admin Demo', 'admin@itpi.test', NOW(), '$2y$12$b4mR4.bChBrdgFLqrHZhiuJHhnO1lXH8IJGxwVSivm1dOICGh4EYK', 'admin', NOW(), NOW());

-- SEED 3: clients (6 perusahaan, masing-masing 3 PIC)
INSERT INTO clients (name, nickname, company, email, phone, address, active,
  pic_marketing_name, pic_marketing_phone, pic_marketing_email, pic_marketing_desc,
  pic_it_name, pic_it_phone, pic_it_email, pic_it_desc,
  pic_procurement_name, pic_procurement_phone, pic_procurement_email, pic_procurement_desc,
  created_at, updated_at) VALUES
('PT IT Prer Indonesia Technologi', 'ITPI', 'PT IT Prer Indonesia Technologi', 'client@itpi.test', '(021) 5890-1234', 'Menara ITPI Lt.12, Jl. HR Rasuna Said Kav.B-4, Jaksel', TRUE, 'Andi Wijaya', '081234567890', 'andi.wijaya@itpi.co.id', 'Head of Business Development', 'Budi Santoso', '081298765432', 'budi.santoso@itpi.co.id', 'Technical Lead & System Architect', 'Citra Lestari', '081311223344', 'citra.lestari@itpi.co.id', 'Senior Procurement Specialist', NOW(), NOW()),
('PT Bank Nusantara Raya', 'BNR', 'PT Bank Nusantara Raya', 'contact@banknusantara.co.id', '(021) 2999-8800', 'Nusantara Tower Lt.25, Sudirman CBD, Jakpus', TRUE, 'Dian Sastro', '081122334455', 'dian.marketing@banknusantara.co.id', 'Product Marketing Manager', 'Eko Prasetyo', '081199887766', 'eko.it@banknusantara.co.id', 'AVP Core Banking Application', 'Fiona Anggita', '081344556677', 'fiona.procurement@banknusantara.co.id', 'IT Procurement Manager', NOW(), NOW()),
('PT Data Solusi Teknologi', 'DST', 'PT Data Solusi Teknologi', 'info@datasolusi.co.id', '(021) 3201-5678', 'Centennial Tower Lt.8, Jl. Gatot Subroto Kav.24-25, Jaksel', TRUE, 'Gunawan Hadi', '081555667788', 'gunawan@datasolusi.co.id', 'Business Relations Manager', 'Hendra Kurniawan', '081666778899', 'hendra.it@datasolusi.co.id', 'IT Project Lead', 'Indah Permata', '081777889900', 'indah@datasolusi.co.id', 'Procurement Officer', NOW(), NOW()),
('PT Media Kreasi Digital', 'MKD', 'PT Media Kreasi Digital', 'contact@mediakreasi.co.id', '(021) 4455-6677', 'Jl. Jend. Sudirman No.123, Jakarta Pusat', TRUE, 'Joko Santoso', '081888990011', 'joko@mediakreasi.co.id', 'Head of Digital Marketing', 'Kartika Dewi', '081999001122', 'kartika.it@mediakreasi.co.id', 'Senior Software Engineer Lead', 'Lukman Hakim', '082111223344', 'lukman@mediakreasi.co.id', 'Procurement Specialist', NOW(), NOW()),
('PT Teknologi Nusantara Maju', 'TNM', 'PT Teknologi Nusantara Maju', 'contact@teknusantara.co.id', '(021) 5566-7788', 'Wisma Nusantara Lt.15, Jl. MH.Thamrin No.59, Jakpus', TRUE, 'Mawar Indah', '082222334455', 'mawar@teknusantara.co.id', 'Marketing & Partnership Manager', 'Nanda Prasetya', '082333445566', 'nanda.it@teknusantara.co.id', 'Chief Technology Officer', 'Omar Farouk', '082444556677', 'omar@teknusantara.co.id', 'Senior Procurement Manager', NOW(), NOW()),
('PT Finansial Kapital Indonesia', 'FKI', 'PT Finansial Kapital Indonesia', 'contact@finansialkapital.co.id', '(021) 6677-8899', 'Equity Tower Lt.35, Jl. Jend. Sudirman Kav.52-53, Jaksel', TRUE, 'Prita Kusuma', '082555667788', 'prita@finansialkapital.co.id', 'VP Business Development', 'Qori Hidayat', '082666778899', 'qori.it@finansialkapital.co.id', 'Head of IT Architecture', 'Ratna Dewi', '082777889900', 'ratna@finansialkapital.co.id', 'Senior Procurement Lead', NOW(), NOW());

-- SEED 4: pegawais (15 karyawan internal ITPI)
INSERT INTO pegawais (nip, name, address, position, is_active, created_at, updated_at) VALUES
('ITP-2022-001', 'Rizky Firmansyah, S.Kom', 'Jl. Tebet Barat Raya No.15, Jaksel', 'Project Manager (PM)', TRUE, NOW(), NOW()),
('ITP-2022-002', 'Sari Wahyuni, M.Kom', 'Jl. Cipete Raya No.22, Jaksel', 'PM Head (PMH)', TRUE, NOW(), NOW()),
('ITP-2023-003', 'Andika Wicaksono, ST', 'Jl. Pondok Labu No.5, Jaksel', 'Presales / Marketing', TRUE, NOW(), NOW()),
('ITP-2023-004', 'Fitriani Rahayu, SE', 'Jl. Bintaro Permai No.7, Tangerang Selatan', 'Finance / Keuangan', TRUE, NOW(), NOW()),
('ITP-2023-014', 'Fajar Nugraha', 'Komplek Permata Buana Blok C3, Jakbar', 'Senior Developer', TRUE, NOW(), NOW()),
('ITP-2023-022', 'Dewi Anggraini', 'Jl. Kemang Timur No.8, Jaksel', 'QA / Software Tester', TRUE, NOW(), NOW()),
('ITP-2024-005', 'Bambang Pamungkas', 'Jl. Fatmawati Raya No.40, Jaksel', 'System Analyst', TRUE, NOW(), NOW()),
('ITP-2024-006', 'Hendra Setiawan, S.T.', 'Jl. Kelapa Gading Permai Blok A1, Jakut', 'Full Stack Developer', TRUE, NOW(), NOW()),
('ITP-2024-007', 'Nia Ramadhani', 'Jl. Daan Mogot No.88, Jakbar', 'Frontend Developer', TRUE, NOW(), NOW()),
('ITP-2024-008', 'Ahmad Fauzi, S.Kom', 'Jl. Bekasi Timur Raya No.33, Bekasi', 'Backend Developer', TRUE, NOW(), NOW()),
('ITP-2025-009', 'Sri Mulyani, M.M.', 'Jl. Rawa Belong No.12, Jakbar', 'Project Manager (PM)', TRUE, NOW(), NOW()),
('ITP-2025-010', 'Bagus Wibowo', 'Jl. Mangga Besar No.56, Jakbar', 'Mobile Developer (Android)', TRUE, NOW(), NOW()),
('ITP-2025-011', 'Cantika Putri', 'Jl. Cilandak Timur No.3, Jaksel', 'Mobile Developer (iOS)', TRUE, NOW(), NOW()),
('ITP-2025-012', 'Doni Kusuma, ST.', 'Jl. Pangeran Antasari No.18, Jaksel', 'DevOps Engineer', TRUE, NOW(), NOW()),
('ITP-2025-013', 'Eka Ramdani', 'Jl. Pluit Sakti No.9, Jakut', 'Database Administrator', TRUE, NOW(), NOW());

-- SEED 5: change_requests (10 CR, semua status tercover)
INSERT INTO change_requests (kode_cr, judul, user_id, client_id, klien, proyek_terkait,
  owner_cr, pic_sales, bap_date, deskripsi, catatan_pengajuan,
  tanggal_pengajuan, target_selesai, target_start_date, status, prioritas,
  biaya_pengerjaan, harga_penawaran, mindesk_analisis, mindesk_development, mindesk_testing,
  created_at, updated_at) VALUES
('CR-2026-001','Fitur SSO Login Karyawan Portal',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='ITPI'),'PT IT Prer Indonesia Technologi','Portal & Pegawai','Fiko Adisty','Andika Wicaksono, ST','2026-09-01','Implementasi OAuth 2.0 PKCE flow SSO untuk single login karyawan ke semua sistem internal.','Pastikan testing dengan user dari semua departemen sebelum go-live.','2026-09-15','2026-10-15','2026-09-20','awaiting_pm','kritis',NULL,NULL,NULL,NULL,NULL,'2026-09-15 09:30:12','2026-09-15 09:30:12'),
('CR-2026-002','Modul Absensi Mobile App GPS Geofencing',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='MKD'),'PT Media Kreasi Digital','Mobile HR Platform','Maya Sari','Andika Wicaksono, ST','2026-09-02','Pengembangan absensi karyawan berbasis GPS geofencing dan face recognition untuk iOS & Android.','API geocoding Google Maps sudah siap. Perlu koordinasi tim mobile.','2026-09-10','2026-10-20','2026-09-18','awaiting_pmh','normal',NULL,NULL,NULL,NULL,NULL,'2026-09-10 11:15:40',NOW()),
('CR-2026-003','Integrasi API Payment Gateway QRIS dan Virtual Account',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='TNM'),'PT Teknologi Nusantara Maju','E-Commerce Platform','Rizky Pratama','Andika Wicaksono, ST','2026-09-05','Integrasi QRIS Midtrans SNAP, Virtual Account BCA/Mandiri, dan ShopeePay pada checkout.','Testing sandbox wajib selesai sebelum go-live production.','2026-09-08','2026-10-25','2026-09-15','validated','kritis',22000000,25000000,1.5,5.0,2.0,'2026-09-08 14:22:05',NOW()),
('CR-2026-004','Dashboard Laporan Keuangan Real-Time',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='FKI'),'PT Finansial Kapital Indonesia','Sistem Enterprise BI','Dewi Sartika','Andika Wicaksono, ST','2026-09-06','Dashboard visualisasi transaksi pendapatan harian, mingguan, dan rekonsiliasi kas realtime dengan export Excel & PDF.','Harus support filter: tanggal, cabang, jenis transaksi.','2026-09-07','2026-10-30','2026-09-14','analisa','kritis',18000000,22000000,2.0,3.0,1.0,'2026-09-07 16:45:18',NOW()),
('CR-2026-005','Notifikasi Push Multi-Platform FCM dan APNs',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='MKD'),'PT Media Kreasi Digital','Mobile App Platform','Bagus Wibowo','Andika Wicaksono, ST','2026-09-08','Implementasi FCM v1 API dan APNs untuk blast promosi dan reminder tagihan dengan dashboard kampanye.','Koordinasi tim iOS untuk APNs certificate sebelum 20 September.','2026-09-05','2026-10-10','2026-09-10','development','normal',10000000,12000000,1.0,3.0,1.0,'2026-09-05 10:18:22',NOW()),
('CR-2026-006','Modul Rekonsiliasi Laporan Hutang-Piutang Otomatis',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='FKI'),'PT Finansial Kapital Indonesia','Sistem Akuntansi Enterprise','Siti Rahayu','Andika Wicaksono, ST','2026-08-28','Rekonsiliasi otomatis data hutang-piutang ERP vs laporan bank dengan algoritma matching dan laporan selisih.','Testing dengan data real periode Agustus 2026.','2026-08-25','2026-09-28','2026-09-01','sit','normal',25000000,30000000,2.0,6.0,2.5,'2026-08-25 13:50:33',NOW()),
('CR-2026-007','Upgrade HRIS Modul Penggajian dan PPh21 TER',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='ITPI'),'PT IT Prer Indonesia Technologi','HRIS Enterprise','Ahmad Fauzan','Andika Wicaksono, ST','2026-08-15','Upgrade modul penggajian mendukung perhitungan PPh21 TER sesuai PMK No.168/2023, update template slip gaji dan SPT 1721-A1.','UAT melibatkan tim HRD dan Keuangan untuk validasi perhitungan.','2026-08-12','2026-09-20','2026-08-18','uat','kritis',35000000,40000000,2.5,7.0,3.0,'2026-08-12 09:00:00',NOW()),
('CR-2026-008','Koneksi ERP SAP dengan Revenue Portal',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='BNR'),'PT Bank Nusantara Raya','Data Integration dan ERP','Siti Rahayu','Andika Wicaksono, ST','2026-07-10','Middleware REST-to-SAP BAPI untuk otomasi booking jurnal SAP FI dengan error handling dan retry mechanism.','Go-Live 15 September 2026 bersama tim SAP client.','2026-07-08','2026-09-15','2026-07-15','golive','normal',28000000,32000000,2.0,6.0,2.0,'2026-07-08 13:50:33',NOW()),
('CR-2026-009','Implementasi Digital Signature Dokumen Kontrak BSrE',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='DST'),'PT Data Solusi Teknologi','Legal dan Document Management','Bimo Prakoso','Andika Wicaksono, ST','2026-06-20','Tanda tangan digital menggunakan SDK BSrE BSSN pada dokumen kontrak dengan workflow approval dan audit log.','Sudah go-live, menunggu proses invoicing Finance.','2026-06-18','2026-08-31','2026-06-25','invoicing','normal',20000000,24000000,1.5,4.5,2.0,'2026-06-18 10:00:00',NOW()),
('CR-2026-010','Migrasi Server On-Premise ke AWS Cloud Zero-Downtime',(SELECT id FROM users WHERE email='client@itpi.test'),(SELECT id FROM clients WHERE nickname='TNM'),'PT Teknologi Nusantara Maju','Cloud Infrastructure','Rendi Kurniawan','Andika Wicaksono, ST','2026-09-01','Migrasi seluruh server on-premise ke AWS EC2 dengan blue-green deployment. Setup VPC, EC2, RDS, dan DNS cutover.','Ditolak: scope terlalu luas dan budget tidak sesuai.','2026-09-01','2026-10-31',NULL,'ditolak','normal',NULL,NULL,NULL,NULL,NULL,'2026-09-01 08:00:00',NOW());

-- SEED 6: solution_papers
INSERT INTO solution_papers (change_request_id, status, pegawai_id, creator_name,
  target_date_start, target_date_end, actual_completion_date, notes, created_at, updated_at) VALUES
((SELECT id FROM change_requests WHERE kode_cr='CR-2026-003'),'Completed',
 (SELECT id FROM pegawais WHERE nip='ITP-2024-005'),'Bambang Pamungkas',
 '2026-09-14','2026-09-18','2026-09-18','Solution paper selesai, direview dan disetujui PM Head. Estimasi mandays final.',NOW(),NOW()),
((SELECT id FROM change_requests WHERE kode_cr='CR-2026-004'),'Draft',
 (SELECT id FROM pegawais WHERE nip='ITP-2024-005'),'Bambang Pamungkas',
 '2026-09-12','2026-09-16',NULL,'Sedang dalam proses penulisan spesifikasi teknis detail.',NOW(),NOW()),
((SELECT id FROM change_requests WHERE kode_cr='CR-2026-008'),'Completed',
 (SELECT id FROM pegawais WHERE nip='ITP-2024-005'),'Bambang Pamungkas',
 '2026-07-20','2026-07-25','2026-07-25','Solution paper selesai, go-live sudah divalidasi PM Head.',NOW(),NOW());

-- SEED 7: cr_invoices (3 invoice untuk 2 CR yang sudah golive/invoicing)
INSERT INTO cr_invoices (change_request_id, invoice_number, invoice_date,
  nominal, job_description, planned_payment_date, actual_payment_date,
  term_name, percentage, payment_status, created_by, created_at, updated_at) VALUES
((SELECT id FROM change_requests WHERE kode_cr='CR-2026-008'),
 'INV/2026/09/0001','2026-09-16',9600000,'DP 30% - Koneksi ERP SAP dengan Revenue Portal',
 '2026-09-30','2026-09-28','DP 30%',30.00,'paid',
 (SELECT id FROM users WHERE email='finance@itpi.test'),NOW(),NOW()),
((SELECT id FROM change_requests WHERE kode_cr='CR-2026-008'),
 'INV/2026/09/0002','2026-09-17',22400000,'Pelunasan 70% - Koneksi ERP SAP dengan Revenue Portal',
 '2026-10-17',NULL,'Pelunasan 70%',70.00,'pending',
 (SELECT id FROM users WHERE email='finance@itpi.test'),NOW(),NOW()),
((SELECT id FROM change_requests WHERE kode_cr='CR-2026-009'),
 'INV/2026/09/0003','2026-09-17',24000000,'Full Payment - Implementasi Digital Signature BSrE',
 '2026-10-17',NULL,'Full Payment',100.00,'pending',
 (SELECT id FROM users WHERE email='finance@itpi.test'),NOW(),NOW());

-- SEED 8: migrations (Laravel tracking)
INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_08_20_000001_create_change_requests_table', 2),
('2026_08_20_000002_add_revision_fields_to_change_requests_table', 2),
('2026_08_20_000002_add_role_to_users_table', 2),
('2026_08_20_000003_add_user_id_to_change_requests_table', 2),
('2026_08_20_000004_add_initial_attachment_to_change_requests_table', 2),
('2026_08_20_000004_add_workflow_fields_to_change_requests_table', 2),
('2026_08_20_000005_add_cost_to_change_requests_table', 2),
('2026_08_21_000001_add_priority_and_target_date_to_change_requests_table', 2),
('2026_08_22_000001_add_business_workflow_fields_to_change_requests_table', 3),
('2026_08_22_000002_create_clients_table', 3),
('2026_08_26_000001_remove_analyst_role', 3),
('2026_08_26_000002_normalize_change_request_statuses', 3),
('2026_08_26_000003_add_client_message_to_change_requests', 3),
('2026_08_26_000004_add_google_id_to_users_table', 3),
('2026_08_28_000001_create_email_verifications_table', 4),
('2026_09_04_000001_upgrade_clients_master_table', 5),
('2026_09_04_000002_create_pegawais_and_master_statuses_tables', 5),
('2026_09_04_000003_upgrade_change_requests_and_solution_papers_table', 5),
('2026_09_04_000004_create_cr_invoices_table', 5),
('2026_09_16_000001_add_figma_fields_to_change_requests_table', 6);

-- ==========================================================
-- VERIFIKASI (hapus komentar untuk jalankan)
-- ==========================================================
-- SELECT 'users'           AS tabel, COUNT(*) FROM users;
-- SELECT 'clients'         AS tabel, COUNT(*) FROM clients;
-- SELECT 'pegawais'        AS tabel, COUNT(*) FROM pegawais;
-- SELECT 'master_statuses' AS tabel, COUNT(*) FROM master_statuses;
-- SELECT 'change_requests' AS tabel, COUNT(*) FROM change_requests;
-- SELECT 'solution_papers' AS tabel, COUNT(*) FROM solution_papers;
-- SELECT 'cr_invoices'     AS tabel, COUNT(*) FROM cr_invoices;

-- ==========================================================
-- ALUR WORKFLOW CR MONITORING:
--  CLIENT   Ajukan CR        -> status: awaiting_pm
--  PM       Verifikasi        -> status: awaiting_pmh
--  PMH      Tolak/Revisi      -> status: revision_needed
--  PM       Revisi ulang      -> status: awaiting_pmh
--  PMH      Validasi          -> status: validated
--  PM       Mulai analisis    -> status: analisa
--  PM       Development       -> status: development
--  PM       Testing internal  -> status: sit
--  PM+Cient UAT bersama       -> status: uat
--  PM       Training          -> status: training
--  PM       Upload BA Go-Live -> status: awaiting_golive_validation
--  PMH      Validasi Go-Live  -> status: golive
--  Finance  Terbitkan Invoice -> status: invoicing
--  PMH      Tolak permanen    -> status: ditolak
-- ==========================================================
-- TOTAL TABEL  : 16
-- TOTAL RECORDS: users=6, clients=6, pegawais=15,
--                master_statuses=13, change_requests=10,
--                solution_papers=3, cr_invoices=3
-- STATUS COVERED: awaiting_pm, awaiting_pmh, validated,
--   analisa, development, sit, uat, golive, invoicing, ditolak
-- ==========================================================