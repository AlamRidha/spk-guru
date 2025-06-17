@extends('layouts.kepsek')

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
    </div>

    <!-- Modal for Create/Edit -->
    <div class="modal fade" id="modal-nilai" tabindex="-1" role="dialog" aria-labelledby="modalNilaiLabel" aria-hidden="true">
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
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                const table = $('#penilaian-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('kepsek.penilaians.data') }}',
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
                    $.get('{{ route('kepsek.penilaians.get') }}?guru_id=' + guruId, function(data) {
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
                    const url = '{{ route('kepsek.penilaians.store') }}';

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
                                url: '{{ route('kepsek.penilaians.destroy-by-guru') }}',
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
        });
    </script>
@endpush
