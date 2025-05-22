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

class Income_controller extends Controller
{
    public function index()
    {
        $accounts = Account::all();
        return view('Backend.Pages.Accounts.Income.Report', compact('accounts'));
    }

    public function report(Request $request)
{
    $from = $request->input('from_date');
    $to = $request->input('end_date');

    $incomeAccounts = DB::table('account_transactions as t')
        ->join('accounts as a', 't.account_id', '=', 'a.id')
        ->where('a.type', 'income')
        ->when($from, fn($q) => $q->whereDate('t.transaction_date', '>=', $from))
        ->when($to, fn($q) => $q->whereDate('t.transaction_date', '<=', $to))
        ->select('a.name', DB::raw('SUM(t.amount) as total'))
        ->groupBy('a.name')
        ->get();

    $expenseAccounts = DB::table('account_transactions as t')
        ->join('accounts as a', 't.account_id', '=', 'a.id')
        ->where('a.type', 'expense')
        ->when($from, fn($q) => $q->whereDate('t.transaction_date', '>=', $from))
        ->when($to, fn($q) => $q->whereDate('t.transaction_date', '<=', $to))
        ->select('a.name', DB::raw('SUM(t.amount) as total'))
        ->groupBy('a.name')
        ->get();

    $rows = '';
    $totalIncome = 0;
    $totalExpense = 0;

    $rows .= '<tr><td colspan="2"><strong>Income</strong></td></tr>';
    foreach ($incomeAccounts as $acc) {
        $rows .= '<tr><td>' . $acc->name . '</td><td>' . number_format($acc->total, 2) . '</td></tr>';
        $totalIncome += $acc->total;
    }

    $rows .= '<tr><td><strong>Total Income</strong></td><td><strong>' . number_format($totalIncome, 2) . '</strong></td></tr>';

    $rows .= '<tr><td colspan="2"><strong>Expense</strong></td></tr>';
    foreach ($expenseAccounts as $acc) {
        $rows .= '<tr><td>' . $acc->name . '</td><td>' . number_format($acc->total, 2) . '</td></tr>';
        $totalExpense += $acc->total;
    }

    $rows .= '<tr><td><strong>Total Expense</strong></td><td><strong>' . number_format($totalExpense, 2) . '</strong></td></tr>';

    $profit = $totalIncome - $totalExpense;
    $rows .= '<tr style="background:#f0f0f0;"><td><strong>Net Profit</strong></td><td><strong>' . number_format($profit, 2) . '</strong></td></tr>';

    return response()->json([
        'success' => true,
        'html' => $rows
    ]);
}
}
