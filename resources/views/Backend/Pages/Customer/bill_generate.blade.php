@extends('Backend.Layout.App')
@section('title', 'Bill Generate | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Bill Generate',
                    'description' => 'Generate and manage user bills for the current month.',
                    'icon' => '<i class="fas fa-money-bill-alt"></i>',
                ])

                <div class="card-body">

                    <div class="table-responsive responsive-table">

                        <table id="datatable1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="">ID</th>
                                    <th class="">Username</th>
                                    <th class="">Month</th>
                                    <th class="">Price</th>
                                    <th class="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row['id'] }}</td>
                                        <td>{!! $row['username'] !!}</td>
                                        <td>{!! $row['month'] !!}</td>
                                        <td>{!! $row['price'] !!}</td>
                                        <td><button class="btn btn-success" onclick="markAsPaid({{ $row['id'] }})"><i class="fas fa-money-bill-alt"></i> Mark as Paid</button></td>
                                    </tr>
                                @endforeach

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
        $(document).ready(function() {
            $("#datatable1").DataTable();


        });
        function markAsPaid(customerId) {
            alert("Marking customer ID " + customerId + " as paid.");
        }
    </script>

@endsection
