@extends('Backend.Layout.App')
@section('title', 'Permission List | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h4> Permission List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tableStyle">
                        <table id="role_datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    use Spatie\Permission\Models\Permission;
                                    $permissions = Permission::orderBy('id', 'asc')->get();
                                @endphp

                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td>{{ $permission->name }}</td>
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

    $("#role_datatable").DataTable();

</script>


@endsection
