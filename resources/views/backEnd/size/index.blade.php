@extends('backEnd.layouts.master')
@section('title','Size Manage')
@section('css')
<style>

</style>

@endsection
@section('content')
 <div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                <!-- Left -->
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px; height:50px;">
                        <i class="mdi mdi-shape-outline fs-4"></i>
                    </div>

                    <div>
                        <h4 class="mb-0">Size Manage</h4>
                        <small class="text-muted"> Product Size Manage easily</small>
                    </div>
                </div>

                <!-- Right -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="vr d-none d-sm-block"></div>

                    <a href="{{ route('sizes.create') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-1"></i> Size Add
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
                    <table id="sizes_datatable" class="table table-hover align-middle mb-0">
                        <thead >
                            <tr>
                                <th>SL</th>
                                <th>Size Name</th>
                                <th>Status</th>
                                <th>Action</th>
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

                                 <td>{{$value->sizeName ?? '---'}}</td>



                                <td>
                                    @if($value->status == 1)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="text-left">
                                    <div class="d-flex justify-content-left gap-2">

                                        @if($value->status == 1)
                                            <form method="post"
                                                action="{{ route('sizes.inactive') }}"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden"
                                                    value="{{ $value->id }}"
                                                    name="hidden_id">

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger change-confirm"
                                                        title="Deactivate">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post"
                                                action="{{ route('sizes.active') }}"
                                                class="d-inline">
                                                @csrf
                                                <input type="hidden"
                                                    value="{{ $value->id }}"
                                                    name="hidden_id">

                                                <button type="submit"
                                                        class="btn btn-sm btn-success change-confirm"
                                                        title="Activate">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('sizes.edit', $value->id) }}"
                                        class="btn btn-sm btn-primary"
                                        title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="post" action="{{ route('sizes.destroy') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this ?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">

                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
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
    $('#sizes_datatable').DataTable();
 </script>
 {!! Toastr::message() !!}
@endsection
