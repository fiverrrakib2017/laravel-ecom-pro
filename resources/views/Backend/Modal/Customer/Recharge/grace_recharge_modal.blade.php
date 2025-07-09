<div class="modal fade" id="graceRechargeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{route('admin.customer.grace.recharge.store')}}" id="graceRechargeForm" method="POST">
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
<script>
$(document).ready(function () {
    $('#graceRechargeForm').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        submitButton.prop('disabled', true).text('Processing...');

        var formData = new FormData(this);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#graceRechargeModal').modal('hide');
                    form[0].reset();
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    toastr.warning(response.message);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                submitButton.prop('disabled', false).text('Recharge');
            }
        });
    });
});
</script>
