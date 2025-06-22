<?php

namespace App\Http\Controllers\Wakur;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PenilaianController extends Controller
{
    public function index()
    {
        // Hanya ambil kriteria untuk penilai wakil_kurikulum
        $kriterias = Kriteria::where('penilai', 'wakil_kurikulum')
            ->with('subKriterias')
            ->get();

        return view('wakur.penilaians.index', [
            'title' => 'Penilaian',
            'kriterias' => $kriterias
        ]);
    }

    public function getData(Request $request)
    {
        $data = Guru::with(['penilaians' => function ($query) {
            $query->where('user_id', Auth::id())
                ->whereHas('kriteria', function ($q) {
                    $q->where('penilai', 'wakil_kurikulum');
                });
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
            ->whereHas('kriteria', function ($q) {
                $q->where('penilai', 'wakil_kurikulum');
            })
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
            // Pastikan kriteria ini untuk penilai wakil kurikulum
            $kriteria = Kriteria::where('id', $kriteria_id)
                ->where('penilai', 'wakil_kurikulum')
                ->first();

            if (!$kriteria) {
                continue; // Skip jika kriteria tidak untuk wakil kurikulum
            }

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
        // Pastikan penilaian ini milik user yang login dan untuk kriteria wakil kurikulum
        if (
            $penilaian->user_id != Auth::id() ||
            $penilaian->kriteria->penilai != 'wakil_kurikulum'
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $penilaian->delete();
        return response()->json(['message' => 'Penilaian berhasil dihapus']);
    }

    public function destroyByGuru(Request $request)
    {
        $request->validate(['guru_id' => 'required|exists:gurus,id']);

        $deleted = Penilaian::where('user_id', Auth::id())
            ->where('guru_id', $request->guru_id)
            ->whereHas('kriteria', function ($q) {
                $q->where('penilai', 'wakil_kurikulum');
            })
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Penilaian berhasil dihapus']);
        }

        return response()->json(['message' => 'Gagal menghapus penilaian'], 500);
    }

    public function matrikKeputusan()
    {
        try {
            // Ambil data guru dengan penilaiannya untuk wakil kurikulum
            $gurus = Guru::with(['penilaians' => function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereHas('kriteria', function ($q) {
                        $q->where('penilai', 'wakil_kurikulum');
                    })
                    ->with('kriteria')
                    ->orderBy('kriteria_id');
            }])
                ->get();

            // Ambil kriteria wakil kurikulum yang sudah dinilai
            $kriteriaDinilai = Kriteria::where('penilai', 'wakil_kurikulum')
                ->whereHas('penilaians', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->orderBy('id')
                ->get();

            $matriks = [];
            foreach ($gurus as $index => $guru) {
                $row = [
                    'no' => $index + 1,
                    'guru' => $guru->nama,
                    'nilai' => []
                ];

                foreach ($kriteriaDinilai as $kriteria) {
                    $penilaian = $guru->penilaians->firstWhere('kriteria_id', $kriteria->id);
                    $row['nilai'][] = $penilaian ? $penilaian->nilai : '-';
                }

                $matriks[] = $row;
            }

            return response()->json([
                'matriks' => $matriks,
                'total_kriteria' => $kriteriaDinilai->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMatriksKeputusan: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function normalisasiMatriks()
    {
        try {
            // Ambil data guru dengan penilaian wakil kurikulum
            $gurus = Guru::with(['penilaians' => function ($query) {
                $query->where('user_id', Auth::id())
                    ->whereHas('kriteria', function ($q) {
                        $q->where('penilai', 'wakil_kurikulum');
                    })
                    ->with('kriteria')
                    ->orderBy('kriteria_id');
            }])
                ->get();

            // Ambil kriteria wakil kurikulum yang sudah dinilai
            $kriteriaDinilai = Kriteria::where('penilai', 'wakil_kurikulum')
                ->whereHas('penilaians', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->orderBy('id')
                ->get();

            // Hitung matriks keputusan
            $matriks = [];
            foreach ($gurus as $index => $guru) {
                $row = [
                    'no' => $index + 1,
                    'guru' => $guru->nama,
                    'nilai' => []
                ];

                foreach ($kriteriaDinilai as $kriteria) {
                    $penilaian = $guru->penilaians->firstWhere('kriteria_id', $kriteria->id);
                    $row['nilai'][] = $penilaian ? $penilaian->nilai : 0;
                }

                $matriks[] = $row;
            }

            // Hitung normalisasi
            $normalisasi = [
                'sum_squares' => [],
                'normalized' => []
            ];

            // Hitung sum of squares untuk setiap kriteria
            foreach ($kriteriaDinilai as $i => $kriteria) {
                $sum = 0;
                foreach ($matriks as $row) {
                    $sum += pow($row['nilai'][$i], 2);
                }
                $normalisasi['sum_squares'][$i] = sqrt($sum);
            }

            // Hitung nilai normalisasi
            foreach ($matriks as $row) {
                $normalizedRow = [
                    'no' => $row['no'],
                    'guru' => $row['guru'],
                    'nilai' => []
                ];

                foreach ($row['nilai'] as $i => $nilai) {
                    $normalizedRow['nilai'][$i] = $normalisasi['sum_squares'][$i] != 0
                        ? $nilai / $normalisasi['sum_squares'][$i]
                        : 0;
                }

                $normalisasi['normalized'][] = $normalizedRow;
            }

            return view('wakur.penilaians.normalisasi', [
                'title' => 'Normalisasi Matriks Keputusan (Wakil Kurikulum)',
                'matriks' => $matriks,
                'normalisasi' => $normalisasi,
                'totalKriteria' => $kriteriaDinilai->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in normalisasiMatriks: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses data');
        }
    }
}
