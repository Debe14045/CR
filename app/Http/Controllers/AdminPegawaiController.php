<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%")
                  ->orWhere('departemen', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $perPage = (int) $request->input('per_page', 10);
        $pegawais = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        return view('admin.pegawai.create');
    }

    private function pegawaiValidationRules(bool $hasRole, ?int $pegawaiId = null, ?int $userId = null): array
    {
        $rules = [
            'nip' => 'required|string|max:50|unique:pegawais,nip' . ($pegawaiId ? ',' . $pegawaiId : ''),
            'name' => 'required|string|max:255',
            'divisi' => ['nullable', 'string', 'max:100', 'required_without_all:departemen,position'],
            'departemen' => ['nullable', 'string', 'max:100', 'required_without_all:divisi,position'],
            'position' => ['nullable', 'string', 'max:255', 'required_without_all:divisi,departemen'],
            'phone' => 'required|string|max:25',
            'address' => 'nullable|string',
            'role' => 'nullable|in:pm,pmh,presales,admin',
            'can_act_as_pm' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
        ];

        $rules['email'] = $hasRole
            ? 'required|email|max:255|unique:users,email' . ($userId ? ',' . $userId : '')
            : 'nullable|email|max:255';

        return $rules;
    }

    private function pegawaiValidationMessages(): array
    {
        return [
            'divisi.required_without_all' => 'Minimal salah satu dari Divisi, Departemen, atau Jabatan harus dipilih.',
            'departemen.required_without_all' => 'Minimal salah satu dari Divisi, Departemen, atau Jabatan harus dipilih.',
            'position.required_without_all' => 'Minimal salah satu dari Divisi, Departemen, atau Jabatan harus dipilih.',
            'email.required' => 'Email pegawai wajib diisi karena role sistem CR dipilih untuk akses login.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
        ];
    }

    public function store(Request $request)
    {
        $selectedRole = $request->filled('role') ? $request->role : null;
        $hasAccount = !is_null($selectedRole);

        $request->validate(
            $this->pegawaiValidationRules($hasAccount),
            $this->pegawaiValidationMessages()
        );

        DB::beginTransaction();
        try {
            $userId = null;
            $pegawaiEmail = $request->filled('email') ? trim($request->email) : null;
            $role = 'non_login';

            if ($hasAccount) {
                $rawPassword = $request->filled('password') ? $request->password : User::DEFAULT_PASSWORD;

                $user = User::create([
                    'name' => $request->name,
                    'email' => $pegawaiEmail,
                    'password' => Hash::make($rawPassword),
                    'role' => $selectedRole,
                    'phone' => $request->phone,
                    'can_act_as_pm' => $selectedRole === 'pmh' ? $request->boolean('can_act_as_pm') : false,
                ]);

                $userId = $user->id;
                $role = $selectedRole;
            }

            $pegawai = Pegawai::create([
                'user_id' => $userId,
                'nip' => strtoupper(trim($request->nip)),
                'name' => $request->name,
                'email' => $pegawaiEmail,
                'phone' => $request->phone,
                'divisi' => $request->filled('divisi') ? $request->divisi : null,
                'departemen' => $request->filled('departemen') ? $request->departemen : null,
                'position' => $request->filled('position') ? $request->position : null,
                'role' => $role,
                'address' => $request->address,
                'is_active' => true,
            ]);

            DB::commit();

            $msg = $hasAccount
                ? 'Data pegawai ' . $pegawai->name . ' berhasil ditambahkan beserta akun loginnya.'
                : 'Data pegawai ' . $pegawai->name . ' berhasil ditambahkan tanpa akun login web.';

            return redirect()->route('admin.pegawai.index')->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan pegawai: ' . $e->getMessage());
        }
    }

    public function edit(Pegawai $pegawai)
    {
        $pegawai->load('user');
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $selectedRole = $request->filled('role') ? $request->role : null;
        $hasAccount = !is_null($selectedRole);
        $userId = $pegawai->user_id;

        $request->validate(
            $this->pegawaiValidationRules($hasAccount, $pegawai->id, $userId),
            $this->pegawaiValidationMessages()
        );

        if (!$hasAccount && $pegawai->user) {
            $u = $pegawai->user;
            if (
                auth()->id() === $u->id
                || $u->changeRequests()->exists()
                || $u->managedProjects()->exists()
                || $u->projectsAsPm()->exists()
                || $u->changeRequestsAsPm()->exists()
            ) {
                return back()->withInput()->with(
                    'error',
                    'Akun login pegawai ini tidak dapat dihapus karena masih sedang login atau terhubung ke data proyek / Change Request.'
                );
            }
        }

        DB::beginTransaction();
        try {
            $newUserId = $userId;
            $pegawaiEmail = $request->filled('email') ? trim($request->email) : null;
            $role = 'non_login';

            if ($hasAccount) {
                $userData = [
                    'name' => $request->name,
                    'email' => $pegawaiEmail,
                    'role' => $selectedRole,
                    'phone' => $request->phone,
                    'can_act_as_pm' => $selectedRole === 'pmh' ? $request->boolean('can_act_as_pm') : false,
                ];

                if ($pegawai->user) {
                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->password);
                    }
                    $pegawai->user->update($userData);
                } else {
                    $rawPassword = $request->filled('password') ? $request->password : User::DEFAULT_PASSWORD;
                    $userData['password'] = Hash::make($rawPassword);
                    $createdUser = User::create($userData);
                    $newUserId = $createdUser->id;
                }

                $role = $selectedRole;
            } elseif ($pegawai->user) {
                $pegawai->user->delete();
                $newUserId = null;
            }

            $pegawai->update([
                'user_id' => $newUserId,
                'nip' => strtoupper(trim($request->nip)),
                'name' => $request->name,
                'email' => $pegawaiEmail,
                'phone' => $request->phone,
                'divisi' => $request->filled('divisi') ? $request->divisi : null,
                'departemen' => $request->filled('departemen') ? $request->departemen : null,
                'position' => $request->filled('position') ? $request->position : null,
                'role' => $role,
                'address' => $request->address,
                'is_active' => $hasAccount ? $pegawai->is_active : true,
            ]);

            DB::commit();

            return redirect()->route('admin.pegawai.index')
                             ->with('success', 'Data pegawai ' . $pegawai->name . ' berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui pegawai: ' . $e->getMessage());
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        DB::beginTransaction();
        try {
            $hadAccount = !is_null($pegawai->user);
            if ($hadAccount) {
                $pegawai->user->delete();
            }
            $pegawai->delete();

            DB::commit();
            $message = $hadAccount
                ? 'Data pegawai beserta akun login berhasil dihapus.'
                : 'Data pegawai berhasil dihapus.';

            return redirect()->route('admin.pegawai.index')->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pegawai: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Pegawai $pegawai)
    {
        if (!$pegawai->hasWebAccount()) {
            return redirect()->back()->with('error', 'Pegawai tanpa akun login web tidak memiliki status akun aktif/nonaktif.');
        }

        $pegawai->update(['is_active' => !$pegawai->is_active]);
        $statusText = $pegawai->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', 'Status pegawai ' . $pegawai->name . ' berhasil ' . $statusText . '.');
    }
}

