<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><i class="mdi mdi-account-check"></i> &nbsp; New POP/Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.pop.store') }}" id="popForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name">Full Name</label>
                            <input name="name" placeholder="Enter Fullname" class="form-control" type="text" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username">Username</label>
                            <input name="username" placeholder="Enter Username" class="form-control" type="text" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password">Password</label>
                            <input name="password" placeholder="Enter Password" class="form-control" type="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone">Phone Number</label>
                            <input class="form-control" type="text" name="phone" placeholder="Type Phone Number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email">Email</label>
                            <input class="form-control" type="email" name="email" placeholder="Type Your Email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address">Address</label>
                            <input class="form-control" type="text" name="address" placeholder="Type Your Address" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status">Status</label>
                            <select class="form-select form-control" name="status" required>
                                <option value="">---Select---</option>
                                <option value="1">Active</option>
                                <option value="0">Disable</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade bs-example-modal-lg" id="PopRechargeModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span class="mdi mdi mdi-battery-charging-90 mdi-18px"></span> &nbsp;
                    POP/Branch Recharge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.pop.brnach.recharge.store') }}" id="popRechargeForm" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-2">
                        <label>Amount</label>
                        <input type="text" name="pop_id" value="<?php echo $pop->id ?? ''?>" class="d-none">
                        <input name="amount" placeholder="Enter Amount" class="form-control" type="number"
                            required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="">Transaction Type</label>
                        <select type="text" class="form-select" name="transaction_type" style="width: 100%;" required>
                            <option value="">---Select---</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                            <option value="bkash">Bkash</option>
                            <option value="nagad">Nagad</option>
                            <option value="bank">Bank</option>
                            <option value="due_paid">Due Paid</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Remarks</label>
                        <input name="note" placeholder="Enter Remarks" class="form-control" type="text"
                            >
                    </div>
                    <div class="modal-footer ">
                        <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
