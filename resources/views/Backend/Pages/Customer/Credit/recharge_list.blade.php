@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
               <div class="card-header bg-info text-white d-flex align-items-center gap-2">
                        <i class="fas fa-money-bill-wave me-2 text-white fs-4"></i>&nbsp;
                    <h5 class="mb-0 fw-semibold">Credit Recharge List </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        <table id="customer_credit_recharge_datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>POP/Branch</th>
                                    <th>Area</th>
                                    <th>Phone Number</th>
                                    <th>Month</th>
                                    <th>Recharged</th>
                                    <th>Total Paid</th>
                                    <th>Total Due</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="7" style="text-align:right">
                                        <i class="fas fa-calculator"></i> Total Due:
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-danger print-btn" onclick="printTable()"><i class="fa fa-print"></i></button>

                    <button class="btn btn btn-success" id="export_to_excel">Export <img
                            src="https://img.icons8.com/?size=100&id=117561&format=png&color=000000"
                            class="img-fluid icon-img" style="height: 20px !important;"></button>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

        /* GET POP-Branch */
        var pop_branches = @json($pop_branches);
        var pop_filter = `
            <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                <select id="search_pop_id" name="search_pop_id" class="form-control form-control-sm select2">
                    <option value="">--Select POP/Branch--</option>`;
                    pop_branches.forEach(function(item) {
                        pop_filter += `<option value="${item.id}">${item.name}</option>`;
                    });
                    pop_filter += `</select></div>`;

                    /* Get Areas */
                    var areas = @json($areas);
                    var area_filter = `
                        <div class="form-group mb-0 mr-2" style="min-width: 150px;">
                            <select id="search_area_id" name="search_area_id" class="form-control form-control-sm select2">
                                <option value="">--Select Area--</option>`;
                    areas.forEach(function(item) {
                        area_filter += `<option value="${item.id}">${item.name}</option>`;
                    });
                    area_filter += `</select></div>`;

                    setTimeout(() => {
                        var filters_wrapper = `
                            <div class="row no-gutters mb-0  " style=" row-gap: 0.5rem;">
                                <!-- Left: Per Page -->
                                <div class="col-12 col-md-auto dataTables_length_container d-flex align-items-center mb-2 mb-md-0 pr-md-3"></div>

                                <!-- Middle: Filters -->
                                <div class="col-12 col-md d-flex flex-wrap align-items-center mb-2 mb-md-0" style="gap: 0.5rem;">
                                    ${pop_filter + area_filter }
                                </div>

                                <!-- Right: Search Input -->
                                <div class="col-12 col-md-auto dataTables_filter_container d-flex justify-content-md-end"></div>
                            </div>
                        `;
                        /* Append the filters to the DataTable wrapper */
                            var tableWrapper = $('#customer_credit_recharge_datatable1').closest('.dataTables_wrapper');
                            tableWrapper.prepend(filters_wrapper);

                            tableWrapper.find('.dataTables_length').appendTo(tableWrapper.find('.dataTables_length_container'));
                            tableWrapper.find('.dataTables_filter').appendTo(tableWrapper.find('.dataTables_filter_container'));
                        $('#search_pop_id').select2({ width: 'resolve' });
                        $('#search_area_id').select2({ width: 'resolve' });
                    }, 1000);
            var table = $('#customer_credit_recharge_datatable1').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{route('admin.customer.show_credit_recharge_list_data')}}",
                    data: function(d) {
                        d.pop_id = $('#search_pop_id').val();
                        d.area_id = $('#search_area_id').val();
                    },
                    dataSrc: function(json) {
                        // footer update
                        $(table.column(7).footer()).html(
                            '<i class="fas fa-money-bill-wave text-success"></i> ' +
                            new Intl.NumberFormat().format(json.total_due_all)
                        );
                        return json.data;
                    },
                    error: function(xhr) {
                        console.log("AJAX Error:", xhr.responseText);
                    }
                },
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: '_MENU_ items/page',
                    processing: `<div class="spinner-grow text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                                <div class="spinner-grow text-secondary" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                                <div class="spinner-grow text-success" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                                <div class="spinner-grow text-danger" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>`,
                },
                columns: [
                    { data: 'username', name: 'username' },
                    { data: 'pop', name: 'pop' },
                    { data: 'area', name: 'area' },
                    { data: 'phone', name: 'phone' },
                    { data: 'months', name: 'months' },
                    { data: 'recharged', name: 'recharged' },
                    { data: 'paid', name: 'paid' },
                    { data: 'due', name: 'due' }
                ],
            });

            $(document).on('change','select[name="search_pop_id"]',function(){
                var areas = @json($areas);
                var selectedPopId = $(this).val();
                var filteredAreas = areas.filter(function(item) {
                    return item.pop_id == selectedPopId;
                });
                var areasOptions = '<option value="">--Select Area--</option>';
                filteredAreas.forEach(function(item) {
                    areasOptions += '<option value="' + item.id + '">' + item.name + '</option>';
                });
                $('#customer_credit_recharge_datatable1').DataTable().ajax.reload(null, false);
                $('select[name="search_area_id"]').html(areasOptions);
            });
            /*Handle Area filter change*/
            $(document).on('change', 'select[name="search_area_id"]', function() {
                $('#customer_credit_recharge_datatable1').DataTable().ajax.reload(null, false);
            });
        });

        function printTable() {
            var printContents = document.getElementById('customer_credit_recharge_datatable1').outerHTML;
            var originalContents = document.body.innerHTML;

            var newWindow = window.open('', '', 'width=800, height=600');
            newWindow.document.write('<html><head><title>Print Table</title>');
            newWindow.document.write('<style>');
            newWindow.document.write('table {width: 100%; border-collapse: collapse; border: 1px solid black;}');
            newWindow.document.write('th, td {border: 2px dotted #ababab; padding: 8px; text-align: left;}');
            newWindow.document.write('</style></head><body>');

            newWindow.document.write('<div class="header">');
            newWindow.document.write(
                '<img src="http://103.146.16.154/assets/images/it-fast.png" class="logo" alt="Company Logo" style="display:block; margin:auto; height:50px;">'
            );
            newWindow.document.write('<h2 style="text-align:center; color: #000;">Star Communication</h2>');
            newWindow.document.write('<p style="text-align:center;">Credit Recharge Report</p>');
            newWindow.document.write('</div>');

            newWindow.document.write(printContents);
            newWindow.document.write('</body></html>');

            newWindow.document.close();
            newWindow.print();
            newWindow.close();
        }
    </script>

@endsection


