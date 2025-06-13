<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubKriteriaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('grouped')) {
                $kriterias = Kriteria::withCount('subKriterias')
                    ->having('sub_kriterias_count', '>', 0)
                    ->with('subKriterias')
                    ->get();

                return DataTables::of($kriterias)
                    ->addIndexColumn()
                    ->addColumn('subkriteria_list', function ($kriteria) {
                        return $kriteria->subKriterias->map(function ($sub) {
                            return $sub->nama . ' (Nilai: ' . $sub->nilai . ')';
                        })->implode('<br>');
                    })
                    ->addColumn('action', function ($kriteria) {
                        return '<button class="btn btn-sm btn-primary btn-manage" 
                                data-kriteria-id="' . $kriteria->id . '"
                                data-kriteria-nama="' . $kriteria->nama . '">
                            <i class="fas fa-edit"></i> Kelola Sub Kriteria
                        </button>';
                    })
                    ->rawColumns(['subkriteria_list', 'action'])
                    ->make(true);
            }

            // Default non-grouped view
            $subKriterias = SubKriteria::with('kriteria');
            return DataTables::of($subKriterias)->make(true);
        }

        return view('admin.sub-kriterias.index', [
            'title' => 'Manajemen Sub Kriteria',
            'kriterias' => Kriteria::all()
        ]);
    }

    public function getByKriteria($kriteriaId)
    {
        $subKriterias = SubKriteria::where('kriteria_id', $kriteriaId)->get();
        return response()->json($subKriterias);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kriteria_id' => 'required|exists:kriterias,id',
            'nama' => 'required|string|max:100',
            'nilai' => 'required|integer|between:1,5',
            'keterangan' => 'required|string|max:255'
        ]);

        SubKriteria::create($validated);

        return response()->json(['message' => 'Sub Kriteria berhasil ditambahkan!']);
    }

    public function update(Request $request, SubKriteria $subKriteria)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nilai' => 'required|integer|between:1,5',
            'keterangan' => 'required|string|max:255'
        ]);

        $subKriteria->update($validated);

        return response()->json(['message' => 'Sub Kriteria berhasil diperbarui!']);
    }

    public function destroy(SubKriteria $subKriteria)
    {
        $subKriteria->delete();
        return response()->json(['message' => 'Sub Kriteria berhasil dihapus!']);
    }
}
