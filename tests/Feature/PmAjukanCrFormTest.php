<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\ChangeRequest;
use App\Models\SolutionPaper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PmAjukanCrFormTest extends TestCase
{
    use RefreshDatabase;

    private User $pm;
    private User $pmh;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pm = User::firstOrCreate(
            ['email' => 'pm@itpi.test'],
            [
                'name' => 'Totok Antok',
                'password' => bcrypt('password'),
                'role' => 'pm',
                'is_active' => true,
            ]
        );

        $this->pmh = User::firstOrCreate(
            ['email' => 'pmh@itpi.test'],
            [
                'name' => 'PM Head User',
                'password' => bcrypt('password'),
                'role' => 'pmh',
                'is_active' => true,
            ]
        );

        $this->client = Client::create([
            'name' => 'Totok Antok',
            'company' => 'PT Maju Bersama',
            'email' => 'client@majubersama.test',
            'phone' => '08123456789',
            'active' => true,
        ]);
    }

    /**
     * Test PM visits create CR and sees the unified single-container layout matching screenshot
     */
    public function test_pm_create_cr_view_renders_all_screenshot_fields(): void
    {
        $response = $this->actingAs($this->pm)->get('/change-requests/create');
        $response->assertOk();

        // 1. Header
        $response->assertSee('Ajukan Change Request Baru');
        $response->assertSee('Pembuatan Pengajuan CR');

        // 2. Row 1: Nama Perusahaan*, Inisial Klien, Nama PIC, CR Owner*
        $response->assertSee('Nama Perusahaan');
        $response->assertSee('Inisial Klien');
        $response->assertSee('Nama PIC');
        $response->assertSee('CR Owner');

        // 3. Row 2: Nama Project*, Tanggal, Request Date*
        $response->assertSee('Nama Project');
        $response->assertSee('Tanggal');
        $response->assertSee('Request Date');

        // 4. Row 3: Nama CR*
        $response->assertSee('Nama CR');
        $response->assertSee('Contoh: Integrasi API Pembayaran OVO');

        // 5. Row 4: Detail CR* (TinyMCE editor)
        $response->assertSee('Detail CR');
        $response->assertSee('pmEditorBody');
        $response->assertSee('Build with');
        $response->assertSee('tinyMCE');

        // 6. Row 5: Dokumen Lainnya* & Prioritas CR*
        $response->assertSee('Dokumen Lainnya');
        $response->assertSee('Prioritas CR');
        $response->assertSee('Normal');
        $response->assertSee('Urgent');

        // 7. Row 6: Estimasi Mandays (Analisis, Development, Testing, Total Mandays)
        $response->assertSee('Estimasi Mandays');
        $response->assertSee('Analisis (hari)');
        $response->assertSee('Development (hari)');
        $response->assertSee('Testing (hari)');
        $response->assertSee('Total Mandays');
        $response->assertDontSee('id="pm_total_mandays" readonly' . "\n" . '                                   value="10"', false);
        $response->assertSee('id="pm_total_mandays" readonly' . "\n" . '                                   value=""', false);

        // 8. Row 7: CR Notes
        $response->assertSee('CR Notes');
        $response->assertSee('Catatan tambahan atau konteks...');

        // 9. Row 8: Butuh Solution Paper?* & Upload Solution Paper (PDF)*
        $response->assertSee('Butuh Solution Paper?');
        $response->assertSee('Upload Solution Paper (PDF)');

        // 10. Row 9: Bottom Actions
        $response->assertSee('Kembali');
        $response->assertSee('Simpan Draft');
        $response->assertSee('Submit CR');
    }

    /**
     * Test PM submits CR with mandays transitions status to awaiting_pmh
     */
    public function test_pm_submits_cr_with_mandays_successfully(): void
    {
        Storage::fake('public');

        $docFile = UploadedFile::fake()->create('dokumen_tambahan.pdf', 500, 'application/pdf');
        $spFile = UploadedFile::fake()->create('solution_paper_v1.pdf', 500, 'application/pdf');

        $payload = [
            'nama_perusahaan' => 'PT Maju Bersama',
            'nama_project' => 'Eprocurement PT eagle Hight plantanions TBK',
            'cr_owner' => 'Andik Vermansyah',
            'nama_pic' => 'Totok Antok',
            'cr_name' => 'Integrasi API Pembayaran OVO',
            'detail_cr' => '<p>Kebutuhan integrasi payment gateway OVO.</p>',
            'date' => '04 Sep 2026',
            'request_date' => '04 Sep 2026',
            'prioritas' => 'normal',
            'mindesk_analisis' => 2,
            'mindesk_development' => 5,
            'mindesk_testing' => 3,
            'cr_notes' => 'Harap ditindaklanjuti untuk sprint Q3.',
            'solution_paper_required' => 1,
            'dokumen_lainnya' => $docFile,
            'solution_paper_file' => $spFile,
            'action' => 'submit',
        ];

        $response = $this->actingAs($this->pm)->post('/change-requests', $payload);
        $response->assertRedirect('/change-requests');

        $cr = ChangeRequest::where('judul', 'Integrasi API Pembayaran OVO')->first();
        $this->assertNotNull($cr);
        $this->assertEquals('awaiting_pmh', $cr->status);
        $this->assertEquals('PT Maju Bersama', $cr->klien);
        $this->assertEquals('Eprocurement PT eagle Hight plantanions TBK', $cr->proyek_terkait);
        $this->assertEquals('Andik Vermansyah', $cr->owner_cr);
        $this->assertEquals(2, $cr->mindesk_analisis);
        $this->assertEquals(5, $cr->mindesk_development);
        $this->assertEquals(3, $cr->mindesk_testing);

        // Verify SolutionPaper created
        $sp = SolutionPaper::where('change_request_id', $cr->id)->first();
        $this->assertNotNull($sp);
        $this->assertNotEmpty($sp->solution_paper_file);
    }

    /**
     * Test PM saves CR as draft
     */
    public function test_pm_saves_cr_as_draft(): void
    {
        $payload = [
            'nama_perusahaan' => 'PT Maju Bersama',
            'nama_project' => 'Eprocurement PT eagle Hight plantanions TBK',
            'cr_owner' => 'Andik Vermansyah',
            'cr_name' => 'Draft Modul Sync HR',
            'detail_cr' => 'Detail draft kebutuhan sync HR',
            'action' => 'draft',
        ];

        $response = $this->actingAs($this->pm)->post('/change-requests', $payload);
        $response->assertRedirect('/change-requests?view=draft');

        $cr = ChangeRequest::where('judul', 'Draft Modul Sync HR')->first();
        $this->assertNotNull($cr);
        $this->assertEquals('draft', $cr->status);
    }
}
