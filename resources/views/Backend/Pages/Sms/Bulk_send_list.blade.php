@extends('Backend.Layout.App')
@section('title', 'Dashboard | SMS Template | Admin Panel')
@section('style')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                     @include('Backend.Component.Customer.search-form')
                </div>
                <div class="card-body d-none" id="print_area">

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="send_message_btn" class="btn btn-primary mb-2"><i
                                    class="far fa-envelope"></i>
                                Process </button>
                        </div>
                    </div>

                     <div class="table-responsive responsive-table">
                        @include('Backend.Component.Customer.table')
                    </div>
                </div>
            </div>

        </div>
    </div>





    <!------ Bulk send message ---------->
    @include('Backend.Modal.Sms.bulk_send_message_modal')
@endsection

@section('script')
    <script type="text/javascript">

         $(document).on('click', '#send_message_btn', function(event) {
            event.preventDefault();
            var selectedCustomers = [];
            $(".checkSingle:checked").each(function() {
                selectedCustomers.push($(this).val());
            });
            var countText = "You have selected " + selectedCustomers.length + " customers.";
            $("#selectedCustomerCount").text(countText);
            $('#sendMessageModal').modal('show');
        });
        /*Send Message Template*/
        $("#send_bulk_message_form").submit(function(event){
            event.preventDefault();
            var button = $('.send_message_button');
            button.html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`);
            button.attr('disabled', true);
            var selectedCustomers = [];
            /*Get Message Data Value*/
            var message = $("#send_bulk_message_form textarea[name='message']").val();

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
                        $('#sendMessageModal').modal('hide');
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
    </script>

@endsection
