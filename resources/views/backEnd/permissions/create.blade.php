@extends('backEnd.layouts.master')
@section('title','Permission Create')

@section('content')
<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                    <!-- LEFT -->
                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                             style="width:60px;height:60px;">
                            <i class="mdi mdi-account-group fs-2 text-white" style="font-size:28px;"></i>
                        </div>

                        <div>
                            <h4 class="mb-1 ">Permission Management</h4>
                            <p class="text-muted mb-0">
                                Create assign permissions
                            </p>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('permissions.index') }}" class="btn btn-primary">
                            <i class="fe-list me-1"></i> Permission List
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- FORM -->
    <div class="row">
        <div class="col-lg-12">

            <div class="card border-0 shadow-sm">
                <div class="card-body">

                      <form action="{{route('permissions.store')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="Enter Permission Name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="mt-3">
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function(){

    $('#checkall').on('click', function () {
        $('.permission-checkbox').prop('checked', $(this).prop('checked'));
    });

});
</script>
@endsection
