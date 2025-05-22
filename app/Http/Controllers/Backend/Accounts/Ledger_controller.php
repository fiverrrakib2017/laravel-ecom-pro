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

class Ledger_controller extends Controller
{
    public function index()
    {
        $accounts = Account::all();
       return view('Backend.Pages.Accounts.Ledger.Report',compact('accounts'));
    }

   public function report(Request $request)
{
    $accountId = $request->input('account_id');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('end_date');

    $transactions = Account_transaction::where(function ($query) use ($accountId) {
        $query->where('account_id', $accountId)
              ->orWhere('related_account_id', $accountId);
    })
    ->when($fromDate, fn($q) => $q->whereDate('transaction_date', '>=', $fromDate))
    ->when($toDate, fn($q) => $q->whereDate('transaction_date', '<=', $toDate))
    ->orderBy('transaction_date')
    ->get();

    $balance = 0;
    $rows = '';

    foreach ($transactions as $trx) {
        $debit = $trx->account_id == $accountId ? $trx->amount : 0;
        $credit = $trx->related_account_id == $accountId ? $trx->amount : 0;
        $balance += ($debit - $credit);

        $rows .= '<tr>
                    <td>' . $trx->transaction_date . '</td>
                    <td>' . $trx->description . '</td>
                    <td>' . number_format($debit, 2) . '</td>
                    <td>' . number_format($credit, 2) . '</td>
                    <td>' . number_format($balance, 2) . '</td>
                 </tr>';
    }

    return response()->json(
        [
            'success' => true,
            'html' => $rows
        ]
    );
}

}
