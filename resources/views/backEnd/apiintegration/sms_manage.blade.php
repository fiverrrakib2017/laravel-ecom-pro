@extends('backEnd.layouts.master')
@section('title','SMS Gateway')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-10">

        <div class="card shadow-sm border-0">

            <!-- Header -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">

                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:55px;height:55px;">
                        <i class="mdi mdi-message-processing-outline fs-3"></i>
                    </div>

                    <div>
                        <h4 class="mb-0 ">SMS Gateway Settings</h4>
                        <small class="text-muted">
                            Configure your SMS API credentials and SMS services.
                        </small>
                    </div>

                </div>
            </div>

            <div class="card-body">

                <form action="{{ route('smsgeteway.update') }}" method="POST" class="row g-4"
                    enctype="multipart/form-data">

                    @csrf

                    <input type="hidden" name="id" value="{{ $sms->id }}">

                    <!-- URL -->

                    <div class="col-md-6">
                        <label class="form-label">
                            API URL
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="mdi mdi-web"></i>
                            </span>

                            <input type="text"
                                class="form-control @error('url') is-invalid @enderror"
                                name="url"
                                value="{{ $sms->url }}"
                                placeholder="https://example.com/api">

                            @error('url')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror

                        </div>
                    </div>

                    <!-- API KEY -->

                    <div class="col-md-6">
                        <label class="form-label ">
                            API Key
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="mdi mdi-key"></i>
                            </span>

                            <input type="text"
                                class="form-control @error('api_key') is-invalid @enderror"
                                name="api_key"
                                value="{{ $sms->api_key }}"
                                placeholder="Enter API Key">

                            @error('api_key')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror

                        </div>
                    </div>

                    <!-- Sender -->

                    <div class="col-md-6">
                        <label class="form-label ">
                            Sender ID
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="mdi mdi-account-circle-outline"></i>
                            </span>

                            <input type="text"
                                class="form-control @error('serderid') is-invalid @enderror"
                                name="serderid"
                                value="{{ $sms->serderid }}"
                                placeholder="Sender ID">

                            @error('serderid')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror

                        </div>
                    </div>

                    <!-- Empty Column -->

                    <div class="col-md-6"></div>

                    <div class="col-12">
                        <hr>
                        <h5 class=" mb-3">
                            SMS Services
                        </h5>
                    </div>

                    <!-- Status -->

                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body text-center">

                                <i class="mdi mdi-power text-success display-6"></i>

                                <h6 class="mt-2 mb-3">
                                    Gateway Status
                                </h6>

                                <label class="switch">
                                    <input type="checkbox"
                                        value="1"
                                        name="status"
                                        @checked($sms->status)>
                                    <span class="slider round"></span>
                                </label>

                            </div>

                        </div>

                    </div>

                    <!-- Order -->

                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body text-center">

                                <i class="mdi mdi-cart-check text-primary display-6"></i>

                                <h6 class="mt-2 mb-3">
                                    Order Confirm
                                </h6>

                                <label class="switch">
                                    <input type="checkbox"
                                        value="1"
                                        name="order"
                                        @checked($sms->order)>
                                    <span class="slider round"></span>
                                </label>

                            </div>

                        </div>

                    </div>

                    <!-- Forgot -->

                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body text-center">

                                <i class="mdi mdi-lock-reset text-warning display-6"></i>

                                <h6 class="mt-2 mb-3">
                                    Forgot Password
                                </h6>

                                <label class="switch">
                                    <input type="checkbox"
                                        value="1"
                                        name="forget_pass"
                                        @checked($sms->forget_pass)>
                                    <span class="slider round"></span>
                                </label>

                            </div>

                        </div>

                    </div>

                    <!-- Password -->

                    <div class="col-lg-3 col-md-6">

                        <div class="card border shadow-sm h-100">

                            <div class="card-body text-center">

                                <i class="mdi mdi-lock-plus text-danger display-6"></i>

                                <h6 class="mt-2 mb-3">
                                    Password Generator
                                </h6>

                                <label class="switch">
                                    <input type="checkbox"
                                        value="1"
                                        name="password_g"
                                        @checked($sms->password_g)>
                                    <span class="slider round"></span>
                                </label>

                            </div>

                        </div>

                    </div>

                    <!-- Button -->

                    <div class="col-12 text-end">

                        <button class="btn btn-success px-5">
                            <i class="mdi mdi-content-save me-1"></i>
                            Save Settings
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>
 {!! Toastr::message() !!}
@endsection

