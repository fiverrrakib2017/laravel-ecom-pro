<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daily_usages extends Model
{
    use HasFactory;
     protected $fillable = [
        'customer_id',
        'session_id',
        'router_id',
        'ip',
        'mac',
        'uptime',
        'download',
        'upload',
        'date'
    ];
}
