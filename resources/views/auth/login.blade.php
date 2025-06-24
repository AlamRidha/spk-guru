@extends('layouts.auth')

@section('content')
    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.1); width: 400px;">
        <div class="card-header text-center py-4"
            style="background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%); border: none;">
            <img src="{{ asset('img/logo_sekolah.png') }}" alt="Logo Sekolah" height="70" class="mb-2">
            <h4 style="font-weight: 600; color: #fff; margin-bottom: 0.5rem; letter-spacing: 0.5px;">Sistem SPK Guru Tetap
            </h4>
            <p style="color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 0;">SMK Global Cendekia</p>
        </div>

        <div class="card-body px-4 py-4" style="background-color: #f8f9fe;">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible rounded-lg" style="font-size: 0.9rem; border: none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5 style="font-size: 1rem;"><i class="icon fas fa-ban mr-1"></i> Error!</h5>
                    <ul class="mb-0 pl-3" style="font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="form-group mb-3">
                    <label for="email" style="font-size: 0.9rem; color: #525f7f; font-weight: 500;">Email</label>
                    <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-envelope" style="color: #5e72e4;"></i>
                            </span>
                        </div>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email"
                            value="{{ old('email') }}" required autofocus
                            style="border-left: none; font-size: 0.9rem; height: calc(2.5rem + 2px);">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="password" style="font-size: 0.9rem; color: #525f7f; font-weight: 500;">Password</label>
                    <div class="input-group" style="border-radius: 8px; overflow: hidden;">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background-color: #fff; border-right: none;">
                                <i class="fas fa-lock" style="color: #5e72e4;"></i>
                            </span>
                        </div>
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Masukkan password" required
                            style="border-left: none; font-size: 0.9rem; height: calc(2.5rem + 2px);">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                style="background-color: #fff; border-left: none;">
                                <i class="fas fa-eye" style="color: #5e72e4;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block py-2 mb-2"
                    style="font-weight: 500; font-size: 0.95rem; background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%); 
                       border: none; border-radius: 8px; height: calc(2.5rem + 2px); box-shadow: 0 4px 12px rgba(94, 114, 228, 0.3);">
                    <i class="fas fa-sign-in-alt mr-2"></i> MASUK
                </button>
            </form>
        </div>

        <div class="card-footer text-center py-2" style="background-color: #fff; border-top: 1px solid #e9ecef;">
            <p style="font-size: 0.8rem; color: #8898aa; margin-bottom: 0;">
                &copy; {{ date('Y') }} SMK Global Cendekia | Versi 1.0.0
            </p>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f2f5;
        }

        .login-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .card {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .form-control {
            font-size: 0.9rem;
        }

        .input-group-text {
            transition: all 0.3s;
        }

        .toggle-password {
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-password:hover {
            background-color: #f8f9fe !important;
        }

        .btn-primary {
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(94, 114, 228, 0.4) !important;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: #5e72e4;
            border-color: #5e72e4;
        }

        a:hover {
            text-decoration: underline !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            // Toggle password visibility
            $('.toggle-password').click(function() {
                const password = $('#password');
                const type = password.attr('type') === 'password' ? 'text' : 'password';
                password.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            // Toast notification
            @if (session('toast_type') && session('toast_message'))
                toastr.{{ session('toast_type') }}("{{ session('toast_message') }}");
            @endif
        });
    </script>
@endpush
