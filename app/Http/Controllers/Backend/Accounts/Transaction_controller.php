<?php

namespace App\Http\Controllers\Backend\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Employee_shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
    $columnsForOrderBy = ['id', 'name', 'type', 'description'];
    $orderByColumnIndex = $request->order[0]['column'];
    $orderDirection = $request->order[0]['dir'];
    $orderByColumn = $columnsForOrderBy[$orderByColumnIndex];

    $query = Account::with('parent');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('type', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%")
              ->orWhereHas('parent', function($q) use ($search) {
                  $q->where('name', 'like', "%$search%");
              });
        });
    }

    $totalRecords = Account::count();
    $filteredRecords = $query->count();

    $items = $query->orderBy($orderByColumn, $orderDirection)
                   ->skip($request->start)
                   ->take($request->length)
                   ->get();

    // map the data to include parent account name
    $formatted = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'type' => $item->type,
            'description' => $item->description,
            'parent_name' => $item->parent ? $item->parent->name : '—',
        ];
    });

    return response()->json([
        'draw' => $request->draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $formatted,
    ]);
}


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:Income,Expense,Asset,Liability,Equity',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $account = new Account();
        $account->name = $request->name;
        $account->type = $request->type;
        $account->parent_account_id = $request->parent_account_id ?? NULL;
        $account->description = $request->description;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Account added successfully.'
        ]);
    }

    public function get_account($id)
    {
        $data = Account::find($id);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:accounts,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:Income,Expense,Asset,Liability,Equity',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $account = Account::find($request->id);
        $account->name = $request->name;
        $account->type = $request->type;
        $account->parent_account_id = $request->parent_account_id;
        $account->description = $request->description;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully.'
        ]);
    }

    public function delete(Request $request)
    {
        $account = Account::find($request->id);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.'
            ], 404);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.'
        ]);
    }
}
