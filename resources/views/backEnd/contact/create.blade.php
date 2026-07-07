@extends('backEnd.layouts.master')
@section('title','Contact Create')
@section('css')
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 55px;
    height: 28px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #dee2e6;
    transition: .3s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
}

input:checked + .slider {
    background-color: #198754;
}

input:checked + .slider:before {
    transform: translateX(27px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>
@endsection
@section('content')
 <div class="row">
    <div class="col-9 m-auto">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                <!-- Left -->
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 50px; height: 50px;">
                       <i class="mdi mdi-card-account-phone fs-3"></i>
                    </div>

                    <div>
                        <h4 class="mb-0">Contact Create</h4>
                        <small class="text-muted"> Contact Create easily</small>
                    </div>
                </div>

                <!-- Right -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="vr d-none d-sm-block"></div>

                    <a href="{{ route('contact.index') }}" class="btn btn-primary">
                       <i class="mdi mdi-share-variant"></i> Manage Contact
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
              <form action="{{route('contact.store')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                   <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="hotline" class="form-label">Hotline Number</label>
                            <input type="text" placeholder="Enter Hotline Number" class="form-control @error('hotline') is-invalid @enderror" name="hotline" value="{{ old('hotline') }}" id="hotline">
                            @error('hotline')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="hotmail" class="form-label">Hot Mail</label>
                            <input type="email" placeholder="Enter Hotline Mail" class="form-control @error('hotmail') is-invalid @enderror" name="hotmail" value="{{ old('hotmail') }}" id="hotmail">
                            @error('hotmail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="text" placeholder="Enter Phone Number" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}"  id="phone" required="">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="text" placeholder="Enter Email Address" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  id="email" required="">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address*</label>
                            <input type="text" placeholder="Enter Address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}"  id="address" required="">
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="maplink" class="form-label">Google Map</label>
                            <input type="text" placeholder="Enter Google Map" class="form-control @error('maplink') is-invalid @enderror" name="maplink" value="{{ old('maplink') }}"  id="maplink" >
                            @error('maplink')
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
                        <button type="submit" class="btn btn-success" >Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
   </div>
@endsection


@section('script')
 <script type="text/javascript">
     $('select').select2();
    $(document).ready(function () {

    });
 </script>
@endsection
