<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the homepage matches the Figma design structure and content.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Navbar assertions
        $response->assertSee('Fitur');
        $response->assertSee('Cara Kerja');
        $response->assertSee('Tentang Kami');
        $response->assertSee('Masuk');
        $response->assertSee('Buat Akun');

        // Hero assertions
        $response->assertSee('Platform CR Management Praktis No. 1');
        $response->assertSee('Kelola Change');
        $response->assertSee('Request Tanpa Ribet');
        $response->assertSee('Satu platform untuk klien, PM, dan tim development.');
        $response->assertSee('Buat Akun Gratis');
        $response->assertSee('Masuk ke Akun');
        $response->assertSee('Gratis 14 hari tanpa kartu kredit');
        $response->assertSee('Setup dalam 5 menit');
        $response->assertSee('Support via WhatsApp & email');
        $response->assertSee('Data tersimpan aman di server Indonesia');

        // Stats assertions
        $response->assertSee('2.400+');
        $response->assertSee('Change Request dikelola');
        $response->assertSee('98%');
        $response->assertSee('Tingkat kepuasan klien');
        $response->assertSee('60+');
        $response->assertSee('Perusahaan aktif');
        $response->assertSee('Lebih cepat dari email');

        // Fitur Unggulan assertions
        $response->assertSee('FITUR UNGGULAN');
        $response->assertSee('Semua yang dibutuhkan tim Anda');
        $response->assertSee('Manajemen CR Terpusat');
        $response->assertSee('Estimasi Mandays Akurat');
        $response->assertSee('Kolaborasi Multi-Peran');
        $response->assertSee('Notifikasi & Reminder Otomatis');
        $response->assertSee('Audit Trail Lengkap');
        $response->assertSee('Manajemen Dokumen Terintegrasi');

        // Cara Kerja assertions
        $response->assertSee('CARA KERJA');
        $response->assertSee('Mulai dalam 3 langkah sederhana');
        $response->assertSee('Klien Buat CR');
        $response->assertSee('PM Review & Estimasi');
        $response->assertSee('Pantau Progres Live');

        // CTA & Footer assertions
        $response->assertSee('Siap mengelola CR dengan lebih profesional?');
        $response->assertSee('Privasi');
        $response->assertSee('Syarat');
        $response->assertSee('Kontak');
    }
}
