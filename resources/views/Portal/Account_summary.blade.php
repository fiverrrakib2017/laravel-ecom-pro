<div class="card">
    <div
        class="card-header d-flex justify-content-between align-items-center bg-gradient-primary text-white rounded-top">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-circle fa-lg mr-2"></i>
            <h3 class="card-title mb-0 font-weight-bold">Account Summary</h3>
        </div>
        <button type="button" data-toggle="modal" data-target="#changePasswordModal"
            class="btn btn-light btn-sm shadow-sm">
            <i class="fas fa-key text-primary"></i> Change Password
        </button>
    </div>

    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-5">Name</dt>
            <dd class="col-7">{{ auth('customer')->user()->fullname ?? '' }}</dd>
            <dt class="col-5">Username</dt>
            <dd class="col-7">{{ auth('customer')->user()->username ?? '' }}</dd>
            <dt class="col-5">Phone</dt>
            <dd class="col-7">{{ auth('customer')->user()->phone ?? '' }}</dd>
            <dt class="col-5">Address</dt>
            <dd class="col-7">{{ auth('customer')->user()->address ?? '' }}</dd>
            <dt class="col-5">Area</dt>
            <dd class="col-7">{{ auth('customer')->user()->area_id ?? '' }}</dd>
            <dt class="col-5">POP</dt>
            <dd class="col-7">{{ auth('customer')->user()->pop_id ?? '' }}</dd>
            <dt class="col-5">Router</dt>
            <dd class="col-7">Mikrotik {{ auth('customer')->user()->mikrotik_id ?? '' }}</dd>
            <dt class="col-5">Last Seen</dt>
            <dd class="col-7">{{ auth('customer')->user()->last_seen ?? 'N/A' }}</dd>
        </dl>
    </div>
</div>
