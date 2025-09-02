<section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">Customer Portal</h1>
                <small class="text-muted">Account ID: CUST-{{ auth('customer')->user()->id ?? '' }} • Service: Home Broadband</small>
            </div>
            <div class="d-flex">
                <button id="themeToggle" class="btn btn-outline-secondary btn-sm mr-2">
                    <i class="fas fa-adjust"></i> Theme
                </button>
                <button onclick="window.print()" class="btn btn-outline-info btn-sm mr-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <form action="{{ route('customer.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </section>
