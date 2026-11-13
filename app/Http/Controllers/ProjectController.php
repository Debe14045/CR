<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['client', 'pm.pegawai', 'pms.pegawai', 'changeRequests']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_proyek', 'like', "%{$search}%")
                  ->orWhere('nama_proyek', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company', 'like', "%{$search}%")
                         ->orWhere('nickname', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pm', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pms', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('pm_id')) {
            $pmFilterId = $request->pm_id;
            $query->where(function ($q) use ($pmFilterId) {
                $q->where('pm_id', $pmFilterId)
                  ->orWhereHas('pms', function ($pq) use ($pmFilterId) {
                      $pq->where('users.id', $pmFilterId);
                  });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $projects = $query->latest()->paginate($perPage)->withQueryString();
        $clients = Client::orderBy('company')->get();
        $pmUsers = User::projectManagers();

        return view('admin.projects.index', compact('projects', 'clients', 'pmUsers'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('company')->get();
        $pmUsers = User::projectManagers();
        $selectedClientId = $request->query('client_id');

        return view('admin.projects.create', compact('clients', 'pmUsers', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProject($request);
        $pmIds = $this->extractPmIds($validated);

        $project = DB::transaction(function () use ($validated, $pmIds) {
            $proj = Project::create([
                'client_id' => $validated['client_id'],
                'pm_id' => $pmIds[0],
                'kode_proyek' => strtoupper(trim($validated['kode_proyek'])),
                'nama_proyek' => $validated['nama_proyek'],
                'deskripsi' => $validated['deskripsi'] ?? null,
            ]);

            $proj->syncPms($pmIds);

            return $proj;
        });

        // Jika ada redirect khusus ke detail klien
        if ($request->has('redirect_to_client')) {
            return redirect()->route('admin.clients.show', $validated['client_id'])
                             ->with('success', 'Proyek ' . $project->nama_proyek . ' berhasil ditambahkan untuk klien ini.');
        }

        return redirect()->route('admin.projects.index')
                         ->with('success', 'Master proyek ' . $project->nama_proyek . ' berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        $project->load('pms');
        $clients = Client::orderBy('company')->get();
        $assignedIds = $project->assignedPms()->pluck('id')->all();
        $pmUsers = User::projectManagers($assignedIds);
        $pm1Id = $assignedIds[0] ?? $project->pm_id;
        $pm2Id = $assignedIds[1] ?? null;

        return view('admin.projects.edit', compact('project', 'clients', 'pmUsers', 'pm1Id', 'pm2Id'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $this->validateProject($request, $project);
        $pmIds = $this->extractPmIds($validated);

        DB::transaction(function () use ($project, $validated, $pmIds) {
            $project->update([
                'client_id' => $validated['client_id'],
                'pm_id' => $pmIds[0],
                'kode_proyek' => strtoupper(trim($validated['kode_proyek'])),
                'nama_proyek' => $validated['nama_proyek'],
                'deskripsi' => $validated['deskripsi'] ?? null,
            ]);

            $project->syncPms($pmIds);
        });

        return redirect()->route('admin.projects.index')
                         ->with('success', 'Master proyek berhasil diperbarui.');
    }

    /**
     * Validasi terpusat untuk tambah dan edit proyek termasuk maksimal 2 PM penanggung jawab.
     */
    private function validateProject(Request $request, ?Project $project = null): array
    {
        $ensureIds = $project ? $project->assignedPms()->pluck('id')->all() : [];
        $eligiblePmIds = User::projectManagers($ensureIds)->pluck('id')->all();

        return $request->validate([
            'client_id' => 'required|exists:clients,id',
            'pm_id' => ['required', Rule::in($eligiblePmIds)],
            'pm_id_2' => ['nullable', 'different:pm_id', Rule::in($eligiblePmIds)],
            'kode_proyek' => [
                'required',
                'string',
                'max:50',
                Rule::unique('projects', 'kode_proyek')->ignore($project?->id),
            ],
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'pm_id.required' => 'Nama PM utama yang bertanggung jawab wajib dipilih.',
            'pm_id.in' => 'PM utama yang dipilih tidak valid atau bukan Project Manager aktif.',
            'pm_id_2.different' => 'PM ke-2 tidak boleh sama dengan PM utama.',
            'pm_id_2.in' => 'PM ke-2 yang dipilih tidak valid atau bukan Project Manager aktif.',
        ]);
    }

    /**
     * Susun array ID PM unik maksimal 2 orang dengan PM utama di indeks pertama.
     */
    private function extractPmIds(array $validated): array
    {
        return collect([$validated['pm_id'], $validated['pm_id_2'] ?? null])
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(2)
            ->values()
            ->all();
    }

    public function destroy(Project $project)
    {
        // Cek jika proyek memiliki CR terkait
        if ($project->changeRequests()->count() > 0) {
            return back()->with('error', 'Proyek tidak dapat dihapus karena sudah memiliki pengajuan Change Request aktif.');
        }

        $project->delete();
        return redirect()->route('admin.projects.index')
                         ->with('success', 'Master proyek berhasil dihapus.');
    }
}
