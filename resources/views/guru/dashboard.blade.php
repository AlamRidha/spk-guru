@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Selamat Datang {{ Auth::user()->nama }}</h1>
            </div>
        </div>

        <!-- Bagian Ranking dengan Data Langsung dari Database -->
        <div class="row mt-4">
            <!-- Ranking Kepsek -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">Ranking Berdasarkan Kepala Sekolah</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%">Rank</th>
                                        <th>Nama Guru</th>
                                        <th width="20%">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        echo $rankingKepsek;
                                    @endphp --}}
                                    @foreach ($rankingKepsek as $rank)
                                        <tr>
                                            <td class="text-center">{{ $rank->ranking }}</td>
                                            <td>{{ $rank->nama_guru }}</td>
                                            <td class="text-center">{{ $rank->nilai_optimasi }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ranking Wakur -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">Ranking Berdasarkan Wakil Kurikulum</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%">Rank</th>
                                        <th>Nama Guru</th>
                                        <th width="20%">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        echo $rankingWakur;
                                    @endphp --}}
                                    @foreach ($rankingWakur as $rank)
                                        <tr>
                                            <td class="text-center">{{ $rank->ranking }}</td>
                                            <td>{{ $rank->nama_guru }}</td>
                                            <td class="text-center">{{ $rank->nilai_optimasi }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
