<div class="modal fade bs-example-modal-lg" id="addSendMessageModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content col-md-12">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel"><span class="mdi mdi-account-check mdi-18px"></span> &nbsp;Send
                    Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.sms.send_message_store') }}" id="SendMessageForm" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-2">
                        <label>Customer Name</label>
                        <select name="customer_id" class="form-control" type="text" required>
                           @include('Backend.Component.Common.Customer')
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>Template Name</label>
                        <select name="template_id" class="form-control" type="text" required style="width: 100%">
                            <option value="">---Select---</option>
                            @php
                                $data = \App\Models\Message_template::latest()->get();
                            @endphp
                            @foreach ($data as $item)
                                <option value="{{ $item->id }}"> {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group mb-2">
                        <label>SMS </label>
                        <textarea name="message" placeholder="Enter SMS" class="form-control" type="text" style="height: 158px;"></textarea>
                    </div>
                    <div class="modal-footer ">
                        <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                        <button type="submit" class="btn btn-success send_message_button">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('Backend/plugins/jquery/jquery.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("select[name='template_id']").on('change', function() {
            var template_id = $(this).val();
            if (template_id) {
                $.ajax({
                    url: "{{ route('admin.sms.template_get', ':id') }}".replace(':id',
                        template_id),
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        $("textarea[name='message']").val(response.data.message);
                    },
                    error: function(xhr, status, error) {
                        console.log("Error:", error);
                    }
                });
            } else {
                $("textarea[name='message']").val('');
            }
        });
        $("#SendMessageForm").submit(function(event){
                event.preventDefault();

                var button = $('.send_message_button');
                button.html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
                button.attr('disabled', true);
                if(!selectedCustomers){
                    var selectedCustomers=[];
                }
                /*Get Message Data Value*/
                var customer_id = $("#SendMessageForm select[name='customer_id']").val();
                var message = $("#SendMessageForm textarea[name='message']").val();

                selectedCustomers.push(customer_id);

                /*Check Select Customer Logic*/
                if(selectedCustomers.length==0){
                    toastr.error('Please Selete Customer');
                    button.html('Send Message');
                    button.attr('disabled', false);
                    return false;
                }
                $.ajax({
                    url: "{{ route('admin.sms.send_message_store') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {  _token: "{{ csrf_token() }}", message: message, customer_ids:selectedCustomers },
                    success: function(response) {
                        if(response.success==true){
                            toastr.success(response.message);
                            $('#addSendMessageModal').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }

                        if(response.success==false) {
                            toastr.error(response.message);
                        }
                    },
                    complete: function() {
                        button.html('Send Message');
                        button.attr('disabled', false);
                    }
                });
            });

    });
</script>
