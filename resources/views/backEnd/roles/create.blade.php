@extends('backEnd.layouts.master')
@section('title','Role Create')

@section('content')
<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                    <!-- LEFT -->
                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                             style="width:60px;height:60px;">
                            <i class="mdi mdi-account-group fs-2 text-white"></i>
                        </div>

                        <div>
                            <h4 class="mb-1 ">Create Role</h4>
                            <p class="text-muted mb-0">
                                Add new role and assign permissions
                            </p>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('roles.index') }}" class="btn btn-primary">
                            <i class="fe-list me-1"></i> Role List
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

                    <form action="{{ route('roles.store') }}"
                          method="POST"
                          class="row"
                          data-parsley-validate
                          enctype="multipart/form-data">

                        @csrf

                        <!-- ROLE NAME -->
                        <div class="col-12 mb-3">

                            <label class="form-label ">
                                Role Name <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   name="name"
                                   placeholder="Enter role name"
                                   required>

                            @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>

                        <!-- CHECK ALL -->
                        <div class="col-12 mb-3">

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <label class=" text-primary mb-0">
                                    Permissions
                                </label>

                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="checkall">
                                    <label class="form-check-label" for="checkall">
                                        Check All
                                    </label>
                                </div>

                            </div>

                        </div>

                        <!-- PERMISSIONS -->
                        <div class="col-12">

                            <div class="row">

                                @foreach($permission as $value)

                                <div class="col-lg-4 col-md-6 mb-2">

                                    <label class="d-flex align-items-center gap-2 p-2 border rounded permission-item">

                                        <input type="checkbox"
                                               name="permission[]"
                                               class="form-check-input permission-checkbox"
                                               value="{{ $value->id }}"
                                               id="customCheck{{ $value->id }}">

                                        <span class="ms-1">
                                            {{ $value->name }}
                                        </span>

                                    </label>

                                </div>

                                @endforeach

                            </div>

                        </div>

                        <!-- SUBMIT -->
                        <div class="col-12 mt-3">
                            <a href="{{ route('roles.index') }}" class="btn btn-danger ms-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fe-plus me-1"></i>
                                Create Role
                            </button>

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
