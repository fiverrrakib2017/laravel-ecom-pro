<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot_user extends Model
{
    use HasFactory;
    protected $fillable = [
'router_id','hotspot_profile_id','username','password_encrypted','mac_lock','status','expires_at','last_seen_at','upload_bytes','download_bytes','uptime_seconds','created_by','comment'
];
protected $dates = ['expires_at','last_seen_at'];
protected $casts = [
    'expires_at' => 'datetime',
];

public function router(){ return $this->belongsTo(Router::class); }
public function profile(){ return $this->belongsTo(Hotspot_profile::class,'hotspot_profile_id'); }
}
