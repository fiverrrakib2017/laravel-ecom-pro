<div class="modal fade" id="graceRechargeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{route('admin.customer.grace.recharge.store')}}" method="POST">
      @csrf
      <input type="hidden" name="customer_id" id="grace_customer_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Grace Recharge for <span id="grace_customer_name"></span></h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label>Days</label>
            <select type="text" name="days" class="form-control">
                <option >---Select Days---</option>
                @for($i = 1; $i <= 15; $i++)
                    <option value="{{ $i }}">{{ $i }} day{{ $i > 1 ? 's' : '' }}</option>
                @endfor
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Recharge</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>
