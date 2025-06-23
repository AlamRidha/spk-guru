<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Hasil;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    // public function index()
    // {
    //     // Ambil penilaian dari kepala sekolah untuk guru yang login
    //     $penilaianKepsek = Penilaian::with('kriteria')
    //         ->where('guru_id', Auth::id())
    //         ->whereHas('kriteria', function ($q) {
    //             $q->where('penilai', 'kepsek');
    //         })
    //         ->get();

    //     // Ambil penilaian dari wakil kurikulum untuk guru yang login
    //     $penilaianWakur = Penilaian::with('kriteria')
    //         ->where('guru_id', Auth::id())
    //         ->whereHas('kriteria', function ($q) {
    //             $q->where('penilai', 'wakil_kurikulum');
    //         })
    //         ->get();

    //     // Hitung total nilai
    //     $totalNilaiKepsek = $penilaianKepsek->sum('nilai');
    //     $totalNilaiWakur = $penilaianWakur->sum('nilai');

    //     // Hitung nilai akhir (40% kepsek + 60% wakur)
    //     $nilaiAkhir = ($totalNilaiKepsek * 0.4) + ($totalNilaiWakur * 0.6);

    //     // Ambil data ranking semua guru
    //     $rankingData = $this->getRankingData();


    //     return view('guru.dashboard', [
    //         'title' => 'Dashboard Guru',
    //         'penilaianKepsek' => $penilaianKepsek,
    //         'penilaianWakur' => $penilaianWakur,
    //         'totalNilaiKepsek' => $totalNilaiKepsek,
    //         'totalNilaiWakur' => $totalNilaiWakur,
    //         'nilaiAkhir' => number_format($nilaiAkhir, 2),
    //         'totalGuru' => $rankingData->count(),
    //         'rankingData' => $rankingData,
    //         'rankingKepsek' => $this->getRankingKepsek(),
    //         'rankingWakur' => $this->getRankingWakur()
    //     ]);
    // }

    public function index()
    {
        $rankingKepsek = DB::table('hasils')
            ->where('tahun_penilaian', date('Y'))
            ->where('jenis_penilai', 'kepsek')
            ->join('gurus', 'hasils.guru_id', '=', 'gurus.id')
            ->select(
                'hasils.id',
                'hasils.guru_id',
                'hasils.penilai_id',
                'hasils.nilai_optimasi',
                'hasils.ranking',
                'gurus.nama as nama_guru'
            )
            ->orderBy('ranking')
            ->get();

        $rankingWakur = DB::table('hasils')
            ->where('tahun_penilaian', date('Y'))
            ->where('jenis_penilai', 'wakil_kurikulum')
            ->join('gurus', 'hasils.guru_id', '=', 'gurus.id')
            ->select(
                'hasils.id',
                'hasils.guru_id',
                'hasils.penilai_id',
                'hasils.nilai_optimasi',
                'hasils.ranking',
                'gurus.nama as nama_guru'
            )
            ->orderBy('ranking')
            ->get();

        return view('guru.dashboard', [
            'title' => 'Dashboard Guru',
            'rankingKepsek' => $rankingKepsek,
            'rankingWakur' => $rankingWakur
        ]);
    }

    private function getRankingData()
    {
        $tahun = now()->year;

        return Hasil::with('guru')
            ->where('tahun_penilaian', $tahun)
            ->get()
            ->groupBy('guru_id')
            ->map(function ($item, $guruId) {
                $kepsek = $item->where('jenis_penilai', 'kepsek')->first();
                $wakur = $item->where('jenis_penilai', 'wakil_kurikulum')->first();

                return [
                    'guru_id' => $guruId,
                    'nama_guru' => $item->first()->guru->nama,
                    'nilai_kepsek' => $kepsek ? $kepsek->nilai_optimasi : 0,
                    'nilai_wakur' => $wakur ? $wakur->nilai_optimasi : 0,
                    'nilai_akhir' => ($kepsek ? $kepsek->nilai_optimasi * 0.4 : 0)
                        + ($wakur ? $wakur->nilai_optimasi * 0.6 : 0)
                ];
            })
            ->sortByDesc('nilai_akhir')
            ->values()
            ->map(function ($item, $index) {
                $item['ranking'] = $index + 1;
                return $item;
            });
    }

    private function getRankingKepsek()
    {
        $tahun = now()->year;

        return Hasil::with('guru')
            ->where('tahun_penilaian', $tahun)
            ->where('jenis_penilai', 'kepsek')
            ->orderByDesc('nilai_optimasi')
            ->get()
            ->groupBy('guru_id') // Group by guru_id untuk menghindari duplikat
            ->map(function ($items, $guruId) {
                return [
                    'guru_id' => $guruId,
                    'nama_guru' => $items->first()->guru->nama,
                    'nilai' => $items->sum('nilai_optimasi') // Jumlahkan jika ada multiple entries
                ];
            })
            ->sortByDesc('nilai')
            ->values()
            ->map(function ($item, $index) {
                $item['ranking'] = $index + 1;
                return $item;
            });
    }

    private function getRankingWakur()
    {
        $tahun = now()->year;

        return Hasil::with('guru')
            ->where('tahun_penilaian', $tahun)
            ->where('jenis_penilai', 'wakil_kurikulum')
            ->orderByDesc('nilai_optimasi')
            ->get()
            ->groupBy('guru_id') // Group by guru_id untuk menghindari duplikat
            ->map(function ($items, $guruId) {
                return [
                    'guru_id' => $guruId,
                    'nama_guru' => $items->first()->guru->nama,
                    'nilai' => $items->sum('nilai_optimasi') // Jumlahkan jika ada multiple entries
                ];
            })
            ->sortByDesc('nilai')
            ->values()
            ->map(function ($item, $index) {
                $item['ranking'] = $index + 1;
                return $item;
            });
    }
}
