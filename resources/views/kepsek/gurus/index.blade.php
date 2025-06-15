@extends('layouts.kepsek')

@section('title', $title)


@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover w-100" id="gurus-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Guru</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(function() {
            const table = $('#gurus-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('kepsek.gurus.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    }
                ],
            });
        });
    </script>
@endpush
