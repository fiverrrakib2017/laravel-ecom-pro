@extends('Backend.Layout.App')
@section('title', 'Employee Profile | Admin Panel')

@section('content')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-success card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @if (empty($data->photo) || $data->photo == '')
                                    <img src="{{ asset('Backend/images/avatar.png') }}" alt="Profile Picture"
                                        class="profile-user-img img-fluid img-circle border border-primary">
                                @else
                                    <img src="{{ asset('uploads/photos/' . $data->photo) }}" alt="Profile Picture"
                                        class="profile-user-img img-fluid img-circle border border-primary">
                                @endif

                            </div>

                            <h3 class="profile-username text-center mt-2">{{ $data->name ?? 'N/A' }}</h3>
                            <p class="text-muted text-center">
                                <i class="fas fa-user-tag"></i> Employee ID: {{ $data->id ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="card  shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0"> Employee Information</h5>
                        </div>
                        <ul class="list-group list-group-flush">

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-alt text-primary mr-2"></i> <strong>Name:</strong>
                                </div>
                                <span class="">{{ $data->name ?? 'N/A' }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-phone-alt text-success mr-2"></i> <strong>Phone:</strong>
                                </div>
                                <span class="">{{ $data->phone ?? 'N/A' }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-map-marker-alt text-info mr-2"></i> <strong>Address:</strong>
                                </div>
                                <span class="">{{ $data->address ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#leave"
                                        data-toggle="tab">Leave</a></li>
                                <li class="nav-item"><a class="nav-link" href="#tickets"
                                        data-toggle="tab">Attendance</a></li>
                                <li class="nav-item"><a class="nav-link" href="#recharge" data-toggle="tab">Advance
                                        Salary</a></li>
                                <li class="nav-item"><a class="nav-link" href="#personal_info" data-toggle="tab">Personal
                                        Information</a></li>
                                <li class="nav-item"><a class="nav-link" href="#job_info" data-toggle="tab">Job
                                        Information</a></li>


                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Tickets -->
                                <div class="active tab-pane" id="tickets">
                                    <div class="table-responsive">

                                    </div>
                                </div>
                                <!-- Customer Recharge Section  -->
                                <div class="tab-pane" id="recharge">
                                    <div class="table-responsive">

                                    </div>
                                </div>
                                <!-- Personal Information Section  -->
                                <div class="tab-pane" id="personal_info">
                                      <div class="card shadow-sm mt-3">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-info-circle"></i> Personal Details
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Phone:</strong>
                                        <span>{{ $data->phone ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Email:</strong>
                                        <span>{{ $data->email ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Gender:</strong>
                                        <span>{{ ucfirst($data->gender) ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>Birth Date:</strong>
                                        <span>{{ $data->birth_date ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <strong>National ID:</strong>
                                        <span>{{ $data->national_id ?? 'N/A' }}</span>
                                    </li>
                                </ul>
                            </div>
                                </div>
                                <!-- Job Information Section  -->
                                <div class="tab-pane" id="job_info">
                                    <div class="card shadow-sm mt-3">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-briefcase"></i> Job Information
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between">
                                                <strong>Department:</strong>
                                                <span>{{ $data->department->name ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <strong>Designation:</strong>
                                                <span>{{ $data->designation->name ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <strong>Hire Date:</strong>
                                                <span>{{ $data->hire_date ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <strong>Salary:</strong>
                                                <span>{{ $data->salary ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <strong>Status:</strong>
                                                <span
                                                    class="badge badge-{{ $data->status == 'active' ? 'success' : ($data->status == 'inactive' ? 'secondary' : 'danger') }}">
                                                    {{ ucfirst($data->status) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>

                </div>

            </div>
        </div>
    </section>
@endsection


@section('script')


    <script type="text/javascript"></script>

@endsection
