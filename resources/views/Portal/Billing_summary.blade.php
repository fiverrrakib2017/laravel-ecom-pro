<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <h3 class="card-title mb-0 mr-2">Billing Summary</h3>
    </div>

    {{-- <div class="card-tools" style="width: 320px;">
      <div class="input-group input-group-sm">
        <select id="invoiceFilter" class="custom-select">
          <option value="all" selected>All invoices</option>
          <option value="paid">Paid</option>
          <option value="due">Due</option>
        </select>
        <div class="input-group-append">
          <a class="btn btn-secondary" href="#" data-toggle="tooltip" title="Download last invoice (PDF)">
            <i class="far fa-file-alt mr-1"></i> Last PDF
          </a>
        </div>
      </div>
    </div> --}}
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-striped table-sm text-nowrap mb-0">
        <thead class="thead-light">
          <tr>
            <th style="width:48px;">#</th>
            <th>Invoice</th>
            <th>Date</th>
            <th>Status</th>
            <th class="text-right">Amount (৳)</th>
            <th class="text-center" style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody id="invoiceTable">
          <tr data-status="paid">
            <td>1</td>
            <td>INV-2025-0901</td>
            <td>01 Sep 2025</td>
            <td><span class="badge badge-success">Paid</span></td>
            <td class="text-right">1,200.00</td>
            <td class="text-center">
              <a href="#" class="btn btn-xs btn-outline-primary mr-1" data-toggle="tooltip" title="View">
                <i class="far fa-eye"></i>
              </a>
              <a href="#" class="btn btn-xs btn-outline-secondary" data-toggle="tooltip" title="Download">
                <i class="fas fa-download"></i>
              </a>
            </td>
          </tr>
          <tr data-status="paid">
            <td>2</td>
            <td>INV-2025-0815</td>
            <td>15 Aug 2025</td>
            <td><span class="badge badge-success">Paid</span></td>
            <td class="text-right">1,200.00</td>
            <td class="text-center">
              <a href="#" class="btn btn-xs btn-outline-primary mr-1" data-toggle="tooltip" title="View">
                <i class="far fa-eye"></i>
              </a>
              <a href="#" class="btn btn-xs btn-outline-secondary" data-toggle="tooltip" title="Download">
                <i class="fas fa-download"></i>
              </a>
            </td>
          </tr>
          <tr data-status="due">
            <td>3</td>
            <td>INV-2025-0801</td>
            <td>01 Aug 2025</td>
            <td><span class="badge badge-danger">Due</span></td>
            <td class="text-right">1,200.00</td>
            <td class="text-center">
              <a href="#" class="btn btn-xs btn-outline-primary mr-1" data-toggle="tooltip" title="View">
                <i class="far fa-eye"></i>
              </a>
              <a href="#" class="btn btn-xs btn-outline-secondary" data-toggle="tooltip" title="Download">
                <i class="fas fa-download"></i>
              </a>
            </td>
          </tr>
        </tbody>
        <tfoot class="bg-light">
          <tr>
            <th colspan="4" class="text-right">Total (last 3)</th>
            <th class="text-right">3,600.00</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="card-footer d-flex justify-content-between align-items-center">
    <nav aria-label="Invoice pagination">
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">«</a></li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">»</a></li>
      </ul>
    </nav>
    <a href="#" class="btn btn-success">
      <i class="fas fa-money-check-alt mr-1"></i> Pay Now (bKash/Nagad)
    </a>
  </div>
</div>
