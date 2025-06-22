@extends('layouts.admin')

@section('title', $title)

@push('styles')
    <style>
        .swal2-popup {
            font-size: 1.6rem !important;
        }

        .password-field {
            display: block;
            /* Default show */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">{{ $title }}</h3>
                <button class="btn btn-light float-right" id="btn-create">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="users-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th width="15%">Role</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal (Tetap dalam file yang sama) -->
    <div class="modal fade" id="user-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="modal-title">Tambah User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="user-form">
                    @csrf
                    <input type="hidden" id="user-id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="kepsek">Kepala Sekolah</option>
                                <option value="wakil_kurikulum">Wakil Kurikulum</option>
                                <option value="guru">Guru</option>
                            </select>
                        </div>
                        <div class="form-group password-field">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group password-field">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users.index') }}',
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
                        data: 'email',
                        name: 'email',
                        orderable: false
                    },
                    {
                        data: 'role_formatted',
                        name: 'role',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-sm btn-warning btn-edit mr-1" 
                            data-id="${row.id}" 
                            data-nama="${row.nama}"
                            data-email="${row.email}"
                            data-role="${row.role}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" 
                            data-id="${row.id}"
                            data-nama="${row.nama}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        }
                    }
                ]
            });

            // Show create modal
            $('#btn-create').click(function() {
                $('#user-form')[0].reset();
                $('#modal-title').text('Tambah User Baru');
                $('.password-field').show();
                $('#user-id').val('');
                $('#user-modal').modal('show');
            });

            // Show edit modal
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const email = $(this).data('email');
                const role = $(this).data('role');

                $('#user-id').val(id);
                $('#nama').val(nama);
                $('#email').val(email);
                $('#role').val(role);
                $('#modal-title').text('Edit User');
                $('.password-field').hide(); // Sembunyikan field password saat edit
                $('#user-modal').modal('show');
            });

            // Submit form
            $('#user-form').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const id = $('#user-id').val();
                const url = id ? `/admin/users/${id}` : '{{ route('admin.users.store') }}';

                // Tambahkan _method ke FormData
                if (id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST', // Selalu gunakan POST
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#user-modal').modal('hide');
                        table.ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';

                        $.each(errors, function(key, value) {
                            errorMessages += `<li>${value[0]}</li>`;
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}-error`).remove(); // Hapus error sebelumnya
                            $(`#${key}`).after(
                                `<div id="${key}-error" class="invalid-feedback">${value[0]}</div>`
                            );
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: `<ul>${errorMessages}</ul>`,
                        });
                    }
                });
            });

            // Delete user
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus User?',
                    html: `Anda akan menghapus user <b>${nama}</b>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/users/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON.message,
                                });
                            }
                        });
                    }
                });
            });

            // Reset form ketika modal ditutup
            $('#user-modal').on('hidden.bs.modal', function() {
                $('#user-form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
