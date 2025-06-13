@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white my-auto">{{ $title }}</h3>
                <button class="btn btn-light" id="btn-create-kriteria">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Kriteria
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover w-100" id="kriterias-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>Bobot</th>
                                <th>Jenis</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            @if (count($normalizedWeights ?? []))
                <div class="mt-4 mx-4">
                    <h5 class="font-weight-bold">Perbaikan Bobot Kriteria (Normalisasi)</h5>
                    <table class="table table-bordered" id="normalized-weights-table" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kriteria</th>
                                <th>Bobot Asli</th>
                                <th>Bobot Normalisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Create/Edit -->
    <div class="modal fade" id="kriteriaModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="kriteria-form">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="kriteriaModalLabel">Tambah Kriteria</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="kriteria_id">
                        <div class="form-group">
                            <label for="nama">Nama Kriteria <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" required>
                            <small class="text-danger" id="nama-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="bobot">Bobot <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" class="form-control" name="bobot" id="bobot"
                                required>
                            <small class="text-danger" id="bobot-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="jenis">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" id="jenis" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="benefit">Benefit</option>
                                <option value="cost">Cost</option>
                            </select>
                            <small class="text-danger" id="jenis-error"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save-kriteria">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            let normalizedTable;

            const table = $('#kriterias-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.kriterias.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'bobot',
                        name: 'bobot',
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    updateNormalizedTable();
                }
            });

            function updateNormalizedTable() {
                $.ajax({
                    url: '{{ route('admin.dashboard.normalized-weights') }}', // Sesuaikan dengan nama route yang benar
                    type: 'GET',
                    success: function(data) {
                        const totalBobot = data.reduce((acc, item) => acc + parseFloat(item.bobot), 0);

                        const normalizedData = data.map((item, index) => {
                            return {
                                kode: 'C' + (index + 1),
                                nama: item.nama,
                                bobot_asli: item.bobot_asli,
                                bobot_normalisasi: item.bobot_normalisasi
                            };
                        });

                        if ($.fn.DataTable.isDataTable('#normalized-weights-table')) {
                            normalizedTable.clear().rows.add(normalizedData).draw();
                        } else {
                            normalizedTable = $('#normalized-weights-table').DataTable({
                                data: normalizedData,
                                columns: [{
                                        data: 'kode'
                                    },
                                    {
                                        data: 'nama'
                                    },
                                    {
                                        data: 'bobot_asli',
                                        className: "text-right",
                                        render: function(data) {
                                            return parseFloat(data).toFixed(2);
                                        }
                                    },
                                    {
                                        data: 'bobot_normalisasi',
                                        className: "text-right",
                                        render: function(data) {
                                            return parseFloat(data).toFixed(5);
                                        }
                                    }
                                ],
                                paging: false,
                                searching: false,
                                info: false,
                                ordering: false,
                                autoWidth: false,
                                columnDefs: [{
                                    className: "text-center",
                                    targets: [0]
                                }]
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching normalized data:', xhr.responseText);
                    }
                });
            }

            $('#btn-create-kriteria').click(function() {
                $('#kriteria-form')[0].reset();
                $('#kriteria_id').val('');
                $('#kriteriaModalLabel').text('Tambah Kriteria');
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');
                $('#kriteriaModal').modal('show');
            });

            $(document).on('click', '.btn-edit', function() {
                const kriteria = $(this).data('kriteria');
                $('#kriteriaModalLabel').text('Edit Kriteria');
                $('#kriteria_id').val(kriteria.id);
                $('#nama').val(kriteria.nama);
                $('#bobot').val(kriteria.bobot);
                $('#jenis').val(kriteria.jenis);
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');
                $('#kriteriaModal').modal('show');
            });

            $('#kriteria-form').on('submit', function(e) {
                e.preventDefault();
                const id = $('#kriteria_id').val();
                const url = id ? `/admin/kriterias/${id}` : `{{ route('admin.kriterias.store') }}`;
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url,
                    type: 'POST',
                    data: $(this).serialize() + `&_method=${method}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: res => {
                        $('#kriteriaModal').modal('hide');
                        table.ajax.reload(null, false);
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                    },
                    error: err => {
                        const res = err.responseJSON;
                        if (res.errors) {
                            Object.entries(res.errors).forEach(([key, val]) => {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}-error`).text(val[0]);
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: res.message || 'Terjadi kesalahan.'
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data tidak bisa dikembalikan setelah dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/kriterias/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: res => {
                                table.ajax.reload(null, false);
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            },
                            error: () => {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus data'
                                });
                            }
                        });
                    }
                });
            });
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: toast => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
@endpush
