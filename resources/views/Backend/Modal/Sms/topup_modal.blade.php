<!-- TOP UP SMS Modal -->
<div class="modal fade" id="smsTopUpModal" tabindex="-1" aria-labelledby="smsTopUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smsTopUpModalLabel">Buy SMS Package</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Package List -->
                <div class="row" id="smsPackages">
                    <!-- Example SMS Packages -->
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header text-center">
                                <strong>Basic</strong>
                            </div>
                            <div class="card-body text-center">
                                <h4>৳100</h4>
                                <p>1,000 SMS</p>
                                <button class="btn btn-sm btn-primary select-package" data-price="100"
                                    data-sms="1000">Select</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header text-center">
                                <strong>Standard</strong>
                            </div>
                            <div class="card-body text-center">
                                <h4>৳200</h4>
                                <p>2,200 SMS</p>
                                <button class="btn btn-sm btn-primary select-package" data-price="200"
                                    data-sms="2200">Select</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header text-center">
                                <strong>Premium</strong>
                            </div>
                            <div class="card-body text-center">
                                <h4>৳500</h4>
                                <p>6,000 SMS</p>
                                <button class="btn btn-sm btn-primary select-package" data-price="500"
                                    data-sms="6000">Select</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" name="sms_pay_button" class="btn btn-success">Pay With bKash</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
