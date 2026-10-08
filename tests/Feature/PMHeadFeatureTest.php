<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PMHeadFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createPmhUser(): User
    {
        return User::factory()->create([
            'name' => 'Maya Sari',
            'email' => 'pmh@itpi.test',
            'role' => User::ROLE_PMH,
        ]);
    }

    public function test_pmh_can_access_persetujuan_page(): void
    {
        $pmh = $this->createPmhUser();

        $response = $this->actingAs($pmh)->get(route('pmh.persetujuan'));

        $response->assertOk();
        $response->assertSee('Butuh Persetujuan');
        $response->assertSee('Semua CR');
        $response->assertSee('CR Aktif');
        $response->assertSee('CR Selesai');
        $response->assertSee('NAMA PM');
    }

    public function test_pmh_can_access_development_page(): void
    {
        $pmh = $this->createPmhUser();

        $response = $this->actingAs($pmh)->get(route('pmh.development'));

        $response->assertOk();
        $response->assertSee('Development');
        $response->assertSee('Danendra dada');
    }

    public function test_pmh_can_access_golive_page(): void
    {
        $pmh = $this->createPmhUser();

        $response = $this->actingAs($pmh)->get(route('pmh.golive'));

        $response->assertOk();
        $response->assertSee('GO LIVE');
    }

    public function test_pmh_can_access_outstanding_payment_page(): void
    {
        $pmh = $this->createPmhUser();

        $response = $this->actingAs($pmh)->get(route('pmh.outstanding-payment'));

        $response->assertOk();
        $response->assertSee('OUTSTANDING PAYMENT');
        $response->assertSee('Total Tagihan');
        $response->assertSee('Rp. 200.000.000');
        $response->assertSee('Export Rekap (XLS/PDF)');
        $response->assertSee('PT Delta Solusi');
        $response->assertSee('BELUM BAYAR');
        $response->assertSee('LUNAS');
    }

    public function test_pmh_can_export_outstanding_payment_csv(): void
    {
        $pmh = $this->createPmhUser();

        $response = $this->actingAs($pmh)->get(route('pmh.outstanding-payment.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_pmh_can_view_detail_review_sheet(): void
    {
        $pmh = $this->createPmhUser();
        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-09-0042',
            'judul' => 'Integrasi API Pembayaran OVO',
            'klien' => 'PT Maju Bersama',
            'proyek_terkait' => 'Sistem E-commerce',
            'deskripsi' => 'Deskripsi integrasi',
            'tanggal_pengajuan' => now(),
            'status' => 'awaiting_pmh',
            'prioritas' => 'normal',
        ]);

        $response = $this->actingAs($pmh)->get(route('pmh.review', $cr));

        $response->assertOk();
        $response->assertSee('Detail Change Request');
        $response->assertSee('Integrasi API Pembayaran OVO');
        $response->assertSee('STATUS ALUR CR');
        $response->assertSee('Approval PM Head');
        $response->assertSee('Keputusan Approval');
        $response->assertSee('Tolak');
        $response->assertSee('Setujui');
    }

    public function test_pmh_can_approve_change_request(): void
    {
        $pmh = $this->createPmhUser();
        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-001',
            'judul' => 'CR to Approve',
            'klien' => 'Client A',
            'proyek_terkait' => 'Project A',
            'deskripsi' => 'Deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'awaiting_pmh',
        ]);

        $response = $this->actingAs($pmh)->post(route('pmh.decision', $cr), [
            'action' => 'approve',
            'catatan_approval' => 'Sudah diverifikasi dan disetujui.',
        ]);

        $response->assertRedirect(route('pmh.persetujuan'));
        $cr->refresh();
        $this->assertSame('validated', $cr->status);
        $this->assertSame('Maya Sari', $cr->pmh_approved_by);
        $this->assertNotNull($cr->pmh_approved_at);
    }

    public function test_pmh_reject_requires_reason_and_updates_status(): void
    {
        $pmh = $this->createPmhUser();
        $cr = ChangeRequest::create([
            'kode_cr' => 'CR-2026-002',
            'judul' => 'CR to Reject',
            'klien' => 'Client B',
            'proyek_terkait' => 'Project B',
            'deskripsi' => 'Deskripsi',
            'tanggal_pengajuan' => now(),
            'status' => 'awaiting_pmh',
        ]);

        // Attempt reject without reason
        $failResponse = $this->actingAs($pmh)->post(route('pmh.decision', $cr), [
            'action' => 'reject',
            'catatan_approval' => '',
        ]);
        $failResponse->assertSessionHasErrors(['catatan_approval']);

        // Success reject with reason
        $successResponse = $this->actingAs($pmh)->post(route('pmh.decision', $cr), [
            'action' => 'reject',
            'catatan_approval' => 'Mohon lengkapi solution paper arsitektur sistem.',
        ]);

        $successResponse->assertRedirect(route('pmh.persetujuan'));
        $cr->refresh();
        $this->assertSame('revision_needed', $cr->status);
        $this->assertSame('Mohon lengkapi solution paper arsitektur sistem.', $cr->reject_reason);
    }

    public function test_client_cannot_access_pmh_routes(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $response = $this->actingAs($client)->get(route('pmh.persetujuan'));

        $response->assertForbidden();
    }

    public function test_user_can_access_profile_page(): void
    {
        $pmh = $this->createPmhUser();

        $response = $this->actingAs($pmh)->get(route('profile'));

        $response->assertOk();
        $response->assertSee('Profile');
        $response->assertSee('INFORMASI AKUN');
        $response->assertSee('GANTI PASSWORD');
        $response->assertSee('PREFERENSI');
        $response->assertSee('PROJECT YANG DITANGANI');
    }
}

