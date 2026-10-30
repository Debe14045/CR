<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresalesFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createPresalesUser(): User
    {
        return User::factory()->create([
            'name' => 'Presales Demo',
            'email' => 'presales@itpi.test',
            'role' => User::ROLE_PRESALES,
            'password' => 'password',
        ]);
    }

    public function test_presales_user_logs_in_and_is_redirected_to_presales_dashboard(): void
    {
        $presales = $this->createPresalesUser();

        $response = $this->post('/login', [
            'email' => 'presales@itpi.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('presales.dashboard'));
        $this->assertAuthenticatedAs($presales);
    }

    public function test_presales_can_access_dashboard_and_sees_metrics(): void
    {
        $presales = $this->createPresalesUser();

        ChangeRequest::create([
            'kode_cr' => 'CR-2026-001',
            'judul' => 'Fitur SSO Login',
            'klien' => 'PT Delta Solusi',
            'proyek_terkait' => 'Auth System',
            'deskripsi' => 'Kebutuhan SSO',
            'status' => 'diajukan',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($presales)->get(route('presales.dashboard'));

        $response->assertOk();
        $response->assertSee('DASHBOARD');
        $response->assertSee('Pantau seluruh permintaan perubahan Anda');
        $response->assertSee('Total CR');
        $response->assertSee('Butuh Persetujuan');
        $response->assertSee('Development');
        $response->assertSee('GO LIVE');
        $response->assertSee('NOTIFIKASI');
        $response->assertSee('Grafik Request CR');
    }

    public function test_presales_can_access_semua_cr_table(): void
    {
        $presales = $this->createPresalesUser();

        ChangeRequest::create([
            'kode_cr' => 'CR-2026-002',
            'judul' => 'Integrasi Payment Gateway',
            'klien' => 'PT Maju Bersama',
            'proyek_terkait' => 'E-Commerce',
            'deskripsi' => 'Payment Gateway',
            'status' => 'development',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($presales)->get(route('presales.change-requests'));

        $response->assertOk();
        $response->assertSee('Change Request');
        $response->assertSee('Semua CR');
        $response->assertSee('Integrasi Payment Gateway');
        $response->assertSee('PT Maju Bersama');
        $response->assertSee('Review');
    }

    public function test_presales_can_access_persetujuan_page(): void
    {
        $presales = $this->createPresalesUser();

        $response = $this->actingAs($presales)->get(route('presales.persetujuan'));

        $response->assertOk();
        $response->assertSee('Butuh Persetujuan');
    }

    public function test_presales_can_access_development_page(): void
    {
        $presales = $this->createPresalesUser();

        $response = $this->actingAs($presales)->get(route('presales.development'));

        $response->assertOk();
        $response->assertSee('Development');
    }

    public function test_presales_can_access_golive_page(): void
    {
        $presales = $this->createPresalesUser();

        $response = $this->actingAs($presales)->get(route('presales.golive'));

        $response->assertRedirect(route('presales.change-requests', ['status' => 'golive']));
    }

    public function test_client_cannot_access_presales_dashboard(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $response = $this->actingAs($client)->get(route('presales.dashboard'));

        $response->assertForbidden();
    }

    public function test_presales_title_changes_dynamically_with_status_filter(): void
    {
        $presales = $this->createPresalesUser();

        // 1. Diajukan
        $res = $this->actingAs($presales)->get(route('presales.change-requests', ['status' => 'diajukan']));
        $res->assertOk();
        $res->assertSee('Diajukan');
        $res->assertSee('Pantau seluruh permintaan perubahan berstatus Diajukan');

        // 2. Analysis
        $res = $this->actingAs($presales)->get(route('presales.change-requests', ['status' => 'analisa']));
        $res->assertOk();
        $res->assertSee('Analysis');
        $res->assertSee('Pantau seluruh permintaan perubahan berstatus Analysis');

        // 3. Develop
        $res = $this->actingAs($presales)->get(route('presales.change-requests', ['status' => 'development']));
        $res->assertOk();
        $res->assertSee('Develop');
        $res->assertSee('Pantau seluruh permintaan perubahan berstatus Develop');

        // 4. Go Live
        $res = $this->actingAs($presales)->get(route('presales.change-requests', ['status' => 'golive']));
        $res->assertOk();
        $res->assertSee('Go Live');
        $res->assertSee('Pantau seluruh permintaan perubahan berstatus Go Live');

        // 5. Default (Semua Status)
        $res = $this->actingAs($presales)->get(route('presales.change-requests'));
        $res->assertOk();
        $res->assertSee('Change Request');
        $res->assertSee('Pantau seluruh permintaan perubahan Anda');
    }

    public function test_landing_page_displays_masuk_akun_and_buat_akun(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Masuk Akun');
        $response->assertSee('Buat Akun');
    }

    public function test_presales_review_top_defaults_to_empty_and_omits_total_harga_input(): void
    {
        $presales = User::factory()->create([
            'role' => User::ROLE_PRESALES,
        ]);

        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-099',
            'judul' => 'Review Quotation Test',
            'klien' => 'PT Test',
            'proyek_terkait' => 'Core App',
            'deskripsi' => 'Testing quotation',
            'status' => 'awaiting_quotation',
            'pm_head_approved_at' => now(),
            'solution_paper' => 'dummy.pdf',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($presales)->get(route('presales.review', $cr));
        $response->assertOk();
        $response->assertSee('Belum ada Termin Pembayaran (TOP)');
        $response->assertDontSee('id="totalHargaField"', false);
    }

    public function test_presales_can_save_quotation_with_custom_termin_milestones(): void
    {
        $presales = User::factory()->create([
            'role' => User::ROLE_PRESALES,
        ]);

        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-100',
            'judul' => 'Save Quotation Test',
            'klien' => 'PT Test',
            'proyek_terkait' => 'Core App',
            'deskripsi' => 'Testing save quotation',
            'status' => 'awaiting_quotation',
            'pm_head_approved_at' => now(),
            'solution_paper' => 'dummy.pdf',
            'tanggal_pengajuan' => now(),
        ]);

        $terminJson = json_encode([
            [
                'name' => 'Termin 1 (DP)',
                'badge' => 'DP 30%',
                'deliverable' => 'Kickoff & Spesifikasi Teknis',
                'nominal' => 15000000,
            ],
            [
                'name' => 'Termin 2 (Pelunasan)',
                'badge' => 'Pelunasan 70%',
                'deliverable' => 'UAT & Go-Live sign-off',
                'nominal' => 35000000,
            ],
        ]);

        $response = $this->actingAs($presales)->post(route('change-requests.quotation.save', $cr), [
            'harga_penawaran' => 50000000,
            'biaya_pengerjaan' => 45045045,
            'termin_data' => $terminJson,
        ]);

        $response->assertRedirect();
        $cr->refresh();

        $this->assertEquals(50000000, (int) $cr->harga_penawaran);
        $this->assertCount(2, $cr->invoices);
        $this->assertEquals(15000000, (int) $cr->invoices[0]->nominal);
        $this->assertEquals(35000000, (int) $cr->invoices[1]->nominal);
    }

    public function test_pm_stepper_track_is_constrained_between_nodes(): void
    {
        $pm = User::factory()->create([
            'role' => User::ROLE_PM,
        ]);

        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-101',
            'judul' => 'Stepper Test',
            'klien' => 'PT Test',
            'proyek_terkait' => 'Core App',
            'deskripsi' => 'Testing stepper track',
            'status' => 'golive',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($pm)->get(route('change-requests.show', $cr));
        $response->assertOk();
        $response->assertSee('left: 30px; right: 30px; height: 3px;', false);
        $response->assertSee('100% — Go Live');
    }

    public function test_presales_detail_cr_shows_development_status_stepper(): void
    {
        $presales = User::factory()->create([
            'role' => User::ROLE_PRESALES,
        ]);

        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-102',
            'judul' => 'Presales Stepper Test',
            'klien' => 'PT Test',
            'proyek_terkait' => 'Core App',
            'deskripsi' => 'Testing presales status development',
            'status' => 'development',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($presales)->get(route('presales.review', $cr));
        $response->assertOk();
        $response->assertSee('PROGRESS PENGERJAAN TEKNIS (STATUS DEVELOPMENT)');
        $response->assertDontSee('modalUpdateStatusDev');
        $response->assertSee('Develop');
    }

    public function test_updating_status_updates_all_dashboards(): void
    {
        $presales = User::factory()->create([
            'role' => User::ROLE_PRESALES,
        ]);
        $pm = User::factory()->create([
            'role' => User::ROLE_PM,
        ]);
        $pmh = User::factory()->create([
            'role' => User::ROLE_PMH,
        ]);

        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-103',
            'judul' => 'Dashboard Status Sync Test',
            'klien' => 'PT Sync',
            'proyek_terkait' => 'Core App',
            'deskripsi' => 'Testing dashboard update sync',
            'status' => 'development',
            'tanggal_pengajuan' => now(),
        ]);

        // 1. Check Presales Dashboard initially sees it in Development
        $resPresalesDash = $this->actingAs($presales)->get(route('presales.dashboard'));
        $resPresalesDash->assertOk();
        $this->assertEquals(1, $resPresalesDash->viewData('figmaDevCr'));

        // 2. PM updates status to 'sit' (Presales is view-only for development progress)
        $response = $this->actingAs($pm)->put(route('change-requests.update', $cr), [
            'from_tab' => 'presales_status',
            'status' => 'sit',
        ]);
        $response->assertRedirect(route('presales.review', [$cr, 'tab' => 'detail']));

        $cr->refresh();
        $this->assertEquals('sit', $cr->status);

        // 3. Verify all dashboards reflect the updated status
        // A. Presales Dashboard Development count still includes SIT
        $resPresalesDashAfter = $this->actingAs($presales)->get(route('presales.dashboard'));
        $resPresalesDashAfter->assertOk();
        $this->assertEquals(1, $resPresalesDashAfter->viewData('figmaDevCr'));

        // B. Presales Development Table shows this CR
        $resPresalesDev = $this->actingAs($presales)->get(route('presales.development'));
        $resPresalesDev->assertOk();
        $resPresalesDev->assertSee('CR-2026-103');
        $resPresalesDev->assertSee('SIT');

        // C. PM Dashboard Development View includes this CR
        $resPmDev = $this->actingAs($pm)->get(route('change-requests.index', ['view' => 'development']));
        $resPmDev->assertOk();
        $this->assertEquals(1, $resPmDev->viewData('figmaDevCr'));
        $resPmDev->assertSee('CR-2026-103');

        // D. PM Head Development page includes this CR
        $resPmhDev = $this->actingAs($pmh)->get(route('pmh.development'));
        $resPmhDev->assertOk();
        $resPmhDev->assertSee('CR-2026-103');
    }
}

