<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Send_message extends Model
{
    use HasFactory;
    public function pop(){
        return $this->belongsTo(Pop_branch::class,'pop_id','id');
    }
    public function area(){
        return $this->belongsTo(Pop_area::class,'area_id','id');
    }
    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

}
