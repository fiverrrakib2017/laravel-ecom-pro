<!-- Modal for Change Customer Expire Date -->
<div class="modal fade " id="bulk_change_expire_dateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">  <i class="fas fa-calendar-alt"></i>
                    &nbsp;Change Expire Date </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="selectedCustomerCount"></div>
                <form action="{{ route('admin.customer.expire_date.update') }}" id="bulk_change_expire_dateForm" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="customer_expire_date" class="form-label">Expire Date</label><span class="text-danger">*</span>
                        <input type="date" name="customer_expire_date" id="customer_expire_date"
                            class="form-control" required>
                    </div>
                    <div class="modal-footer ">
                        <button data-dismiss="modal" type="button" class="btn btn-danger">Close</button>
                        <button type="submit" class="btn btn-success">Change Expire Date</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
