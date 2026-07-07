@extends('backEnd.layouts.master')
@section('title','Create Compaign Page')
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
                         <i class="mdi mdi-web fs-4"></i>
                    </div>

                    <div>
                         <h4 class="mb-0">Landing Page Management</h4>
                        <small class="text-muted">Landing Page Create easily</small>
                    </div>
                </div>

                <!-- Right -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="vr d-none d-sm-block"></div>

                    <a href="{{ route('campaign.index') }}" class="btn btn-primary">
                        <i class="mdi mdi-web me-1"></i> Manage Landing Page
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
          <form action="{{route('campaign.store')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
            @csrf
            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="banner_title" class="form-label">Banner Title *</label>
                    <input type="text" placeholder="Enter Banner Name" class="form-control @error('banner_title') is-invalid @enderror" name="banner_title" value="{{ old('banner_title') }}"  id="banner_title" required="">
                    @error('banner_title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->

            <div class="col-sm-6 mb-3">
                <div class="form-group">
                    <label for="image_one" class="form-label">Intro Baner Image One *</label>
                    <input type="file" class="form-control @error('image_one') is-invalid @enderror " name="image_one"  value="{{ old('image_one') }}"  id="image_one" required="">
                    @error('image_one')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

                <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="price" class="form-label">Price *</label>
                    <input type="text" placeholder="Enter Price" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}"  id="price" required="">
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Landing Page Title *</label>
                    <input type="text" placeholder="Enter Page Title" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}"  id="name" required="">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>


            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="youtube_video" class="form-label">Youtube Video Embed Code </label>
                    <input type="text" placeholder="Enter Video Embed Code" class="form-control @error('youtube_video') is-invalid @enderror" name="youtube_video" value="{{ old('youtube_video') }}"  id="youtube_video">
                    @error('youtube_video')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->

            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="short_details" class="form-label">Meta Description *</label>
                    <input type="text" placeholder="Enter Meta Description" class="form-control @error('short_details') is-invalid @enderror" name="short_details" value="{{ old('short_details') }}"  id="short_details" required="">
                    @error('short_details')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->


            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="upokarita_title" class="form-label">Upokarita Tittle *</label>
                    <input type="text" placeholder="Enter Upokarita Title" class="form-control @error('upokarita_title') is-invalid @enderror" name="upokarita_title" value="{{ old('upokarita_title') }}"  id="upokarita_title" required="">
                    @error('upokarita_title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->


            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="upokarita_text1" class="form-label">Upokarita Text 1*</label>
                    <input type="text" placeholder="Enter Upokarita Title 1" class="form-control @error('upokarita_text1') is-invalid @enderror" name="upokarita_text1" value="{{ old('upokarita_text1') }}"  id="upokarita_text1" required="">
                    @error('upokarita_text1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="upokarita_text2" class="form-label">Upokarita Text 2 *</label>
                    <input type="text" placeholder="Enter Upokarita Title 2" class="form-control @error('upokarita_text2') is-invalid @enderror" name="upokarita_text2" value="{{ old('upokarita_text2') }}"  id="upokarita_text2" required="">
                    @error('upokarita_text2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="upokarita_text3" class="form-label">Upokarita Text 3 *</label>
                    <input type="text" placeholder="Enter Upokarita Text 3" class="form-control @error('upokarita_text3') is-invalid @enderror" name="upokarita_text3" value="{{ old('upokarita_text3') }}"  id="upokarita_text3" required="">
                    @error('upokarita_text3')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="upokarita_text4" class="form-label">Upokarita Text 4*</label>
                    <input type="text" placeholder="Enter Upokarita Text 4" class="form-control @error('upokarita_text4') is-invalid @enderror" name="upokarita_text4" value="{{ old('upokarita_text4') }}"  id="upokarita_text4" required="">
                    @error('upokarita_text4')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>







            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="niyom_tittle" class="form-label">Niyom Tittle *</label>
                    <input type="text" placeholder="Enter Niyom Title" class="form-control @error('niyom_tittle') is-invalid @enderror" name="niyom_tittle" value="{{ old('niyom_tittle') }}"  id="niyom_tittle" required="">
                    @error('niyom_tittle')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->


            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="niyom_text1" class="form-label">Niyom Text 1*</label>
                    <input type="text" placeholder="Enter Niyom Text 1" class="form-control @error('niyom_text1') is-invalid @enderror" name="niyom_text1" value="{{ old('niyom_text1') }}"  id="niyom_text1" required="">
                    @error('niyom_text1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="niyom_text2" class="form-label">Niyom Text 2 *</label>
                    <input type="text" placeholder="Enter Niyom Text 2" class="form-control @error('niyom_text2') is-invalid @enderror" name="niyom_text2" value="{{ old('niyom_text2') }}"  id="niyom_text2" required="">
                    @error('niyom_text2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="niyom_text3" class="form-label">Niyom Text 3 *</label>
                    <input type="text" placeholder="Enter Niyom Text 3" class="form-control @error('niyom_text3') is-invalid @enderror" name="niyom_text3" value="{{ old('niyom_text3') }}"  id="niyom_text3" required="">
                    @error('niyom_text3')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="niyom_text4" class="form-label">Niyom Text 4*</label>
                    <input type="text" placeholder="Enter Niyom Text 4" class="form-control @error('niyom_text4') is-invalid @enderror" name="niyom_text4" value="{{ old('niyom_text4') }}"  id="niyom_text4" required="">
                    @error('niyom_text4')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>




            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="slider_tittle" class="form-label">Slider Tittle *</label>
                    <input type="text" placeholder="Enter Slider Title" class="form-control @error('slider_tittle') is-invalid @enderror" name="slider_tittle" value="{{ old('slider_tittle') }}"  id="slider_tittle" required="">
                    @error('slider_tittle')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->









            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="review" class="form-label">Review*</label>
                    <input type="text" placeholder="Enter Review" class="form-control @error('review') is-invalid @enderror" name="review" value="{{ old('review') }}"  id="review" required="">
                    @error('review')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>


            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="product_id" class="form-label">Products *</label>
                        <select class="select2 form-control @error('product_id') is-invalid @enderror" value="{{ old('product_id') }}" name="product_id" data-placeholder="Choose ..." required="">

                        <option value="">Select..</option>
                        @foreach($products as $value)
                        <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach

                    </select>
                    @error('product_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col end -->







            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <label for="details_tittle" class="form-label">Details Tittle *</label>
                    <input type="text" placeholder="Details Title" class="form-control @error('details_tittle') is-invalid @enderror" name="details_tittle" value="{{ old('details_tittle') }}"  id="details_tittle" required="">
                    @error('details_tittle')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- col-end -->


            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="details_text1" class="form-label">Details Text 1</label>
                    <input type="text" placeholder="Details Text 1" class="form-control @error('details_text1') is-invalid @enderror" name="details_text1" value="{{ old('details_text1') }}"  id="details_text1" required="">
                    @error('details_text1')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="details_text2" class="form-label">Details Text 2</label>
                    <input type="text" placeholder="Details Text 2" class="form-control @error('details_text2') is-invalid @enderror" name="details_text2" value="{{ old('details_text2') }}"  id="details_text2" required="">
                    @error('details_text2')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="details_text3" class="form-label">Details Text 3</label>
                    <input type="text" placeholder="Enter Details Text 3" class="form-control @error('details_text3') is-invalid @enderror" name="details_text3" value="{{ old('details_text3') }}"  id="details_text3" required="">
                    @error('details_text3')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="details_text4" class="form-label">Details Text 4</label>
                    <input type="text" placeholder="Details Text 4" class="form-control @error('details_text4') is-invalid @enderror" name="details_text4" value="{{ old('details_text4') }}"  id="details_text4" required="">
                    @error('details_text4')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>


            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="details_text5" class="form-label">Details Text 5</label>
                    <input type="text" placeholder="Enter Details Text 5" class="form-control @error('details_text5') is-invalid @enderror" name="details_text5" value="{{ old('details_text5') }}"  id="details_text5" required="">
                    @error('details_text5')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <div class="form-group mb-3">
                    <label for="details_text6" class="form-label">Details Text 6</label>
                    <input type="text" placeholder="Enter Details Text 6" class="form-control @error('details_text6') is-invalid @enderror" name="details_text6" value="{{ old('details_text6') }}"  id="details_text6" required="">
                    @error('details_text6')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>









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
<script>
    $(".summernote").summernote({
        placeholder: "Enter Your Text Here",
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment").after(html);
        });
        $("body").on("click", ".btn-danger", function () {
            $(this).parents(".control-group").remove();
        });
        $('.select2').select2();
    });
</script>
@endsection

