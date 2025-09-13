<div class="card brand-gradient mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Welcome, {{ auth('customer')->user()->fullname ?? '' }}!</h3>
                        <div>Username: <strong>{{ auth('customer')->user()->username ?? '' }}</strong> • Plan: <strong>{{ \App\Models\Branch_package::find(auth('customer')->user()->package_id)->name ?? 'N/A' }}</strong></div>
                    </div>
                    <span class="badge badge-pill badge-light px-3 py-2">
                        Customer Since: {{ auth('customer')->user()->created_at?->format('d M Y') }}
                    </span>

                </div>
            </div>
