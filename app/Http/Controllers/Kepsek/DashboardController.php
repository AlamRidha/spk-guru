<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        return view('kepsek.dashboard', [
            'title' => 'Dashboard',
            'totalGuru' => Guru::count(),
        ]);
    }
}
