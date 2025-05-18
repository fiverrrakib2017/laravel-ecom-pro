@extends('Backend.Layout.App')
@section('title', 'Dashboard | Advance Salary Report | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                   <div class="card-header">
                        <h4>Advance Salary Report</h4>
                    </div>
                <div class="card-body ">
                    <form class="row g-3 align-items-end" id="search_box">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">From Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="from_date" id="from_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="form-label">End Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-3 d-grid">
                            <div class="form-group">
                                <button type="button" name="search_btn" class="btn btn-success">
                                    <i class="fas fa-search me-1"></i> Search Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body" id="print_area">

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="print_button" class="btn btn-danger mb-2"><i class="fas fa-print"></i></button>
                        </div>
                    </div>

                    <div class="table-responsive responsive-table">

                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="">No.</th>
                                    <th class="">Employee Name</th>
                                    <th class="">Department</th>
                                    <th class="">Designation</th>
                                    <th class="">Amount</th>
                                    <th class="">Advance Date</th>
                                    <th class="">Approve Date</th>
                                    <th class="">Approve By</th>
                                    <th class="">Status</th>
                                    <th class="">Description</th>
                                </tr>
                            </thead>
                            <tbody id="_data">
                                <tr id="no-data">
                                    <td colspan="10" class="text-center">No data available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">


          /***Load Customer **/
          $("button[name='search_btn']").click(function() {
                var button = $(this);

                button.html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
                button.attr('disabled', true);
                var from_date = $("#from_date").val();
                var end_date = $("#end_date").val();

                // if ( $.fn.DataTable.isDataTable("#datatable1") ) {
                //     $("#datatable1").DataTable().destroy();
                // }
                $.ajax({
                    url: "{{ route('admin.hr.employee.salary.advance.fetch.report') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {  _token: "{{ csrf_token() }}",from_date: from_date, end_date: end_date},
                    success: function(response) {
                        if(response.success==true){
                            $("#print_area").removeClass('d-none');
                            $("#_data").html(response.html);
                            // $("#datatable1").DataTable({
                            //     "paging": true,
                            //     "searching": true,
                            //     "ordering": true,
                            //     "info": true
                            // });
                        }
                    },
                    complete: function() {
                        button.html('<i class="fas fa-search me-1"></i> Search Now');
                        button.attr('disabled', false);
                    }
                });
            });
    </script>

@endsection
