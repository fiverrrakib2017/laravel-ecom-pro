{{-- Select2 --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/select2/css/select2.min.css') }}?v={{ filemtime(public_path('Backend/plugins/select2/css/select2.min.css')) }}">
<link rel="stylesheet" href="{{ asset('Backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}?v={{ filemtime(public_path('Backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')) }}">

{{-- Google Fonts & Ionicons (external, no cache versioning needed) --}}
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

{{-- Font Awesome --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/fontawesome-free/css/all.min.css') }}?v={{ filemtime(public_path('Backend/plugins/fontawesome-free/css/all.min.css')) }}">

{{-- Tempusdominus --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}?v={{ filemtime(public_path('Backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')) }}">

{{-- iCheck --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}?v={{ filemtime(public_path('Backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css')) }}">

{{-- JQVMap --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/jqvmap/jqvmap.min.css') }}?v={{ filemtime(public_path('Backend/plugins/jqvmap/jqvmap.min.css')) }}">

{{-- AdminLTE Theme --}}
<link rel="stylesheet" href="{{ asset('Backend/dist/css/adminlte.min.css') }}?v={{ filemtime(public_path('Backend/dist/css/adminlte.min.css')) }}">

{{-- overlayScrollbars --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}?v={{ filemtime(public_path('Backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')) }}">

{{-- Daterangepicker --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/daterangepicker/daterangepicker.css') }}?v={{ filemtime(public_path('Backend/plugins/daterangepicker/daterangepicker.css')) }}">

{{-- Summernote --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/summernote/summernote-bs4.min.css') }}?v={{ filemtime(public_path('Backend/plugins/summernote/summernote-bs4.min.css')) }}">

{{-- DataTables --}}
<link rel="stylesheet" href="{{ asset('Backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}?v={{ filemtime(public_path('Backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset('Backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}?v={{ filemtime(public_path('Backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset('Backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}?v={{ filemtime(public_path('Backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')) }}">

{{-- Toastr --}}
<link rel="stylesheet" href="{{ asset('Backend/dist/css/toastr.min.css') }}?v={{ filemtime(public_path('Backend/dist/css/toastr.min.css')) }}">

{{-- Custom Delete Modal --}}
<link rel="stylesheet" href="{{ asset('Backend/dist/css/deleteModal.css') }}?v={{ filemtime(public_path('Backend/dist/css/deleteModal.css')) }}">

{{-- Inline Custom Style --}}
<style>
  label:not(.form-check-label):not(.custom-file-label) {
      font-weight: 500 !important;
  }
  .table-bordered {
      border: 2px dotted #dee2e6 !important;
  }
</style>

@yield('style')
