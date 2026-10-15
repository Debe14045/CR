<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\Client;
use App\Models\MasterPegawai;
use App\Models\MasterStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllPagesAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $pm;
    private User $pmh;
    private User $presales;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::factory()->create(['role' => 'client', 'name' => 'Klien Demo']);
        $this->pm = User::factory()->create(['role' => 'pm', 'name' => 'PM Demo']);
        $this->pmh = User::factory()->create(['role' => 'pmh', 'name' => 'PM Head Demo']);
        $this->presales = User::factory()->create(['role' => 'presales', 'name' => 'Presales Demo']);
        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Demo']);

        // Create sample CR in each state
        ChangeRequest::create([
            'kode_cr' => 'CR-AUDIT-001',
            'judul' => 'CR Pengujian Integrasi Portal',
            'klien' => 'PT Demo Mitra',
            'proyek_terkait' => 'Core Portal',
            'deskripsi' => 'Pengujian lengkap sistem',
            'status' => 'development',
            'prioritas' => 'normal',
            'user_id' => $this->client->id,
            'tanggal_pengajuan' => now(),
            'biaya_pengerjaan' => 20000000,
            'harga_penawaran' => 25000000,
        ]);
    }

    public function test_guest_can_access_public_auth_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/forgot-password')->assertOk();
        $this->get('/check-email')->assertOk();
    }

    public function test_client_pages_load_without_errors(): void
    {
        $this->actingAs($this->client)->get('/change-requests')->assertOk();
        $this->actingAs($this->client)->get('/change-requests?view=total')->assertOk();
        $this->actingAs($this->client)->get('/change-requests?view=draft')->assertOk();
        $this->actingAs($this->client)->get('/change-requests/create')->assertOk();
        $this->actingAs($this->client)->get('/profile')->assertOk();
        $this->actingAs($this->client)->get('/notifications')->assertOk();
    }

    public function test_pm_pages_load_without_errors(): void
    {
        $this->actingAs($this->pm)->get('/change-requests')->assertOk();
        $this->actingAs($this->pm)->get('/change-requests?view=dashboard')->assertOk();
        $this->actingAs($this->pm)->get('/change-requests?view=total')->assertOk();
        $this->actingAs($this->pm)->get('/change-requests?view=draft')->assertOk();
        $this->actingAs($this->pm)->get('/change-requests/create')->assertOk();
        $this->actingAs($this->pm)->get('/dev-board')->assertOk();
        $this->actingAs($this->pm)->get('/profile')->assertOk();
        $this->actingAs($this->pm)->get('/notifications')->assertOk();
    }

    public function test_pmh_pages_load_without_errors(): void
    {
        $this->actingAs($this->pmh)->get('/pm-head/change-requests')->assertOk();
        $this->actingAs($this->pmh)->get('/pm-head/persetujuan')->assertOk();
        $this->actingAs($this->pmh)->get('/pm-head/development')->assertOk();
        $this->actingAs($this->pmh)->get('/pm-head/golive')->assertOk();
        $this->actingAs($this->pmh)->get('/pm-head/outstanding-payment')->assertOk();
        $this->actingAs($this->pmh)->get('/pm-head/notifikasi')->assertOk();
        $this->actingAs($this->pmh)->get('/profile')->assertOk();
    }

    public function test_presales_pages_load_without_errors(): void
    {
        $this->actingAs($this->presales)->get('/presales/dashboard')->assertOk();
        $this->actingAs($this->presales)->get('/presales/change-requests')->assertOk();
        $this->actingAs($this->presales)->get('/presales/persetujuan')->assertOk();
        $this->actingAs($this->presales)->get('/presales/golive')->assertRedirect(route('presales.change-requests', ['status' => 'golive']));
        $this->actingAs($this->presales)->get('/presales/notifikasi')->assertOk();
        $this->actingAs($this->presales)->get('/profile')->assertOk();
    }

    public function test_admin_master_pages_load_without_errors(): void
    {
        $this->actingAs($this->admin)->get('/master/clients')->assertOk();
        $this->actingAs($this->admin)->get('/master/pegawai')->assertOk();
        $this->actingAs($this->admin)->get('/master/status')->assertOk();
        $this->actingAs($this->admin)->get('/dev-board')->assertOk();
    }
}
