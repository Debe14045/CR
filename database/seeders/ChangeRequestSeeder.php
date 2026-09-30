<?php

namespace Database\Seeders;

use App\Models\ChangeRequest;
use Illuminate\Database\Seeder;

class ChangeRequestSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode_cr' => 'CR-2026-09-0042',
                'judul' => 'Integrasi API Pembayaran OVO',
                'klien' => 'PT Maju Bersama',
                'proyek_terkait' => 'Sistem E-commerce',
                'owner_cr' => 'Totok Antok',
                'pic_sales' => 'Andik Virmansyah',
                'bap_date' => '2024-01-15',
                'tanggal_pengajuan' => '2024-01-15',
                'target_selesai' => '2024-01-15',
                'google_drive_url' => 'https://drive.google.com/drive/folders/abc123',
                'solution_paper_url' => 'https://drive.google.com/file/sp001',
                'biaya_pengerjaan' => 15000000,
                'harga_penawaran' => 15000000,
                'deskripsi' => "1. LATAR BELAKANG & TUJUAN\nImplementasi penambahan kanal pembayaran digital menggunakan e-wallet OVO (Push to Pay & QRIS) pada modul checkout Sistem E-commerce. Hal ini bertujuan untuk menaikkan rasio konversi checkout pelanggan serta mengurangi tingkat abandoned cart pada saat proses transaksi pembelian online.\n\n2. RUANG LINGKUP PERUBAHAN (SCOPE OF WORK)\nPenambahan opsi pembayaran OVO Wallet pada step 3 (Metode Pembayaran) di aplikasi Web dan Mobile.\nIntegrasi Webhook Callback Service untuk konfirmasi status settlement secara real-time.\nPenyelarasan modul rekonsiliasi harian dan penyesuaian laporan keuangan di portal admin.\nPenambahan unit test coverage dan staging automated testing minimum 85%.\n\n3. DAMPAK TEKNIS & DEPENDENCIES\nCatatan Dependensi: Membutuhkan integrasi API Gateway credentials (Client ID & Secret Key) production dari pihak Payment Aggregator sebelum tanggal 18 Sep 2026.",
                'catatan_pengajuan' => 'Mohon diprioritaskan untuk integrasi sandbox staging sebelum tanggal 15 September agar tim QA dapat melakukan testing payment gateway secara menyeluruh. Testing account sudah kami koordinasikan dengan pihak vendor OVO.',
                'created_at' => '2026-09-04 09:30:12',
                'status' => 'diajukan',
                'prioritas' => 'normal',
                'mindesk_analisis' => 0,
                'mindesk_development' => 0,
                'mindesk_testing' => 0,
            ],
            [
                'kode_cr' => 'CR-2024-002',
                'judul' => 'Modul Absensi Mobile App',
                'klien' => 'PT Media Kreasi',
                'proyek_terkait' => 'Akses & Dashboard',
                'owner_cr' => 'Maya Sari',
                'pic_sales' => 'Andika Wicaksono, ST',
                'bap_date' => '2024-09-02',
                'target_selesai' => '2024-09-20',
                'biaya_pengerjaan' => 18000000,
                'deskripsi' => 'Pengembangan fitur absensi karyawan berbasis GPS geofencing dan face recognition pada mobile app iOS & Android.',
                'catatan_pengajuan' => 'Integrasi API geocoding Google Maps sudah siap di sisi backend.',
                'tanggal_pengajuan' => '2024-09-14',
                'created_at' => '2024-09-14 11:15:40',
                'status' => 'diajukan',
                'prioritas' => 'normal',
            ],
            [
                'kode_cr' => 'CR-2024-003',
                'judul' => 'Integrasi API Payment Gateway',
                'klien' => 'PT Teknologi Nusantara',
                'proyek_terkait' => 'E-Commerce Platform',
                'owner_cr' => 'Rizky Pratama',
                'pic_sales' => 'Andika Wicaksono, ST',
                'bap_date' => '2024-09-05',
                'target_selesai' => '2024-09-22',
                'biaya_pengerjaan' => 25000000,
                'deskripsi' => 'Integrasi kanal pembayaran QRIS, Virtual Account BCA/Mandiri, dan ShopeePay pada checkout e-commerce.',
                'tanggal_pengajuan' => '2024-09-12',
                'created_at' => '2024-09-12 14:22:05',
                'status' => 'analisa',
                'prioritas' => 'kritis',
            ],
            [
                'kode_cr' => 'CR-2024-004',
                'judul' => 'Dashboard Laporan Keuangan Real-Time',
                'klien' => 'PT Finansial Kapital',
                'proyek_terkait' => 'Sistem Enterprise',
                'owner_cr' => 'Dewi Sartika',
                'pic_sales' => 'Andika Wicaksono, ST',
                'bap_date' => '2024-09-06',
                'target_selesai' => '2024-09-25',
                'biaya_pengerjaan' => 22000000,
                'deskripsi' => 'Pembuatan dashboard visualisasi transaksi pendapatan harian, mingguan, dan rekonsiliasi kas realtime.',
                'tanggal_pengajuan' => '2024-09-10',
                'created_at' => '2024-09-10 16:45:18',
                'status' => 'analisa',
                'prioritas' => 'kritis',
            ],
            [
                'kode_cr' => 'CR-2024-005',
                'judul' => 'Notifikasi Push Multi-Platform',
                'klien' => 'PT Digital Kreasi',
                'proyek_terkait' => 'Mobile App Platform',
                'owner_cr' => 'Bagus Wibowo',
                'pic_sales' => 'Andika Wicaksono, ST',
                'bap_date' => '2024-09-08',
                'target_selesai' => '2024-09-28',
                'biaya_pengerjaan' => 12000000,
                'deskripsi' => 'Implementasi FCM (Firebase Cloud Messaging) dan APNs untuk blast promosi dan reminder tagihan.',
                'tanggal_pengajuan' => '2024-09-08',
                'created_at' => '2024-09-08 10:18:22',
                'status' => 'development',
                'prioritas' => 'normal',
            ],
            [
                'kode_cr' => 'CR-2024-006',
                'judul' => 'Koneksi ERP SAP Revenue Portal',
                'klien' => 'PT Solusi Mandiri',
                'proyek_terkait' => 'Data Integration',
                'owner_cr' => 'Siti Rahayu',
                'pic_sales' => 'Andika Wicaksono, ST',
                'bap_date' => '2024-09-09',
                'target_selesai' => '2024-09-30',
                'biaya_pengerjaan' => 30000000,
                'deskripsi' => 'Koneksi middleware REST API antara portal invoicing dengan modul SAP Finance.',
                'tanggal_pengajuan' => '2024-09-05',
                'created_at' => '2024-09-05 13:50:33',
                'status' => 'golive',
                'prioritas' => 'normal',
            ],
        ];

        $clientId = \App\Models\User::where('email', 'client@itpi.test')->value('id');

        foreach ($data as $item) {
            $item['user_id'] = $clientId;
            ChangeRequest::updateOrCreate(['kode_cr' => $item['kode_cr']], $item);
        }
    }
}
