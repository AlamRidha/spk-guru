<?php

namespace App\Http\Controllers\Wakur;

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

        return view('wakur.gurus.index', [
            'title' => 'Manajemen Guru',
        ]);
    }
}
