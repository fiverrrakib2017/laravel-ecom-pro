<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotspot_session extends Model
{
    use HasFactory;
    protected $fillable = ['router_id', 'username', 'mac', 'ip', 'login_time', 'logout_time', 'uptime_seconds', 'download_bytes', 'upload_bytes', 'terminate_cause', 'was_kicked', 'voucher_id'];
    protected $casts = ['login_time' => 'datetime', 'logout_time' => 'datetime', 'was_kicked' => 'boolean'];
}
