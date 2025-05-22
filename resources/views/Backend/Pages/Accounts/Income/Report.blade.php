@extends('Backend.Layout.App')
@section('title', 'Dashboard |Income Statment Report | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Profit & Loss Report</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body ">
                    <form class="row g-3 align-items-end" id="search_box">


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label"> From Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="from_date" id="from_date" class="form-control"
                                    placeholder="From Date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label"> End Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    placeholder="End Date" required>
                            </div>
                        </div>

                        <div class="col-md-4 d-grid">
                            <div class="form-group">
                                <button type="button" name="search_btn" class="btn btn-success">
                                    <i class="fas fa-search me-1"></i> Search Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body d-none" id="print_area">

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="send_message_btn" class="btn btn-danger mb-2"><i
                                    class="fas fa-print"></i> </button>
                        </div>
                    </div>

                    <div class="table-responsive responsive-table">

                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Account Titles</th>
                                    <th>Amount</th>
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
        /*** Get Ledger From Controller **/
        $("button[name='search_btn']").click(function() {
            var button = $(this);

            button.html(
                `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`
                );
            button.attr('disabled', true);
            var from_date = $("#from_date").val();
            var end_date = $("#end_date").val();
            if ($.fn.DataTable.isDataTable("#datatable1")) {
                $("#datatable1").DataTable().destroy();
            }
            $.ajax({
                url: "{{ route('admin.account.income_statment.report') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    from_date: from_date,
                    end_date: end_date
                },
                success: function(response) {
                    if (response.success == true) {

                        $("#print_area").removeClass('d-none');
                        $("#_data").html(response.html);
                        // $("#datatable1").DataTable({
                        //     "paging": true,
                        //     "searching": true,
                        //     "ordering": true,
                        //     "info": true
                        // });
                    }

                    if (response.success == false) {
                        toastr.error(response.message);
                        $("#_data").html(
                            '<tr id="no-data"><td colspan="10" class="text-center">No data available</td></tr>'
                            );
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
