<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch_transaction extends Model
{
    use HasFactory;
    public function pop(){
        return $this->belongsTo(Pop_branch::class, 'pop_id','id');
    }
}
