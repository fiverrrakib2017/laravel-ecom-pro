<?php
namespace App\Services;
use App\Models\Account_transaction;
use App\Models\Client;
use App\Models\Client_invoice;
use App\Models\Client_invoice_details;
use App\Models\Supplier_Invoice_Details;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Supplier_Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceService{
    public function createInvoice($type){
        $data=array();
        $data['product']=Product::latest()->get();
        if ($type === 'client') {
            $data['client'] = Client::latest()->get();
            return view('Backend.Pages.Client.invoice_create')->with($data);
        } elseif ($type === 'Supplier') {
            $data['supplier']=Supplier::latest()->get();
            return view('Backend.Pages.Supplier.invoice_create')->with($data);
        }

    }
    public function store_invoice($request, $userId=1, $type = 'client')
    {
        $this->__validate_method($request);
        DB::beginTransaction();
        try {
            $invoice = new Client_invoice();
            $invoice = $type === 'supplier' ? new Supplier_Invoice() : new Client_invoice();
            $invoice->transaction_number = "TRANSID-".strtoupper(uniqid());
            $invoice->usr_id = $userId;

            /*Create A logic for customer and supplier*/
            if (!empty($type) && isset($type) && $type === 'client') {
                $invoice->client_id = $request->client_id;
            }else{
                $invoice->supplier_id = $request->client_id;
            }

            $invoice->invoice_date = $request->date;
            $invoice->sub_total = $request->table_total_amount ?? 0;
            $invoice->discount = $request->table_discount_amount ?? 0;
            $invoice->grand_total = $request->table_total_amount ?? 0;
            $invoice->due_amount = $request->table_due_amount ?? 0;
            $invoice->paid_amount = $request->table_paid_amount ?? 0;
            $invoice->note = $request->note ?? '';
            $invoice->status = $request->table_status ?? 0;
            $invoice->save();
            /* Add invoice details */
            $this->_hadle_invoice_details($request,$invoice,$type);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Invoice created successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error creating invoice: ' . $e->getMessage()]);
        }
    }
    public function update_invoice($request, $invoiceId, $userId=1, $type = 'customer'){
        $this->__validate_method($request);
        DB::beginTransaction();
        try {
            $invoice = $type === 'supplier' ? Supplier_Invoice::find($invoiceId) : Client_invoice::find($invoiceId);
            $invoice->usr_id = $userId;

            /*Create A logic for customer and supplier*/
            if (!empty($type) && isset($type) && $type === 'client') {
                $invoice->client_id = $request->client_id;
            }else{
                $invoice->supplier_id = $request->client_id;
            }

            $invoice->invoice_date = $request->date;
            $invoice->sub_total = $request->table_total_amount ?? 0;
            $invoice->discount = $request->table_discount_amount ?? 0;
            $invoice->grand_total = $request->table_total_amount ?? 0;
            $invoice->due_amount = $request->table_due_amount ?? 0;
            $invoice->paid_amount = $request->table_paid_amount ?? 0;
            $invoice->note = $request->note ?? '';
            $invoice->status = $request->table_status ?? 0;
            $invoice->update();

            /*Delete Previous invoice details*/
            if ($type==="client") {
                Client_invoice_details::where('invoice_id', $invoiceId)->delete();

            }
            if ($type==="supplier") {
                Supplier_Invoice_Details::where('invoice_id', $invoiceId)->delete();

            }

            /* Insert invoice details AND Accounts transaction*/
            $this->_hadle_invoice_details($request,$invoice,$type);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Invoice Update successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error Update invoice: ' . $e->getMessage()]);
        }
    }
    public function delete_invoice($request,$type='client'){
        try {
            $invoice = $type ==='client' ? Client_invoice::find($request->id) : Supplier_Invoice::find($request->id);
            if (empty($invoice)) {
                return response()->json(['success' => false, 'message' => 'Invoice not found.']);
            }
            if(!empty($invoice->transaction_number) && isset($invoice->transaction_number)){
                Account_transaction::where('transaction_number', $invoice->transaction_number)->delete();
            }
            $invoice->delete();
            return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting invoice: ' . $e->getMessage()]);
        }
    }
    private function __validate_method($request){

        /*Validate the form data*/
        $rules=[
           'client_id' => 'required|exists:customers,id',
           'date' => 'required|date',
           'table_product_id' => 'required|array',
           'table_product_id.*' => 'exists:products,id',
           'table_qty' => 'required|array',
           'table_qty.*' => 'integer|min:1',
           'table_price' => 'required|array',
           'table_price.*' => 'numeric|min:0',
           'table_total_price' => 'required|array',
           'table_total_price.*' => 'numeric|min:0',
           'products' => 'required|array',
           'products.*.product_id' => 'required|exists:products,id',
           'products.*.qty' => 'required|integer|min:1',
           'products.*.price' => 'required|numeric|min:0',
       ];
       $validator = Validator::make($request->all(), $rules);

       if ($validator->fails()) {
           return response()->json([
               'success' => false,
               'errors' => $validator->errors()
           ], 422);
       }
   }
   private function _hadle_invoice_details($request,$invoice,$type){
    foreach ($request->table_product_id as $index => $productId) {

        $inv_details = $type ==='client' ? new Client_invoice_details() : new Supplier_Invoice_Details();
        $inv_details->invoice_id = $invoice->id;
        $inv_details->transaction_number = $invoice->transaction_number;
        $inv_details->product_id = $productId;
        $inv_details->qty = $request->table_qty[$index];
        $inv_details->price = intval($request->table_price[$index]);
        $inv_details->total_price = intval($request->table_qty[$index] * $request->table_price[$index]);
        $inv_details->status = $request->table_status ?? 0;
        $inv_details->save();
    }
   }
}
