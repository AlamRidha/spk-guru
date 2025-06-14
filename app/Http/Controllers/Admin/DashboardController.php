<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Hasil;
use App\Models\Kriteria;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'totalGuru' => Guru::count(),
            'totalKriteria' => Kriteria::count(),
            'totalUser' => User::count(),
            'recentHasil' => Hasil::with('guru')
                ->orderBy('ranking', 'asc')
                ->limit(5)
                ->get()
        ]);
    }


    public function getNormalizedWeights()
    {
        $kriterias = Kriteria::select('id', 'nama', 'bobot')->get();
        $totalBobot = $kriterias->sum('bobot');

        $normalized = $kriterias->map(function ($kriteria, $index) use ($totalBobot) {
            return [
                'kode' => 'C' . ($index + 1),
                'nama' => $kriteria->nama,
                'bobot_asli' => (float)$kriteria->bobot,
                'bobot_normalisasi' => $totalBobot > 0 ? (float)$kriteria->bobot / $totalBobot : '0.00000'
            ];
        });

        return response()->json($normalized);
    }
}
