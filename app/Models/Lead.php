<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
     protected $casts = [
        'estimated_close_date' => 'datetime:Y-m-d',  // Cast to Carbon
        'first_contacted_at' => 'datetime',
        'last_contacted_at' => 'datetime',
    ];
}
