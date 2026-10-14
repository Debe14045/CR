<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\Client;
use App\Models\CrInvoice;
use App\Models\MasterPegawai;
use App\Models\MasterStatus;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AllRolesEndToEndAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $clientUser;
    private User $pmUser;
    private User $pmhUser;
    private User $presalesUser;
    private User $adminUser;
    private Client $demoClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->demoClient = Client::create([
            'name' => 'PT Maju Bersama',
            'company' => 'PT Maju Bersama',
            'nickname' => 'MB',
            'email' => 'client@itpi.test',
            'active' => true,
        ]);

        $this->clientUser = User::create([
            'name' => 'Totok Antok',
            'email' => 'client@itpi.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
        ]);

        $this->pmUser = User::create([
            'name' => 'PM Demo',
            'email' => 'pm@itpi.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PM,
        ]);

        $this->pmhUser = User::create([
            'name' => 'PM Head Demo',
            'email' => 'pmh@itpi.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PMH,
        ]);

        $this->presalesUser = User::create([
            'name' => 'Presales Demo',
            'email' => 'presales@itpi.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PRESALES,
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@itpi.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * 1. CLIENT ROLE AUDIT
     */
    public function test_client_complete_flow(): void
    {
        // Login
        $loginRes = $this->post('/login', [
            'email' => 'client@itpi.test',
            'password' => 'password',
        ]);
        $loginRes->assertRedirect(route('change-requests.index'));
        $this->assertAuthenticatedAs($this->clientUser);

        // Dashboard
        $dash = $this->actingAs($this->clientUser)->get(route('change-requests.index'));
        $dash->assertOk();
        $dash->assertSee('DASHBOARD');
        $dash->assertSee('Semua CR');
        $dash->assertSee('Development');
        $dash->assertSee('UAT');
        $dash->assertSee('GO LIVE');

        // Semua CR Table
        $allCr = $this->actingAs($this->clientUser)->get(route('change-requests.index', ['view' => 'total']));
        $allCr->assertOk();
        $allCr->assertSee('Change Request');
        $allCr->assertSee('Semua Status');

        // Dynamic Status Title check
        foreach ([
            'diajukan' => 'Diajukan',
            'analisa' => 'Analysis',
            'development' => 'Develop',
            'uat' => 'UAT',
            'golive' => 'Go-Live',
        ] as $stParam => $expectedTitle) {
            $filterRes = $this->actingAs($this->clientUser)->get(route('change-requests.index', ['view' => 'total', 'status' => $stParam]));
            $filterRes->assertOk();
            $filterRes->assertSee($expectedTitle);
            $filterRes->assertSee('Pantau seluruh permintaan perubahan berstatus ' . $expectedTitle);
        }

        // Create CR Form
        $createForm = $this->actingAs($this->clientUser)->get(route('change-requests.create'));
        $createForm->assertOk();
        $createForm->assertSee('Ajukan Change Request Baru');

        // Submit New CR into Database
        $submitRes = $this->actingAs($this->clientUser)->post(route('change-requests.store'), [
            'nama_perusahaan' => 'PT Maju Bersama',
            'inisial_klien' => 'MB',
            'nama_pic' => 'Totok Antok',
            'date' => now()->format('d M Y'),
            'judul' => 'Audit Test CR Baru',
            'proyek_terkait' => 'Core System',
            'deskripsi' => 'Deskripsi kebutuhan audit sistem change request',
            'alasan' => 'Audit kepatuhan alur kerja',
            'prioritas' => 'normal',
        ]);
        $submitRes->assertRedirect(route('change-requests.index'));
        $this->assertDatabaseHas('change_requests', [
            'judul' => 'Audit Test CR Baru',
            'status' => 'diajukan',
        ]);

        $createdCr = ChangeRequest::where('judul', 'Audit Test CR Baru')->firstOrFail();

        // Save Draft CR into Database
        $draftSubmit = $this->actingAs($this->clientUser)->post(route('change-requests.store'), [
            'action' => 'draft',
            'nama_perusahaan' => 'PT Maju Bersama',
            'inisial_klien' => 'MB',
            'nama_pic' => 'Totok Antok',
            'date' => now()->format('d M Y'),
            'judul' => 'Draft Audit CR',
            'proyek_terkait' => 'Draft System',
            'deskripsi' => 'Draft test',
        ]);
        $draftSubmit->assertRedirect(route('change-requests.index', ['view' => 'draft']));
        $this->assertDatabaseHas('change_requests', [
            'judul' => 'Draft Audit CR',
            'status' => 'draft',
        ]);

        $draftCr = ChangeRequest::where('judul', 'Draft Audit CR')->firstOrFail();

        // View Draft CR Page
        $draftPage = $this->actingAs($this->clientUser)->get(route('change-requests.index', ['view' => 'draft']));
        $draftPage->assertOk();
        $draftPage->assertSee('DRAFT CR');
        $draftPage->assertSee('Draft Audit CR');
        $draftPage->assertSee('Hapus draft ini?');

        // Delete Draft CR
        $delRes = $this->actingAs($this->clientUser)->delete(route('change-requests.destroy', $draftCr->id));
        $delRes->assertRedirect();
        $this->assertDatabaseMissing('change_requests', ['id' => $draftCr->id]);

        // CR Detail
        $detail = $this->actingAs($this->clientUser)->get(route('change-requests.show', $createdCr->id));
        $detail->assertOk();
        $detail->assertSee('Audit Test CR Baru');

        // Send Client Message
        $msgRes = $this->actingAs($this->clientUser)->post(route('change-requests.message', $createdCr->id), [
            'pesan_client' => 'Halo tim IT, mohon dicek segera.',
        ]);
        $msgRes->assertRedirect();
        $this->assertDatabaseHas('change_requests', [
            'id' => $createdCr->id,
            'pesan_client' => 'Halo tim IT, mohon dicek segera.',
        ]);

        // Notifications
        $notif = $this->actingAs($this->clientUser)->get(route('notifications.index'));
        $notif->assertOk();
        $notif->assertSee('Notifikasi');

        // Profile
        $profile = $this->actingAs($this->clientUser)->get(route('profile'));
        $profile->assertOk();
        $profile->assertSee('Totok Antok');
    }

    /**
     * 2. PM ROLE AUDIT
     */
    public function test_pm_complete_flow(): void
    {
        // Login PM
        $loginRes = $this->post('/login', [
            'email' => 'pm@itpi.test',
            'password' => 'password',
        ]);
        $loginRes->assertRedirect(route('change-requests.index'));
        $this->assertAuthenticatedAs($this->pmUser);

        // Dashboard PM
        $dash = $this->actingAs($this->pmUser)->get(route('change-requests.index'));
        $dash->assertOk();
        $dash->assertSee('DASHBOARD');
        $dash->assertSee('Butuh Persetujuan');

        // Dev Board
        $devBoard = $this->actingAs($this->pmUser)->get(route('change-requests.devboard'));
        $devBoard->assertOk();
        $devBoard->assertSee('Dev Board');

        // Create sample CR for PM review
        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-PM-001',
            'judul' => 'PM Feature Review',
            'klien' => 'PT Maju Bersama',
            'proyek_terkait' => 'Mobile App',
            'deskripsi' => 'Testing PM workflow',
            'status' => 'analisa',
            'prioritas' => 'normal',
            'tanggal_pengajuan' => now(),
        ]);

        // PM Views Detail
        $detail = $this->actingAs($this->pmUser)->get(route('change-requests.show', $cr->id));
        $detail->assertOk();
        $detail->assertSee('PM Feature Review');

        // PM Sequential Status Update
        $seqRes = $this->actingAs($this->pmUser)->post(route('change-requests.sequential-status', $cr->id), [
            'status' => 'development',
            'catatan_status' => 'Progres development sprint 1 dimulai.',
        ]);
        $seqRes->assertRedirect();
        $this->assertDatabaseHas('change_requests', [
            'id' => $cr->id,
            'status' => 'development',
        ]);

        // Profile
        $profile = $this->actingAs($this->pmUser)->get(route('profile'));
        $profile->assertOk();
    }

    /**
     * 3. PM HEAD (PMH) ROLE AUDIT
     */
    public function test_pmh_complete_flow(): void
    {
        // Login PMH
        $loginRes = $this->post('/login', [
            'email' => 'pmh@itpi.test',
            'password' => 'password',
        ]);
        $loginRes->assertRedirect(route('pmh.change-requests'));
        $this->assertAuthenticatedAs($this->pmhUser);

        // Dashboard / Semua CR
        $semuaCr = $this->actingAs($this->pmhUser)->get(route('pmh.change-requests'));
        $semuaCr->assertOk();
        $semuaCr->assertSee('Change Request');
        $semuaCr->assertSee('Semua CR');
        $semuaCr->assertSee('CR Aktif');
        $semuaCr->assertSee('CR Selesai');

        // Butuh Persetujuan
        $persetujuan = $this->actingAs($this->pmhUser)->get(route('pmh.persetujuan'));
        $persetujuan->assertOk();
        $persetujuan->assertSee('Butuh Persetujuan');

        // Development
        $dev = $this->actingAs($this->pmhUser)->get(route('pmh.development'));
        $dev->assertOk();
        $dev->assertSee('Development');

        // Go Live
        $golive = $this->actingAs($this->pmhUser)->get(route('pmh.golive'));
        $golive->assertOk();
        $golive->assertSee('GO LIVE');

        // Outstanding Payment
        $op = $this->actingAs($this->pmhUser)->get(route('pmh.outstanding-payment'));
        $op->assertOk();
        $op->assertSee('OUTSTANDING PAYMENT');
        $op->assertSee('Total Tagihan');

        // Outstanding Payment Export
        $export = $this->actingAs($this->pmhUser)->get(route('pmh.outstanding-payment.export'));
        $export->assertOk();

        // Sample CR for PMH Decision
        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-PMH-001',
            'judul' => 'Approval CR Test',
            'klien' => 'PT Delta Solusi',
            'proyek_terkait' => 'Security',
            'deskripsi' => 'Butuh approval PM Head',
            'status' => 'awaiting_pmh',
            'prioritas' => 'urgent',
            'tanggal_pengajuan' => now(),
        ]);

        // Review Page
        $reviewPage = $this->actingAs($this->pmhUser)->get(route('pmh.review', $cr->id));
        $reviewPage->assertOk();
        $reviewPage->assertSee('Approval CR Test');

        // Decision: Setuju (action => approve)
        $decisionRes = $this->actingAs($this->pmhUser)->post(route('pmh.decision', $cr->id), [
            'action' => 'approve',
            'catatan_approval' => 'Disetujui oleh PM Head.',
        ]);
        $decisionRes->assertRedirect(route('pmh.persetujuan'));
        $this->assertDatabaseHas('change_requests', [
            'id' => $cr->id,
            'status' => 'validated',
        ]);

        // Notifikasi
        $notif = $this->actingAs($this->pmhUser)->get(route('pmh.notifications'));
        $notif->assertOk();
        $notif->assertSee('NOTIFIKASI');

        // Profile
        $profile = $this->actingAs($this->pmhUser)->get(route('profile'));
        $profile->assertOk();
    }

    /**
     * 4. PRESALES ROLE AUDIT
     */
    public function test_presales_complete_flow(): void
    {
        // Login Presales
        $loginRes = $this->post('/login', [
            'email' => 'presales@itpi.test',
            'password' => 'password',
        ]);
        $loginRes->assertRedirect(route('presales.dashboard'));
        $this->assertAuthenticatedAs($this->presalesUser);

        // Dashboard
        $dash = $this->actingAs($this->presalesUser)->get(route('presales.dashboard'));
        $dash->assertOk();
        $dash->assertSee('DASHBOARD');
        $dash->assertSee('Total CR');
        $dash->assertSee('Butuh Persetujuan');
        $dash->assertSee('Development');

        // Semua CR
        $allCr = $this->actingAs($this->presalesUser)->get(route('presales.change-requests'));
        $allCr->assertOk();
        $allCr->assertSee('Change Request');
        $allCr->assertSee('Semua Status');

        // Butuh Persetujuan
        $persetujuan = $this->actingAs($this->presalesUser)->get(route('presales.persetujuan'));
        $persetujuan->assertOk();
        $persetujuan->assertSee('Butuh Persetujuan');

        // Development
        $dev = $this->actingAs($this->presalesUser)->get(route('presales.development'));
        $dev->assertOk();
        $dev->assertSee('Development');

        // Sample CR for Presales Review & Quotation
        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-PRE-001',
            'judul' => 'Presales Estimasi & Quotation',
            'klien' => 'PT Maju Bersama',
            'proyek_terkait' => 'Finance Core',
            'deskripsi' => 'Presales review test',
            'status' => 'analisa',
            'prioritas' => 'normal',
            'tanggal_pengajuan' => now(),
        ]);

        // Review Page
        $review = $this->actingAs($this->presalesUser)->get(route('presales.review', $cr->id));
        $review->assertOk();
        $review->assertSee('Presales Estimasi & Quotation');

        // Save Quotation
        $quoteRes = $this->actingAs($this->presalesUser)->post(route('change-requests.quotation.save', $cr->id), [
            'biaya_pengerjaan' => 50000000,
            'harga_penawaran' => 65000000,
            'estimasi_waktu' => '30 Hari',
            'estimasi_resource' => '2 Frontend, 2 Backend',
        ]);
        $quoteRes->assertRedirect();
        $this->assertDatabaseHas('change_requests', [
            'id' => $cr->id,
            'biaya_pengerjaan' => 50000000,
            'harga_penawaran' => 65000000,
        ]);

        // Prepare CR for invoicing (must be in golive stage)
        $cr->update([
            'status' => 'golive',
            'golive_validated_at' => now(),
        ]);

        // Create Invoice
        $invRes = $this->actingAs($this->presalesUser)->post(route('change-requests.invoices.store', $cr->id), [
            'invoice_number' => 'INV-2026-001',
            'invoice_date' => now()->toDateString(),
            'nominal' => 65000000,
            'job_description' => 'Termin 1 Pembayaran Finance Core',
            'planned_payment_date' => now()->addDays(14)->toDateString(),
            'payment_status' => 'pending',
        ]);
        $invRes->assertRedirect();
        $this->assertDatabaseHas('cr_invoices', [
            'change_request_id' => $cr->id,
            'invoice_number' => 'INV-2026-001',
            'nominal' => 65000000,
        ]);

        $invoice = CrInvoice::where('invoice_number', 'INV-2026-001')->firstOrFail();

        // Update Invoice Status
        $invStatRes = $this->actingAs($this->presalesUser)->put(route('change-requests.invoices.status', $invoice->id), [
            'payment_status' => 'paid',
        ]);
        $invStatRes->assertRedirect();
        $this->assertDatabaseHas('cr_invoices', [
            'id' => $invoice->id,
            'payment_status' => 'paid',
        ]);

        // Notifikasi
        $notif = $this->actingAs($this->presalesUser)->get(route('presales.notifications'));
        $notif->assertOk();
        $notif->assertSee('NOTIFIKASI');

        // Profile
        $profile = $this->actingAs($this->presalesUser)->get(route('profile'));
        $profile->assertOk();
    }

    /**
     * 5. ADMIN ROLE AUDIT
     */
    public function test_admin_complete_flow(): void
    {
        // Login Admin
        $loginRes = $this->post('/login', [
            'email' => 'admin@itpi.test',
            'password' => 'password',
        ]);
        $loginRes->assertRedirect(route('change-requests.index'));
        $this->assertAuthenticatedAs($this->adminUser);

        // Dashboard
        $dash = $this->actingAs($this->adminUser)->get(route('change-requests.index'));
        $dash->assertOk();

        // Master Clients Index
        $mc = $this->actingAs($this->adminUser)->get(route('master.clients.index'));
        $mc->assertOk();
        $mc->assertSee('Master Client');

        // Create Master Client
        $mcStore = $this->actingAs($this->adminUser)->post(route('master.clients.store'), [
            'company' => 'PT Audit Nusantara',
            'name' => 'Budi Santoso',
            'nickname' => 'AN',
            'email' => 'budi@audit.test',
            'phone' => '081234567890',
            'pic_marketing_name' => 'Marketing PIC',
            'pic_marketing_phone' => '0811111111',
            'pic_it_name' => 'IT PIC',
            'pic_it_phone' => '0822222222',
            'pic_procurement_name' => 'Procurement PIC',
            'pic_procurement_phone' => '0833333333',
        ]);
        $mcStore->assertRedirect(route('master.clients.index'));
        $this->assertDatabaseHas('clients', [
            'company' => 'PT Audit Nusantara',
            'nickname' => 'AN',
        ]);

        // Master Pegawai Index
        $mp = $this->actingAs($this->adminUser)->get(route('master.pegawai.index'));
        $mp->assertOk();
        $mp->assertSee('Master Pegawai');

        // Create Master Pegawai
        $mpStore = $this->actingAs($this->adminUser)->post(route('master.pegawai.store'), [
            'nip' => 'PEG-2026-999',
            'name' => 'Agus Karyawan',
            'position' => 'Senior Developer',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'is_active' => true,
        ]);
        $mpStore->assertRedirect(route('master.pegawai.index'));
        $this->assertDatabaseHas('pegawais', [
            'nip' => 'PEG-2026-999',
            'name' => 'Agus Karyawan',
        ]);

        // Master Status Index
        $ms = $this->actingAs($this->adminUser)->get(route('master.status.index'));
        $ms->assertOk();
        $ms->assertSee('Master Status');

        // Create Master Status
        $msStore = $this->actingAs($this->adminUser)->post(route('master.status.store'), [
            'code' => 'custom_audit_status',
            'name' => 'Custom Audit',
            'badge_color' => 'primary',
            'sort_order' => 10,
        ]);
        $msStore->assertRedirect(route('master.status.index'));
        $this->assertDatabaseHas('master_statuses', [
            'code' => 'custom_audit_status',
            'name' => 'Custom Audit',
        ]);

        // Dev Board
        $devBoard = $this->actingAs($this->adminUser)->get(route('change-requests.devboard'));
        $devBoard->assertOk();

        // Excel Export
        $excel = $this->actingAs($this->adminUser)->get(route('change-requests.export.excel'));
        $excel->assertOk();
        $excel->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // PDF Export
        $pdf = $this->actingAs($this->adminUser)->get(route('change-requests.export.pdf'));
        $pdf->assertOk();

        // Profile
        $profile = $this->actingAs($this->adminUser)->get(route('profile'));
        $profile->assertOk();
    }

    /**
     * 6. ACCESS RESTRICTIONS AUDIT
     */
    public function test_access_restrictions(): void
    {
        // Client cannot access Admin Master Data
        $res = $this->actingAs($this->clientUser)->get(route('master.clients.index'));
        $res->assertForbidden();

        // Client cannot access PM Dev Board
        $res = $this->actingAs($this->clientUser)->get(route('change-requests.devboard'));
        $res->assertForbidden();

        // Client cannot access Presales Dashboard
        $res = $this->actingAs($this->clientUser)->get(route('presales.dashboard'));
        $res->assertForbidden();

        // Client cannot access PM Head Persetujuan
        $res = $this->actingAs($this->clientUser)->get(route('pmh.persetujuan'));
        $res->assertForbidden();
    }
}
