@extends('layouts.kepsek')

@section('title', $title)

@section('content')
    <div class="container">
        <h3>{{ $title }}</h3>

        <!-- Tabel Matriks Awal -->
        <h4 class="mt-4">Matriks Keputusan</h4>
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>No</th>
                    <th>Alternatif</th>
                    @for ($i = 1; $i <= $totalKriteria; $i++)
                        <th>C{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($matriks as $row)
                    <tr>
                        <td>{{ $row['no'] }}</td>
                        <td>{{ $row['guru'] }}</td>
                        @foreach ($row['nilai'] as $nilai)
                            <td>{{ $nilai }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tabel Normalisasi -->
        <h4 class="mt-4">Matriks Normalisasi</h4>
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>No</th>
                    <th>Alternatif</th>
                    @for ($i = 1; $i <= $totalKriteria; $i++)
                        <th>
                            C{{ $i }}<br>
                            <small class="text-muted">√∑ =
                                {{ number_format($normalisasi['sum_squares'][$i - 1], 5) }}</small>
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($normalisasi['normalized'] as $row)
                    <tr>
                        <td>{{ $row['no'] }}</td>
                        <td>{{ $row['guru'] }}</td>
                        @foreach ($row['nilai'] as $nilai)
                            <td>{{ number_format($nilai, 5) }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Penjelasan Perhitungan -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5>Penjelasan Perhitungan</h5>
            </div>
            <div class="card-body">
                @for ($i = 0; $i < $totalKriteria; $i++)
                    <h6>Normalisasi Kolom C{{ $i + 1 }}</h6>
                    <p>
                        C{{ $i + 1 }} = √(
                        @foreach ($matriks as $idx => $row)
                            {{ $row['nilai'][$i] }}²{{ $idx < count($matriks) - 1 ? ' + ' : '' }}
                        @endforeach
                        ) =
                        √{{ number_format(
                            array_reduce(
                                $matriks,
                                function ($carry, $item) use ($i) {
                                    return $carry + pow($item['nilai'][$i], 2);
                                },
                                0,
                            ),
                            2,
                        ) }}
                        = {{ number_format($normalisasi['sum_squares'][$i], 5) }}
                    </p>
                    <p class="mb-4">
                        @foreach ($matriks as $idx => $row)
                            X{{ $idx + 1 }},{{ $i + 1 }} =
                            {{ $row['nilai'][$i] }}/{{ number_format($normalisasi['sum_squares'][$i], 5) }}
                            = {{ number_format($row['nilai'][$i] / $normalisasi['sum_squares'][$i], 5) }}<br>
                        @endforeach
                    </p>
                @endfor
            </div>
        </div>
    </div>
@endsection
