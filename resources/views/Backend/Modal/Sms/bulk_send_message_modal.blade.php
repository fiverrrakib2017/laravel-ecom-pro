 <!-- Modal for Send Message -->
 <div class="modal fade bs-example-modal-lg" id="sendMessageModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog " role="document">
         <div class="modal-content col-md-12">
             <div class="modal-header">
                 <h5 class="modal-title" id="ModalLabel"><span class="mdi mdi-account-check mdi-18px"></span><i
                         class="fas fa-paper-plane"></i> &nbsp;Send Message</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <div class="alert alert-success" id="selectedCustomerCount"></div>
                 <form id="send_bulk_message_form" action="{{ route('admin.sms.send_message_store') }}" method="POST">
                     @csrf

                     <div class="form-group mb-2">
                         <label>Message Template </label>
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
                     <script type="text/javascript">
                         $(document).ready(function() {
                             /*Load Message Template*/
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
                         });
                     </script>
                     <div class="form-group mb-2">
                         <label>SMS </label>
                         <textarea name="message" id="textMessage" placeholder="Enter SMS" class="form-control" type="text" style="height: 158px;"></textarea>
                     </div>
                     <!-- Shortcodes Buttons -->
                     <div class="mb-2">
                         <label>Insert Shortcodes:</label><br>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{id}">{id}</button>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{username}">{username}</button>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{mobile}">{mobile}</button>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{area}">{area}</button>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{package}">{package}</button>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{expiry_date}">{expiry_date}</button>
                         <button type="button" class="btn btn-sm btn-outline-info shortcode-btn"
                             data-code="{due}">{due}</button>
                     </div>
                     <script type="text/javascript">
                         $(document).ready(function() {
                             $(".shortcode-btn").on("click", function() {
                                 let shortcode = $(this).data("code");
                                 let textarea = $("#textMessage");

                                 let cursorPos = textarea.prop("selectionStart");
                                 let v = textarea.val();
                                 let textBefore = v.substring(0, cursorPos);
                                 let textAfter = v.substring(cursorPos, v.length);

                                 textarea.val(textBefore + shortcode + textAfter);
                                 textarea.focus();
                                 textarea[0].selectionStart = cursorPos + shortcode.length;
                                 textarea[0].selectionEnd = cursorPos + shortcode.length;
                             });
                         });
                     </script>

                     <div class="modal-footer ">
                         <button data-dismiss="modal" type="button" class="btn btn-danger">Cancel</button>
                         <button type="submit" class="btn btn-success"><i
                         class="fas fa-paper-plane"></i>&nbsp; Send Message</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
