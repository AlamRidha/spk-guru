@extends('layouts.kepsek')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $title }}</h3>
                <div class="card-tools">
                    <a href="{{ route('kepsek.ranking.export') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-primary">
                            <tr>
                                <th width="5%">Rank</th>
                                <th width="5%">No</th>
                                <th>Nama Guru</th>
                                <th class="text-center" width="15%">Nilai Optimasi</th>
                                <th width="30%">Detail Perhitungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($optimasi as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $row['no'] }}</td>
                                    <td>{{ $row['guru'] }}</td>
                                    <td class="text-center font-weight-bold">{{ number_format($row['yi'], 5) }}</td>
                                    <td><small>{{ $row['detail_perhitungan'] }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
