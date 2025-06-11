<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::where('role', '!=', 'guru');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                    $userData = htmlspecialchars(json_encode([
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'role' => $user->role,
                    ]));

                    return '<button class="btn btn-sm btn-warning btn-edit" data-user="' . $userData . '">Edit</button>
                <form action="' . route('admin.users.destroy', $user->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus user?\')">Hapus</button>
                </form>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.users.index', [
            'title' => 'Manajemen User',
        ]);
    }


    // create function
    public function create()
    {
        return view('admin.users.create', [
            'title' => 'Tambah User Baru',
        ]);
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => ['required', Rule::in(['admin', 'kepsek'])],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran (role) wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Buat user baru
        User::create([
            'nama' => $request->nama,
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return redirect()->route('admin.users.index')->with('toast_success', 'User berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        // Pastikan user yang diedit bukan 'guru'
        if ($user->role == 'guru') {
            abort(403, 'Akses ditolak. Anda tidak bisa mengedit user dengan peran guru.');
        }

        return view('admin.users.edit', [
            'title' => 'Edit User',
            'user' => $user,
        ]);
    }

    /**
     * Memperbarui user di database.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'kepsek'])],
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        // Update data
        $user->nama = $request->nama;
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('toast_success', 'User berhasil diperbarui!');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Pastikan user yang dihapus bukan 'guru'
        if ($user->role == 'guru') {
            return back()->with('toast_error', 'Akses ditolak. Anda tidak bisa menghapus user dengan peran guru.');
        }

        // Hapus user
        $user->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.users.index')->with('toast_success', 'User berhasil dihapus!');
    }
}
