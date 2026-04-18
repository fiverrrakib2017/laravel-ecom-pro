@extends('backEnd.layouts.master')
@section('title','Product Edit')
@section('css')
<style>
  .increment_btn,
  .remove_btn {
    margin-top: -17px;
    margin-bottom: 10px;
  }

  /* FIX summernote UI */
  .note-editor.note-frame {
      border-radius: 8px;
  }

  .note-editable {
      min-height: 150px !important;
      max-height: 300px;
      overflow-y: auto;
      word-break: break-word;
  }
  /* SWITCH BASE */
.switch {
  position: relative;
  display: inline-block;
  width: 52px;
  height: 26px;
}

/* Hide default checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* Slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #dee2e6;
  transition: all 0.3s ease;
  border-radius: 50px;
}

/* Circle */
.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: #fff;
  transition: 0.3s;
  border-radius: 50%;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* ON state */
.switch input:checked + .slider {
  background: linear-gradient(45deg, #198754, #20c997);
}

/* Move circle */
.switch input:checked + .slider:before {
  transform: translateX(26px);
}

/* Hover effect */
.switch:hover .slider {
  box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

/* Optional label spacing */
.form-group label {
  font-weight: 500;
  margin-bottom: 5px;
}
</style>

<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"/>
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
                        <i class="fas fa-edit fs-4"></i>
                    </div>

                    <div>
                        <h4 class="mb-0">Product Edit</h4>
                        <small class="text-muted">Edit product easily</small>
                    </div>
                </div>

                <!-- Right -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="vr d-none d-sm-block"></div>

                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fe-shopping-cart me-1"></i> Product List
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="row order_page">
    <div class="col-12">
        <div class="card">
             <div class="card-body">
                <form action="{{route('products.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data" name="editForm">

                    @csrf
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$edit_data->name }}" id="name" required="" />
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-md-4 mb-2">
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">
                                Categories <span class="text-danger">*</span>
                            </label>

                            <select class="form-control form-select select2 @error('category_id') is-invalid @enderror"
                                    name="category_id"
                                    required>

                                <option value="">Select Category</option>

                                @foreach($categories as $category)

                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $edit_data->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>

                                    @foreach($category->childrenCategories as $childCategory)
                                        <option value="{{ $childCategory->id }}"
                                            {{ old('category_id', $edit_data->category_id) == $childCategory->id ? 'selected' : '' }}>
                                            — {{ $childCategory->name }}
                                        </option>
                                    @endforeach

                                @endforeach

                            </select>

                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="subcategory_id" class="form-label">SubCategories (Optional)</label>
                        <select class="form-control form-select select2-multiple @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id" data-placeholder="Choose ...">
                        <optgroup>
                            <option value="">Select..</option>
                            @foreach($subcategory as $key=>$value)
                            <option value="{{$value->id}}">{{$value->subcategoryName}}</option>
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
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="childcategory_id" class="form-label">Child Categories (Optional)</label>
                        <select class="form-control form-select select2-multiple @error('childcategory_id') is-invalid @enderror" id="childcategory_id" name="childcategory_id" data-placeholder="Choose ...">
                        <optgroup>
                            <option value="">Select..</option>
                            @foreach($childcategory as $key=>$value)
                            <option value="{{$value->id}}">{{$value->childcategoryName}}</option>
                            @endforeach
                        </optgroup>
                        </select>

                        @error('childcategory_id')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col end -->

                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="category_id" class="form-label">Brands</label>
                        <select class="form-control select2 @error('brand_id') is-invalid @enderror" value="{{ old('brand_id') }}" name="brand_id">
                        <option value="">Select..</option>
                        @foreach($brands as $value)
                        <option value="{{$value->id}}" @if($edit_data->brand_id==$value->id) selected @endif>{{$value->name}}</option>
                        @endforeach
                        </select>
                        @error('brand_id')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="purchase_price" class="form-label">Purchase Price <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('purchase_price') is-invalid @enderror" name="purchase_price" value="{{ $edit_data->purchase_price}}" id="purchase_price" required />

                        @error('purchase_price')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col-end -->
                    <!-- col-end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="old_price" class="form-label">Old Price <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('old_price') is-invalid @enderror" name="old_price" value="{{ $edit_data->old_price }}" id="old_price" />

                        @error('old_price')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="new_price" class="form-label">New Price <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('new_price') is-invalid @enderror" name="new_price" value="{{ $edit_data->new_price }}" id="new_price" required />

                        @error('new_price')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{ $edit_data->stock }}" id="stock" />

                        @error('stock')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col-end -->

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Product Images <span class="text-danger">*</span></label>

                        <!-- Existing Images Preview -->
                        @if($edit_data->images->count() > 0)
                        <div class="border rounded p-2 mb-3 bg-light">
                            <div class="row g-2">
                                @foreach($edit_data->images as $image)
                                <div class="col-4 position-relative">
                                    <div class="border rounded overflow-hidden position-relative">
                                        <img src="{{ asset($image->image) }}"
                                            class="img-fluid w-100"
                                            style="height: 100px; object-fit: cover;"
                                            alt="Product Image">

                                        <a href="{{ route('products.image.destroy',['id'=>$image->id]) }}"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle"
                                        onclick="return confirm('Are you sure to delete this image?')">
                                            <i class="mdi mdi-close"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Hidden Clone Template -->
                        <div class="clone d-none">
                            <div class="input-group mb-2">
                                <input type="file" name="image[]" class="form-control" />
                                <button class="btn btn-outline-danger btn-remove" type="button">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Upload Input -->
                        <div class="image-upload-wrapper">
                            <div class="input-group increment mb-2">
                                <input type="file"
                                    name="image[]"
                                    class="form-control @error('image') is-invalid @enderror" />

                                <button class="btn btn-success btn-increment" type="button">
                                    <i class="fa fa-plus"></i> Add More
                                </button>
                            </div>

                            @error('image')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="pro_unit" class="form-label">Product Unit (Optional)</label>
                        <input type="text" class="form-control @error('pro_unit') is-invalid @enderror" name="pro_unit" value="{{ $edit_data->pro_unit }}" id="pro_unit" />

                        @error('pro_unit')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="pro_video" class="form-label">Product Video (Optional)</label>

                        <input type="text" class="form-control @error('pro_video') is-invalid @enderror" name="pro_video" value="{{ $edit_data->pro_video }}" id="pro_video" />

                        @error('pro_video')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>

                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="roles" class="form-label">Size (Option)</label>
                        <select class="form-control select2" name="proSize[]" multiple="multiple">
                        <option value="">Select</option>
                        @foreach($totalsizes as $totalsize)
                        <option value="{{$totalsize->id}}" @foreach($selectsizes as $selectsize) @if($totalsize->id == $selectsize->size_id)selected="selected"@endif @endforeach>{{$totalsize->sizeName}}</option>
                        @endforeach
                        </select>
                        @error('sizes')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!--col end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group mb-3">
                        <label for="color" class="form-label">Color (Optional)</label>
                        <select class="form-control select2" name="proColor[]" multiple="multiple">
                        <option value="">Select</option>
                            @foreach($totalcolors as $totalcolor)
                                <option value="{{$totalcolor->id}}" @foreach($selectcolors as $selectcolor) @if($totalcolor->id == $selectcolor->color_id) selected="selected" @endif @endforeach>{{$totalcolor->colorName}} </option>
                            @endforeach

                        </select>
                        @error('colors')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!--col end -->
                    <div class="col-sm-12 mb-3">
                    <div class="form-group">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror">{{$edit_data->description}}</textarea>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col end -->

                    <!-- col end -->
                    <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label for="status" class="d-block">Status</label>
                        <label class="switch">
                        <input type="checkbox" value="1" name="status"  @if($edit_data->status==1)checked @endif />
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
                    <div class="col-md-4 mb-2">
                    <div class="form-group">
                        <label for="topsale" class="d-block">Hot Deals</label>
                        <label class="switch">
                        <input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1)checked @endif/>
                        <span class="slider round"></span>
                        </label>
                        @error('topsale')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    </div>
                    <!-- col end -->

                    <div>
                    <input type="submit" class="btn btn-success" value="Submit" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Assign User End-->
