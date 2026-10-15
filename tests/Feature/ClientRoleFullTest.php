<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientRoleFullTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Find or create primary demo client
        $this->client = User::firstOrCreate(
            ['email' => 'client@itpi.test'],
            [
                'name' => 'Totok Antok',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_active' => true,
            ]
        );
    }

    /**
     * 1. Dashboard redirect to change-requests
     */
    public function test_client_dashboard_redirects_to_change_requests(): void
    {
        $response = $this->actingAs($this->client)->get('/dashboard');
        $response->assertRedirect(route('change-requests.index'));
    }

    /**
     * 2. View Semua CR and tabs
     */
    public function test_client_can_view_all_cr_and_filter_tabs(): void
    {
        \App\Models\ChangeRequest::create([
            'kode_cr' => 'CR-2026-001',
            'judul' => 'Integrasi Payment Gateway',
            'deskripsi' => 'Deskripsi CR',
            'proyek_terkait' => 'E-Commerce',
            'klien' => 'PT Maju Bersama',
            'user_id' => $this->client->id,
            'status' => 'diajukan',
            'prioritas' => 'normal',
            'tanggal_pengajuan' => now(),
        ]);

        // Default index
        $res = $this->actingAs($this->client)->get(route('change-requests.index'));
        $res->assertOk();
        $res->assertSee('Semua CR');
        $res->assertSee('Review');

        // Total CR view
        $resTotal = $this->actingAs($this->client)->get(route('change-requests.index', ['view' => 'total']));
        $resTotal->assertOk();
        $resTotal->assertSee('Semua CR');

        // Category filter tabs
        foreach (['pending', 'progress', 'payment', 'history'] as $tab) {
            $resTab = $this->actingAs($this->client)->get(route('change-requests.index', ['tab' => $tab]));
            $resTab->assertOk();
        }

        // Search feature
        $resSearch = $this->actingAs($this->client)->get(route('change-requests.index', ['search' => 'Integrasi']));
        $resSearch->assertOk();
    }

    /**
     * 3. View Draft CR page
     */
    public function test_client_can_view_draft_cr_page_with_actions(): void
    {
        \App\Models\ChangeRequest::create([
            'kode_cr' => 'CR-DRAFT-001',
            'judul' => 'Fitur SSO Login Karyawan',
            'deskripsi' => 'Draft description',
            'proyek_terkait' => 'Authentication',
            'klien' => 'PT Maju Bersama',
            'user_id' => $this->client->id,
            'status' => 'draft',
            'prioritas' => 'normal',
            'tanggal_pengajuan' => now(),
        ]);

        $res = $this->actingAs($this->client)->get(route('change-requests.index', ['view' => 'draft']));
        $res->assertOk();
        $res->assertSee('DRAFT CR');
        $res->assertSee('Edit');
        $res->assertSee('Review');
        $res->assertSee('Hapus');
    }

    /**
     * 4. View Create CR form and submit new CR
     */
    public function test_client_can_view_and_submit_new_cr(): void
    {
        // View create form
        $resCreate = $this->actingAs($this->client)->get(route('change-requests.create'));
        $resCreate->assertOk();
        $resCreate->assertSee('Ajukan Change Request Baru');
        $resCreate->assertSee('PT Maju Bersama');
        $resCreate->assertSee('Submit CR');

        // Submit form
        $payload = [
            'nama_perusahaan' => 'PT Maju Bersama',
            'inisial_klien'   => 'MB',
            'nama_pic'        => 'Totok Antok',
            'date'            => '04 Sep 2026',
            'request_date'    => '18 Sep 2026',
            'nama_project'    => 'Sistem E-commerce',
            'cr_owner'        => 'Andik Virmansyah',
            'cr_name'         => 'Test CR Baru Dari Client Automated Test',
            'deskripsi_cr'    => 'Deskripsi lengkap perubahan modul pembayaran e-wallet.',
            'prioritas'       => 'urgent',
            'cr_notes'        => 'Catatan khusus untuk QA dan Developer.',
        ];

        $resSubmit = $this->actingAs($this->client)->post(route('change-requests.store'), $payload);
        $resSubmit->assertRedirect(route('change-requests.index'));

        // Verify stored in DB
        $this->assertDatabaseHas('change_requests', [
            'judul' => 'Test CR Baru Dari Client Automated Test',
            'user_id' => $this->client->id,
            'status' => 'diajukan',
        ]);
    }

    /**
     * 5. Review Change Request Detail page (Figma pixel-perfect view)
     */
    public function test_client_can_view_review_detail_page_without_internal_verification(): void
    {
        $cr = ChangeRequest::where('user_id', $this->client->id)->first();
        if (!$cr) {
            $cr = ChangeRequest::create([
                'kode_cr' => 'CR-2026-09-0042',
                'judul' => 'Integrasi API Pembayaran OVO',
                'klien' => 'PT Maju Bersama',
                'proyek_terkait' => 'Sistem E-commerce',
                'deskripsi' => 'Deskripsi integrasi API OVO',
                'tanggal_pengajuan' => now(),
                'user_id' => $this->client->id,
                'status' => 'diajukan',
            ]);
        }

        $response = $this->actingAs($this->client)->get(route('change-requests.show', $cr));
        $response->assertOk();

        // Assert header and core structure
        $response->assertSee('Detail Change Request');
        $response->assertSee('Melihat Data Lengkap CR');
        $response->assertSee('• Analisa');
        $response->assertSee('Menunggu Review');
        $response->assertSee('STATUS ALUR CR');
        $response->assertSee('Total Progres Alur: 17%');
        $response->assertSee('Sedang Berjalan');
        $response->assertSee('INFORMASI CR');
        $response->assertSee('DOKUMEN');
        $response->assertSee('Detail Deskripsi CR');
        $response->assertSee('ESTIMASI MANDAYS');
        $response->assertSee('CR Notes / Catatan');

        // Strictly assert internal verification and extra tabs are NOT present for client
        $response->assertDontSee('Panel Verifikasi Change Request');
        $response->assertDontSee('tabPanel-verifikasi');
    }

    /**
     * 6. Edit Draft Change Request
     */
    public function test_client_can_edit_and_update_draft_cr(): void
    {
        $draft = ChangeRequest::where('status', 'draft')->where('user_id', $this->client->id)->first();
        if (!$draft) {
            $draft = ChangeRequest::create([
                'kode_cr' => 'CR-DRAFT-TEST',
                'judul' => 'Draft Original Title',
                'klien' => 'PT Maju Bersama',
                'proyek_terkait' => 'Sistem E-commerce',
                'deskripsi' => 'Deskripsi draft',
                'tanggal_pengajuan' => now(),
                'user_id' => $this->client->id,
                'status' => 'draft',
            ]);
        }

        // View edit page
        $resEdit = $this->actingAs($this->client)->get(route('change-requests.edit', $draft));
        $resEdit->assertOk();
        $resEdit->assertSee('Edit Draft Change Request');

        // Submit update
        $resUpdate = $this->actingAs($this->client)->put(route('change-requests.update', $draft), [
            'cr_name' => 'Draft Updated Title Automated',
            'deskripsi_cr' => 'Updated draft description.',
            'prioritas' => 'normal',
        ]);

        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('change_requests', [
            'id' => $draft->id,
            'judul' => 'Draft Updated Title Automated',
        ]);
    }

    /**
     * 7. Delete Draft Change Request
     */
    public function test_client_can_delete_draft_cr(): void
    {
        $draft = ChangeRequest::create([
            'kode_cr' => 'CR-DRAFT-DELETE-TEST',
            'judul' => 'Draft To Be Deleted',
            'klien' => 'PT Maju Bersama',
            'proyek_terkait' => 'Sistem E-commerce',
            'deskripsi' => 'Deskripsi untuk dihapus',
            'tanggal_pengajuan' => now(),
            'user_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $resDelete = $this->actingAs($this->client)->delete(route('change-requests.destroy', $draft));
        $resDelete->assertRedirect(route('change-requests.index', ['view' => 'draft']));

        $this->assertDatabaseMissing('change_requests', [
            'id' => $draft->id,
        ]);
    }

    /**
     * 8. Client Profile and Password Update
     */
    public function test_client_can_view_profile_and_update_password(): void
    {
        // View profile
        $resProfile = $this->actingAs($this->client)->get(route('client.profile'));
        $resProfile->assertOk();
        $resProfile->assertSee('Profile');
        $resProfile->assertSee('KEAMANAN');

        // Update password with valid credentials
        $resPassword = $this->actingAs($this->client)->post(route('client.profile.password'), [
            'old_password' => 'password',
            'password' => 'newpassword123',
        ]);

        $resPassword->assertRedirect();
        $this->client->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->client->password));

        // Revert back to 'password' for demo consistency
        $this->client->update(['password' => Hash::make('password')]);
    }

    /**
     * 9. Client can logout
     */
    public function test_client_can_logout(): void
    {
        $resLogout = $this->actingAs($this->client)->post(route('logout'));
        $resLogout->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * 10. Client can register new account with Get Started button and data is saved to DB
     */
    public function test_client_can_register_new_account_with_get_started(): void
    {
        $uniqueEmail = 'bambang_' . uniqid() . '@perusahaan.test';
        $payload = [
            'name'     => 'Bambang Sudirman',
            'email'    => $uniqueEmail,
            'password' => 'secret123',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('change-requests.index'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'name'  => 'Bambang Sudirman',
            'role'  => 'client',
        ]);

        $this->assertDatabaseHas('clients', [
            'email'   => $uniqueEmail,
            'company' => 'PT Bambang Sudirman',
        ]);
    }
}
