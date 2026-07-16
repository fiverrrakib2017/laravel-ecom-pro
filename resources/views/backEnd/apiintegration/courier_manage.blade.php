@extends('backEnd.layouts.master')
@section('title','Courier Api Gateway')
@section('css')


@endsection
@section('content')
    <!-- ===================== Steadfast Courier Configuration ===================== -->

    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3"
                        style="width:60px;height:60px;">

                        <i class="mdi mdi-truck-fast fs-2"></i>

                    </div>

                    <div>

                        <h4 class="mb-1 ">
                            Steadfast Courier
                        </h4>

                        <small class="text-muted">
                            Configure Steadfast Courier API credentials.
                        </small>

                    </div>

                </div>

                <span class="badge bg-success-subtle text-success px-3 py-2">
                    Active Gateway
                </span>

            </div>
        </div>

        <div class="card-body">
            <form action="{{route('courierapi.update')}}" method="POST" class="row" data-parsley-validate="" enctype="multipart/form-data">
                @csrf
                 <input type="hidden" name="id" value="{{$steadfast->id}}">
                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            API Key
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="mdi mdi-key"></i>
                            </span>

                            <input type="password"
                                id="steadfast_api"
                                name="api_key"
                                value="{{ $steadfast->api_key }}"
                                class="form-control">

                            <button class="btn btn-light"
                                    type="button"
                                    onclick="togglePassword('steadfast_api',this)">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label ">
                            Secret Key
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="mdi mdi-lock"></i>
                            </span>

                            <input type="password"
                                id="steadfast_secret"
                                name="secret_key"
                                value="{{ $steadfast->secret_key }}"
                                class="form-control">

                            <button class="btn btn-light"
                                    type="button"
                                    onclick="togglePassword('steadfast_secret',this)">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>

                        </div>

                    </div>

                    <div class="col-md-8 mb-4">

                        <label class="form-label ">
                            API URL
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="mdi mdi-web"></i>
                            </span>

                            <input type="text"
                                class="form-control"
                                name="url"
                                value="{{ $steadfast->url }}">

                        </div>

                    </div>

                    <div class="col-md-4 mb-4">

                        <label class="form-label  d-block">
                            Gateway Status
                        </label>

                        <div class="border rounded-3 p-3">

                            <label class="switch mb-0">

                                <input type="checkbox"
                                    value="1"
                                    name="status"
                                    @checked($steadfast->status)>

                                <span class="slider round"></span>

                            </label>

                        </div>

                    </div>

                </div>

                <hr class="my-4">

                <div class="text-end">

                    <button type="reset"
                            class="btn btn-outline-secondary px-4">

                        <i class="mdi mdi-refresh"></i>

                        Reset

                    </button>

                    <button type="submit"
                            class="btn btn-success px-5">

                        <i class="mdi mdi-content-save"></i>

                        Save Changes

                    </button>

                </div>
            </form>
        </div>

    </div>


  <!-- ===================== Pathao Courier Configuration ===================== -->

    <div class="card shadow-sm border-0 rounded-3 mt-4">

        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">

                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3"
                    style="width:55px;height:55px;">
                    <i class="mdi mdi-credit-card-settings fs-3"></i>
                </div>

                <div>
                    <h4 class="mb-1 ">Pathao Courier Configuration</h4>
                    <small class="text-muted">
                        Configure your Pathao Courier  gateway credentials.
                    </small>
                </div>

            </div>
        </div>

        <div class="card-body">

            <form action="{{ route('courierapi.update') }}"
                method="POST"
                class="row g-3"
                enctype="multipart/form-data"
                data-parsley-validate>

                @csrf

                <input type="hidden" name="id" value="{{ $pathao->id}}">
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <label for="url" class="form-label">URL *</label>
                        <input type="text" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ $pathao->url}}" id="url" required="" />
                        @error('url')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <!-- col-end -->
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <label for="token" class="form-label">Token *</label>
                        <input type="text" class="form-control @error('token') is-invalid @enderror" name="token" value="{{ $pathao->token}}" id="token" required="" />
                        @error('token')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <!-- col-end -->
                  <div class="col-md-4 mb-4">
                        <label class="form-label  d-block">
                            Gateway Status
                        </label>

                        <div class="border rounded-3 p-3">

                            <label class="switch mb-0">

                                <input type="checkbox"
                                    value="1"
                                    name="status"
                                    @checked($pathao->status)>

                                <span class="slider round"></span>

                            </label>

                        </div>

                    </div>
                <!-- col end -->

                <!-- Button -->

                <div class="col-12 text-end">

                    <button type="reset" class="btn btn-danger px-4">
                        <i class="mdi mdi-refresh"></i>
                        Reset
                    </button>

                    <button type="submit" class="btn btn-primary px-4">

                        <i class="mdi mdi-content-save"></i>

                        Update Gateway

                    </button>

                </div>

            </form>

        </div>

    </div>


@endsection

@section('script')
<script>
function togglePassword(id, btn) {

    let input = document.getElementById(id);
    let icon = btn.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.className = "mdi mdi-eye-off-outline";
    } else {
        input.type = "password";
        icon.className = "mdi mdi-eye-outline";
    }
}
</script>

 {!! Toastr::message() !!}
@endsection
