<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChangeRequestFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/change-requests');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'pm',
            'email' => 'pm@itpi.test',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/change-requests');
        $this->assertAuthenticatedAs($user);
    }

    public function test_presales_demo_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'presales@itpi.test',
            'role' => User::ROLE_PRESALES,
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/change-requests');

        $this->assertAuthenticatedAs($user);
    }

    public function test_all_active_roles_can_login(): void
    {
        foreach ([
            User::ROLE_CLIENT,
            User::ROLE_PM,
            User::ROLE_PMH,
            User::ROLE_PRESALES,
            User::ROLE_ADMIN,
        ] as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'email' => $role . '@itpi.test',
            ]);

            $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])->assertRedirect('/change-requests');

            $this->assertAuthenticatedAs($user);
            $this->post('/logout');
        }
    }

    public function test_export_excel_is_available_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'pm']);

        $this->actingAs($user)
            ->get('/change-requests/export/excel')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_presales_can_export_pipeline_excel(): void
    {
        $presales = User::factory()->create(['role' => 'presales']);

        ChangeRequest::create([
            'kode_cr' => 'CR-2026-999',
            'judul' => 'CR Presales Pipeline Test',
            'klien' => 'Klien Pipeline',
            'proyek_terkait' => 'Proyek Alpha',
            'deskripsi' => 'Deskripsi pipeline',
            'tanggal_pengajuan' => now(),
            'status' => 'disetujui',
            'harga_penawaran' => 50000000,
        ]);

        $response = $this->actingAs($presales)->get(route('change-requests.export.excel'));
        
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_presales_can_save_quotation_without_status_field(): void
    {
        $presales = User::factory()->create(['role' => 'presales']);
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-107',
            'judul' => 'CR quotation presales',
            'klien' => 'Client Presales',
            'proyek_terkait' => 'Project Presales',
            'deskripsi' => 'Deskripsi kebutuhan',
            'tanggal_pengajuan' => now(),
            'status' => 'disetujui',
        ]);

        $this->actingAs($presales)->put(route('change-requests.update', $changeRequest), [
            'harga_penawaran' => 15000000,
            'potensi_penjualan' => 'Peluang lanjutan',
        ])->assertRedirect(route('change-requests.index'));

        $this->assertSame('disetujui', $changeRequest->refresh()->status);
        $this->assertSame('15000000.00', $changeRequest->harga_penawaran);
    }

    public function test_client_can_leave_a_message_on_own_change_request(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-108',
            'judul' => 'CR pesan client',
            'klien' => $client->name,
            'proyek_terkait' => 'Project Komunikasi',
            'deskripsi' => 'Deskripsi kebutuhan',
            'tanggal_pengajuan' => now(),
            'status' => 'dianalisis',
            'user_id' => $client->id,
        ]);

        $this->actingAs($client)->post(route('change-requests.message', $changeRequest), [
            'pesan_client' => 'Mohon update estimasi pengerjaan CR ini.',
        ])->assertRedirect();

        $this->assertSame('Mohon update estimasi pengerjaan CR ini.', $changeRequest->refresh()->pesan_client);
    }

    public function test_pm_can_access_pdf_export_and_client_cannot(): void
    {
        $pm = User::factory()->create(['role' => 'pm']);
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($pm)
            ->get('/change-requests/export/pdf')
            ->assertOk();

        $this->actingAs($client)
            ->get('/change-requests/export/pdf')
            ->assertForbidden();
    }

    public function test_client_dashboard_only_shows_own_change_requests(): void
    {
        $clientOne = User::factory()->create(['role' => 'client', 'name' => 'Client One']);
        $clientTwo = User::factory()->create(['role' => 'client', 'name' => 'Client Two']);

        ChangeRequest::create([
            'kode_cr' => 'CR-2026-101',
            'judul' => 'CR milik client satu',
            'klien' => 'Client One',
            'proyek_terkait' => 'Project A',
            'deskripsi' => 'deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
            'user_id' => $clientOne->id,
        ]);

        ChangeRequest::create([
            'kode_cr' => 'CR-2026-102',
            'judul' => 'CR milik client dua',
            'klien' => 'Client Two',
            'proyek_terkait' => 'Project B',
            'deskripsi' => 'deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
            'user_id' => $clientTwo->id,
        ]);

        $response = $this->actingAs($clientOne)->get('/change-requests');

        $response->assertOk();
        $response->assertSee('CR milik client satu');
        $response->assertDontSee('CR milik client dua');
    }

    public function test_client_can_create_change_request_with_pdf_reference(): void
    {
        Storage::fake('public');
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->post(route('change-requests.store'), [
            'judul' => 'Permintaan Export Laporan',
            'owner_cr' => $client->name,
            'klien' => $client->name,
            'proyek_terkait' => 'Project B',
            'deskripsi' => 'Mohon pertimbangkan desain terlampir.',
            'alasan' => 'Kebutuhan operasional baru.',
            'catatan_pengajuan' => 'Catatan tambahan dari client.',
            'solution_paper_required' => '0',
            'tanggal_pengajuan' => now()->toDateString(),
            'lampiran_pengajuan' => UploadedFile::fake()->create('blueprint-tambahan.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('change-requests.index'));
        $changeRequest = ChangeRequest::where('judul', 'Permintaan Export Laporan')->firstOrFail();
        Storage::disk('public')->assertExists($changeRequest->lampiran_pengajuan);
    }

    public function test_client_request_without_title_is_visible_on_pm_dashboard(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $pm = User::factory()->create(['role' => 'pm']);

        $this->actingAs($client)->post(route('change-requests.store'), [
            'judul' => 'Permintaan Perubahan Blueprint Portal',
            'owner_cr' => $client->name,
            'klien' => $client->name,
            'proyek_terkait' => 'Blueprint Portal Baru',
            'deskripsi' => 'Tambahkan alur approval untuk kebutuhan operasional.',
            'catatan_pengajuan' => 'Catatan kebutuhan operasional.',
            'solution_paper_required' => '0',
            'tanggal_pengajuan' => now()->toDateString(),
        ])->assertRedirect(route('change-requests.index'));

        $changeRequest = ChangeRequest::where('proyek_terkait', 'Blueprint Portal Baru')->firstOrFail();
        $this->assertSame('Permintaan Perubahan Blueprint Portal', $changeRequest->judul);
        $this->assertSame('diajukan', $changeRequest->status);

        $this->actingAs($pm)
            ->get(route('change-requests.index'))
            ->assertOk()
            ->assertSee($changeRequest->judul)
            ->assertSee('menunggu review awal');
    }

    public function test_client_cannot_access_another_clients_change_request(): void
    {
        $owner = User::factory()->create(['role' => 'client']);
        $otherClient = User::factory()->create(['role' => 'client']);
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-104',
            'judul' => 'CR privat',
            'klien' => $owner->name,
            'proyek_terkait' => 'Project A',
            'deskripsi' => 'Deskripsi kebutuhan',
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($otherClient)
            ->get(route('change-requests.show', $changeRequest))
            ->assertForbidden();
    }

    public function test_client_can_view_change_request_detail_figma_design(): void
    {
        $owner = User::factory()->create(['role' => 'client', 'name' => 'Totok Antok']);
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-09-0042',
            'judul' => 'Integrasi API Pembayaran OVO',
            'klien' => 'PT Maju Bersama',
            'owner_cr' => 'Totok Antok',
            'proyek_terkait' => 'Sistem E-commerce',
            'deskripsi' => 'Implementasi penambahan kanal pembayaran digital menggunakan e-wallet OVO',
            'tanggal_pengajuan' => now(),
            'status' => 'diajukan',
            'user_id' => $owner->id,
            'google_drive_url' => 'https://drive.google.com/drive/folders/abc123',
            'solution_paper_url' => 'https://drive.google.com/file/sp001',
        ]);

        $response = $this->actingAs($owner)
            ->get(route('change-requests.show', $changeRequest))
            ->assertOk();

        $response->assertSee('Detail Change Request');
        $response->assertSee('Melihat Data Lengkap CR');
        $response->assertSee('PROGRESS PENGERJAAN');
        $response->assertSee('Training');
        $response->assertSee('INFORMASI CR');
        $response->assertSee('DOKUMEN');
        $response->assertSee('Detail Deskripsi CR');
        $response->assertSee('Rendered WYSIWYG Content');
        $response->assertSee('ESTIMASI MANDAYS');
        $response->assertSee('CR Notes / Catatan');
        $response->assertSee('data-i18n="detail_cr_title"', false);
        $response->assertSee('data-i18n="section_info_cr"', false);
        $response->assertSee('data-i18n="section_documents"', false);
        $response->assertSee('data-i18n="section_detail_desc"', false);
        $response->assertSee('data-i18n="section_mandays"', false);
        $response->assertSee('data-i18n="section_cr_notes"', false);
        $response->assertSee('data-i18n="cr_description_text"', false);
        $response->assertSee('data-i18n="cr_scope_item_1"', false);
        $response->assertSee('data-i18n="cr_notes_quote"', false);
        $response->assertSee('data-i18n="val_company_name"', false);
        $response->assertSee('data-i18n="val_project_name"', false);
        $response->assertSee('data-i18n="mandays_analisis_val"', false);
        $response->assertSee('data-i18n="mandays_total_val"', false);
        $response->assertSee('https://drive.google.com/drive/folders/abc123', false);
        $response->assertSee('https://drive.google.com/file/sp001', false);
    }

    public function test_only_presales_can_save_estimated_work_cost(): void
    {
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-105',
            'judul' => 'CR dengan estimasi biaya',
            'klien' => 'Client Cost',
            'proyek_terkait' => 'Project Cost',
            'deskripsi' => 'Deskripsi kebutuhan',
            'tanggal_pengajuan' => now(),
            'status' => 'dianalisis',
        ]);
        $pm = User::factory()->create(['role' => 'pm']);
        $presales = User::factory()->create(['role' => 'presales']);

        $this->actingAs($pm)->put(route('change-requests.update', $changeRequest), [
            'status' => 'dikerjakan',
            'biaya_pengerjaan' => 25000000,
        ])->assertRedirect(route('change-requests.index'));
        $this->assertSame('dikerjakan', $changeRequest->refresh()->status);
        $this->assertNull($changeRequest->refresh()->biaya_pengerjaan);

        $this->actingAs($presales)->put(route('change-requests.update', $changeRequest), [
            'biaya_pengerjaan' => 25000000,
        ])->assertRedirect(route('change-requests.index'));
        $this->assertSame('25000000.00', $changeRequest->refresh()->biaya_pengerjaan);
    }

    public function test_new_client_can_register_and_is_persisted_to_database(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Bambang Client Baru',
            'company' => 'PT Makmur Sentosa Jaya',
            'email' => 'bambang@makmur.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('change-requests.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'bambang@makmur.test',
            'name' => 'Bambang Client Baru',
            'role' => User::ROLE_CLIENT,
        ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'bambang@makmur.test',
            'name' => 'Bambang Client Baru',
            'company' => 'PT Makmur Sentosa Jaya',
        ]);

        $this->assertAuthenticated();
    }

    public function test_presales_cannot_quote_before_pmh_approval_and_receives_validation_error(): void
    {
        $presales = User::factory()->create(['role' => 'presales']);
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-110',
            'judul' => 'CR belum disetujui PMH',
            'klien' => 'Client Test',
            'proyek_terkait' => 'Project A',
            'deskripsi' => 'Deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'dianalisis',
            'pmh_approved_at' => null,
        ]);

        $response = $this->actingAs($presales)
            ->from(route('change-requests.edit', $changeRequest))
            ->put(route('change-requests.update', $changeRequest), [
                'harga_penawaran' => 20000000,
                'potensi_penjualan' => 'Peluang baru',
            ]);

        $response->assertRedirect(route('change-requests.edit', $changeRequest));
        $response->assertSessionHasErrors('harga_penawaran');
        $this->assertNull($changeRequest->refresh()->harga_penawaran);
    }

    public function test_pmh_can_approve_change_request_and_advance_workflow(): void
    {
        $pmh = User::factory()->create(['role' => 'pmh', 'name' => 'Pak PM Head']);
        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-111',
            'judul' => 'CR review PMH',
            'klien' => 'Client Test',
            'proyek_terkait' => 'Project A',
            'deskripsi' => 'Deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'dianalisis',
        ]);

        $response = $this->actingAs($pmh)->put(route('change-requests.update', $changeRequest), [
            'status' => 'disetujui',
            'catatan_approval' => 'Kajian teknis sudah matang dan disetujui.',
        ]);

        $response->assertRedirect(route('change-requests.index'));
        $changeRequest->refresh();
        $this->assertSame('disetujui', $changeRequest->status);
        $this->assertNotNull($changeRequest->pmh_approved_at);
        $this->assertSame('Pak PM Head', $changeRequest->pmh_approved_by);
        $this->assertTrue($changeRequest->canBeQuoted());
    }

    public function test_dual_approval_advances_status_to_development(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $pm = User::factory()->create(['role' => 'pm']);

        $changeRequest = ChangeRequest::create([
            'kode_cr' => 'CR-2026-112',
            'judul' => 'CR dual approval',
            'klien' => $client->name,
            'proyek_terkait' => 'Project A',
            'deskripsi' => 'Deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'disetujui',
            'pmh_approved_at' => now(),
            'harga_penawaran' => 30000000,
            'user_id' => $client->id,
        ]);

        // Client approval
        $this->actingAs($client)
            ->post(route('change-requests.quotation.client', $changeRequest))
            ->assertRedirect();
        
        $changeRequest->refresh();
        $this->assertNotNull($changeRequest->quotation_approved_client_at);
        $this->assertSame('disetujui', $changeRequest->status); // Still waiting for TP

        // TP approval
        $this->actingAs($pm)
            ->post(route('change-requests.quotation.tp', $changeRequest))
            ->assertRedirect();

        $changeRequest->refresh();
        $this->assertNotNull($changeRequest->quotation_approved_tp_at);
        $this->assertSame('dikerjakan', $changeRequest->status); // Automatically advanced to development!
    }

    public function test_can_view_figma_create_cr_form_with_dropdowns_and_details(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get(route('change-requests.create'));

        $response->assertOk();
        $response->assertSee('Ajukan Change Request Baru');
        $response->assertSee('Pembuatan Pengajuan CR');
        $response->assertSee('Nama Perusahaan');
        $response->assertSee('PT Maju Bersama');
        $response->assertSee('PT Megah Jaya');
        $response->assertSee('PT Harian Bersama');
        $response->assertSee('PT Sumber Makmur');
        $response->assertSee('PT Nahkoda Biru');
        $response->assertSee('Inisial Klien');
        $response->assertSee('Nama PIC');
        $response->assertSee('Date');
        $response->assertSee('Nama Project');
        $response->assertSee('Sistem E-commerce');
        $response->assertSee('E-Procurement');
        $response->assertSee('Sistem Enterprise');
        $response->assertSee('E-Cooper');
        $response->assertSee('Data Integration');
        $response->assertSee('CR Owner');
        $response->assertSee('CR Name');
        $response->assertSee('tinyMCE');
        $response->assertSee('Klik untuk upload PDF');
        $response->assertSee('Prioritas CR');
        $response->assertSee('Normal');
        $response->assertSee('Urgent');
        $response->assertSee('CR Notes');
        $response->assertSee('Kembali');
        $response->assertSee('Submit CR');
    }

    public function test_can_submit_new_cr_using_figma_form_inputs(): void
    {
        $client = User::factory()->create(['role' => 'client', 'name' => 'Totok Antok']);

        $payload = [
            'nama_perusahaan' => 'PT Maju Bersama',
            'inisial_klien'   => 'MB',
            'nama_pic'        => 'Totok Antok',
            'nama_project'    => 'Sistem E-commerce',
            'cr_owner'        => 'Andik Virmansyah',
            'date'            => '04 Sep 2026',
            'cr_name'         => 'Integrasi API Pembayaran OVO',
            'deskripsi_cr'    => '1. LATAR BELAKANG & TUJUAN: Integrasi sistem pembayaran e-wallet.',
            'prioritas'       => 'urgent',
            'cr_notes'        => 'Catatan pengajuan CR prioritas tinggi.',
        ];

        $response = $this->actingAs($client)
            ->post(route('change-requests.store'), $payload);

        $response->assertRedirect(route('change-requests.index'));

        $this->assertDatabaseHas('change_requests', [
            'klien' => 'PT Maju Bersama',
            'proyek_terkait' => 'Sistem E-commerce',
            'judul' => 'Integrasi API Pembayaran OVO',
            'owner_cr' => 'Andik Virmansyah',
            'pic_sales' => 'Totok Antok',
            'prioritas' => 'kritis',
            'catatan_pengajuan' => 'Catatan pengajuan CR prioritas tinggi.',
            'status' => 'diajukan',
        ]);
    }
}
