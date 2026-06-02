@extends('backEnd.layouts.master')
@section('title','User Create')
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
                        <i class="mdi mdi-account-group fs-3"></i>
                    </div>

                    <div>
                        <h4 class="mb-0">User Create</h4>
                        <small class="text-muted"> User Create easily</small>
                    </div>
                </div>

                <!-- Right -->
                <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="vr d-none d-sm-block"></div>

                    <a href="{{ route('users.index') }}" class="btn btn-primary">
                       <i class="mdi mdi-account-group"></i> User List
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
              <form action="{{route('users.store')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter Name" id="name" required="">
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
                            <label for="email" class="form-label">Email *</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  id="email" placeholder="Enter Email" required="">
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
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" id="password" placeholder="Enter Password" required="">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password" value="{{ old('confirm-password') }}"  id="confirm-password" placeholder="Enter Confirm Password" required="">
                            @error('confirm-password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="roles" class="form-label">Role *</label>
                             <select class="form-control select2-multiple" name="roles[]" data-toggle="select2" multiple="multiple" data-placeholder="Choose ..." required>
                                <optgroup label="Select Role">
                                    @foreach($roles as $role)
                                    <option value="{{$role->name}}">{{$role->name}}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('roles')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="image" class="form-label">Image *</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" value="{{ old('image') }}"  id="image" required="">
                            @error('image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body py-3">

                                <label class="form-label mb-3">
                                    Status
                                </label>

                                <div class="d-flex align-items-center">
                                    <label class="switch mb-0">
                                        <input type="checkbox" value="1" name="status" checked>
                                        <span class="slider round"></span>
                                    </label>


                                </div>

                                @error('status')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
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
