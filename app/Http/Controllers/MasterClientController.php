<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class MasterClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('pic_marketing_name', 'like', "%{$search}%")
                  ->orWhere('pic_it_name', 'like', "%{$search}%")
                  ->orWhere('pic_procurement_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

        return view('master.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('master.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'nickname' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            // PIC Marketing
            'pic_marketing_name' => 'required|string|max:255',
            'pic_marketing_phone' => 'required|string|max:50',
            'pic_marketing_email' => 'nullable|email|max:255',
            'pic_marketing_desc' => 'nullable|string|max:500',
            // PIC IT / Programmer
            'pic_it_name' => 'required|string|max:255',
            'pic_it_phone' => 'required|string|max:50',
            'pic_it_email' => 'nullable|email|max:255',
            'pic_it_desc' => 'nullable|string|max:500',
            // PIC Procurement
            'pic_procurement_name' => 'required|string|max:255',
            'pic_procurement_phone' => 'required|string|max:50',
            'pic_procurement_email' => 'nullable|email|max:255',
            'pic_procurement_desc' => 'nullable|string|max:500',
            'active' => 'nullable|boolean',
        ]);

        $validated['name'] = $validated['name'] ?? $validated['company'];
        $validated['active'] = $request->has('active') ? $request->boolean('active') : true;

        Client::create($validated);

        return redirect()->route('master.clients.index')
            ->with('success', 'Master Client berhasil ditambahkan beserta 3 PIC lengkap.');
    }

    public function edit(Client $client)
    {
        return view('master.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'company' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'nickname' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            // PIC Marketing
            'pic_marketing_name' => 'required|string|max:255',
            'pic_marketing_phone' => 'required|string|max:50',
            'pic_marketing_email' => 'nullable|email|max:255',
            'pic_marketing_desc' => 'nullable|string|max:500',
            // PIC IT / Programmer
            'pic_it_name' => 'required|string|max:255',
            'pic_it_phone' => 'required|string|max:50',
            'pic_it_email' => 'nullable|email|max:255',
            'pic_it_desc' => 'nullable|string|max:500',
            // PIC Procurement
            'pic_procurement_name' => 'required|string|max:255',
            'pic_procurement_phone' => 'required|string|max:50',
            'pic_procurement_email' => 'nullable|email|max:255',
            'pic_procurement_desc' => 'nullable|string|max:500',
            'active' => 'nullable|boolean',
        ]);

        $validated['name'] = $validated['name'] ?? $validated['company'];
        $validated['active'] = $request->has('active') ? $request->boolean('active') : false;

        $client->update($validated);

        return redirect()->route('master.clients.index')
            ->with('success', 'Data Master Client berhasil diperbarui.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('master.clients.index')
            ->with('success', 'Master Client berhasil dihapus.');
    }
}
