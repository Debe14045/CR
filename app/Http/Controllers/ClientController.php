<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPic;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['pics.user', 'projects'])->withCount('changeRequests');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('pics', function ($picQuery) use ($search) {
                      $picQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $clients = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:50',
            'address' => 'nullable|string',

            // Validasi data PIC (Minimal 1 PIC, bisa sampai 3 PIC)
            'pics' => 'required|array|min:1',
            'pics.*.name' => 'required|string|max:255',
            'pics.*.email' => 'required|email|unique:users,email',
            'pics.*.role_type' => 'required|in:marketing,procurement,it',
            'pics.*.phone' => 'required|string|max:25',
            'pics.*.password' => 'nullable|string|min:6',

            // Data Proyek Awal (Opsional)
            'kode_proyek' => 'nullable|string|max:50|unique:projects,kode_proyek',
            'nama_proyek' => 'nullable|string|max:255',
            'deskripsi_proyek' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat data perusahaan klien
            $client = Client::create([
                'company' => $request->company,
                'nickname' => $request->nickname,
                'address' => $request->address,
            ]);

            // 2. Buat akun login & data untuk masing-masing PIC
            foreach ($request->pics as $picData) {
                if (empty($picData['name']) || empty($picData['email'])) {
                    continue;
                }

                $rawPassword = !empty($picData['password']) ? $picData['password'] : User::DEFAULT_PASSWORD;

                $user = User::create([
                    'name' => $picData['name'],
                    'email' => $picData['email'],
                    'password' => Hash::make($rawPassword),
                    'role' => 'client',
                    'phone' => $picData['phone'] ?? null,
                ]);

                ClientPic::create([
                    'client_id' => $client->id,
                    'user_id' => $user->id,
                    'role_type' => $picData['role_type'],
                    'name' => $picData['name'],
                    'email' => $picData['email'],
                    'phone' => $picData['phone'] ?? null,
                ]);
            }

            // 3. Jika proyek awal diisi, buatkan langsung agar klien bisa mengajukan CR
            if ($request->filled('kode_proyek') && $request->filled('nama_proyek')) {
                Project::create([
                    'client_id' => $client->id,
                    'kode_proyek' => strtoupper(trim($request->kode_proyek)),
                    'nama_proyek' => $request->nama_proyek,
                    'deskripsi' => $request->deskripsi_proyek,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.clients.index')
                             ->with('success', 'Klien ' . $client->company . ' berhasil didaftarkan beserta akun PIC dan proyek awal.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mendaftarkan klien: ' . $e->getMessage());
        }
    }

    public function show(Client $client)
    {
        $client->load([
            'pics.user',
            'projects.changeRequests',
            'changeRequests' => fn ($q) => $q->with('project')->latest(),
        ]);

        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $client->load('pics.user');
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'company' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'pics' => 'nullable|array',
            'pics.*.id' => 'nullable|integer',
            'pics.*.name' => 'required|string|max:255',
            'pics.*.phone' => 'required|string|max:25',
            'pics.*.password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $client->update([
                'company' => $request->company,
                'nickname' => $request->nickname,
                'address' => $request->address,
            ]);

            // Update data PIC jika ada
            if ($request->has('pics')) {
                foreach ($request->pics as $picData) {
                    if (isset($picData['id'])) {
                        $pic = ClientPic::find($picData['id']);
                        if ($pic && $pic->client_id == $client->id) {
                            $pic->update([
                                'name' => $picData['name'],
                                'phone' => $picData['phone'] ?? null,
                            ]);

                            if ($pic->user) {
                                $updateUser = ['name' => $picData['name'], 'phone' => $picData['phone'] ?? null];
                                if (!empty($picData['password'])) {
                                    $updateUser['password'] = Hash::make($picData['password']);
                                }
                                $pic->user->update($updateUser);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.clients.show', $client)
                             ->with('success', 'Data klien dan akun PIC berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui klien: ' . $e->getMessage());
        }
    }

    public function destroy(Client $client)
    {
        DB::beginTransaction();
        try {
            // Hapus user PIC terkait
            foreach ($client->pics as $pic) {
                if ($pic->user) {
                    $pic->user->delete();
                }
            }
            $client->delete();

            DB::commit();
            return redirect()->route('admin.clients.index')
                             ->with('success', 'Data klien beserta akun dan proyek berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus klien: ' . $e->getMessage());
        }
    }
}
