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
}
