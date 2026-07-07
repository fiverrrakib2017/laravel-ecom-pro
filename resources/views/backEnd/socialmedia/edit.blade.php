@extends('backEnd.layouts.master')
@section('title','Social Media Edit')
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
<div class="container-fluid">
    <!-- start page title -->
     <div class="row">
        <div class="col-9 m-auto">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">

                    <!-- Left -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                             <i class="mdi mdi-share-variant fs-3"></i>
                        </div>

                        <div>
                            <h4 class="mb-0">Social Media Edit</h4>
                            <small class="text-muted"> Social Media  Edit easily</small>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                        <div class="vr d-none d-sm-block"></div>

                        <a href="{{ route('socialmedias.index') }}" class="btn btn-primary">
                        <i class="mdi mdi-share-variant"></i> Manage Social Media
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
   <div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                 <form action="{{route('socialmedias.update')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="id">
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ $edit_data->title}}" id="title" required="">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="icon" class="form-label">Icon *</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" name="icon" value="{{ $edit_data->icon}}"  id="icon" required="">
                            @error('icon')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="link" class="form-label">Link *</label>
                            <input type="text" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ $edit_data->link }}"  id="link" required="">
                            @error('link')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="color" class="form-label">Color *</label>
                            <input type="color" class="form-control @error('color') is-invalid @enderror" name="color" value="{{ $edit_data->color}}"  id="color" required="">
                            @error('color')
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
 <script type="text/javascript">
    $('select').select2();
    $(document).ready(function () {

    });
 </script>
@endsection
