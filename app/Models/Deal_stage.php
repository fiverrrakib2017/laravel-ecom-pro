<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal_stage extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'is_won', 'is_lost'];

    protected $casts = [
        'is_won'  => 'boolean',
        'is_lost' => 'boolean',
    ];
}
