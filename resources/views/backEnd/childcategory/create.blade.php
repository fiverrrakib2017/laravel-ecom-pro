@extends('backEnd.layouts.master')
@section('title','Child-Category Create')
@section('css')
<style>

</style>
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"/>

@endsection
@section('content')
 <div class="row">
    <div class="col-9 m-auto">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                <!-- Left -->
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px; height:50px;">
                        <i class="mdi mdi-shape-outline fs-4"></i>
                    </div>

                    <div>
                        <h4 class="mb-0">Child-Category Add</h4>
                        <small class="text-muted"> Product Child-Category Add easily</small>
                    </div>
                </div>

                <!-- Right -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="vr d-none d-sm-block"></div>

                    <a href="{{ route('childcategories.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-1"></i>Child-Category List
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-9 m-auto">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                 <form action="{{route('childcategories.store')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="subcategory_id" class="form-label">Sub Category *</label>
                             <select class="form-control select2-multiple @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id" value="{{ old('subcategory_id') }}" data-toggle="select2"  data-placeholder="Choose ..."required>
                                <optgroup >
                                    <option value="">Choose..</option>
                                    @foreach ($menucategories as $category)
                                    <option value="">== == == {{ $category->name }} == == ==</option>
                                        @foreach ($category->subcategories as $subcat)
                                            <option value="{{ $subcat->id }}"  >{{ $subcat->subcategoryName }}</option>

                                        @endforeach
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('subcategory_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="childcategoryName" class="form-label">Childcategory Name *</label>
                            <input type="text" id="childcategoryName" class="form-control @error('childcategoryName') is-invalid @enderror" name="childcategoryName" value="{{ old('childcategoryName') }}" id="childcategoryName" required="">
                            @error('childcategoryName')
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
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" name="meta_title" value="{{ old('meta_title') }}" id="meta_title">
                            @error('meta_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="meta_description" class="form-label">Meta Description*</label>
                            <textarea type="text" class="summernote form-control @error('meta_description') is-invalid @enderror" name="meta_description" rows="6" value="{{ old('meta_description') }}"  id="meta_description"></textarea>
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

            </div>
        </div>
    </div>
   </div>
@endsection


@section('script')
<!-- Plugins js -->
<script src="{{asset('backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>
 <script type="text/javascript">
      $('#subcategory_id').select2();
    $(document).ready(function () {
        $('.summernote').summernote({
            placeholder: "Enter Your Text Here",
            height: 200,
        });
    });
 </script>
  {!! Toastr::message() !!}
@endsection
