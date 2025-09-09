<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher_batch extends Model
{
    use HasFactory;
    protected $fillable = ['router_id', 'hotspot_profile_id', 'name', 'qty', 'code_prefix', 'username_length', 'password_length', 'validity_days_override', 'expires_at', 'price_minor', 'status', 'meta'];
    protected $casts = ['meta' => 'array', 'expires_at' => 'datetime'];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
    public function profile()
    {
        return $this->belongsTo(Hotspot_profile::class, 'hotspot_profile_id');
    }
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
