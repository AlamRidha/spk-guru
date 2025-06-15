<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GuruController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $gurus = Guru::query();

            return DataTables::of($gurus)
                ->addIndexColumn()
                ->make(true);
        }

        return view('kepsek.gurus.index', [
            'title' => 'Manajemen Guru',
        ]);
    }
}
