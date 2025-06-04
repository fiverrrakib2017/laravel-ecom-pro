<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radcheck extends Model
{
    use HasFactory;
    protected $connection = 'radius'; // <-- IMPORTANT
    protected $table = 'radcheck';
    public $timestamps = false;

    protected $fillable = [
        'username', 'attribute', 'op', 'value'
    ];
}
