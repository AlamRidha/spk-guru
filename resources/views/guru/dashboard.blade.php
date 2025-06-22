@extends('layouts.guru')

@section('title', 'Dashboard Guru')

<style>
    .bg-purple {
        background-color: #6f42c1;
        color: white;
    }

    .table-primary {
        background-color: #cfe2ff;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .card-header {
        font-weight: bold;
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Selamat Datang {{ Auth::user()->nama }}</h1>
            </div>
        </div>

        <div class="row">
            <!-- Penilaian Kepala Sekolah -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Penilaian Kepala Sekolah</h3>
                    </div>
                    <div class="card-body">
                        @if ($penilaianKepsek->isEmpty())
                            <div class="alert alert-info">Belum ada penilaian dari Kepala Sekolah</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kriteria</th>
                                            <th>Nilai</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penilaianKepsek as $nilai)
                                            <tr>
                                                <td>{{ $nilai->kriteria->nama }}</td>
                                                <td class="text-center">{{ $nilai->nilai }}</td>
                                                <td class="text-center">{{ $nilai->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Penilaian Wakil Kurikulum -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Penilaian Wakil Kurikulum</h3>
                    </div>
                    <div class="card-body">
                        @if ($penilaianWakur->isEmpty())
                            <div class="alert alert-info">Belum ada penilaian dari Wakil Kurikulum</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kriteria</th>
                                            <th>Nilai</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penilaianWakur as $nilai)
                                            <tr>
                                                <td>{{ $nilai->kriteria->nama }}</td>
                                                <td class="text-center">{{ $nilai->nilai }}</td>
                                                <td class="text-center">{{ $nilai->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking Per Penilai -->
        <div class="row mt-4">
            <!-- Ranking Kepsek -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Ranking Berdasarkan Kepala Sekolah</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">Rank</th>
                                        <th>Nama Guru</th>
                                        <th width="15%">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rankingKepsek as $rank)
                                        <tr class="{{ $rank['nama_guru'] == Auth::user()->nama ? 'table-primary' : '' }}">
                                            <td class="text-center">{{ $rank['ranking'] }}</td>
                                            <td>{{ $rank['nama_guru'] }}</td>
                                            <td class="text-center">{{ number_format($rank['nilai'], 2) }}</td>
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
                    <div class="card-header bg-info">
                        <h3 class="card-title">Ranking Berdasarkan Wakil Kurikulum</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">Rank</th>
                                        <th>Nama Guru</th>
                                        <th width="15%">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rankingWakur as $rank)
                                        <tr class="{{ $rank['nama_guru'] == Auth::user()->nama ? 'table-primary' : '' }}">
                                            <td class="text-center">{{ $rank['ranking'] }}</td>
                                            <td>{{ $rank['nama_guru'] }}</td>
                                            <td class="text-center">{{ number_format($rank['nilai'], 2) }}</td>
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
