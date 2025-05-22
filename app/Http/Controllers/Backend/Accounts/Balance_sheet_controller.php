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

class Balance_sheet_controller extends Controller
{
    public function index()
    {
        return view('Backend.Pages.Accounts.Balance_sheet.Report');
    }

    public function report(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('end_date');

        $types = ['asset', 'liability', 'equity'];
        $data = [];

        foreach ($types as $type) {
            $accounts = DB::table('account_transactions as t')->join('accounts as a', 't.account_id', '=', 'a.id')->where('a.type', $type)->when($from, fn($q) => $q->whereDate('t.transaction_date', '>=', $from))->when($to, fn($q) => $q->whereDate('t.transaction_date', '<=', $to))->select('a.name', DB::raw('SUM(t.amount) as total'))->groupBy('a.name')->get();

            $data[$type] = $accounts;
        }

        $rows = '';
        $totalAsset = 0;
        $totalLiability = 0;
        $totalEquity = 0;

        // Assets
        $rows .= '<tr><td colspan="2"><strong>Assets</strong></td></tr>';
        foreach ($data['asset'] as $item) {
            $rows .= '<tr><td>' . $item->name . '</td><td>' . number_format($item->total, 2) . '</td></tr>';
            $totalAsset += $item->total;
        }
        $rows .= '<tr><td><strong>Total Assets</strong></td><td><strong>' . number_format($totalAsset, 2) . '</strong></td></tr>';

        // Liabilities
        $rows .= '<tr><td colspan="2"><strong>Liabilities</strong></td></tr>';
        foreach ($data['liability'] as $item) {
            $rows .= '<tr><td>' . $item->name . '</td><td>' . number_format($item->total, 2) . '</td></tr>';
            $totalLiability += $item->total;
        }
        $rows .= '<tr><td><strong>Total Liabilities</strong></td><td><strong>' . number_format($totalLiability, 2) . '</strong></td></tr>';

        // Equity
        $rows .= '<tr><td colspan="2"><strong>Owner\'s Equity</strong></td></tr>';
        foreach ($data['equity'] as $item) {
            $rows .= '<tr><td>' . $item->name . '</td><td>' . number_format($item->total, 2) . '</td></tr>';
            $totalEquity += $item->total;
        }
        $rows .= '<tr><td><strong>Total Equity</strong></td><td><strong>' . number_format($totalEquity, 2) . '</strong></td></tr>';

        $rows .= '<tr style="background:#f0f0f0;"><td><strong>Total Liability + Equity</strong></td><td><strong>' . number_format($totalLiability + $totalEquity, 2) . '</strong></td></tr>';

        return response()->json([
            'success' => true,
            'html' => $rows,
        ]);
    }
}
