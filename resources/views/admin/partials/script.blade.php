{{-- resources/views/partials/scripts.blade.php --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script> --}}
{{-- <script> $.widget.bridge('uibutton', $.ui.button) </script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/js/jquery.overlayScrollbars.min.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js" defer></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.usa.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


@stack('scripts') {{-- Untuk JS khusus halaman atau Toastr --}}

{{-- Toastr Notifications (Pastikan ini setelah Toastr JS dimuat) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('toast_success'))
            toastr.success("{{ session('toast_success') }}");
        @endif
        @if (session('toast_error'))
            toastr.error("{{ session('toast_error') }}");
        @endif
    });
</script>
