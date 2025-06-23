<?php

namespace App\Http\Controllers\Wakur;

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

            return view('wakur.hasils.index', [
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

    /**
     * Menyimpan hasil ranking ke tabel hasils
     */
    private function simpanHasilRanking($optimasi)
    {
        DB::beginTransaction();
        try {
            $tahun = now()->year;
            $penilaiId = Auth::id();

            // Hapus ranking lama untuk wakil_kurikulum di tahun ini
            $deletedRows = Hasil::where('penilai_id', $penilaiId)
                ->where('tahun_penilaian', $tahun)
                ->where('jenis_penilai', 'wakil_kurikulum')
                ->delete();

            Log::info("Deleted $deletedRows old records");

            // Simpan ranking baru
            foreach ($optimasi as $index => $row) {
                $guru = Guru::where('nama', $row['guru'])->first();

                if (!$guru) {
                    Log::error("Guru tidak ditemukan: " . $row['guru']);
                    continue;
                }

                $hasil = Hasil::create([
                    'guru_id' => $guru->id,
                    'penilai_id' => $penilaiId,
                    'nilai_optimasi' => $row['yi'],
                    'ranking' => $index + 1,
                    'tahun_penilaian' => $tahun,
                    'jenis_penilai' => 'wakil_kurikulum'
                ]);

                if (!$hasil) {
                    Log::error("Gagal menyimpan data untuk guru: " . $row['guru']);
                    throw new \Exception("Gagal menyimpan data ranking");
                }
            }

            DB::commit();

            // Verifikasi data tersimpan
            $savedCount = Hasil::where('penilai_id', $penilaiId)
                ->where('tahun_penilaian', $tahun)
                ->where('jenis_penilai', 'wakil_kurikulum')
                ->count();

            Log::info("Jumlah data tersimpan: $savedCount");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan ranking wakil kurikulum: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan hasil ranking wakil kurikulum');
        }
    }

    private function getBobotKriteria()
    {
        $kriterias = Kriteria::where('penilai', 'wakil_kurikulum')
            ->orderBy('id')
            ->get();


        $totalBobot = $kriterias->sum('bobot');

        return $kriterias->map(function ($item, $index) use ($totalBobot) {
            return (object)[
                'kode' => "C" . ($index + 1),
                'nama' => $item->nama,
                'jenis' => $item->jenis,
                'bobot_asli' => $item->bobot,
                'bobot_normalisasi' => $totalBobot > 0 ? $item->bobot / $totalBobot : 0
            ];
        })->toArray();

        Log::info('Bobot Kriteria:', $bobot);
    }

    private function getMatriksData()
    {
        $gurus = Guru::with(['penilaians' => function ($query) {
            $query->where('user_id', Auth::id())
                ->whereHas('kriteria', fn($q) => $q->where('penilai', 'wakil_kurikulum'))
                ->with('kriteria')
                ->orderBy('kriteria_id');
        }])
            ->orderBy('nama')
            ->get();

        $kriteriaDinilai = Kriteria::where('penilai', 'wakil_kurikulum')
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

        Log::info('Matriks Data:', $matriks);

        return [
            'matriks' => $matriks,
            'total_kriteria' => $kriteriaDinilai->count()
        ];
    }

    private function hitungNormalisasi($matriks, $totalKriteria)
    {
        // Hitung sum of squares untuk setiap kriteria
        $sumSquares = array_fill(0, $totalKriteria, 0);

        foreach ($matriks as $row) {
            for ($i = 0; $i < $totalKriteria; $i++) {
                if (isset($row['nilai'][$i])) {
                    $sumSquares[$i] += pow($row['nilai'][$i], 2);
                }
            }
        }

        // Hitung akar kuadrat dengan pembulatan
        $sumSquares = array_map(function ($val) {
            return sqrt($val);
        }, $sumSquares);

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
                $divisor = $sumSquares[$i] ?? 1;
                $normalizedRow['nilai'][$i] = $divisor != 0 ? $nilai / $divisor : 0;
            }

            $normalized[] = $normalizedRow;
        }

        Log::info('Sum Squares:', $sumSquares);
        Log::info('Normalized Data:', $normalized);

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
                if (!isset($bobot[$i])) continue;

                $kriteria = $bobot[$i];
                $weight = $kriteria->bobot_normalisasi;

                if ($kriteria->jenis == 'cost') {
                    $yi -= $xij * $weight;
                    $detailParts[] = sprintf(" - (%.5f × %.5f)", $xij, $weight);
                } else {
                    $yi += $xij * $weight;
                    $detailParts[] = sprintf(" + (%.5f × %.5f)", $xij, $weight);
                }
            }

            $results[] = [
                'no' => $row['no'],
                'guru' => $row['guru'],
                'yi' => $yi,
                'detail_perhitungan' => 'Yi =' . implode('', $detailParts) . ' = ' . number_format($yi, 5)
            ];
        }

        Log::info("Perhitungan Yi untuk {$row['guru']}:");
        foreach ($row['nilai'] as $i => $xij) {
            if (!isset($bobot[$i])) continue;
            $kriteria = $bobot[$i];
            Log::info("C" . ($i + 1) . " ({$kriteria->jenis}): {$xij} × {$kriteria->bobot_normalisasi}");
        }

        return $results;
    }
}
