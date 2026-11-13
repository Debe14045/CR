<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('changeRequest.client')->latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create(ChangeRequest $changeRequest)
    {
        return view('invoices.create', compact('changeRequest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'change_request_id' => 'required|exists:change_requests,id',
            'term_name' => 'required|string|max:100',
            'percentage' => 'required|numeric|min:1|max:100',
            'nominal' => 'required|numeric|min:0',
            'job_description' => 'nullable|string',
            'planned_payment_date' => 'nullable|date',
            'gr_file' => 'nullable|mimes:pdf|max:5120',
        ]);

        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

        $data = $request->all();
        $data['invoice_number'] = $invoiceNumber;
        $data['invoice_date'] = Carbon::now();
        $data['payment_status'] = 'pending';

        if ($request->hasFile('gr_file')) {
            $data['gr_file'] = $request->file('gr_file')->store('invoices/gr', 'public');
        }

        Invoice::create($data);

        return redirect()->route('invoices.index')
                         ->with('success', 'Data penagihan (Invoice) berhasil dibuat.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,cancelled',
        ]);

        $invoice->update([
            'payment_status' => $request->payment_status,
            'actual_payment_date' => $request->payment_status === 'paid' ? Carbon::now() : null,
        ]);

        return redirect()->back()->with('success', 'Status pembayaran invoice berhasil diperbarui.');
    }
}
