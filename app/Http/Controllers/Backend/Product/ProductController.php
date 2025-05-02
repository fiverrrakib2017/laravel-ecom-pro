<?php

namespace App\Http\Controllers\Backend\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client_invoice_details;
use App\Models\Customer_Invoice_Details;
use App\Models\Product;
use App\Models\Supplier_Invoice_Details;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return view('Backend.Pages.Product.index');
    }
    public function get_all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy = ['id', 'name', 'purchase_price', 'sale_price', 'unit', 'store', 'qty'];
        $orderByColumnIndex = $request->order[0]['column'];
        $orderDirection = $request->order[0]['dir'];
        $orderByColumn = $columnsForOrderBy[$orderByColumnIndex];

        /* Start building the query */
        $query = Product::with('brand', 'category', 'unit', 'store', 'purchases', 'sales');

        /* Apply the search filter */
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('unit', function($q) use ($search) {
                    $q->where('unit_name', 'like', "%$search%");
                })
                ->orWhereHas('store', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('purchase_price', 'like', "%$search%")
                ->orWhere('sale_price', 'like', "%$search%");
            });
        }

        /* Get the total count of records */
        $totalRecords = Product::count();

        /* Get the count of filtered records */
        $filteredRecords = $query->count();

        /* Apply ordering, pagination and get the data */
        $items = $query->orderBy($orderByColumn, $orderDirection)
                    ->skip($request->start)
                    ->take($request->length)
                    ->get()
                    ->map(function ($product) {
                        $totalPurchased = $product->purchases->sum('qty');
                        $totalSold = $product->sales->sum('qty');
                        $availableQty = ($totalPurchased - $totalSold) + $product->qty;

                        /* Add availableQty to the product data*/
                        $product->available_qty = $availableQty;

                        return $product;
                    });

        /* Return the response in JSON format */
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $items,
        ]);
    }

    public function store(Request $request)
    {
        /*Validate the form data*/
        $this->validateForm($request);

        $product = new Product();
        $product->name = $request->name;
        $product->brand_id = $request->brand_id;
        $product->category_id = $request->category_id;
        $product->purchase_price = $request->purchase_price;
        $product->sale_price = $request->sales_price;
        $product->unit_id = $request->unit_id;
        $product->store_id = $request->store_id;
        $product->qty = $request->qty;

        /* Save to the database table*/
        $product->save();
        return response()->json([
            'success' => true,
            'message' => 'Added successfully!'
        ]);
    }


    public function delete(Request $request)
    {
        $object = Product::find($request->id);

        if (empty($object)) {
            return response()->json(['error' => 'Product not found.'], 404);
        }
        /* Delete it From Database Table */
        $object->delete();

        return response()->json(['success' =>true, 'message'=> 'Deleted successfully.']);
    }
    public function edit($id)
    {
        $data = Product::find($id);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
            exit;
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }
    }
    public function product_view($id){
        /*Find the product Purchase History*/


        /*Find the product Sales History*/
        $sales_invoice_history = Client_invoice_details::with('invoice.client')->where('product_id','=',$id)->get();
        $purchase_invoice_history = Supplier_Invoice_Details::with('invoice.supplier')->where('product_id','=',$id)->get();
        $data = Product::with('brand','category','unit','store')->find($id);
        return view('Backend.Pages.Product.view',compact('data','sales_invoice_history','purchase_invoice_history'));
    }
    public function check_product_qty(Request $request)
    {
        /* Validate the form data*/
        $rules = [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /* Find the product*/
        $product = Product::with('purchases', 'sales')->find($request->product_id);

        // Check if product exists
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        /* Calculate the actual available quantity*/
        $totdalQty = $product->qty;
        $totalPurchased = $product->purchases->sum('qty');
        $totalSold = $product->sales->sum('qty');
        $availableQty = ($totalPurchased - $totalSold) + $totdalQty;
        /* Check if product quantity is sufficient*/
        if ($availableQty < $request->qty) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not available.'
            ]);
        }

        /*If everything is fine, return success*/
        return response()->json([
            'success' => true,
            'message' => 'Product quantity is available.',
        ]);
    }



    public function update(Request $request, $id)
    {

        $this->validateForm($request);

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->brand_id = $request->brand_id;
        $product->category_id = $request->category_id;
        $product->purchase_price = $request->purchase_price;
        $product->sale_price = $request->sales_price;
        $product->unit_id = $request->unit_id;
        $product->store_id = $request->store_id;
        $product->qty = $request->qty;
        $product->update();

        return response()->json([
            'success' => true,
            'message' => 'Update successfully!'
        ]);
    }
    private function validateForm($request)
    {

        /*Validate the form data*/
        $rules=[
            'name' => 'required|string|max:255|unique:products,name',
            'brand' => 'required|integer|exists:product_brands,id',
            'category' => 'required|integer|exists:product_categories,id',

            'unit_id' => 'required|integer|exists:units,id',
            'store' => 'required|integer|exists:stores,id',
        ];
        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    }
}
