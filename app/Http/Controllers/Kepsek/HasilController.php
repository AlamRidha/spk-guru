<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MooraController;
use App\Models\Guru;
use App\Models\Hasil;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class HasilController extends Controller
{

    public function index()
    {
        try {
            // 1. Ambil dan normalisasi bobot kriteria
            $bobot = $this->getBobotKriteria();

            // 2. Ambil data matriks keputusan
            $matriksData = $this->getMatriksData();

            // 3. Normalisasi matriks keputusan
            $normalisasi = $this->hitungNormalisasi($matriksData['matriks'], $matriksData['total_kriteria']);

            // 4. Hitung nilai optimasi (Yi)
            $optimasi = $this->hitungNilaiOptimasi($normalisasi['normalized'], $bobot);

            // 5. Urutkan berdasarkan nilai Yi tertinggi
            usort($optimasi, function ($a, $b) {
                return $b['yi'] <=> $a['yi'];
            });

            // 6. Simpan hasil ranking ke database
            $this->simpanHasilRanking($optimasi);

            return view('kepsek.hasils.index', [
                'title' => 'Hasil Perhitungan MOORA',
                'bobot' => $bobot,
                'optimasi' => $optimasi,
                'totalBobot' => array_sum(array_column($bobot, 'bobot_asli'))
            ]);
        } catch (\Exception $e) {
            Log::error('Error in HasilController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Simpan hasil ranking ke database
    private function simpanHasilRanking($optimasi)
    {
        DB::beginTransaction();
        try {
            $tahun = now()->year;
            $penilaiId = Auth::id();

            // Hapus ranking lama untuk kepsek di tahun ini
            Hasil::where('penilai_id', $penilaiId)
                ->where('tahun_penilaian', $tahun)
                ->where('jenis_penilai', 'kepsek')
                ->delete();

            // Simpan ranking baru
            foreach ($optimasi as $index => $row) {
                $guru = Guru::where('nama', $row['guru'])->first();

                if ($guru) {
                    Hasil::create([
                        'guru_id' => $guru->id,
                        'penilai_id' => $penilaiId,
                        'nilai_optimasi' => $row['yi'],
                        'ranking' => $index + 1,
                        'tahun_penilaian' => $tahun,
                        'jenis_penilai' => 'kepsek'
                    ]);
                }
            }

            DB::commit();
            Log::info('Data ranking berhasil disimpan', [
                'tahun' => $tahun,
                'penilai_id' => $penilaiId,
                'jumlah_data' => count($optimasi)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan ranking: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan hasil ranking');
        }
    }

    private function getBobotKriteria()
    {
        return Kriteria::where('penilai', 'kepsek')
            ->orderBy('id')
            ->get()
            ->map(function ($item, $index) {
                $totalBobot = Kriteria::where('penilai', 'kepsek')->sum('bobot');

                return (object)[
                    'kode' => "C" . $index + 1,
                    'nama' => $item->nama,
                    'jenis' => $item->jenis,
                    'bobot_asli' => $item->bobot,
                    'bobot_normalisasi' => $totalBobot > 0 ? $item->bobot / $totalBobot : 0
                ];
            })
            ->toArray();
    }

    private function getMatriksData()
    {
        $gurus = Guru::with(['penilaians' => function ($query) {
            $query->where('user_id', Auth::id())
                ->whereHas('kriteria', fn($q) => $q->where('penilai', 'kepsek'))
                ->with('kriteria')
                ->orderBy('kriteria_id');
        }])
            ->orderBy('nama')
            ->get();

        $kriteriaDinilai = Kriteria::where('penilai', 'kepsek')
            ->whereHas('penilaians', fn($q) => $q->where('user_id', Auth::id()))
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
                $row['nilai'][] = $penilaian ? $penilaian->nilai : 0;
            }

            $matriks[] = $row;
        }

        return [
            'matriks' => $matriks,
            'total_kriteria' => $kriteriaDinilai->count()
        ];
    }

    private function hitungNormalisasi($matriks, $totalKriteria)
    {
        $sumSquares = array_fill(0, $totalKriteria, 0);

        // Hitung sum of squares untuk setiap kriteria
        foreach ($matriks as $row) {
            for ($i = 0; $i < $totalKriteria; $i++) {
                $nilai = $row['nilai'][$i] ?? 0;
                $sumSquares[$i] += pow($nilai, 2);
            }
        }

        // Hitung akar kuadrat
        $sumSquares = array_map('sqrt', $sumSquares);

        // Normalisasi matriks
        $normalized = [];
        foreach ($matriks as $row) {
            $normalizedRow = [
                'no' => $row['no'],
                'guru' => $row['guru'],
                'nilai' => []
            ];

            for ($i = 0; $i < $totalKriteria; $i++) {
                $nilai = $row['nilai'][$i] ?? 0;
                $divisor = $sumSquares[$i] ?: 1; // Hindari division by zero
                $normalizedRow['nilai'][$i] = $nilai / $divisor;
            }

            $normalized[] = $normalizedRow;
        }

        return [
            'normalized' => $normalized,
            'sum_squares' => $sumSquares
        ];
    }

    private function hitungNilaiOptimasi($normalizedData, $bobot)
    {
        $results = [];

        foreach ($normalizedData as $row) {
            $yi = 0;
            $detailParts = [];

            foreach ($row['nilai'] as $i => $xij) {
                $kriteria = $bobot[$i] ?? null;
                if (!$kriteria) continue;

                $weight = $kriteria->bobot_normalisasi;
                $operator = '+';

                if ($kriteria->jenis == 'benefit') {
                    $yi += $xij * $weight;
                } else {
                    $yi -= $xij * $weight;
                    $operator = '-';
                }

                $detailParts[] = sprintf(
                    "%s(%.5f × %.5f)",
                    $operator,
                    $xij,
                    $weight
                );
            }

            $results[] = [
                'no' => $row['no'],
                'guru' => $row['guru'],
                'yi' => $yi,
                'detail_perhitungan' => 'Yi = ' . implode(' ', $detailParts) . ' = ' . number_format($yi, 5)
            ];
        }

        return $results;
    }
}
