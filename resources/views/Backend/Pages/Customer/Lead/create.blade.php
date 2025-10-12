@extends('Backend.Layout.App')
@section('title', 'Lead Create | Dashboard | Admin Panel')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @include('Backend.Component.Common.card-header', [
                    'title' => 'Create New Lead',
                    'description' => 'Add and manage potential customer information.',
                    'icon' => '<i class="fas fa-user-plus"></i>',
                ])
                <div class="card-body">
                    <form action="{{ route('admin.customer.lead.store') }}" id="leadForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="full_name">Full Name</label>
                                <input type="text" name="full_name" placeholder="Enter Full Name" class="form-control" >
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" placeholder="Enter Phone" class="form-control" >
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" placeholder="Enter Email" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" placeholder="Enter Address" class="form-control"></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="source">Source</label>
                                <select name="source" class="form-control">
                                    <option>---Select---</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="referral">Referral</option>
                                    <option value="walk_in">Walk In</option>
                                    <option value="website">Website</option>
                                    <option value="phone_call">Phone Call</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status">Status</label>
                                <select name="status" class="form-control">
                                    <option>---Select---</option>
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="unqualified">Unqualified</option>
                                    <option value="converted">Converted</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="priority">Priority</label>
                                <select name="priority" class="form-control">
                                    <option>---Select---</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="interest_level">Interest Level</label>
                                <select id="interest_level" name="interest_level" class="form-control">
                                    <option>---Select---</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="service_interest">Service Interest</label>
                                <input type="text" id="service_interest" name="service_interest" placeholder="Enter Service Interest" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="feedback">Feedback</label>
                                <textarea id="feedback" name="feedback" placeholder="Enter Feedback" class="form-control"></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lead_score">Lead Score</label>
                                <input type="number" name="lead_score" placeholder="Enter Lead Score" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estimated_close_date">Estimated Close Date</label>
                                <input type="date" name="estimated_close_date" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="follow_up_required">Follow Up Required</label>
                                <select name="follow_up_required" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="first_contacted_at">First Contacted At</label>
                                <input type="datetime-local" name="first_contacted_at" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_contacted_at">Last Contacted At</label>
                                <input type="datetime-local" name="last_contacted_at" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="campaign_source">Campaign Source</label>
                                <input type="text" name="campaign_source" placeholder="Enter Campaign Source" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="follow_up_count">Follow Up Count</label>
                                <input type="number" name="follow_up_count" placeholder="Enter Follow Up Count" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="internal_notes">Internal Notes</label>
                                <textarea name="internal_notes" placeholder="Enter Internal Notes" class="form-control"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Save Lead</button>
                        <button type="button" class="btn btn-danger" onclick="history.back();">Back</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('Backend/assets/js/__handle_submit.js') }}"></script>
    <script type="text/javascript">
        handle_submit_form("#leadForm");
    </script>
@endsection
