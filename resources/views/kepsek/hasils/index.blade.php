@extends('layouts.kepsek')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">{{ $title }}</h3>
            </div>
            <div class="card-body">
                <!-- Tabel Bobot Kriteria -->
                <div class="mb-4">
                    <h4 class="mb-3">Bobot Kriteria</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th width="10%">Kode</th>
                                    <th width="30%">Kriteria</th>
                                    <th width="15%" class="text-center">Jenis</th>
                                    <th width="20%" class="text-right">Bobot Asli</th>
                                    <th width="25%" class="text-right">Bobot Normalisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bobot as $item)
                                    <tr>
                                        <td>{{ $item->kode }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $item->jenis == 'benefit' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($item->jenis) }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($item->bobot_asli, 4) }}</td>
                                        <td class="text-right">{{ number_format($item->bobot_normalisasi, 5) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Hasil Ranking -->

                <div class="mb-4">
                    <h4 class="mb-3">Hasil Perhitungan Ranking</h4>
                    @if (count($optimasi) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="5%" class="text-center">Rank</th>
                                        <th width="25%">Nama Guru</th>
                                        <th width="15%" class="text-center">Nilai Optimasi (Yi)</th>
                                        <th width="50%">Detail Perhitungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($optimasi as $index => $row)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $row['guru'] }}</td>
                                            <td class="text-center font-weight-bold">{{ number_format($row['yi'], 5) }}</td>
                                            <td><small class="text-monospace">{{ $row['detail_perhitungan'] }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Tidak ada data ranking yang tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Contoh Perhitungan Manual -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">Contoh Perhitungan Manual</h5>
        </div>
        <div class="card-body">
            @foreach (array_slice($optimasi, 0, 3) as $row)
                <div class="mb-3">
                    <h6>Alternatif {{ $row['no'] }} ({{ $row['guru'] }})</h6>
                    <div class="bg-light p-3 mb-2 rounded">
                        <code class="text-dark">{!! nl2br($row['detail_perhitungan']) !!}</code>
                    </div>
                    <p class="mb-0">
                        Nilai Optimasi (Yi) = <strong>{{ number_format($row['yi'], 5) }}</strong>
                    </p>
                </div>
                @if (!$loop->last)
                    <hr>
                @endif
            @endforeach
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
