<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PenilaianController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::with('subKriterias')->get();
        return view('kepsek.penilaians.index', [
            'title' => 'Penilaian',
            'kriterias' => $kriterias
        ]);
    }

    public function getData(Request $request)
    {
        $data = Guru::with(['penilaians' => function ($query) {
            $query->where('user_id', Auth::id());
        }, 'penilaians.kriteria'])->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nilai_summary', function ($guru) {
                if ($guru->penilaians->isEmpty()) {
                    return '<span class="badge bg-secondary">Belum dinilai</span>';
                }

                $summary = [];
                foreach ($guru->penilaians as $penilaian) {
                    $summary[] = $penilaian->kriteria->nama . ': ' . $penilaian->nilai;
                }

                return implode('<br>', $summary);
            })
            ->addColumn('action', function ($guru) {
                $buttons = '<button class="btn btn-sm btn-primary btn-nilai mr-1" data-id="' . $guru->id . '" data-nama="' . $guru->nama . '">Nilai</button>';

                if (!$guru->penilaians->isEmpty()) {
                    $buttons .= '<button class="btn btn-sm btn-warning btn-edit mr-1" data-guru-id="' . $guru->id . '" data-guru-nama="' . $guru->nama . '">Edit</button>';
                    $buttons .= '<button class="btn btn-sm btn-danger btn-delete" data-guru-id="' . $guru->id . '" data-guru-nama="' . $guru->nama . '">Hapus</button>';
                }

                return $buttons;
            })
            ->rawColumns(['nilai_summary', 'action'])
            ->make(true);
    }

    public function getPenilaian(Request $request)
    {
        $request->validate(['guru_id' => 'required|exists:gurus,id']);

        $penilaians = Penilaian::with(['kriteria.subKriterias' => function ($query) {
            $query->orderBy('nilai');
        }])
            ->where('user_id', Auth::id())
            ->where('guru_id', $request->guru_id)
            ->get();

        return response()->json($penilaians);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'nilai.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $kriteriaId = str_replace('nilai.', '', $attribute);
                    $exists = SubKriteria::where('kriteria_id', $kriteriaId)
                        ->where('nilai', $value)
                        ->exists();
                    if (!$exists) {
                        $fail('Nilai sub kriteria tidak valid.');
                    }
                }
            ],
        ]);

        foreach ($request->nilai as $kriteria_id => $nilai) {
            Penilaian::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'guru_id' => $request->guru_id,
                    'kriteria_id' => $kriteria_id,
                ],
                ['nilai' => $nilai]
            );
        }

        return response()->json(['message' => 'Penilaian berhasil disimpan']);
    }

    public function destroy(Penilaian $penilaian)
    {
        // Standard resource deletion
        $penilaian->delete();
        return response()->json(['message' => 'Penilaian berhasil dihapus']);
    }

    public function destroyByGuru(Request $request)
    {
        $request->validate(['guru_id' => 'required|exists:gurus,id']);

        $deleted = Penilaian::where('user_id', Auth::id())
            ->where('guru_id', $request->guru_id)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Penilaian berhasil dihapus']);
        }

        return response()->json(['message' => 'Gagal menghapus penilaian'], 500);
    }
}
