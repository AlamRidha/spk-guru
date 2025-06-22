<?php

namespace App\Http\Controllers\Wakur;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RankingController extends Controller
{
    public function index()
    {
        try {
            // 1. Ambil data kriteria dan hitung bobot ternormalisasi
            $kriterias = Kriteria::orderBy('id')->get();
            $totalBobot = $kriterias->sum('bobot');

            $weights = [];
            foreach ($kriterias as $kriteria) {
                $weights[$kriteria->id] = [
                    'kode' => 'C' . $kriteria->id,
                    'nama' => $kriteria->nama,
                    'bobot' => $kriteria->bobot / $totalBobot,
                    'jenis' => $kriteria->jenis
                ];
            }

            // 2. Hitung sum of squares untuk normalisasi
            $sumSquares = [];
            foreach ($kriterias as $kriteria) {
                $sumSquares[$kriteria->id] = sqrt(
                    Penilaian::where('kriteria_id', $kriteria->id)
                        ->where('user_id', Auth::id())
                        ->sum(DB::raw('POW(nilai, 2)'))
                ) ?? 1;
            }

            // 3. Ambil data guru dan penilaian
            $gurus = Guru::with(['penilaians' => function ($query) {
                $query->where('user_id', Auth::id())
                    ->with('kriteria');
            }])->orderBy('nama')->get();

            // 4. Hitung nilai optimasi (Yi) untuk setiap guru
            $results = [];
            foreach ($gurus as $guru) {
                $benefit = 0;
                $cost = 0;
                $detailParts = [];

                foreach ($kriterias as $kriteria) {
                    $penilaian = $guru->penilaians->firstWhere('kriteria_id', $kriteria->id);
                    $nilai = $penilaian ? $penilaian->nilai : 0;
                    $normalized = $nilai / $sumSquares[$kriteria->id];

                    // Sesuai contoh Anda, semua kriteria dianggap benefit tapi C2 dikurangkan
                    if ($kriteria->id == 2) { // C2 dikurangkan
                        $cost += $normalized * $weights[$kriteria->id]['bobot'];
                        $detailParts[] = sprintf("(%.5f×%.5f)", $normalized, $weights[$kriteria->id]['bobot']);
                    } else {
                        $benefit += $normalized * $weights[$kriteria->id]['bobot'];
                        $detailParts[] = sprintf("(%.5f×%.5f)", $normalized, $weights[$kriteria->id]['bobot']);
                    }
                }

                $yi = $benefit - $cost;

                $results[] = [
                    'id' => $guru->id,
                    'guru' => $guru->nama,
                    'yi' => $yi,
                    'detail' => implode(" + ", $detailParts) . " = " . number_format($yi, 6)
                ];
            }

            // 5. Urutkan berdasarkan Yi tertinggi
            usort($results, function ($a, $b) {
                return $b['yi'] <=> $a['yi'];
            });

            return view('wakur.ranking.index', [
                'title' => 'Perangkingan MOORA',
                'ranking' => $results,
                'weights' => $weights,
                'kriterias' => $kriterias
            ]);
        } catch (\Exception $e) {
            Log::error('Error in RankingController: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan dalam perhitungan ranking');
        }
    }

    private function getMatriksData()
    {
        $gurus = Guru::with(['penilaians' => function ($query) {
            $query->where('user_id', Auth::id())
                ->with('kriteria')
                ->orderBy('kriteria_id');
        }])->orderBy('nama')->get();

        $kriterias = Kriteria::whereHas('penilaians', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('id')->get();

        $matriks = [];
        foreach ($gurus as $index => $guru) {
            $row = [
                'id' => $guru->id,
                'no' => $index + 1,
                'guru' => $guru->nama,
                'nilai' => []
            ];

            foreach ($kriterias as $kriteria) {
                $penilaian = $guru->penilaians->firstWhere('kriteria_id', $kriteria->id);
                $row['nilai'][$kriteria->id] = $penilaian ? $penilaian->nilai : 0;
            }

            $matriks[] = $row;
        }

        return [
            'matriks' => $matriks,
            'kriterias' => $kriterias
        ];
    }

    private function hitungNormalisasi($matriks, $kriterias)
    {
        $sumSquares = [];
        foreach ($kriterias as $kriteria) {
            $sum = 0;
            foreach ($matriks as $row) {
                $nilai = $row['nilai'][$kriteria->id] ?? 0;
                $sum += pow($nilai, 2);
            }
            $sumSquares[$kriteria->id] = sqrt($sum);
        }

        $normalized = [];
        foreach ($matriks as $row) {
            $normalizedRow = [];
            foreach ($kriterias as $kriteria) {
                $nilai = $row['nilai'][$kriteria->id] ?? 0;
                $divisor = $sumSquares[$kriteria->id] != 0 ? $sumSquares[$kriteria->id] : 1;

                $normalizedRow[$kriteria->id] = ($kriteria->jenis == 'cost')
                    ? (1 - ($nilai / $divisor))
                    : ($nilai / $divisor);
            }
            $normalized[] = [
                'id' => $row['id'],
                'guru' => $row['guru'],
                'nilai' => $normalizedRow
            ];
        }

        return [
            'normalized' => $normalized,
            'sum_squares' => $sumSquares
        ];
    }

    private function getNormalizedWeights($kriterias)
    {
        $totalBobot = $kriterias->sum('bobot');

        $weights = [];
        foreach ($kriterias as $kriteria) {
            $weights[$kriteria->id] = [
                'kode' => 'C' . $kriteria->id,
                'nama' => $kriteria->nama,
                'jenis' => $kriteria->jenis,
                'bobot' => $kriteria->bobot,
                'bobot_normalisasi' => $totalBobot > 0 ? $kriteria->bobot / $totalBobot : 0
            ];
        }

        return $weights;
    }

    private function hitungRanking($normalized, $bobot, $kriterias)
    {
        $results = [];

        foreach ($normalized as $row) {
            $benefit = 0;
            $cost = 0;
            $detail = [];

            foreach ($kriterias as $kriteria) {
                $weight = $bobot[$kriteria->id]['bobot_normalisasi'];
                $value = $row['nilai'][$kriteria->id];

                if ($kriteria->jenis == 'benefit') {
                    $benefit += $value * $weight;
                    $detail[] = sprintf("(%.5f × %.5f)", $value, $weight);
                } else {
                    $cost += $value * $weight;
                    $detail[] = sprintf("(%.5f × %.5f)", $value, $weight);
                }
            }

            $yi = $benefit - $cost;

            $results[] = [
                'id' => $row['id'],
                'guru' => $row['guru'],
                'yi' => $yi,
                'benefit' => $benefit,
                'cost' => $cost,
                'detail' => implode(" + ", $detail) . " = " . number_format($yi, 5)
            ];
        }

        // Urutkan dari Yi tertinggi ke terendah
        usort($results, function ($a, $b) {
            return $b['yi'] <=> $a['yi'];
        });

        // Tambahkan peringkat
        foreach ($results as $index => &$result) {
            $result['peringkat'] = $index + 1;
        }

        return $results;
    }
}
