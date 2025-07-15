@extends('Backend.Layout.App')
@section('title', 'Dashboard | Admin Panel')
@section('style')
    <style>
        /* Print CSS */
        @media print {
            #printButton {
                display: none;
            }

            .card {
                border: none;
                box-shadow: none;
            }
        }

        .school-header {
            text-align: center;
            padding: 15px;
        }

        .school-header img {
            height: 80px;
            width: 80px;
            margin-bottom: 10px;
        }

        .school-header h2 {
            font-weight: 100;
            margin-bottom: 5px;
        }

        .school-header p {
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }

        .info-box {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')

    <div class="row" id="main_div">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" name="upload_csv_file_btn" data-toggle="modal"
                    data-target="#fileImportModal" class="btn btn-danger mr-2" style="margin-top: 16px">
                    <i class="fas fa-upload"></i> Upload CSV</button>

                    <a href="{{ asset('customers.csv') }}" download class="btn btn-primary" style="margin-top: 16px">
                        <i class="fas fa-download"></i> Download CSV Format
                    </a>
                    <button type="button" name="upload_server_btn"  class="btn btn-success mr-2" style="margin-top: 16px">
                    <i class="fas fa-upload"></i> Upload Server</button>
                </div>
                <div class="card-body">
                    <table id="customer_import_datatable" class="table table-bordered dt-responsive nowrap"
    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Package</th>
            <th>Amount</th>
            <th>User Name</th>
            <th>Mobile no.</th>
            <th>POP/Branch</th>
            <th>Area/Location</th>
            <th>Create Date</th>
            <th>Expire Date</th>
        </tr>
    </thead>
    <tbody>
        @php
            $directory = public_path('uploads/csv/');
            $files = scandir($directory);
            $upldNo = 1;

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                    $CSVvar = fopen($directory . $file, 'r');

                    if ($CSVvar !== false) {
                        $i = 0;

                        while (($data = fgetcsv($CSVvar)) !== false) {
                            // Skip empty lines
                            if (!array_filter($data)) {
                                continue;
                            }

                            if ($i > 0) {
                                if (count($data) < 12) {
                                    echo '<tr style="color:red;"><td colspan="8">Invalid row at line ' . $i . ' (only ' . count($data) . ' columns)</td></tr>';
                                } else {
                                    echo '<tr>';
                                    echo '<td>' . $upldNo++ . '</td>';
                                    echo '<td>' . htmlspecialchars($data[0] ?? '') . '</td>';  // Name
                                    echo '<td>' . htmlspecialchars($data[7] ?? '') . '</td>';  // Package
                                    echo '<td>' . htmlspecialchars($data[4] ?? '') . '</td>';  // Amount
                                    echo '<td>' . htmlspecialchars($data[5] ?? '') . '</td>';  // Username
                                    echo '<td>' . htmlspecialchars($data[1] ?? '') . '</td>';  // Mobile
                                    echo '<td>' . htmlspecialchars($data[8] ?? '') . '</td>';  // POP
                                    echo '<td>' . htmlspecialchars($data[9] ?? '') . '</td>'; // Area
                                    echo '<td>' . htmlspecialchars($data[11] ?? '') . '</td>'; // Create DATE
                                    echo '<td>' . htmlspecialchars($data[12] ?? '') . '</td>'; // Expire date
                                    echo '</tr>';
                                }
                            }
                            $i++;
                        }

                        fclose($CSVvar);
                    }
                }
            }
        @endphp
    </tbody>
</table>

<!-- CSV Files List with Delete Option -->
<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>File Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $directory = public_path('uploads/csv/');
            $files = scandir($directory);

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === "csv") {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($file) . '</td>';
                    echo '<td>';
                        echo '<form action="' . route('admin.customer.delete_csv_file', ['file' => $file]) . '" style="display:inline;">
                            <button type="submit" class="btn btn-danger">Delete</button>
                          </form>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
        @endphp
    </tbody>
</table>


                </div>
            </div>

        </div>
    </div>



    <!--------------------CSV File Import Modal---------------------------->
    <div class="modal fade " id="fileImportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-upload"></i> File Import
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.customer.customer_csv_file_import') }}" method="POST" enctype="multipart/form-data" id="fileImportForm">
                    @csrf
                    <div class="modal-body">


                        <div class="form-group mb-2">
                            <label>Upload Your File:</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" name="file_upload_submit_btn" class="btn btn-success"><i
                                class="mdi mdi-upload"></i> Upload Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script  src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script type="text/javascript">
       handleSubmit("#fileImportForm", "#fileImportModal");
      $("#customer_import_datatable").DataTable();
      $(document).on('click','button[name="upload_server_btn"]',function(e){
        e.preventDefault();
        confirm("Are you sure you want to upload the CSV file to the server?") ? null : e.preventDefault();
        /* Get the button that triggered the modal*/

        var submitBtn = $(this);
        var originalBtnText = submitBtn.html();

        // Change button text to indicate loading
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="visually-hidden">Loading...</span>');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.customer.upload_csv_file') }}",
            type: "GET",
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.success){
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }else{
                    toastr.error(response.message);

                }
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(xhr.responseText);
                alert("An error occurred while uploading the file.");
            },
            complete: function() {
                // Re-enable the button and reset its text
                submitBtn.prop('disabled', false);
                submitBtn.html(originalBtnText);
            }
        });
      });
    </script>
@endsection
