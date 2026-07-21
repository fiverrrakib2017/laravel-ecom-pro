<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productprice;
use App\Models\Product;
use Toastr;
use Cart;
use DB;
class ShoppingController extends Controller
{

    public function addTocartGet($id,Request $request){
        $qty=1;
        $productInfo = DB::table('products')->where('id',$id)->first();
        $productImage = DB::table('productimages')->where('product_id',$id)->first();
        $cartinfo=Cart::session('shopping')->add(['id'=>$productInfo->id,'name'=>$productInfo->name,'qty'=>$qty,'price'=>$productInfo->new_price,
            'options' => [
                'image'=>$productImage->image,
                'old_price'=>$productInfo->old_price,
                 'slug' => $productInfo->slug,
                 'purchase_price' => $productInfo->purchase_price,
                 ]]);

        // return redirect()->back();
        return response()->json($cartinfo);
    }

    public function cart_store(Request $request)
    {

        $product = Product::where(['id' => $request->id])->first();

        Cart::session('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => $request->qty,
            'price' => $product->new_price,
            'attributes' => [
                'slug' => $product->slug,
                'image' => $product->image->image,
                'old_price' => $product->new_price,
                'purchase_price' => $product->purchase_price,
                'product_size'=>$request->product_size,
                'product_color'=>$request->product_color,
                'pro_unit'=>$request->pro_unit,
            ],
        ]);

        Toastr::success('Product successfully add to cart', 'Success!');
        return redirect()->route('customer.checkout');

    }
    public function cart_remove(Request $request)
    {
        $remove = Cart::session('shopping')->update($request->id, 0);
        $data = Cart::session('shopping')->getContent();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_increment(Request $request)
    {
        $item = Cart::session('shopping')->get($request->id);
        $qty = $item->qty + 1;
        $increment = Cart::session('shopping')->update($request->id, $qty);
        $data = Cart::session('shopping')->getContent();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_decrement(Request $request)
    {
        $item = Cart::session('shopping')->get($request->id);
        $qty = $item->qty - 1;
        $decrement = Cart::session('shopping')->update($request->id, $qty);
        $data = Cart::session('shopping')->getContent();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }
    public function cart_count(Request $request)
    {
        $data = Cart::session('shopping')->count();
        return view('frontEnd.layouts.ajax.cart_count', compact('data'));
    }
    public function mobilecart_qty(Request $request)
    {
        $data = Cart::session('shopping')->count();
        return view('frontEnd.layouts.ajax.mobilecart_qty', compact('data'));
    }

}
