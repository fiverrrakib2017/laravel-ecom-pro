<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radreply extends Model
{
    use HasFactory;
    protected $connection = 'radiusdb';
    protected $table = 'radreply';
    public $timestamps = false;

    protected $fillable = [
        'username', 'attribute', 'op', 'value'
    ];
}
