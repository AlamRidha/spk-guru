<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Hasil;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalGuru' => Guru::count(),
            'totalKriteria' => Kriteria::count(),
            'recentHasil' => Hasil::with('guru')
                ->orderBy('ranking', 'asc')
                ->limit(5)
                ->get()
        ]);
    }


    /**
     * Hitung MOORA
     */
    // public function calculateMoora(MooraService $moora)
    // {
    //     try {
    //         $hasil = $moora->hitungOptimasi();

    //         Alert::success('Berhasil', 'Perhitungan MOORA selesai!');
    //         return back()->with('hasil', $hasil);
    //     } catch (\Exception $e) {
    //         Alert::error('Error', $e->getMessage());
    //         return back();
    //     }
    // }
}
