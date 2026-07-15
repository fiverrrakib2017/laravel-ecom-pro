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
                                    Banner Edit
                                </h3>

                                <span class="text-muted">
                                    Edit  banner  easily.
                                </span>
                            </div>

                        </div>

                        <div>

                            <a href="{{ route('banners.index') }}"
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

                        Edit Category

                    </h5>

                </div>

                <div class="card-body">

                    <form action="{{ route('banners.update') }}" method="POST"
                          enctype="multipart/form-data" data-parsley-validate>

                        @csrf
                        <input type="hidden" value="{{$edit_data->id}}" name="id">
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="link" class="form-label">link *</label>
                                    <input type="text" class="form-control @error('link') is-invalid @enderror" name="link" value="{{$edit_data->link}}" id="link" required="">
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
                                    <select class="form-control select2-multiple @error('link') is-invalid @enderror" name="category_id" data-toggle="select2"  data-placeholder="Choose ...">
                                        <optgroup>
                                            <option value="">Select..</option>
                                            @foreach($categories as $value)
                                            <option  value="{{$value->id}}" @if($edit_data->category_id==$value->id)selected @endif>{{$value->name}}</option>
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
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" value="{{ $edit_data->image }}"  id="image" >
                                    <img style="width: 50px; height: 50px;" src="{{asset($edit_data->image)}}" alt="" class="edit-image">
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-12 mb-3">
                                <div class="form-group">
                                    <label for="status" class="d-block">Status</label>
                                    <label class="switch">
                                    <input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif>
                                    <span class="slider round"></span>
                                    </label>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->


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

                                Update Banner

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
