@extends('Backend.Layout.App')
@section('title', 'Payment Method Settings | Admin Panel')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card card-primary card-outline">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">Payment Method Settings</h3>
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="paymentTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="bkash-tab" data-toggle="tab" href="#bkash"
                                    role="tab"><img src="{{ asset('Backend/images/bkash.png') }}"alt="bKash" style="height:22px; margin-right:8px; border-radius:3px; padding:2px; background:white;">Bkash</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nagad-tab" data-toggle="tab" href="#nagad" role="tab">Nagad</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="rocket-tab" data-toggle="tab" href="#rocket"
                                    role="tab">Roket</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="others-tab" data-toggle="tab" href="#others"
                                    role="tab">Others</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content mt-3">
                            <!-- Bkash Tab -->
                            <div class="tab-pane fade show active" id="bkash" role="tabpanel">
                                <form action="{{ route('admin.settings.payment.method.store') }}" method="POST" id="BkashForm">
                                @csrf
                                    <div class="form-group d-none">
                                        <input type="text" name="id" value="{{$data->id ?? ''}}">
                                        <input type="text" name="name" value="Bkash">
                                    </div>
                                    <div class="form-group">
                                        <label for="bkash_number">Bkash Number</label>
                                        <input type="text" class="form-control" name="bkash_number"
                                            placeholder="Enter Bkash Merchant Number" value="{{$data->account_number ?? ''}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="bkash_url">Url</label>
                                        <input type="text" class="form-control" name="bkash_url"
                                            placeholder="Enter URL" value="{{$data->url ?? ''}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="bkash_api_key">API Key</label>
                                        <input type="text" class="form-control" name="bkash_api_key"
                                            placeholder="Enter Bkash API Key" value="{{$data->api_key ?? ''}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="bkash_api_secret">API Secret</label>
                                        <input type="text" class="form-control" name="bkash_api_secret" placeholder="Enter Bkash API Secret" value="{{$data->api_secret ?? ''}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="bkash_username">Username</label>
                                        <input type="text" class="form-control"  name="bkash_username" placeholder="Enter Bkash Username" value="{{$data->username ?? ''}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="bkash_password">Password</label>
                                        <input type="text" class="form-control"
                                            name="bkash_password" placeholder="Enter Bkash Password" value="{{$data->password ?? ''}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="bkash_callback_url">Callback URL</label>
                                        <input type="url" class="form-control"
                                            name="bkash_callback_url" placeholder="https://yourdomain.com/bkash/callback" value="{{$data->callback_url ?? ''}}">
                                    </div>

                                    <div class="form-group">
                                        <label for="bkash_status">Status</label>
                                        <select class="form-control"  name="bkash_status">
                                            <option value="1" @if($data->status=='1') selected @endif>Active</option>
                                            <option value="0"@if($data->status=='0') selected @endif>Inactive</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </form>
                            </div>


                            <!-- Nagad Tab -->
                            <div class="tab-pane fade" id="nagad" role="tabpanel">
                                <form>
                                    <div class="form-group">
                                        <label for="nagad_number">নগদ নাম্বার</label>
                                        <input type="text" class="form-control" id="nagad_number" name="nagad_number"
                                            placeholder="Enter Nagad Number">
                                    </div>
                                    <div class="form-group">
                                        <label for="nagad_status">Status</label>
                                        <select class="form-control" id="nagad_status" name="nagad_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </form>
                            </div>

                            <!-- Rocket Tab -->
                            <div class="tab-pane fade" id="rocket" role="tabpanel">
                                <form>
                                    <div class="form-group">
                                        <label for="rocket_number">রকেট নাম্বার</label>
                                        <input type="text" class="form-control" id="rocket_number"
                                            name="rocket_number" placeholder="Enter Rocket Number">
                                    </div>
                                    <div class="form-group">
                                        <label for="rocket_status">Status</label>
                                        <select class="form-control" id="rocket_status" name="rocket_status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </form>
                            </div>

                            <!-- Others Tab -->
                            <div class="tab-pane fade" id="others" role="tabpanel">
                                <form>
                                    <div class="form-group">
                                        <label for="method_name">Method Name</label>
                                        <input type="text" class="form-control" id="method_name" name="method_name"
                                            placeholder="Enter Payment Method">
                                    </div>
                                    <div class="form-group">
                                        <label for="account_details">Account Details</label>
                                        <input type="text" class="form-control" id="account_details"
                                            name="account_details" placeholder="Enter Details">
                                    </div>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script  src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        handle_submit_form('#BkashForm');


    });


  </script>
@endsection
