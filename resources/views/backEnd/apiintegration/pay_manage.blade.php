@extends('backEnd.layouts.master')
@section('title','Payment Gateway')
@section('css')


@endsection
@section('content')
    <!-- ===================== Bkash Configuration ===================== -->

    <div class="card shadow-sm border-0 rounded-3 mb-4">

        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">

                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3"
                    style="width:55px;height:55px;">
                    <i class="mdi mdi-cash-multiple fs-3"></i>
                </div>

                <div>
                    <h4 class="mb-1">Bkash Configuration</h4>
                    <small class="text-muted">
                        Configure your bKash payment gateway credentials.
                    </small>
                </div>

            </div>
        </div>

        <div class="card-body">

            <form action="{{ route('paymentgeteway.update') }}"
                method="POST"
                class="row g-3"
                enctype="multipart/form-data"
                data-parsley-validate>

                @csrf

                <input type="hidden" name="id" value="{{ $bkash->id }}">

                <!-- Username -->

                <div class="col-md-4">

                    <label class="form-label ">
                        Username
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="mdi mdi-account"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            name="username"
                            value="{{ $bkash->username }}"
                            placeholder="Enter Username"
                            required>

                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <!-- App Key -->

                <div class="col-md-4">

                    <label class="form-label">
                        App Key
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="mdi mdi-key"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control @error('app_key') is-invalid @enderror"
                            name="app_key"
                            value="{{ $bkash->app_key }}"
                            placeholder="Enter App Key"
                            required>

                        @error('app_key')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <!-- App Secret -->

                <div class="col-md-4">

                    <label class="form-label ">
                        App Secret
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="mdi mdi-lock"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control @error('app_secret') is-invalid @enderror"
                            name="app_secret"
                            value="{{ $bkash->app_secret }}"
                            placeholder="Enter App Secret"
                            required>

                        @error('app_secret')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <!-- Base URL -->

                <div class="col-md-6">

                    <label class="form-label">
                        Base URL
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control @error('base_url') is-invalid @enderror"
                        name="base_url"
                        value="{{ $bkash->base_url }}"
                        placeholder="https://..."
                        required>

                    @error('base_url')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- Password -->

                <div class="col-md-6">

                    <label class="form-label">
                        Password
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input
                            type="password"
                            id="bkash_password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            value="{{ $bkash->password }}"
                            required>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('bkash_password',this)">

                            <i class="mdi mdi-eye-outline"></i>

                        </button>

                    </div>

                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- Status -->

                <div class="col-md-6">

                    <label class="form-label d-block ">
                        Gateway Status
                    </label>

                    <label class="switch">

                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked($bkash->status==1)>

                        <span class="slider round"></span>

                    </label>

                    <span class="ms-2 badge bg-success">
                        Active
                    </span>

                </div>

                <!-- Buttons -->

                <div class="col-md-6 text-end">

                    <button
                        type="reset"
                        class="btn btn-danger px-4">

                        <i class="mdi mdi-refresh"></i>

                        Reset

                    </button>

                    <button
                        type="submit"
                        class="btn btn-success px-4">

                        <i class="mdi mdi-content-save"></i>

                        Update Gateway

                    </button>

                </div>

            </form>

        </div>

    </div>


  <!-- ===================== ShurjoPay Configuration ===================== -->

    <div class="card shadow-sm border-0 rounded-3 mt-4">

        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">

                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3"
                    style="width:55px;height:55px;">
                    <i class="mdi mdi-credit-card-settings fs-3"></i>
                </div>

                <div>
                    <h4 class="mb-1 ">ShurjoPay Configuration</h4>
                    <small class="text-muted">
                        Configure your ShurjoPay payment gateway credentials.
                    </small>
                </div>

            </div>
        </div>

        <div class="card-body">

            <form action="{{ route('paymentgeteway.update') }}"
                method="POST"
                class="row g-3"
                enctype="multipart/form-data"
                data-parsley-validate>

                @csrf

                <input type="hidden" name="id" value="{{ $shurjopay->id }}">

                <!-- Username -->
                <div class="col-md-4">
                    <label class="form-label ">
                        Username
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="mdi mdi-account"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            name="username"
                            value="{{ $shurjopay->username }}"
                            placeholder="Enter Username"
                            required>

                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>
                </div>

                <!-- Prefix -->

                <div class="col-md-4">

                    <label class="form-label ">
                        Prefix
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="mdi mdi-format-text"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control @error('prefix') is-invalid @enderror"
                            name="prefix"
                            value="{{ $shurjopay->prefix }}"
                            placeholder="Enter Prefix"
                            required>

                        @error('prefix')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <!-- Success URL -->

                <div class="col-md-4">

                    <label class="form-label ">
                        Success URL
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control @error('success_url') is-invalid @enderror"
                        name="success_url"
                        value="{{ $shurjopay->success_url }}"
                        placeholder="https://example.com/success"
                        required>

                    @error('success_url')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- Return URL -->

                <div class="col-md-4">

                    <label class="form-label ">
                        Return URL
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control @error('return_url') is-invalid @enderror"
                        name="return_url"
                        value="{{ $shurjopay->return_url }}"
                        placeholder="https://example.com/return"
                        required>

                    @error('return_url')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- Base URL -->

                <div class="col-md-4">

                    <label class="form-label ">
                        Base URL
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control @error('base_url') is-invalid @enderror"
                        name="base_url"
                        value="{{ $shurjopay->base_url }}"
                        placeholder="https://..."
                        required>

                    @error('base_url')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- Password -->

                <div class="col-md-4">

                    <label class="form-label ">
                        Password
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input
                            type="password"
                            id="sp_password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            value="{{ $shurjopay->password }}"
                            required>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="togglePassword('sp_password',this)">

                            <i class="mdi mdi-eye-outline"></i>

                        </button>

                    </div>

                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <!-- Status -->

                <div class="col-md-6">

                    <label class="form-label d-block ">
                        Gateway Status
                    </label>

                    <label class="switch">

                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked($shurjopay->status==1)>

                        <span class="slider round"></span>

                    </label>

                    <span class="ms-2 badge bg-success">
                        Active
                    </span>

                </div>

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
  });
</script>
<script type="text/javascript">
  $(document).ready(function () {
    $(".increment_btn").click(function () {
      var html = $(".clone_price").html();
      $(".increment_price").after(html);
    });
    $("body").on("click", ".remove_btn", function () {
      $(this).parents(".increment_control").remove();
    });

    $(".select2").select2();
  });
</script>
 {!! Toastr::message() !!}
@endsection
