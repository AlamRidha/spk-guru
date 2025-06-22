@extends('layouts.wakur')

@section('title', $title)

<style>
    .modal-header .close {
        color: #fff;
        text-shadow: 0 1px 0 #fff;
        opacity: 1;
    }

    .modal-header .close:hover {
        opacity: 0.75;
    }
</style>

@section('content')
    <div class="container">
        <h3>{{ $title }}</h3>
        <table id="penilaian-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Guru</th>
                    <th>Nilai</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>

        <table class="table table-bordered mt-4 " id="matriks-table">
            <thead class="bg-light text-center">
                <tr>
                    <th>Alternatif</th>
                    @foreach ($kriterias->sortBy('id') as $kriteria)
                        <th title="{{ $kriteria->nama }}">C{{ $loop->iteration }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <!-- Data akan diisi oleh server-side processing -->
            </tbody>
        </table>
    </div>


    <!-- Modal for Create/Edit -->
    <div class="modal fade" id="modal-nilai" tabindex="-1" role="dialog" aria-labelledby="modalNilaiLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form-nilai">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="modalNilaiLabel">Beri Nilai untuk <span
                                id="nama-guru"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="guru_id" id="guru_id">
                        @foreach ($kriterias as $kriteria)
                            <div class="mb-3">
                                <label>{{ $kriteria->nama }}</label>
                                <select name="nilai[{{ $kriteria->id }}]" class="form-control nilai-select" required>
                                    <option value="">-- Pilih Sub Kriteria --</option>
                                    @foreach ($kriteria->subKriterias as $sub)
                                        <option value="{{ $sub->nilai }}" data-keterangan="{{ $sub->keterangan }}">
                                            {{ $sub->nama }} (Nilai: {{ $sub->nilai }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted keterangan-display"></small>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Load matriks keputusan
            function loadMatriks() {
                $.ajax({
                    url: '{{ route('wakur.penilaians.matriks') }}',
                    method: 'GET',
                    success: function(response) {
                        let html = '';
                        let header = '<tr><th>No</th><th>Alternatif</th>';

                        // Buat header C1, C2, dst berdasarkan jumlah kriteria
                        for (let i = 1; i <= response.total_kriteria; i++) {
                            header += `<th>C${i}</th>`;
                        }
                        header += '</tr>';

                        // Buat body tabel
                        response.matriks.forEach(row => {
                            html += `<tr>
                    <td>${row.no}</td>
                    <td>${row.guru}</td>`;

                            // Tambahkan nilai untuk setiap kriteria
                            row.nilai.forEach(nilai => {
                                html += `<td class="text-center">${nilai}</td>`;
                            });

                            html += `</tr>`;
                        });

                        $('#matriks-table thead').html(header);
                        $('#matriks-table tbody').html(html);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        Swal.fire('Error', 'Gagal memuat matriks keputusan', 'error');
                    }
                });
            }

            // Load pertama kali
            loadMatriks();

            // Refresh setiap kali ada perubahan data
            $(document).on('penilaian.updated', function() {
                loadMatriks();
            });

            const table = $('#penilaian-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('wakur.penilaians.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nilai_summary',
                        name: 'nilai_summary',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Show modal for create
            $(document).on('click', '.btn-nilai', function() {
                $('#form-nilai')[0].reset();
                $('.nilai-select').val('');
                $('#guru_id').val($(this).data('id'));
                $('#penilaian_id').val('');
                $('#nama-guru').text($(this).data('nama'));
                $('#modal-nilai').modal('show');
            });

            // Show keterangan when selecting subkriteria
            $(document).on('change', '.nilai-select', function() {
                const selectedOption = $(this).find('option:selected');
                const keterangan = selectedOption.data('keterangan');
                $(this).siblings('.keterangan-display').text(keterangan || '');
            });

            // Show modal for edit
            $(document).on('click', '.btn-edit', function() {
                const guruId = $(this).data('guru-id');
                const guruNama = $(this).data('guru-nama');

                $('#form-nilai')[0].reset();
                $('.nilai-select').val('');
                $('.keterangan-display').text('');
                $('#guru_id').val(guruId);
                $('#nama-guru').text(guruNama);

                // Load existing values
                $.get('{{ route('wakur.penilaians.get') }}?guru_id=' + guruId, function(
                    data) {
                    data.forEach(item => {
                        const select = $(
                            `select[name="nilai[${item.kriteria_id}]"]`);
                        select.val(item.nilai);

                        // Find and display the matching subkriteria keterangan
                        const selectedSub = item.kriteria.sub_kriterias.find(
                            sub => sub.nilai == item.nilai
                        );
                        if (selectedSub) {
                            select.siblings('.keterangan-display').text(
                                selectedSub.keterangan);
                        }
                    });
                    $('#modal-nilai').modal('show');
                });
            });

            // Submit form
            $('#form-nilai').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const url = '{{ route('wakur.penilaians.store') }}';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: res => {
                        $('#modal-nilai').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    },
                    error: err => {
                        if (err.status === 419) {
                            Swal.fire('Gagal',
                                'Session expired. Please refresh the page.',
                                'error');
                            window.location.reload();
                        } else {
                            Swal.fire('Gagal', err.responseJSON.message ||
                                'Terjadi kesalahan saat menyimpan data', 'error'
                            );
                        }
                    }
                });
            });

            // Delete functionality
            $(document).on('click', '.btn-delete', function() {
                const guruId = $(this).data('guru-id');
                const guruNama = $(this).data('guru-nama');

                Swal.fire({
                    title: 'Hapus Penilaian?',
                    text: `Anda yakin ingin menghapus penilaian untuk ${guruNama}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('wakur.penilaians.destroy-by-guru') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                guru_id: guruId
                            },
                            success: res => {
                                table.ajax.reload();
                                Swal.fire('Berhasil', res.message,
                                    'success');
                            },
                            error: err => {
                                console.log(err); // Log error to console
                                Swal.fire(
                                    'Gagal',
                                    err.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus data',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