@endsection
@section('script')
<script src="{{asset('backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<!-- Plugins js -->
<script src="{{asset('backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
     $('.summernote').summernote({
        placeholder: "Enter Your Text Here",
        height: 200,
        // toolbar: [
        //     ['style', ['bold', 'italic', 'underline']],
        //     ['para', ['ul', 'ol', 'paragraph']],
        //     ['insert', ['link']],
        //     ['view', ['codeview']]
        // ]
    });
    $(".btn-increment").click(function () {
      var html = $(".clone").html();
      $(".increment").after(html);
    });
    $("body").on("click", ".btn-danger", function () {
      $(this).parents(".control-group").remove();
    });
    $(".increment_btn").click(function () {
      var html = $(".clone_price").html();
      $(".increment_price").after(html);
    });
    $("body").on("click", ".remove_btn", function () {
      $(this).parents(".increment_control").remove();
    });

    $(".select2").select2();
  });

  // category to sub
  $("#category_id").on("change", function () {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: "GET",
        url: "{{url('ajax-product-subcategory')}}?category_id=" + ajaxId,
        success: function (res) {
          if (res) {
            $("#subcategory_id").empty();
            $("#subcategory_id").append('<option value="0">Choose...</option>');
            $.each(res, function (key, value) {
              $("#subcategory_id").append('<option value="' + key + '">' + value + "</option>");
            });
          } else {
            $("#subcategory_id").empty();
          }
        },
      });
    } else {
      $("#subcategory_id").empty();
    }
  });

  // subcategory to childcategory
  $("#subcategory_id").on("change", function () {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: "GET",
        url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + ajaxId,
        success: function (res) {
          if (res) {
            $("#childcategory_id").empty();
            $("#childcategory_id").append('<option value="0">Choose...</option>');
            $.each(res, function (key, value) {
              $("#childcategory_id").append('<option value="' + key + '">' + value + "</option>");
            });
          } else {
            $("#childcategory_id").empty();
          }
        },
      });
    } else {
      $("#childcategory_id").empty();
    }
  });
</script>
<script type="text/javascript">
  document.forms["editForm"].elements["category_id"].value = "{{$edit_data->category_id}}";
  document.forms["editForm"].elements["subcategory_id"].value = "{{$edit_data->subcategory_id}}";
  document.forms["editForm"].elements["childcategory_id"].value = "{{$edit_data->childcategory_id}}";
</script>
@endsection

