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

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('role_formatted', function ($user) {
                    return ucfirst($user->role);
                })
                ->addColumn('action', function ($user) {
                    $buttons = '<button class="btn btn-sm btn-warning btn-edit mr-1" 
                               data-id="' . $user->id . '" 
                               data-nama="' . $user->nama . '"
                               data-email="' . $user->email . '"
                               data-role="' . $user->role . '">
                               <i class="fas fa-edit"></i></button>';

                    $buttons .= '<button class="btn btn-sm btn-danger btn-delete" 
                                data-id="' . $user->id . '"
                                data-nama="' . $user->nama . '">
                                <i class="fas fa-trash"></i></button>';

                    return $buttons;
                })
                ->rawColumns(['action', 'role_formatted'])
                ->make(true);
        }

        return view('admin.users.index', [
            'title' => 'Manajemen User',
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => ['required', Rule::in(['admin', 'kepsek', 'wakil_kurikulum'])],
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'kepsek', 'wakil_kurikulum'])],
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        $user->update([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => $request->filled('password')
                ? Hash::make($validatedData['password'])
                : $user->password
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diperbarui!'
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->role == 'guru') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak bisa menghapus user guru'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil dihapus!'
        ]);
    }
}
