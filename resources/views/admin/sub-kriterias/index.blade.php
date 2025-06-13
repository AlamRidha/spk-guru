@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white my-auto">{{ $title }}</h3>
                <div>
                    <button class="btn btn-light" id="btn-add-subkriteria">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Sub Kriteria
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover w-100" id="subkriterias-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Kriteria</th>
                                <th>Sub Kriteria</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kelola Sub Kriteria -->
    <div class="modal fade" id="manageSubkriteriaModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="manageSubkriteriaModalLabel">Kelola Sub Kriteria</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="current-kriteria-id">
                    <h4 id="kriteria-title" class="mb-4"></h4>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="subkriteria-list-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Sub Kriteria</th>
                                    <th width="10%">Nilai</th>
                                    <th>Keterangan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h5 class="mt-4">Tambah Sub Kriteria Baru</h5>
                    <form id="subkriteria-form">
                        @csrf
                        <input type="hidden" name="kriteria_id" id="modal-kriteria-id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modal-nama">Nama Sub Kriteria</label>
                                    <input type="text" class="form-control" name="nama" id="modal-nama" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="modal-nilai">Nilai</label>
                                    <select name="nilai" id="modal-nilai" class="form-control" required>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modal-keterangan">Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" id="modal-keterangan"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus mr-1"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Kriteria -->
    <div class="modal fade" id="selectKriteriaModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Pilih Kriteria</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="select-kriteria">Pilih Kriteria</label>
                        <select class="form-control" id="select-kriteria">
                            <option value="">-- Pilih Kriteria --</option>
                            @foreach ($kriterias as $kriteria)
                                <option value="{{ $kriteria->id }}">{{ $kriteria->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-proceed-subkriteria">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-group {
            display: flex;
            gap: 5px;
        }

        #subkriteria-list-table tbody tr td {
            padding: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            let table;
            let isGrouped = true;

            // Initialize DataTable
            function initDataTable(grouped = true) {
                if ($.fn.DataTable.isDataTable('#subkriterias-table')) {
                    table.destroy();
                }

                const url = grouped ?
                    '{{ route('admin.sub-kriterias.index') }}?grouped=1' :
                    '{{ route('admin.sub-kriterias.index') }}';

                table = $('#subkriterias-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    columns: grouped ? [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'subkriteria_list',
                            name: 'subkriteria_list',
                            orderable: false
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ] : [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'kriteria.nama',
                            name: 'kriteria.nama'
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            // Load sub kriterias for a kriteria
            function loadSubKriterias(kriteriaId) {
                $.ajax({
                    url: '/admin/sub-kriterias/' + kriteriaId + '/by-kriteria',
                    type: 'GET',
                    success: function(data) {
                        let html = '';
                        data.forEach((item, index) => {
                            html += `
                    <tr data-id="${item.id}">
                        <td>${index + 1}</td>
                        <td>${item.nama}</td>
                        <td>${item.nilai}</td>
                        <td>${item.keterangan}</td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit-sub" 
                                data-id="${item.id}"
                                data-nama="${item.nama}"
                                data-nilai="${item.nilai}"
                                data-keterangan="${item.keterangan}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete-sub" 
                                data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                        });

                        $('#subkriteria-list-table tbody').html(html ||
                            '<tr><td colspan="5" class="text-center">Tidak ada sub kriteria</td></tr>'
                        );
                    }
                });
            }

            // Initial load with grouped view
            initDataTable();

            // Open manage modal
            $(document).on('click', '.btn-manage', function() {
                const kriteriaId = $(this).data('kriteria-id');
                const kriteriaNama = $(this).data('kriteria-nama');

                $('#current-kriteria-id').val(kriteriaId);
                $('#modal-kriteria-id').val(kriteriaId);
                $('#kriteria-title').text('Sub Kriteria untuk: ' + kriteriaNama);
                $('#manageSubkriteriaModal').modal('show');

                loadSubKriterias(kriteriaId);
            });

            // Add new sub kriteria
            $('#subkriteria-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('admin.sub-kriterias.store') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });

                        $('#subkriteria-form')[0].reset();
                        loadSubKriterias($('#current-kriteria-id').val());
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (res?.errors) {
                            Object.entries(res.errors).forEach(([field, messages]) => {
                                $(`#modal-${field}`).addClass('is-invalid');
                                $(`#modal-${field}`).next('.text-danger').remove();
                                $(`#modal-${field}`).after(
                                    `<small class="text-danger">${messages[0]}</small>`
                                );
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: res?.message || 'Terjadi kesalahan.'
                            });
                        }
                    }
                });
            });

            // Edit sub kriteria
            // Edit sub kriteria
            $(document).on('click', '.btn-edit-sub', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const nilai = $(this).data('nilai');
                const keterangan = $(this).data('keterangan');

                console.log('Editing:', {
                    id,
                    nama,
                    nilai,
                    keterangan
                });

                const row = $(this).closest('tr');
                row.html(`
        <td>${row.find('td:first').text()}</td>
        <td><input type="text" class="form-control form-control-sm" value="${nama}"></td>   
        <td>
            <select class="form-control form-control-sm">
                ${[1,2,3,4,5].map(i => 
                    `<option value="${i}" ${i == nilai ? 'selected' : ''}>${i}</option>`
                ).join('')}
            </select>
        </td>
        <td><input type="text" class="form-control form-control-sm" value="${keterangan}"></td>
        <td>
            <button class="btn btn-sm btn-success btn-save-sub" data-id="${id}">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn btn-sm btn-secondary btn-cancel-sub">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `);
            });

            // Save edited sub kriteria
            $(document).on('click', '.btn-save-sub', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const data = {
                    nama: row.find('td:eq(1) input').val(),
                    nilai: row.find('td:eq(2) select').val(),
                    keterangan: row.find('td:eq(3) input').val(),
                    _method: 'PUT'
                };

                console.log('Saving:', data);

                $.ajax({
                    url: '/admin/sub-kriterias/' + id,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        loadSubKriterias($('#current-kriteria-id').val());
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseJSON);
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan perubahan'
                        });
                    }
                });
            });

            // Save edited sub kriteria
            $(document).on('click', '.btn-save-sub', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const data = {
                    nama: row.find('td:eq(1) input').val(),
                    nilai: row.find('td:eq(2) select').val(),
                    keterangan: row.find('td:eq(3) input').val(),
                    _method: 'PUT'
                };

                $.ajax({
                    url: '/admin/sub-kriterias/' + id,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        loadSubKriterias($('#current-kriteria-id').val());
                        table.ajax.reload(null, false);
                    }
                });
            });

            // Cancel edit
            $(document).on('click', '.btn-cancel-sub', function() {
                loadSubKriterias($('#current-kriteria-id').val());
            });

            // Delete sub kriteria
            $(document).on('click', '.btn-delete-sub', function() {
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
                            url: '/admin/sub-kriterias/' + id,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                                loadSubKriterias($('#current-kriteria-id').val());
                                table.ajax.reload(null, false);
                            }
                        });
                    }
                });
            });

            // Handle tombol tambah sub kriteria
            $('#btn-add-subkriteria').click(function() {
                $('#selectKriteriaModal').modal('show');
            });

            // Lanjutkan ke form sub kriteria
            $('#btn-proceed-subkriteria').click(function() {
                const kriteriaId = $('#select-kriteria').val();
                const kriteriaNama = $('#select-kriteria option:selected').text();

                if (!kriteriaId) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Silakan pilih kriteria terlebih dahulu'
                    });
                    return;
                }

                $('#selectKriteriaModal').modal('hide');

                // Set nilai untuk modal kelola sub kriteria
                $('#current-kriteria-id').val(kriteriaId);
                $('#modal-kriteria-id').val(kriteriaId);
                $('#kriteria-title').text('Sub Kriteria untuk: ' + kriteriaNama);

                // Kosongkan form dan muat sub kriteria yang ada
                $('#subkriteria-form')[0].reset();
                loadSubKriterias(kriteriaId);

                // Tampilkan modal kelola sub kriteria
                $('#manageSubkriteriaModal').modal('show');
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    </script>
@endpush
