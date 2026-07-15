@extends('backEnd.layouts.master')
@section('title','Banner  Create')

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
                                    Banner Create
                                </h3>

                                <span class="text-muted">
                                    Create a new banner  easily.
                                </span>
                            </div>

                        </div>

                        <div>

                            <a href="{{ route('banner_category.index') }}"
                               class="btn btn-primary">

                                <i class="mdi mdi-format-list-bulleted me-1"></i>

                                Banner List

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

                        Add New Banner

                    </h5>

                </div>

                <div class="card-body">

                    <form action="{{ route('banners.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          data-parsley-validate>

                        @csrf

                       <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="link" class="form-label">link *</label>
                                <input type="text" placeholder="Enter Link" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') }}" id="link" required="">
                                @error('link')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col-end -->
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <label for="category_id" class="form-label">Banner Category</label>
                                <select class="form-control select2-multiple @error('category_id') is-invalid @enderror" name="category_id" data-toggle="select2"  data-placeholder="Choose ...">
                                    <optgroup >
                                        <option value="">Select..</option>
                                        @foreach($categories as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-12 mb-3">
                            <div class="form-group">
                                <label for="image" class="form-label">Image *</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror " name="image"  value="{{ old('image') }}"  id="image" required="">
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-6 mb-3">
                            <div class="form-group">
                                <label for="status" class="d-block">Status</label>
                                <label class="switch">
                                <input type="checkbox" value="1" name="status" checked>
                                <span class="slider round"></span>
                                </label>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
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

                                Save Banner

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

<script type="text/javascript">
    $("select[name='category_id']").select2();
</script>
{!! Toastr::message() !!}

@endsection
