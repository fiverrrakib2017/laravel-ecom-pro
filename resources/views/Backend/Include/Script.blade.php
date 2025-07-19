<!-- jQuery -->
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}?v={{ filemtime(public_path('Backend/plugins/jquery/jquery.min.js')) }}"></script>

<!-- jQuery UI -->
<script src="{{ asset('Backend/plugins/jquery-ui/jquery-ui.min.js') }}?v={{ filemtime(public_path('Backend/plugins/jquery-ui/jquery-ui.min.js')) }}"></script>

<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- Select2 -->
<script src="{{ asset('Backend/plugins/select2/js/select2.full.min.js') }}?v={{ filemtime(public_path('Backend/plugins/select2/js/select2.full.min.js')) }}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('Backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('Backend/plugins/bootstrap/js/bootstrap.bundle.min.js')) }}"></script>

<!-- ChartJS -->
<script src="{{ asset('Backend/plugins/chart.js/Chart.min.js') }}?v={{ filemtime(public_path('Backend/plugins/chart.js/Chart.min.js')) }}"></script>

<!-- Sparkline -->
<script src="{{ asset('Backend/plugins/sparklines/sparkline.js') }}?v={{ filemtime(public_path('Backend/plugins/sparklines/sparkline.js')) }}"></script>

<!-- JQVMap -->
<script src="{{ asset('Backend/plugins/jqvmap/jquery.vmap.min.js') }}?v={{ filemtime(public_path('Backend/plugins/jqvmap/jquery.vmap.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/jqvmap/maps/jquery.vmap.usa.js') }}?v={{ filemtime(public_path('Backend/plugins/jqvmap/maps/jquery.vmap.usa.js')) }}"></script>

<!-- jQuery Knob -->
<script src="{{ asset('Backend/plugins/jquery-knob/jquery.knob.min.js') }}?v={{ filemtime(public_path('Backend/plugins/jquery-knob/jquery.knob.min.js')) }}"></script>

<!-- Daterangepicker -->
<script src="{{ asset('Backend/plugins/moment/moment.min.js') }}?v={{ filemtime(public_path('Backend/plugins/moment/moment.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/daterangepicker/daterangepicker.js') }}?v={{ filemtime(public_path('Backend/plugins/daterangepicker/daterangepicker.js')) }}"></script>

<!-- Tempusdominus -->
<script src="{{ asset('Backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}?v={{ filemtime(public_path('Backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')) }}"></script>

<!-- Summernote -->
<script src="{{ asset('Backend/plugins/summernote/summernote-bs4.min.js') }}?v={{ filemtime(public_path('Backend/plugins/summernote/summernote-bs4.min.js')) }}"></script>

<!-- overlayScrollbars -->
<script src="{{ asset('Backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}?v={{ filemtime(public_path('Backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')) }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('Backend/dist/js/adminlte.js') }}?v={{ filemtime(public_path('Backend/dist/js/adminlte.js')) }}"></script>

<!-- AdminLTE Dashboard (Demo) -->
<script src="{{ asset('Backend/dist/js/pages/dashboard.js') }}?v={{ filemtime(public_path('Backend/dist/js/pages/dashboard.js')) }}"></script>

<!-- DataTables -->
<script src="{{ asset('Backend/plugins/datatables/jquery.dataTables.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables/jquery.dataTables.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-responsive/js/dataTables.responsive.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-buttons/js/dataTables.buttons.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/jszip/jszip.min.js') }}?v={{ filemtime(public_path('Backend/plugins/jszip/jszip.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/pdfmake/pdfmake.min.js') }}?v={{ filemtime(public_path('Backend/plugins/pdfmake/pdfmake.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/pdfmake/vfs_fonts.js') }}?v={{ filemtime(public_path('Backend/plugins/pdfmake/vfs_fonts.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-buttons/js/buttons.html5.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-buttons/js/buttons.html5.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-buttons/js/buttons.print.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-buttons/js/buttons.print.min.js')) }}"></script>
<script src="{{ asset('Backend/plugins/datatables-buttons/js/buttons.colVis.min.js') }}?v={{ filemtime(public_path('Backend/plugins/datatables-buttons/js/buttons.colVis.min.js')) }}"></script>

<!-- Toastr -->
<script src="{{ asset('Backend/dist/js/toastr.min.js') }}?v={{ filemtime(public_path('Backend/dist/js/toastr.min.js')) }}"></script>
<!-- Counter -->
<script src="{{ asset('Backend/dist/js/counter.min.js') }}?v={{ filemtime(public_path('Backend/dist/js/counter.min.js')) }}"></script>
<script>
    $(document).ready(function () {
        $('.counter-value').counterUp({
            delay: 10,
            time: 1000
        });
    });
</script>
@yield('script')
