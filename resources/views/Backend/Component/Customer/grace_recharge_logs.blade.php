<table id="datatable1" class="table table-bordered dt-responsive nowrap"
 style="border-collapse: collapse; border-spacing: 0; width: 100%;">
 <thead>
     <tr>
         <th>ID</th>
         <th>Recharged date</th>
         <th>Customer Username</th>
         <th>Days</th>
         <!-- <th>Paid until</th> -->
     </tr>
 </thead>
 <tbody></tbody>
 </table>
 <style>
     .dataTables_filter {
         display: flex;
         align-items: center;
         gap: 10px;
         flex-wrap: wrap;
     }
     .dataTables_filter label {
         display: flex;
         align-items: center;
         gap: 5px;
         font-weight: 600;
         color: #333;
     }
     .dataTables_filter input,
     .dataTables_filter select {
         height: 35px;
         border-radius: 5px;
         border: 1px solid #ddd;
         padding: 5px;
     }
     .select2-container--default .select2-selection--single {
         height: 35px !important;
         line-height: 35px !important;
         border-radius: 5px;
     }
 </style>
<script src="{{ asset('Backend/assets/js/render_customer_column.js') }}"></script>
 <script type="text/javascript">
  var baseUrl = "{{ url('/') }}";
 $(document).ready(function() {
     /* From Date */
     var from_date = `<label>
                         <span>From:</span>
                         <input class="from_date form-control" type="date" value="">
                     </label>`;

     /* To Date */
     var to_date = `<label>
                         <span>To:</span>
                         <input class="to_date form-control" type="date" value="">
                     </label>`;
     setTimeout(() => {
         let filterContainer = $('.dataTables_filter');
         let lengthContainer = $('.dataTables_length');

         lengthContainer.parent().removeClass('col-sm-12 col-md-6');
         filterContainer.parent().removeClass('col-sm-12 col-md-6');

         filterContainer.append(from_date);
         filterContainer.append(to_date);
        //  filterContainer.append(bill_collect);

         $('.status_filter').select2({ width: '150px' });
        //  $('.bill_collect').select2({ width: '150px' });
     }, 1000);

     var table = $("#datatable1").DataTable({
         "processing": true,
         "responsive": true,
         "serverSide": true,
         ajax: {
               url: "{{ route('admin.customer.grace_recharge.logs.get_all_data') }}",
             data: function (d) {
                 d.start = d.start || 0;
                 d.length = d.length || 10;
                 d.from_date = $('.from_date').val();
                 d.to_date = $('.to_date').val();

             },
         },
         language: {
             searchPlaceholder: 'Search...',
             sSearch: '',
             lengthMenu: '_MENU_ items/page',
         },
         "columns": [
             { "data": "id" },
             {
                 "data": "created_at",
                 "render": function(data, type, row) {
                     var date = new Date(data);
                     var options = { year: 'numeric', month: 'short', day: '2-digit' };
                     return date.toLocaleDateString('en-GB', options);
                 }
             },
             {
                 "data": "customer.fullname",
                  "render": render_customer_column
             },
            //  {
            //      "data": "paid_until",
            //      "render": function(data, type, row) {
            //          var date = new Date(data);
            //          var options = { year: 'numeric', month: 'short', day: '2-digit' };
            //          return date.toLocaleDateString('en-GB', options);
            //      }
            //  },
             {
                 "data": "days",
             },
            //  {
            //      "data": "customer.expire_date",
            //  },
         ],
         order: [[0, "desc"]],
     });
     /* Filter Change Event*/
     $(document).on('change','.from_date, .to_date',function(){
         $('#datatable1').DataTable().ajax.reload();
     });
 });
 </script>
