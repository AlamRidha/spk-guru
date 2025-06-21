<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class KriteriaController extends Controller
{
    public function index()
    {
        return view('admin.kriterias.index', [
            'title' => 'Manajemen Kriteria',
            'subtitle' => 'Daftar Kriteria Penilaian'
        ]);
    }

    // DataTable untuk Kepala Sekolah
    public function dataKepsek(Request $request)
    {
        if ($request->ajax()) {
            $kriterias = Kriteria::where('penilai', 'kepsek');

            return DataTables::of($kriterias)
                ->addIndexColumn()
                ->addColumn('penilai_formatted', function ($kriteria) {
                    return '<span class="badge badge-primary">Kepala Sekolah</span>';
                })
                ->addColumn('action', function ($kriteria) {
                    return $this->actionButtons($kriteria);
                })
                ->editColumn('jenis', function ($kriteria) {
                    return ucfirst($kriteria->jenis);
                })
                ->editColumn('bobot', function ($kriteria) {
                    return number_format($kriteria->bobot, 4);
                })
                ->rawColumns(['action', 'penilai_formatted'])
                ->make(true);
        }
        abort(404);
    }

    // DataTable untuk Wakil Kurikulum
    public function dataWakur(Request $request)
    {
        if ($request->ajax()) {
            $kriterias = Kriteria::where('penilai', 'wakil_kurikulum');

            return DataTables::of($kriterias)
                ->addIndexColumn()
                ->addColumn('penilai_formatted', function ($kriteria) {
                    return '<span class="badge badge-success">Wakil Kurikulum</span>';
                })
                ->addColumn('action', function ($kriteria) {
                    return $this->actionButtons($kriteria);
                })
                ->editColumn('jenis', function ($kriteria) {
                    return ucfirst($kriteria->jenis);
                })
                ->editColumn('bobot', function ($kriteria) {
                    return number_format($kriteria->bobot, 4);
                })
                ->rawColumns(['action', 'penilai_formatted'])
                ->make(true);
        }
        abort(404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0',
            'jenis' => 'required|in:benefit,cost',
            'penilai' => 'required|in:kepsek,wakil_kurikulum',
        ]);

        Kriteria::create($validated);

        return response()->json([
            'message' => 'Kriteria berhasil ditambahkan',
            'penilai' => $request->penilai
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0',
            'jenis' => 'required|in:benefit,cost',
            'penilai' => 'required|in:kepsek,wakil_kurikulum',
        ]);

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update($validated);

        return response()->json([
            'message' => 'Kriteria berhasil diperbarui',
            'penilai' => $kriteria->penilai
        ]);
    }

    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();

        return response()->json(['message' => 'Kriteria berhasil dihapus']);
    }

    private function actionButtons($kriteria)
    {
        return '<div class="btn-group">
            <button class="btn btn-sm btn-warning btn-edit" data-id="' . $kriteria->id . '">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="' . $kriteria->id . '">
                <i class="fas fa-trash"></i>
            </button>
        </div>';
    }
    public function getNormalizedWeightsByPenilai(Request $request)
    {
        $request->validate([
            'penilai' => 'required|in:kepsek,wakil_kurikulum'
        ]);

        $kriterias = Kriteria::where('penilai', $request->penilai)->get();

        // Jika tidak ada kriteria, kembalikan array kosong
        if ($kriterias->isEmpty()) {
            return response()->json([]);
        }

        $totalBobot = $kriterias->sum('bobot');

        // Jika total bobot 0, set semua normalisasi ke 0
        if ($totalBobot <= 0) {
            return response()->json(
                $kriterias->map(function ($kriteria, $loopIndex) {
                    return [
                        'kode' => 'C' . ($loopIndex + 1),
                        'nama' => $kriteria->nama,
                        'bobot_asli' => $kriteria->bobot,
                        'bobot_normalisasi' => 0,
                        'penilai' => $kriteria->penilai
                    ];
                })
            );
        }

        return response()->json(
            $kriterias->map(function ($kriteria, $loopIndex) use ($totalBobot) {
                return [
                    'kode' => 'C' . ($loopIndex + 1),
                    'nama' => $kriteria->nama,
                    'bobot_asli' => $kriteria->bobot,
                    'bobot_normalisasi' => $kriteria->bobot / $totalBobot,
                    'penilai' => $kriteria->penilai
                ];
            })
        );
    }
}
