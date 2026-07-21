 @php
     $product_discount = 0;
 @endphp
 @foreach ($cartinfo as $key => $value)
     <tr>
         <td class="text-center">
             <img height="35" width="35" class="rounded" src="{{ asset($value->attributes->image) }}"
                 alt="product" />
         </td>
         <td>{{ $value->name }}</td>
         <td>
             <div class="d-flex align-items-center gap-1">
                 <button type="button" class="btn btn-sm btn-outline-secondary qty-btn cart_decrement"
                     value="{{ $value->quantity }}" data-id="{{ $value->id }}">-</button>
                 <input type="text" class="cart-qty-input" value="{{ $value->quantity }}" readonly />
                 <button type="button" class="btn btn-sm btn-outline-secondary qty-btn cart_increment"
                     value="{{ $value->quantity }}" data-id="{{ $value->id }}">+</button>
             </div>
         </td>
         <td>৳{{ $value->price }}</td>
         <td>
             <input type="number" class="form-control form-control-sm product_discount"
                 value="{{ $value->attributes->product_discount }}" placeholder="0.00" data-id="{{ $value->id }}"
                 style="width: 80px;" />
         </td>
         <td>৳{{ ($value->price - $value->attributes->product_discount) * $value->quantity }}</td>
         <td class="text-center">
             <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{ $value->id }}"><i
                     class="fa fa-times"></i></button>
         </td>
     </tr>
     @php
         $product_discount += $value->attributes->product_discount * $value->quantity;
         Session::put('product_discount', $product_discount);
     @endphp
 @endforeach
 <script>
     function cart_content() {
         $.ajax({
             type: "GET",
             url: "{{ route('admin.order.cart_content') }}",
             dataType: "html",
             success: function(cartinfo) {
                 $('#cartTable').html(cartinfo)
             }
         });
     }

     function cart_details() {
         $.ajax({
             type: "GET",
             url: "{{ route('admin.order.cart_details') }}",
             dataType: "html",
             success: function(cartinfo) {
                 $('#cart_details').html(cartinfo)
             }
         });
     }
     $(".cart_increment").click(function(e) {
         e.preventDefault();
         var id = $(this).data("id");
         var qty = $(this).val();
         if (id) {
             $.ajax({
                 cache: false,
                 data: {
                     'id': id,
                     'qty': qty
                 },
                 type: "GET",
                 url: "{{ route('admin.order.cart_increment') }}",
                 dataType: "json",
                 success: function(cartinfo) {
                     return cart_content() + cart_details();
                 }
             });
         }
     });
     $(".cart_decrement").click(function(e) {
         e.preventDefault();
         var id = $(this).data("id");
         var qty = $(this).val();
         if (id) {
             $.ajax({
                 cache: false,
                 type: "GET",
                 data: {
                     'id': id,
                     'qty': qty
                 },
                 url: "{{ route('admin.order.cart_decrement') }}",
                 dataType: "json",
                 success: function(cartinfo) {
                     return cart_content() + cart_details();
                 }
             });
         }
     });
     $(".cart_remove").click(function(e) {
         e.preventDefault();
         var id = $(this).data("id");
         if (id) {
             $.ajax({
                 cache: false,
                 type: "GET",
                 data: {
                     'id': id
                 },
                 url: "{{ route('admin.order.cart_remove') }}",
                 dataType: "json",
                 success: function(cartinfo) {
                     return cart_content() + cart_details();
                 }
             });
         }
     });
     $(".product_discount").change(function() {
         var id = $(this).data("id");
         var discount = $(this).val();
         $.ajax({
             cache: false,
             type: "GET",
             data: {
                 'id': id,
                 'discount': discount
             },
             url: "{{ route('admin.order.product_discount') }}",
             dataType: "json",
             success: function(cartinfo) {
                 return cart_content() + cart_details();
             }
         });
     });
     $(".cartclear").click(function(e) {
         $.ajax({
             cache: false,
             type: "GET",
             url: "{{ route('admin.order.cart_clear') }}",
             dataType: "json",
             success: function(cartinfo) {
                 return cart_content() + cart_details();
             }
         });
     });
     // pshippingfee from total
     $("#area").on("change", function() {
         var id = $(this).val();
         $.ajax({
             type: "GET",
             data: {
                 id: id
             },
             url: "{{ route('admin.order.cart_shipping') }}",
             dataType: "html",
             success: function(cartinfo) {
                 return cart_content() + cart_details();
             }
         });
     });
 </script>
