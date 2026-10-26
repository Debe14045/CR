<?php

namespace Tests\Feature;

use App\Models\ChangeRequest;
use App\Models\CrInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenStepWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_ten_step_change_request_workflow(): void
    {
        // ── Setup Users ──
        $client = User::factory()->create([
            'role' => 'client',
            'name' => 'PT Mandiri Mitra Sejahtera',
        ]);
        $pm = User::factory()->create([
            'role' => 'pm',
            'name' => 'Budi Project Manager',
        ]);
        $pmh = User::factory()->create([
            'role' => 'pmh',
            'name' => 'Hendra PM Head',
        ]);
        $presales = User::factory()->create([
            'role' => 'presales',
            'name' => 'Dewi Presales Specialist',
        ]);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 1 & 2: Client creates CR and can save as Draft or Submit to PM
        // ════════════════════════════════════════════════════════════════════════
        // A. Save as Draft
        $draftResponse = $this->actingAs($client)->post(route('change-requests.store'), [
            'judul' => 'CR Integrasi Payment Gateway Draft',
            'proyek_terkait' => 'Core Banking Portal',
            'klien' => $client->name,
            'deskripsi' => 'Pengembangan integrasi QRIS dan Virtual Account.',
            'prioritas' => 'normal',
            'action' => 'draft',
        ]);
        $draftResponse->assertRedirect(route('change-requests.index', ['view' => 'draft']));

        $draftCr = ChangeRequest::where('judul', 'CR Integrasi Payment Gateway Draft')->first();
        $this->assertNotNull($draftCr);
        $this->assertSame('draft', $draftCr->status);
        $this->assertSame($client->id, $draftCr->user_id);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 3: If saved in Draft, Client can Edit and Delete CR
        // ════════════════════════════════════════════════════════════════════════
        // 3a. Edit Draft CR
        $editResponse = $this->actingAs($client)->put(route('change-requests.update', $draftCr), [
            'judul' => 'CR Integrasi Payment Gateway Revisi Draft',
            'proyek_terkait' => 'Core Banking Portal v2',
            'deskripsi' => 'Rincian diperbarui dengan protokol SNAP BI terbaru.',
            'action' => 'draft',
        ]);
        $editResponse->assertRedirect(route('change-requests.index', ['view' => 'draft']));
        $draftCr->refresh();
        $this->assertSame('CR Integrasi Payment Gateway Revisi Draft', $draftCr->judul);
        $this->assertSame('Core Banking Portal v2', $draftCr->proyek_terkait);

        // 3b. Delete Draft CR
        $tempDraft = ChangeRequest::create([
            'kode_cr' => 'CR-DRAFT-TEMP',
            'judul' => 'CR Draft Yang Dihapus',
            'klien' => $client->name,
            'proyek_terkait' => 'Portal',
            'deskripsi' => 'Akan dihapus',
            'status' => 'draft',
            'user_id' => $client->id,
            'tanggal_pengajuan' => now(),
        ]);
        $deleteResponse = $this->actingAs($client)->delete(route('change-requests.destroy', $tempDraft));
        $deleteResponse->assertRedirect(route('change-requests.index', ['view' => 'draft']));
        $this->assertModelMissing($tempDraft);

        // Client submits the draft to PM (Action Submit)
        $submitResponse = $this->actingAs($client)->put(route('change-requests.update', $draftCr), [
            'action' => 'submit',
            'submit_cr' => 1,
            'judul' => 'CR Integrasi Payment Gateway Final',
        ]);
        $submitResponse->assertRedirect(route('change-requests.index'));
        $draftCr->refresh();
        $this->assertSame('diajukan', $draftCr->status);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 4: CR enters and is reviewed by PM
        // ════════════════════════════════════════════════════════════════════════
        $pmReviewResponse = $this->actingAs($pm)->get(route('change-requests.show', $draftCr));
        $pmReviewResponse->assertOk();
        $pmReviewResponse->assertSee('CR Integrasi Payment Gateway Final');

        // ════════════════════════════════════════════════════════════════════════
        // STEP 5: CR can be rejected by PM if unsuitable, and tab Status CR
        // cannot be filled/opened if payment/quotation is not done
        // ════════════════════════════════════════════════════════════════════════
        // 5a. Attempt to update Status CR tab before quotation/approval must be blocked
        $blockedStatusResponse = $this->actingAs($pm)
            ->from(route('change-requests.show', [$draftCr, 'tab' => 'status']))
            ->put(route('change-requests.update', $draftCr), [
                'from_tab' => 'status',
                'status' => 'sit',
            ]);
        $blockedStatusResponse->assertRedirect(route('change-requests.show', [$draftCr, 'tab' => 'status']));
        $blockedStatusResponse->assertSessionHas('error');
        $this->assertSame('diajukan', $draftCr->refresh()->status);

        // 5b. PM can reject CR if unsuitable
        $tempRejectCr = ChangeRequest::create([
            'kode_cr' => 'CR-REJECT-TEST',
            'judul' => 'CR Kurang Detail',
            'klien' => $client->name,
            'proyek_terkait' => 'Core Portal',
            'deskripsi' => 'Deskripsi minim',
            'status' => 'diajukan',
            'user_id' => $client->id,
            'tanggal_pengajuan' => now(),
        ]);
        $rejectResponse = $this->actingAs($pm)->put(route('change-requests.update', $tempRejectCr), [
            'action' => 'reject',
            'reject_reason' => 'Spesifikasi requirement belum lengkap, mohon lampirkan FSD pendukung.',
        ]);
        $rejectResponse->assertRedirect(route('change-requests.index'));
        $tempRejectCr->refresh();
        $this->assertSame('revision_needed', $tempRejectCr->status);
        $this->assertSame('Spesifikasi requirement belum lengkap, mohon lampirkan FSD pendukung.', $tempRejectCr->reject_reason);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 6: If PM verifies/approves, advances to PM Head verification
        // ════════════════════════════════════════════════════════════════════════
        $pmVerifyResponse = $this->actingAs($pm)->put(route('change-requests.update', $draftCr), [
            'action' => 'approve',
            'mindesk_analisis' => 2,
            'mindesk_development' => 5,
            'mindesk_testing' => 3,
        ]);
        $pmVerifyResponse->assertRedirect(route('change-requests.index'));
        $draftCr->refresh();
        $this->assertSame('awaiting_pmh', $draftCr->status);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 7: CR reviewed by PM Head and approved
        // ════════════════════════════════════════════════════════════════════════
        $pmhReviewResponse = $this->actingAs($pmh)->get(route('change-requests.show', $draftCr));
        $pmhReviewResponse->assertOk();

        $pmhApproveResponse = $this->actingAs($pmh)->put(route('change-requests.update', $draftCr), [
            'pmh_action' => 'approve',
            'catatan_approval' => 'Kajian teknis dan estimasi man-days telah ditinjau dan disetujui.',
        ]);
        $pmhApproveResponse->assertRedirect(route('change-requests.index'));
        $draftCr->refresh();
        $this->assertSame('validated', $draftCr->status);
        $this->assertNotNull($draftCr->pmh_approved_at);
        $this->assertSame($pmh->name, $draftCr->pmh_approved_by);
        $this->assertTrue($draftCr->canBeQuoted());

        // ════════════════════════════════════════════════════════════════════════
        // STEP 8: After approval, data enters Presales queue
        // ════════════════════════════════════════════════════════════════════════
        $presalesQueueResponse = $this->actingAs($presales)->get(route('presales.persetujuan'));
        $presalesQueueResponse->assertOk();
        $presalesQueueResponse->assertSee($draftCr->kode_cr);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 9: Presales provides quotation and saves it
        // ════════════════════════════════════════════════════════════════════════
        $presalesSaveResponse = $this->actingAs($presales)->post(route('change-requests.quotation.save', $draftCr), [
            'harga_penawaran' => '35.000.000',
            'biaya_pengerjaan' => '20.000.000',
            'estimasi_hari_kerja' => 10,
            'potensi_penjualan' => 'Kontrak Pemeliharaan Tahunan Lanjutan',
            'catatan_quotation' => 'Penawaran resmi dengan diskon mitra perbankan.',
            'nomor_invoice' => 'INV-2026-ITPI-001',
            'status_invoice' => 'pending',
        ]);
        $presalesSaveResponse->assertRedirect(route('presales.persetujuan'));

        $draftCr->refresh();
        $this->assertSame('35000000.00', $draftCr->harga_penawaran);
        $this->assertSame('20000000.00', $draftCr->biaya_pengerjaan);
        $this->assertSame('development', $draftCr->status);

        $invoice = CrInvoice::where('change_request_id', $draftCr->id)->first();
        $this->assertNotNull($invoice);
        $this->assertSame('INV-2026-ITPI-001', $invoice->invoice_number);

        // ════════════════════════════════════════════════════════════════════════
        // STEP 10: Status CR unlocks and CR can be worked on (progress updated)
        // ════════════════════════════════════════════════════════════════════════
        $statusUpdateResponse = $this->actingAs($pm)
            ->from(route('change-requests.show', [$draftCr, 'tab' => 'status']))
            ->put(route('change-requests.update', $draftCr), [
                'from_tab' => 'status',
                'status' => 'sit',
            ]);
        $statusUpdateResponse->assertRedirect(route('change-requests.show', [$draftCr, 'tab' => 'status']));
        $draftCr->refresh();
        $this->assertSame('sit', $draftCr->status);
    }

    public function test_pm_verifikasi_cr_is_disabled_when_presales_payment_is_done(): void
    {
        $client = User::factory()->create(['role' => 'client', 'name' => 'Client Test']);
        $pm = User::factory()->create(['role' => 'pm', 'name' => 'PM Test']);
        $cr = ChangeRequest::create([
            'user_id' => $client->id,
            'kode_cr' => 'CR-2026-TEST',
            'judul' => 'CR Test Payment Done',
            'proyek_terkait' => 'Core Test System',
            'klien' => 'Client Test Company',
            'deskripsi' => 'Testing disabled verifikasi after payment.',
            'prioritas' => 'normal',
            'status' => 'development',
            'tanggal_pengajuan' => now(),
            'harga_penawaran' => 50000000,
            'quotation_approved_tp_at' => now(),
            'quotation_approved_client_at' => now(),
        ]);
        CrInvoice::create([
            'change_request_id' => $cr->id,
            'invoice_number' => 'INV-TEST-001',
            'term_name' => 'Termin 1',
            'job_description' => 'Test Payment',
            'percentage' => 100,
            'amount' => 50000000,
            'payment_status' => 'paid',
            'planned_payment_date' => now(),
            'actual_payment_date' => now(),
        ]);

        $this->assertTrue($cr->isPresalesPaymentDone());

        // PM opens show view
        $viewResponse = $this->actingAs($pm)->get(route('change-requests.show', [$cr, 'tab' => 'verifikasi']));
        $viewResponse->assertOk();
        $viewResponse->assertSee('(Terkunci)');
        $viewResponse->assertSee('Form Verifikasi CR Dinonaktifkan (Read-Only)');
        $viewResponse->assertSee('Verifikasi CR (Terkunci)');
        $viewResponse->assertSee('disabled readonly', false);

        // PM attempts to submit verification (approve)
        $verifyAttempt = $this->actingAs($pm)
            ->from(route('change-requests.show', [$cr, 'tab' => 'verifikasi']))
            ->put(route('change-requests.update', $cr), [
                'from_tab' => 'verifikasi',
                'action' => 'approve',
                'mindesk_analisis' => 5,
            ]);

        $verifyAttempt->assertRedirect(route('change-requests.show', [$cr, 'tab' => 'verifikasi']));
        $verifyAttempt->assertSessionHas('error', 'Form Verifikasi CR telah dikunci karena proses quotation dan pembayaran dari Presales telah selesai dilakukan.');
    }
}
