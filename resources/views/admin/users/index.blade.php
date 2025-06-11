@extends('layouts.admin')

@section('title', $title)

<style>
    .text-danger {
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">{{ $title }}</h1>
            <button class="btn btn-primary" id="btn-create-user">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-hover" id="users-table">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="user-form">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="userModalLabel">Tambah User</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="user_id">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" name="nama" id="nama" required
                                placeholder="Masukkan nama lengkap">
                            <small class="text-danger" id="nama-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required
                                placeholder="contoh@sekolah.sch.id">
                            <small class="text-danger" id="email-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="role">Peran</label>
                            <select class="form-control" name="role" id="role" required>
                                <option value="" disabled selected>Pilih peran</option>
                                <option value="admin">Admin</option>
                                <option value="kepsek">Kepala Sekolah</option>
                            </select>
                            <small class="text-danger" id="role-error"></small>
                        </div>
                        <div class="form-group password-group">
                            <label for="password">Password <small class="text-muted">(Biarkan kosong jika tidak ingin
                                    mengubah)</small></label>
                            <input type="password" class="form-control" name="password" id="password"
                                placeholder="Minimal 8 karakter">
                            <small class="text-danger" id="password-error"></small>
                        </div>
                        <div class="form-group password-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirmation"
                                id="password_confirmation" placeholder="Ketik ulang password">
                            <small class="text-danger" id="password_confirmation-error"></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-user">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let table;

        $(function() {
            table = $('#users-table').DataTable({
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
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#btn-create-user').click(function() {
                $('#user-form')[0].reset();
                $('#userModalLabel').text('Tambah User');
                $('.password-group').show();
                $('#userModal').modal('show');
                $('.text-muted').hide();

                $('#user_id').val('');
                $('#userModal').modal('show');
            });

            $(document).on('click', '.btn-edit', function() {
                const user = $(this).data('user');

                $('#userModalLabel').text('Edit User');
                $('#user_id').val(user.id);
                $('#nama').val(user.nama);
                $('#email').val(user.email);
                $('#role').val(user.role);
                $('.text-muted').show();

                $('#userModal').modal('show');
            });

            $('#user-form').on('submit', function(e) {
                e.preventDefault();

                $('.text-danger').text('');

                const id = $('#user_id').val();
                const url = id ? `/admin/users/${id}` : `{{ route('admin.users.store') }}`;
                const method = id ? 'PUT' : 'POST';

                let formData = new FormData(this);
                formData.append('_method', method);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#userModal').modal('hide');
                        table.ajax.reload();
                        toastr.success(res.message || 'Berhasil menyimpan data.');
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (res?.errors) {
                            $('.is-invalid').removeClass('is-invalid');
                            $('.text-danger').text('');

                            // Tampilkan error untuk setiap field
                            Object.entries(res.errors).forEach(([field, messages]) => {
                                const inputElement = $(`#${field}`);
                                const errorElement = $(`#${field}-error`);

                                if (inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                }

                                if (errorElement.length) {
                                    errorElement.text(messages[0]);
                                } else {
                                    toastr.error(messages[0]);
                                }
                            });
                        } else {
                            toastr.error(res.message || 'Terjadi kesalahan.');
                        }
                    }
                });
            });
        });
    </script>
@endpush
