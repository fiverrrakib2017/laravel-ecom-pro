@extends('backEnd.layouts.master')
@section('title','Category Edit')
@section('css')
<style>

</style>
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"/>

@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                    <!-- Left -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width:50px; height:50px;">
                            <i class="mdi mdi-shape-outline fs-4"></i>
                        </div>

                        <div>
                            <h4 class="mb-0">Sub Category Edit</h4>
                            <small class="text-muted"> Product Sub Category Edit easily</small>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                        <div class="vr d-none d-sm-block"></div>

                        <a href="{{ route('subcategories.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-1"></i> Sub-Category List
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
   <div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                 <form action="{{route('subcategories.update')}}" method="POST" class="row" data-parsley-validate="" name="editForm"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="id">


                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                             <select class="form-control select2-multiple @error('category_id') is-invalid @enderror" id="category_id" name="category_id" value="{{ old('category_id') }}" data-placeholder="Choose ..."required>
                                <optgroup >
                                    <option value="">Choose..</option>
                                    @foreach($categories as $key=>$value)
                                    <option value="{{$value->id}}" @if($value->id == $edit_data->category_id) selected @endif>{{$value->name}}</option>
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

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="subcategoryName" class="form-label">Sub-Category Name *</label>
                            <input type="text" id="subcategoryName" class="form-control @error('subcategoryName') is-invalid @enderror" name="subcategoryName" value="{{ $edit_data->subcategoryName }}" id="subcategoryName" required="">
                            @error('subcategoryName')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" name="meta_title" value="{{ $edit_data->meta_title }}" id="meta_title">
                            @error('meta_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="summernote form-control @error('meta_description')  is-invalid @enderror" name="meta_description" rows="6"  id="meta_description" >{!!$edit_data->meta_description!!}</textarea>
                            @error('meta_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col mb-3">
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



                    <div>
                         <button type="button" onclick="history.back();" class="btn btn-danger">Back</button>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>


            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection

@section('script')
<!-- Plugins js -->
<script src="{{asset('backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>
 <script type="text/javascript">
    $('#category_id').select2();
    $(document).ready(function () {
        $('.summernote').summernote({
            placeholder: "Enter Your Text Here",
            height: 200,
        });
    });
 </script>
@endsection
