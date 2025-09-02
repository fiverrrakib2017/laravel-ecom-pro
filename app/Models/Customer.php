<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
      use HasFactory, Notifiable;
    protected $fillable = [
        'fullname', 'phone', 'nid', 'address', 'con_charge', 'amount',
        'username', 'password', 'package_id', 'pop_id', 'area_id',
        'router_id', 'status', 'expire_date', 'remarks', 'liabilities', 'is_delete', 'last_seen'
    ];
    public function pop(){
        return $this->belongsTo(Pop_branch::class,'pop_id','id');
    }
    public function area(){
        return $this->belongsTo(Pop_area::class,'area_id','id');
    }
    public function package(){
        return $this->belongsTo(Branch_package::class,'package_id','id');
    }
    public function router(){
        return $this->belongsTo(Router::class,'router_id','id');
    }
}
