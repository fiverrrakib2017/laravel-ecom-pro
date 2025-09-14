 <table id="datatable1" class="table table-bordered dt-responsive nowrap"
     style="border-collapse: collapse; border-spacing: 0; width: 100%;">
     <thead>
         <tr>

             <th class="no-export noVis text-center">
                 <input type="checkbox" id="selectAll" class=" customer-checkbox">
             </th>
             <th class="">ID.</th>
             <th class="">Username</th>
             <th class="">Package </th>
             <th class="">Price </th>
             <th class="">Expire Date </th>
             <th class="">POP/Branch</th>
             <th class="">Area</th>
             <th class="">Phone Number</th>
             <th class="">Address</th>
         </tr>
     </thead>
     <tbody id="_data">
         <tr id="no-data">
             <td colspan="10" class="text-center">No data available</td>
         </tr>
     </tbody>
 </table>

 <script type="text/javascript">
     $(document).ready(function() {
         /***Load Customer **/
         $("button[name='search_btn']").click(function() {
             var button = $(this);
             button.html(
                 `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Loading...`
                 );
             button.attr('disabled', true);

             var pop_id = $("#pop_id").val();
             var area_id = $("#area_id").val();
             var customer_status = $("#customer_status").val();

             if ($.fn.DataTable.isDataTable("#datatable1")) {
                 $("#datatable1").DataTable().destroy();
             }

             $.ajax({
                 url: "{{ route('admin.customer.get_customer_info') }}",
                 type: 'POST',
                 dataType: 'json',
                 data: {
                     _token: "{{ csrf_token() }}",
                     pop_id: pop_id,
                     area_id: area_id,
                     status: customer_status
                 },
                 success: function(response) {
                     if (response.success === true) {
                         $("#print_area").removeClass('d-none');
                         $("#_data").html(response.html);

                         // DataTable init with Buttons
                         var dt = $('#datatable1').DataTable({
                             responsive: true,
                             stateSave: true,
                              lengthChange: true,
                             lengthMenu: [
                                 [10, 25, 50, -1],
                                 [10, 25, 50, "All"]
                             ],
                             pageLength: 10,
                             searching: true,
                             ordering: true,
                             info: true,
                             columnDefs: [{
                                 targets: 0,
                                 orderable: false,
                                 searchable: false,
                                 className: 'no-export noVis text-center'
                             }],
                             // DataTables Buttons
                            dom:
                                "<'row align-items-center'\
                                <'col-sm-12 col-md-4'l>\
                                <'col-sm-12 col-md-4 text-md-center'B>\
                                <'col-sm-12 col-md-4'f>>" +
                                "t" +
                                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                             buttons: [{
                                     extend: 'colvis',
                                     text: 'Choose Columns',
                                     columns: ':not(.noVis)'
                                 },
                                 {
                                     extend: 'print',
                                     text: 'Print',
                                     title: 'Customer List',
                                     exportOptions: {
                                         columns: ':visible:not(.no-export)',
                                         modifier: {
                                            page: 'all'
                                         }
                                     },
                                     customize: function(win) {
                                         $(win.document.body).css(
                                            'font-size', '12px');
                                         $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', 'inherit');
                                     }
                                 },
                                 {
                                     extend: 'excelHtml5',
                                     text: 'Excel',
                                     title: null,
                                     filename: function() {
                                         var d = new Date(),
                                             pad = n => ('0' + n).slice(-2);
                                         return 'customers_' + d
                                             .getFullYear() + pad(d
                                             .getMonth() + 1) + pad(d
                                                 .getDate()) +
                                             '_' + pad(d.getHours()) + pad(d
                                                 .getMinutes());
                                     },
                                     exportOptions: {
                                         columns: ':visible:not(.no-export)',
                                         modifier: {
                                             page: 'all'
                                         }
                                     }
                                 }
                             ]
                         });
                         $('#selectAll').on('click', function() {
                                $('.customer-checkbox').prop('checked', this.checked);
                            });

                            $('.customer-checkbox').on('click', function() {
                                if ($('.customer-checkbox:checked').length == $('.customer-checkbox').length) {
                                    $('#selectAll').prop('checked', true);
                                } else {
                                    $('#selectAll').prop('checked', false);
                                }
                            });

                         /*---- Checkbox Select All toggle---*/
                         $(document).off('change.selectAll').on('change.selectAll',
                             '#selectAll',
                             function() {
                                 $('.row-checkbox').prop('checked', this.checked);
                             });

                         /*----Row checkbox -> update header----*/ 
                         $(document).off('change.rowCk').on('change.rowCk', '.row-checkbox',
                             function() {
                                 var total = $('.row-checkbox').length;
                                 var checked = $('.row-checkbox:checked').length;
                                 $('#selectAll').prop('checked', total > 0 && total ===
                                     checked);
                             });

                     } else {
                         toastr.error(response.message);
                         $("#_data").html(
                             '<tr id="no-data"><td colspan="10" class="text-center">No data available</td></tr>'
                             );
                     }
                 },
                 complete: function() {
                     button.html('<i class="fas fa-search me-1"></i> Search Now');
                     button.attr('disabled', false);
                 }
             });
         });


         function handle_ajax_submit(formId, __success_call_back = null) {
             $(formId).submit(function(e) {
                 e.preventDefault();

                 let form = $(this);
                 let submitBtn = form.find('button[type="submit"]');
                 let originalBtnText = submitBtn.html();

                 submitBtn.html(
                     '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                 ).prop('disabled', true);

                 let formData = new FormData(this);

                 let customer_ids = [];
                 $(".checkSingle:checked").each(function() {
                     customer_ids.push($(this).val());
                 });
                 customer_ids.forEach(function(id) {
                     formData.append('customer_ids[]', id);
                 });

                 $.ajax({
                     type: form.attr('method'),
                     url: form.attr('action'),
                     data: formData,
                     processData: false,
                     contentType: false,
                     beforeSend: function() {
                         form.find(':input').prop('disabled', true);
                     },
                     success: function(response) {
                         if (response.success === true) {
                             toastr.success(response.message);
                             form[0].reset();

                             if (typeof __success_call_back === "function") {
                                 __success_call_back();
                             } else {
                                 setTimeout(() => location.reload(), 500);
                             }

                         } else {
                             toastr.error(response.message);
                         }
                     },
                     error: function(xhr) {
                         if (xhr.status === 422) {
                             let errors = xhr.responseJSON.errors;
                             $.each(errors, function(field, messages) {
                                 $.each(messages, function(index, message) {
                                     toastr.error(message);
                                 });
                             });
                         } else {
                             toastr.error('An error occurred. Please try again.');
                         }
                     },
                     complete: function() {
                         submitBtn.html(originalBtnText).prop('disabled', false);
                         form.find(':input').prop('disabled', false);
                     }
                 });
             });
         }
         /***Trigger button****/
         function handle_trigger(button_selector, modalId, textSelector) {
             $(document).on('click', button_selector, function(event) {
                 event.preventDefault();

                 var __selected_customers = [];
                 $(".checkSingle:checked").each(function() {
                     __selected_customers.push($(this).val());
                 });

                 if (__selected_customers.length === 0) {
                     toastr.error('Please select at least one customer.');
                     return;
                 }

                 var countText = "You have selected " + __selected_customers.length + " customers.";
                 $(textSelector).text(countText);
                 $(modalId).modal('show');
             });
         }
     });
 </script>
