<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Hasil;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // Ambil penilaian dari kepala sekolah untuk guru yang login
        $penilaianKepsek = Penilaian::with('kriteria')
            ->where('guru_id', Auth::id())
            ->whereHas('kriteria', function ($q) {
                $q->where('penilai', 'kepsek');
            })
            ->get();

        // Ambil penilaian dari wakil kurikulum untuk guru yang login
        $penilaianWakur = Penilaian::with('kriteria')
            ->where('guru_id', Auth::id())
            ->whereHas('kriteria', function ($q) {
                $q->where('penilai', 'wakil_kurikulum');
            })
            ->get();

        // Hitung total nilai
        $totalNilaiKepsek = $penilaianKepsek->sum('nilai');
        $totalNilaiWakur = $penilaianWakur->sum('nilai');

        // Hitung nilai akhir (40% kepsek + 60% wakur)
        $nilaiAkhir = ($totalNilaiKepsek * 0.4) + ($totalNilaiWakur * 0.6);

        // Ambil data ranking semua guru
        $rankingData = $this->getRankingData();


        return view('guru.dashboard', [
            'title' => 'Dashboard Guru',
            'penilaianKepsek' => $penilaianKepsek,
            'penilaianWakur' => $penilaianWakur,
            'totalNilaiKepsek' => $totalNilaiKepsek,
            'totalNilaiWakur' => $totalNilaiWakur,
            'nilaiAkhir' => number_format($nilaiAkhir, 2),
            'totalGuru' => $rankingData->count(),
            'rankingData' => $rankingData,
            'rankingKepsek' => $this->getRankingKepsek(),
            'rankingWakur' => $this->getRankingWakur()
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
            ->map(function ($item, $index) {
                return [
                    'ranking' => $index + 1,
                    'nama_guru' => $item->guru->nama,
                    'nilai' => $item->nilai_optimasi
                ];
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
            ->map(function ($item, $index) {
                return [
                    'ranking' => $index + 1,
                    'nama_guru' => $item->guru->nama,
                    'nilai' => $item->nilai_optimasi
                ];
            });
    }
}
