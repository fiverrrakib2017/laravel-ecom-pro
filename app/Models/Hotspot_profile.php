<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot_profile extends Model
{
    use HasFactory;
    protected $fillable = ['router_id', 'name', 'mikrotik_profile', 'rate_limit', 'shared_users', 'idle_timeout', 'keepalive_timeout', 'session_timeout', 'validity_days', 'price_minor', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
    public function users()
    {
        return $this->hasMany(Hotspot_user::class);
    }
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
