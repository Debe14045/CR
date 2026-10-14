<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientFigmaAlignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::firstOrCreate(
            ['email' => 'client@itpi.test'],
            [
                'name' => 'Totok Antok',
                'password' => bcrypt('password'),
                'role' => 'client',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test Draft CR View matches Figma specs
     */
    public function test_draft_cr_view_matches_figma_layout_and_styles(): void
    {
        $response = $this->actingAs($this->client)->get('/change-requests?view=draft');
        $response->assertOk();

        // 1. Title & Subtitle
        $response->assertSee('DRAFT CR');
        $response->assertSee('Pantau seluruh permintaan perubahan Anda');

        // 2. Table Headers (Figma: NO, TERAKHIR DISIMPAN, PEMOHON, JUDUL CR, AKSI)
        $response->assertSee('NO');
        $response->assertSee('TERAKHIR DISIMPAN');
        $response->assertSee('PEMOHON');
        $response->assertSee('JUDUL CR');
        $response->assertSee('AKSI');

        // 3. Action Buttons with Figma colors
        $response->assertSee('Hapus');
        $response->assertSee('#DC2626'); // Red Hapus
        $response->assertSee('Edit');
        $response->assertSee('#FFC107'); // Yellow Edit
        $response->assertSee('Review');
        $response->assertSee('#155DFC'); // Blue Review

        // 4. Header background #ECF1F9
        $response->assertSee('background-color: #ECF1F9 !important');
        $response->assertSee('#ECF1F9');

        // 5. Pagination
        $response->assertSee('TOTAL DATA:');
        $response->assertSee('ROWS PER PAGE');
        $response->assertSee('PAGE');

        // 6. Sidebar icons
        $response->assertSee('bi-files'); // Semua CR
        $response->assertSee('bi-file-earmark-diff'); // Ajukan CR
        $response->assertSee('bi-file-earmark-bar-graph'); // Draft CR

        // 7. Figma Delete Draft Confirmation Modal (media_1791967207459.png)
        $response->assertSee('deleteDraftModal');
        $response->assertSee('Hapus draft ini?');
        $response->assertSee('akan dihapus dan tidak bisa dikembalikan');
        $response->assertSee('Batal');
        $response->assertSee('#FA0404'); // Modal red delete button
        $response->assertSee('#FFBABA'); // Soft red icon circle
    }

    /**
     * Test Ajukan CR View matches Figma specs
     */
    public function test_ajukan_cr_view_matches_figma_layout_and_styles(): void
    {
        $response = $this->actingAs($this->client)->get('/change-requests/create');
        $response->assertOk();

        // 1. Cards
        $response->assertSee('Ajukan Change Request Baru');
        $response->assertSee('Informasi Klien');
        $response->assertSee('Informasi Change Request');
        $response->assertSee('Dokumen Dan Lainnya');

        // 2. Header Colors
        $response->assertSee('#E1F6F1'); // Card 2 mint header
        $response->assertSee('#03A086'); // Card 2 teal icon
        $response->assertSee('#ECEAFD'); // Card 3 lavender header
        $response->assertSee('#907CF8'); // Card 3 purple icon

        // 3. Readonly inputs background
        $response->assertSee('#E9EDF1');

        // 4. Action Buttons
        $response->assertSee('Kembali');
        $response->assertSee('#DC2626');
        $response->assertSee('Simpan Draft');
        $response->assertSee('#FFC107');
        $response->assertSee('Submit CR');
        $response->assertSee('#50CD89');
    }

    /**
     * Test Client Semua CR View matches Figma specs (media_1791968487029.png)
     */
    public function test_client_semua_cr_view_matches_figma_layout_and_styles(): void
    {
        $response = $this->actingAs($this->client)->get('/change-requests?view=total');
        $response->assertOk();

        // 1. Title & Subtitle
        $response->assertSee('Change Request');
        $response->assertSee('Pantau seluruh permintaan perubahan Anda');

        // 2. Search & Filter Bar
        $response->assertSee('Search task or CR number...');
        $response->assertSee('Semua Status');

        // 3. Table Column Headers
        $response->assertSee('NO');
        $response->assertSee('KODE CR');
        $response->assertSee('PEMOHON');
        $response->assertSee('JUDUL CR');
        $response->assertSee('PENGAJUAN');
        $response->assertSee('STATUS');
        $response->assertSee('PRIORITAS');
        $response->assertSee('AKSI');

        // 4. Header background #ECF1F9
        $response->assertSee('background-color: #ECF1F9 !important');
        $response->assertSee('#ECF1F9');

        // 5. Figma Rows Showcase
        $response->assertSee('CR-2024-006');
        $response->assertSee('PT Delta Solusi');
        $response->assertSee('Fitur SSO Login Karyawan');
        $response->assertSee('Authentication');

        $response->assertSee('CR-2024-008');
        $response->assertSee('PT Maju Bersama');
        $response->assertSee('Modul Absensi Mobile App');
        $response->assertSee('Mobile');

        $response->assertSee('CR-2024-011');
        $response->assertSee('PT Teknologi Nusantara');
        $response->assertSee('Integrasi API Payment Gateway');
        $response->assertSee('Integration');

        $response->assertSee('CR-2024-014');
        $response->assertSee('PT Global Inovasi');
        $response->assertSee('Dashboard Laporan Keuangan Real-time');
        $response->assertSee('Reporting');

        $response->assertSee('CR-2024-017');
        $response->assertSee('PT Solusi Digital');
        $response->assertSee('Notifikasi Push Multi-Platform');
        $response->assertSee('Notification');

        $response->assertSee('CR-2024-019');
        $response->assertSee('Modul HR Self-Service Portal');
        $response->assertSee('HR');

        // 6. Badges (Status & Priority)
        $response->assertSee('Diajukan');
        $response->assertSee('Analysis');
        $response->assertSee('Develop');
        $response->assertSee('URGENT');
        $response->assertSee('#FFE2E2');
        $response->assertSee('NORMAL');
        $response->assertSee('#DCFCE7');

        // 7. Action Button
        $response->assertSee('Review');
        $response->assertSee('#155DFC');

        // 8. Pagination
        $response->assertSee('TOTAL DATA:');
        $response->assertSee('ROWS PER PAGE');
        $response->assertSee('PAGE');
    }

    /**
     * Test Client Title and Subtitle dynamically change based on status filter
     */
    public function test_client_title_changes_dynamically_with_status_filter(): void
    {
        // 1. Default (Semua Status)
        $resDefault = $this->actingAs($this->client)->get('/change-requests?view=total');
        $resDefault->assertOk();
        $resDefault->assertSee('Change Request');
        $resDefault->assertSee('Pantau seluruh permintaan perubahan Anda');
        $resDefault->assertSee('Semua Status');

        // 2. Diajukan
        $resDiajukan = $this->actingAs($this->client)->get('/change-requests?view=total&status=diajukan');
        $resDiajukan->assertOk();
        $resDiajukan->assertSee('Diajukan');
        $resDiajukan->assertSee('Pantau seluruh permintaan perubahan berstatus Diajukan');

        // 3. Analysis
        $resAnalysis = $this->actingAs($this->client)->get('/change-requests?view=total&status=analisa');
        $resAnalysis->assertOk();
        $resAnalysis->assertSee('Analysis');
        $resAnalysis->assertSee('Pantau seluruh permintaan perubahan berstatus Analysis');

        // 4. Develop
        $resDevelop = $this->actingAs($this->client)->get('/change-requests?view=total&status=development');
        $resDevelop->assertOk();
        $resDevelop->assertSee('Develop');
        $resDevelop->assertSee('Pantau seluruh permintaan perubahan berstatus Develop');

        // 5. UAT
        $resUat = $this->actingAs($this->client)->get('/change-requests?view=total&status=uat');
        $resUat->assertOk();
        $resUat->assertSee('UAT');
        $resUat->assertSee('Pantau seluruh permintaan perubahan berstatus UAT');

        // 6. Go-Live
        $resGolive = $this->actingAs($this->client)->get('/change-requests?view=total&status=golive');
        $resGolive->assertOk();
        $resGolive->assertSee('Go-Live');
        $resGolive->assertSee('Pantau seluruh permintaan perubahan berstatus Go-Live');
    }
}

