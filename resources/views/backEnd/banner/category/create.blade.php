@extends('backEnd.layouts.master')
@section('title','Banner Category Create')

@section('content')

<div class="container-fluid">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">

                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center shadow"
                                 style="width:65px;height:65px;">
                                <i class="mdi mdi-shape-outline text-white" style="font-size:28px;"></i>
                            </div>

                            <div class="ms-3">
                                <h3 class="mb-1 ">
                                    Banner Category
                                </h3>

                                <span class="text-muted">
                                    Create a new banner category easily.
                                </span>
                            </div>

                        </div>

                        <div>

                            <a href="{{ route('banner_category.index') }}"
                               class="btn btn-primary">

                                <i class="mdi mdi-format-list-bulleted me-1"></i>

                                Category List

                            </a>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <!-- Form Card -->

    <div class="row">

        <div class="col-lg-8 mx-auto">

            <div class="card shadow border-0">

                <div class="card-header bg-white">

                    <h5 class="mb-0 ">

                        <i class="mdi mdi-plus-circle text-success me-2"></i>

                        Add New Category

                    </h5>

                </div>

                <div class="card-body">

                    <form action="{{ route('banner_category.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          data-parsley-validate>

                        @csrf

                        <div class="mb-4">

                            <label class="form-label ">

                                Category Name
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                placeholder="Enter Banner Category Name">

                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>


                        <div class="mb-4">

                            <label class="form-label d-block">

                                Status

                            </label>

                            <label class="switch">

                                <input
                                    type="checkbox"
                                    checked
                                    value="1"
                                    name="status">

                                <span class="slider round"></span>

                            </label>

                            <span class="ms-2 text-muted">

                                Active

                            </span>

                        </div>


                        <hr>


                        <div class="d-flex justify-content-end">

                            <button
                                type="button"
                                onclick="history.back()"
                                class="btn btn-light me-2">

                                <i class="mdi mdi-arrow-left"></i>

                                Back

                            </button>

                            <button
                                type="submit"
                                class="btn btn-success">

                                <i class="mdi mdi-content-save"></i>

                                Save Category

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

{!! Toastr::message() !!}

@endsection
