<div id="bulk_re_connectModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form action="{{route('admin.customer.bulk.re.connect')}}" method="POST" enctype="multipart/form-data" id="bulk_re_connectForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="fas fa-trash"></i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to Re-connect these records?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-undo-alt"></i> Ree-Connect</button>
                </div>
            </div>
        </form>
    </div>
</div>
