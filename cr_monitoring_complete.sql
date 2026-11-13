-- ============================================================
-- ITPI ENTERPRISE CR MONITORING SYSTEM
-- PostgreSQL Database Schema + Workflow Reference Data
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

-- Demo accounts and sample business rows intentionally omitted.

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