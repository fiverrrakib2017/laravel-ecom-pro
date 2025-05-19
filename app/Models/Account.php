<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }
}
