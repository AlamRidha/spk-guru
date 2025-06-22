<?php

namespace App\Http\Controllers\Wakur;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return view('wakur.dashboard', [
            'title' => 'Dashboard',
            'totalGuru' => Guru::count(),
            'totalKriteria' => Kriteria::count(),
        ]);
    }
}
