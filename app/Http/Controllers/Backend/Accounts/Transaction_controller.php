<?php

namespace App\Http\Controllers\Backend\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Account_transaction;
use App\Models\Employee_shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Transaction_controller extends Controller
{
    public function index()
    {
        $accounts = Account::all();
       return view('Backend.Pages.Accounts.Transaction.index',compact('accounts'));
    }

    public function all_data(Request $request)
    {
        $search = $request->search['value'];
        $columnsForOrderBy =  ['id', 'account_id', 'related_account_id', 'amount', 'transaction_date', 'description', 'created_at'];
        $orderByColumn = $columnsForOrderBy[$request->order[0]['column']];
        $orderDirection = $request->order[0]['dir'];

        $query = Account_transaction::with(['debit_account', 'credit_account'])->when($search, function ($query) use ($search) {
            $query
                ->orWhere('description', 'like', "%$search%")
                ->orWhere('transaction_date', 'like', "%$search%")

               ->orWhereHas('debit_account', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                ->orWhereHas('credit_account', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        });

        $total = $query->count();
        $items = $query->orderBy($orderByColumn, $orderDirection)->skip($request->start)->take($request->length)->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $items,
        ]);
    }


    public function store(Request $request)
    {
        $rules = [
            'account_id'            => 'required|exists:accounts,id',
            'related_account_id'    => 'required|exists:accounts,id|different:account_id',
            'amount'                => 'required|numeric|min:1',
            'transaction_date'      => 'required|date',
            'description'           => 'nullable|string',
            'transaction_type'      => 'required|in:payment,receive',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $account                        = new Account_transaction();
        $account->account_id            = $request->account_id;
        $account->related_account_id    = $request->related_account_id;
        $account->amount                = $request->amount;
        $account->description           = $request->description;
        $account->transaction_date      = $request->transaction_date;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaction Added successfully.'
        ]);
    }

    public function get_account_transaction($id)
    {
        $data = Account_transaction::find($id);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'account_id'            => 'required|exists:accounts,id',
            'related_account_id'    => 'required|exists:accounts,id|different:account_id',
            'amount'                => 'required|numeric|min:1',
            'transaction_date'      => 'required|date',
            'description'           => 'nullable|string',
            'transaction_type'      => 'required|in:payment,receive',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $account                        = Account_transaction::find($request->id);
        $account->account_id            = $request->account_id;
        $account->related_account_id    = $request->related_account_id;
        $account->amount                = $request->amount;
        $account->description           = $request->description;
        $account->transaction_date      = $request->transaction_date;
        $account->update();

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully.'
        ]);
    }

    public function delete(Request $request)
    {
        $account = Account_transaction::find($request->id);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.'
            ], 404);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.'
        ]);
    }
}
