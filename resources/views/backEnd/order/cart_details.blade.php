 @php
     $subtotal = Cart::session('pos_shopping')->getSubTotal();
     $subtotal = str_replace([',', '.00'], '', $subtotal);
     $shipping = Session::get('pos_shipping', 0);
     $total_discount = Session::get('pos_discount', 0) + Session::get('product_discount', 0);
 @endphp
 <tr>
     <td>Sub Total</td>
     <td class="text-end fw-bold">৳{{ $subtotal }}</td>
 </tr>
 <tr>
     <td>Shipping Fee</td>
     <td class="text-end fw-bold">৳{{ $shipping }}</td>
 </tr>
 <tr>
     <td>Discount</td>
     <td class="text-end fw-bold text-danger">৳{{ $total_discount }}</td>
 </tr>
 <tr class="border-top">
     <td class="fw-bold fs-5">Total</td>
     <td class="text-end fw-bold fs-5 text-success">৳{{ $subtotal + $shipping - $total_discount }}</td>
 </tr>
