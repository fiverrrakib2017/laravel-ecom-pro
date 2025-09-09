<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = ['voucher_batch_id', 'router_id', 'hotspot_profile_id', 'username', 'password_encrypted', 'status', 'activated_at', 'expires_at', 'hotspot_user_id', 'use_count', 'meta'];
    protected $casts = ['meta' => 'array', 'activated_at' => 'datetime', 'expires_at' => 'datetime'];

    public function batch()
    {
        return $this->belongsTo(Voucher_batch::class, 'voucher_batch_id');
    }
    public function profile()
    {
        return $this->belongsTo(Hotspot_profile::class, 'hotspot_profile_id');
    }
    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
