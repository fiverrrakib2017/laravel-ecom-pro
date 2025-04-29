<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client_invoice extends Model
{
    use HasFactory;
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id','id');
    }

    public function items()
    {
        return $this->hasMany(Client_invoice_details::class,'invoice_id','id');
    }
}
