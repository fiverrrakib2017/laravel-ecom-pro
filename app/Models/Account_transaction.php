<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account_transaction extends Model
{
    use HasFactory;

    public function debit_account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function credit_account()
    {
        return $this->belongsTo(Account::class, 'related_account_id', 'id');
    }
}
