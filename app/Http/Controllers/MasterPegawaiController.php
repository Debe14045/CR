<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class MasterPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $pegawais = $query->latest()->paginate(10)->withQueryString();

        return view('master.pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        return view('master.pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:pegawais,nip',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'position' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        Pegawai::create($validated);

        return redirect()->route('master.pegawai.index')
            ->with('success', 'Master Pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('master.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:pegawais,nip,' . $pegawai->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'position' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;

        $pegawai->update($validated);

        return redirect()->route('master.pegawai.index')
            ->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return redirect()->route('master.pegawai.index')
            ->with('success', 'Master Pegawai berhasil dihapus.');
    }
}
