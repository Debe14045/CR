<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;

class MasterStatusController extends Controller
{
    public function index()
    {
        $statuses = MasterStatus::orderBy('sort_order')->get();
        return view('master.status.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_statuses,code',
            'name' => 'required|string|max:255',
            'badge_color' => 'required|string|in:primary,secondary,success,danger,warning,info,dark',
            'sort_order' => 'nullable|integer',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? (MasterStatus::max('sort_order') + 1);

        MasterStatus::create($validated);

        return redirect()->route('master.status.index')
            ->with('success', 'Master Status berhasil ditambahkan.');
    }

    public function update(Request $request, MasterStatus $status)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'badge_color' => 'required|string|in:primary,secondary,success,danger,warning,info,dark',
            'sort_order' => 'nullable|integer',
            'description' => 'nullable|string|max:1000',
        ]);

        $status->update($validated);

        return redirect()->route('master.status.index')
            ->with('success', 'Master Status berhasil diperbarui.');
    }
}
