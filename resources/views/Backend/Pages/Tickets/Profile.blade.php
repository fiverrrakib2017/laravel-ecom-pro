@extends('Backend.Layout.App')
@section('title', 'Dashboard | Ticket Profile | Admin Panel')
@section('style')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Ticket Profile - #TKT-1023</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5><strong>Customer Information</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>Md. Rahim</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>017xxxxxxxx</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>rahim@example.com</td>
                                </tr>
                                <tr>
                                    <th>Area</th>
                                    <td>Mirpur, Dhaka</td>
                                </tr>
                            </table>

                            <h5 class="mt-4"><strong>Ticket Details</strong></h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Complain Type</th>
                                    <td>Slow Internet</td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>Technician - Rakib Hossain</td>
                                </tr>
                                <tr>
                                    <th>POP Branch</th>
                                    <td>Mirpur POP</td>
                                </tr>
                                <tr>
                                    <th>Priority</th>
                                    <td><span class="badge badge-danger">High</span></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td><span class="badge badge-warning">Open</span></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5><strong>Problem Description</strong></h5>
                            <div class="callout callout-danger">
                                কাস্টমার বলতেছে ইন্টারনেট স্পিড অনেক স্লো। রাত ৮টার পর কোনো কিছু লোড হয় না। ইউটিউব 144p তেও
                                বাফার করে।
                            </div>

                            <h5 class="mt-4"><strong>Activity Timeline</strong></h5>

                            <div class="timeline">
                                <!-- timeline time label -->
                                <div class="time-label">
                                    <span class="bg-red">2025-06-01</span>
                                </div>
                                <!-- /.timeline-label -->

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-ticket-alt bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 10:00 AM</span>
                                        <h3 class="timeline-header">Ticket Created</h3>
                                        <div class="timeline-body">
                                            Customer submitted a ticket for slow internet.
                                        </div>
                                    </div>
                                </div>

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-user-check bg-warning"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 12:30 PM</span>
                                        <h3 class="timeline-header">Assigned to Rakib Hossain</h3>
                                        <div class="timeline-body">
                                            Ticket assigned to technician Rakib for investigation.
                                        </div>
                                    </div>
                                </div>

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-wrench bg-success"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> 3:45 PM</span>
                                        <h3 class="timeline-header">Technician Visited</h3>
                                        <div class="timeline-body">
                                            Technician visited customer's home and resolved the issue.
                                        </div>
                                    </div>
                                </div>

                                <!-- END timeline item -->
                                <div>
                                    <i class="fas fa-check-circle bg-gray"></i>
                                </div>
                            </div>


                            <div class="mt-4">
                                <a href="#" class="btn btn-success"><i class="fas fa-check-circle"></i> Close
                                    Ticket</a>
                                <a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="#" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
