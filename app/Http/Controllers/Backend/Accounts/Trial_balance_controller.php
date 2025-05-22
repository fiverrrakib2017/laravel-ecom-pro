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

class Trial_balance_controller extends Controller
{
    public function index()
    {
        $accounts = Account::all();
        return view('Backend.Pages.Accounts.Trial_balance.Report', compact('accounts'));
    }

   public function report(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('end_date');

    $query = DB::table('account_transactions as at')
        ->select(
            'a.id',
            'a.name',
            DB::raw('SUM(CASE WHEN at.account_id = a.id THEN at.amount ELSE 0 END) as debit'),
            DB::raw('SUM(CASE WHEN at.related_account_id = a.id THEN at.amount ELSE 0 END) as credit')
        )
        ->join('accounts as a', function ($join) {
            $join->on('at.account_id', '=', 'a.id')
                ->orOn('at.related_account_id', '=', 'a.id');
        })
        ->when($fromDate, function ($query, $fromDate) {
            return $query->whereDate('at.transaction_date', '>=', $fromDate);
        })
        ->when($toDate, function ($query, $toDate) {
            return $query->whereDate('at.transaction_date', '<=', $toDate);
        })
        ->groupBy('a.id', 'a.name')
        ->get();

    $rows = '';
    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($query as $item) {
        if ($item->debit == 0 && $item->credit == 0) {
            continue;
        }

        $totalDebit += $item->debit;
        $totalCredit += $item->credit;

        $rows .= '<tr>
                    <td>' . $item->name . '</td>
                    <td>' . number_format($item->debit, 2) . '</td>
                    <td>' . number_format($item->credit, 2) . '</td>
                  </tr>';
    }

    $rows .= '<tr style="font-weight:bold;">
                <td>Total</td>
                <td>' . number_format($totalDebit, 2) . '</td>
                <td>' . number_format($totalCredit, 2) . '</td>
              </tr>';

    return response()->json([
        'success' => true,
        'html' => $rows
    ]);
}
}
