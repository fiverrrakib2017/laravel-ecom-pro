@extends('backEnd.layouts.master')
@section('title','Role Management')
@section('css')

<style>

</style>

@endsection
@section('content')
 <div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                <!-- Left Side -->
                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                         style="width:60px;height:60px;">
                        <i class="mdi mdi-account-group fs-2"></i>
                    </div>

                    <div>
                        <h4 class="mb-1 ">Role Management</h4>
                        <p class="text-muted mb-0">
                            Manage user roles and permissions efficiently.
                        </p>
                    </div>

                </div>

                <!-- Right Side -->
                <div class="mt-3 mt-md-0">

                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle-outline me-1"></i>
                        Create New Role
                    </a>

                </div>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="_datatable" class="table table-hover align-middle mb-0">
                        <thead >
                            <tr>
                                <th width="60">SL</th>
                                <th>Name</th>

                                <th width="160" class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($show_data as $key => $value)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-muted">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                <td>
                                    @if ($value->front_view == 1)
                                        <span class="badge bg-dark px-3 py-2 rounded-pill">
                                            {{ $value->name }}
                                        </span>
                                    @else
                                        <span class="fw-medium">
                                            {{ $value->name }}
                                        </span>
                                    @endif
                                </td>





                                 <td class="text-center">
    <div class="d-flex justify-content-center gap-1">
        <a href="{{route('roles.show',$value->id)}}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i>
        </a>

        <a href="{{route('roles.edit',$value->id)}}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i>
        </a>

        <form method="POST" action="{{route('roles.destroy')}}">
            @csrf
            <input type="hidden" value="{{$value->id}}" name="hidden_id">

            <button type="submit" class="btn btn-danger btn-sm delete-confirm">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
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
    $('#_datatable').DataTable();
 </script>
 {!! Toastr::message() !!}
@endsection
