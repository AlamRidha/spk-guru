@extends('layouts.admin')

@section('title', $title)

<style>
    .nav-tabs .nav-link {
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #007bff;
    }

    .table th {
        white-space: nowrap;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
</style>


@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-alt mr-2"></i>
                            {{ $title }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="kriteriaTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="kepsek-tab" data-toggle="pill" href="#kepsek" role="tab">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Kriteria Kepala Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="wakur-tab" data-toggle="pill" href="#wakur" role="tab">
                                    <i class="fas fa-user-graduate mr-1"></i>
                                    Kriteria Wakil Kurikulum
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="kriteriaTabsContent">
                            <div class="tab-pane fade show active pt-3" id="kepsek" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover w-100" id="kepsek-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Kriteria</th>
                                                <th width="10%">Bobot</th>
                                                <th width="10%">Jenis</th>
                                                <th width="15%">Penilai</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade pt-3" id="wakur" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover w-100" id="wakur-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Kriteria</th>
                                                <th width="10%">Bobot</th>
                                                <th width="10%">Jenis</th>
                                                <th width="15%">Penilai</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Perbaikan Bobot --}}
                    <div class="mt-4 mx-4">
                        <h5 class="font-weight-bold">Perbaikan Bobot Kriteria (Normalisasi)</h5>
                        <div class="mt-2 text-muted">
                            <small>Rumus normalisasi: W<sub>i</sub> = Bobot Kriteria / Σ(Bobot semua kriteria)</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="normalized-weights-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Kriteria</th>
                                        <th>Bobot Asli</th>
                                        <th>Bobot Normalisasi (W)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Total</th>
                                        <th class="text-right" id="total-bobot-asli">0.00</th>
                                        <th class="text-right" id="total-bobot-normalisasi">0.00</th>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary" id="btn-create-kriteria">
                            <i class="fas fa-plus-circle mr-1"></i>Tambah Kriteria
                        </button>
                    </div>
                </div>
            </div>
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
                        <input type="hidden" name="active_tab" id="active_tab" value="kepsek">
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
                        <div class="form-group">
                            <label for="penilai">Penilai <span class="text-danger">*</span></label>
                            <select name="penilai" id="penilai" class="form-control" required>
                                <option value="">-- Pilih Penilai --</option>
                                <option value="kepsek">Kepala Sekolah</option>
                                <option value="wakil_kurikulum">Wakil Kurikulum</option>
                            </select>
                            <small class="text-danger" id="penilai-error"></small>
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
        $(document).ready(function() {
            updateNormalizedTable('kepsek');

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                const penilai = $(e.target).attr('id').replace('-tab', '');
                updateNormalizedTable(penilai);
            });

            // Inisialisasi DataTables
            var kepsekTable = $('#kepsek-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.kriterias.dataKepsek') }}",
                    type: "GET"
                },
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
                        className: 'text-right',
                        orderable: false,
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'jenis',
                        name: 'jenis',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'penilai_formatted',
                        name: 'penilai',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            var wakurTable = $('#wakur-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.kriterias.dataWakur') }}",
                    type: "GET"
                },
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
                        className: 'text-right',
                        orderable: false,
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'jenis',
                        name: 'jenis',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'penilai_formatted',
                        name: 'penilai',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            // Modal handlers
            $('#btn-create-kriteria').click(function() {
                $('#kriteria-form')[0].reset();
                $('#kriteria_id').val('');
                $('#kriteriaModalLabel').text('Tambah Kriteria');
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');
                $('#kriteriaModal').modal('show');
            });

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.get('/admin/kriterias/' + id, function(data) {
                    $('#kriteriaModalLabel').text('Edit Kriteria');
                    $('#kriteria_id').val(id);
                    $('#nama').val(data.nama);
                    $('#bobot').val(data.bobot);
                    $('#jenis').val(data.jenis);
                    $('#penilai').val(data.penilai);
                    $('.is-invalid').removeClass('is-invalid');
                    $('.text-danger').text('');
                    $('#kriteriaModal').modal('show');
                });
            });

            // Form submission
            $('#kriteria-form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var id = $('#kriteria_id').val();
                var url = id ? '/admin/kriterias/' + id : '{{ route('admin.kriterias.store') }}';
                var method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $.param(formData) + '&_method=' + method,
                    success: function(res) {
                        $('#kriteriaModal').modal('hide');

                        // Perbarui tabel berdasarkan penilai
                        if (res.penilai === 'kepsek') {
                            kepsekTable.ajax.reload(null, false);
                        } else {
                            wakurTable.ajax.reload(null, false);
                        }

                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });

                        updateNormalizedTable();
                    },
                    error: function(err) {
                        var res = err.responseJSON;
                        if (res.errors) {
                            Object.entries(res.errors).forEach(([key, val]) => {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(val[0]);
                            });
                        }
                    }
                });
            });

            // Delete handler
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
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
                            url: '/admin/kriterias/' + id,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                kepsekTable.ajax.reload(null, false);
                                wakurTable.ajax.reload(null, false);
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });

                                updateNormalizedTable();
                            },
                            error: function() {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus data'
                                });
                            }
                        });
                    }
                });
            });

            // Toast notification
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

            function updateNormalizedTable(penilai = null) {
                // Jika penilai tidak diberikan, tentukan default-nya
                if (!penilai) {
                    const activeTab = $('.nav-pills .active');
                    if (activeTab.length && activeTab.attr('id')) {
                        penilai = activeTab.attr('id').replace('-tab', '');
                    } else {
                        // Default ke kepsek jika tidak ada tab aktif
                        penilai = 'kepsek';
                    }
                }

                // Pastikan penilai sesuai dengan nilai yang diharapkan
                const penilaiValue = penilai === 'kepsek' ? 'kepsek' : 'wakil_kurikulum';

                $.ajax({
                    url: '{{ route('admin.kriterias.normalized-weights-by-penilai') }}',
                    type: 'GET',
                    data: {
                        penilai: penilaiValue
                    },
                    success: function(data) {
                        console.log("Data received:", data);

                        const totalBobotAsli = data.reduce((sum, item) => sum + parseFloat(item
                            .bobot_asli), 0);
                        const totalBobotNormalisasi = data.reduce((sum, item) => sum + parseFloat(item
                            .bobot_normalisasi), 0);

                        let html = '';
                        data.forEach(item => {
                            html += `
                    <tr>
                        <td>${item.kode}</td>
                        <td>${item.nama}</td>
                        <td class="text-right">${parseFloat(item.bobot_asli).toFixed(2)}</td>
                        <td class="text-right">${parseFloat(item.bobot_normalisasi).toFixed(5)}</td>
                    </tr>
                `;
                        });

                        $('#normalized-weights-table tbody').html(html);
                        // $('#total-bobot-asli').text(totalBobotAsli.toFixed(2));
                        // $('#total-bobot-normalisasi').text(totalBobotNormalisasi.toFixed(5));
                    },
                    error: function(xhr) {
                        console.error('Error fetching normalized data:', xhr.responseText);
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal memuat data normalisasi'
                        });
                    }
                });
            }

        });
    </script>
@endpush
