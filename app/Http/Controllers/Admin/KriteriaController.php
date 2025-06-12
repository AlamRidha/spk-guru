<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KriteriaController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kriterias = Kriteria::query();

            return DataTables::of($kriterias)
                ->addIndexColumn()
                ->addColumn('action', function ($kriteria) {
                    $data = htmlspecialchars(json_encode($kriteria), ENT_QUOTES, 'UTF-8');
                    return '<button class="btn btn-warning btn-sm btn-edit" data-kriteria="' . $data . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="' . $kriteria->id . '">
                            <i class="fas fa-trash"></i> Hapus
                        </button>';
                })
                ->editColumn('jenis', function ($kriteria) {
                    return ucfirst($kriteria->jenis);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // logika normalisasi bobot
        $kriterias = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot');

        // Menghitung bobot normalisasi
        $normalizedWeights = $kriterias->map(function ($kriteria) use ($totalBobot) {
            return [
                'kode' => 'C' . $kriteria->id,
                'nama' => $kriteria->nama,
                'bobot_asli' => $kriteria->bobot,
                'bobot_normalisasi' => $totalBobot > 0 ? round($kriteria->bobot / $totalBobot, 5) : 0,
            ];
        });

        return view('admin.kriterias.index', [
            'title' => 'Manajemen Kriteria',
            'normalizedWeights' => $normalizedWeights,
            // 'totalBobot' => $totalBobot,
        ]);
    }

    public function getAll()
    {
        try {
            $kriterias = Kriteria::all();
            return response()->json($kriterias);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function show(Kriteria $kriteria)
    {
        return response()->json($kriteria);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0',
            'jenis' => 'required|in:benefit,cost',
        ]);

        Kriteria::create($validated);

        return response()->json(['message' => 'Kriteria berhasil ditambahkan']);
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0',
            'jenis' => 'required|in:benefit,cost',
        ]);

        $kriteria->update($validated);

        return response()->json(['message' => 'Kriteria berhasil diperbarui']);
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return response()->json(['message' => 'Kriteria berhasil dihapus']);
    }
}
