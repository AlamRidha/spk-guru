@extends('layouts.admin')

@section('title', $title)


@section('content')
    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white my-auto">{{ $title }}</h3>
                <button class="btn btn-light" id="btn-create-guru">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Guru
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover w-100" id="gurus-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Guru</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    <div class="modal fade" id="guruModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="guru-form">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="guruModalLabel">Tambah Guru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="guru_id">
                        <div class="form-group">
                            <label for="nama">Nama Guru <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" required
                                placeholder="Masukkan nama lengkap">
                            <small class="text-danger" id="nama-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" name="nip" id="nip"
                                placeholder="Masukkan NIP (opsional)">
                            <small class="text-danger" id="nip-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jabatan" id="jabatan" required
                                placeholder="Contoh: Guru Matematika">
                            <small class="text-danger" id="jabatan-error"></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save-guru">
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
            const table = $('#gurus-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.gurus.index') }}',
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
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            $('#btn-create-guru').click(function() {
                $('#guru-form')[0].reset();
                $('#guruModalLabel').text('Tambah Guru');
                $('#guru_id').val('');
                $('.text-danger').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#guruModal').modal('show');
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data guru akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                // Reload DataTable tanpa reset paging
                                table.ajax.reload(null, false);
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message ||
                                        'Data berhasil dihapus!'
                                });
                            },
                            error: function(xhr) {
                                Toast.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message ||
                                        'Gagal menghapus data.'
                                });
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.btn-edit', function() {
                const guru = $(this).data('guru');

                $('#guruModalLabel').text('Edit Guru');
                $('#guru_id').val(guru.id);
                $('#nama').val(guru.nama);
                $('#nip').val(guru.nip);
                $('#jabatan').val(guru.jabatan);
                $('.text-danger').text('');
                $('.is-invalid').removeClass('is-invalid');

                $('#guruModal').modal('show');
            });

            $('#guru-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').text('');
                $('.is-invalid').removeClass('is-invalid');

                const id = $('#guru_id').val();
                const url = id ? `/admin/gurus/${id}` : `{{ route('admin.gurus.store') }}`;
                const method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize() + `&_method=${method}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#guruModal').modal('hide');
                        table.ajax.reload(null, false);
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (res?.errors) {
                            Object.entries(res.errors).forEach(([field, messages]) => {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(messages[0]);
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
        });

        // SweetAlert Toast
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
    </script>
@endpush
