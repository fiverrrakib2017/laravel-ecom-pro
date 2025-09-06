<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_method extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'account_number',
        'api_key',
        'api_secret',
        'username',
        'password',
        'callback_url',
        'status',
    ];
}
