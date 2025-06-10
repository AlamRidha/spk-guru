<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::where('role', '!=', 'guru')->get()
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
            'email' => 'required|email|unique:users', // Pastikan email unik di tabel users
            'role' => ['required', Rule::in(['admin', 'kepsek'])], // Role hanya boleh admin atau kepsek
            'password' => 'required|string|min:8|confirmed', // Password minimal 8 karakter dan harus dikonfirmasi
        ], [
            // Custom error messages (opsional)
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
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']), // Hash password sebelum disimpan
        ]);

        // Redirect dengan pesan sukses
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
        // Pastikan user yang diperbarui bukan 'guru'
        if ($user->role == 'guru') {
            abort(403, 'Akses ditolak. Anda tidak bisa memperbarui user dengan peran guru.');
        }

        // Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            // Email harus unik kecuali untuk user yang sedang diedit
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'kepsek'])], // Role hanya boleh admin atau kepsek
            'password' => 'nullable|string|min:8|confirmed', // Password opsional, jika diisi harus minimal 8 karakter dan dikonfirmasi
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran (role) wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Update data user
        $user->nama = $validatedData['nama'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        // Jika password diisi, hash dan update
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        // Redirect dengan pesan sukses
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
