<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $gurus = Guru::query();

            return DataTables::of($gurus)
                ->addIndexColumn()
                ->addColumn('action', function ($guru) {
                    $guruData = htmlspecialchars(json_encode([
                        'id' => $guru->id,
                        'nama' => $guru->nama,
                        'nip' => $guru->nip,
                        'jabatan' => $guru->jabatan,
                    ]), JSON_UNESCAPED_UNICODE);

                    return '<div class="btn-group" role="group">
                        <button class="btn btn-sm btn-warning btn-edit" data-guru=\'' . $guruData . '\'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="' . route('admin.gurus.destroy', $guru->id) . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.gurus.index', [
            'title' => 'Manajemen Guru',
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:50|unique:gurus',
            'jabatan' => 'required|string|max:100',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'nip.max' => 'NIP maksimal 50 karakter.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.max' => 'Jabatan maksimal 100 karakter.',
        ]);

        Guru::create($validatedData);

        return response()->json([
            'message' => 'Data guru berhasil ditambahkan!'
        ], 201);
    }

    public function update(Request $request, Guru $guru)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'nullable|string|max:50|unique:gurus,nip,' . $guru->id,
            'jabatan' => 'required|string|max:100',
        ]);

        $guru->update($validatedData);

        return response()->json([
            'message' => 'Data guru berhasil diperbarui!'
        ]);
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();

        if (request()->ajax()) {
            return response()->json([
                'message' => 'Data guru berhasil dihapus!'
            ]);
        }

        return redirect()->route('admin.gurus.index')
            ->with('toast_success', 'Data guru berhasil dihapus!');
    }
}
