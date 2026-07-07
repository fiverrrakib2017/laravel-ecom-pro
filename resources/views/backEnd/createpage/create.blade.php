@extends('backEnd.layouts.master')
@section('title','Page Create')
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
                            <i class="mdi mdi-file-document-outline fs-4"></i>
                        </div>

                        <div>
                            <h4 class="mb-0">Page Create</h4>
                            <small class="text-muted"> Page Create easily</small>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                        <div class="vr d-none d-sm-block"></div>

                        <a href="{{ route('pages.index') }}" class="btn btn-primary">
                            <i class="mdi mdi-file-document-outline me-1"></i> Manage Page
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
                <form action="{{route('pages.store')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" placeholder="Enter Name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Title  *</label>
                            <input type="text" placeholder="Enter Title" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}"  id="title" required="">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description*</label>
                            <textarea type="text" placeholder="Enter Description" class="summernote form-control @error('description') is-invalid @enderror" name="description" rows="6" value="{{ old('description') }}"  id="description" required=""></textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
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
    $(document).ready(function () {
        $('.summernote').summernote({
            placeholder: "Enter Your Text Here",
            height: 200,
        });
    });
 </script>
  {!! Toastr::message() !!}
@endsection
