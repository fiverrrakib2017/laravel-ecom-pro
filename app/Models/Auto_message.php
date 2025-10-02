<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auto_message extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'name', 'body', 'is_active'];
}
