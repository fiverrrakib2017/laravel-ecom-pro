<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $table = 'routers';

    protected $fillable = [
        'pop_id','name','ip_address','username','password','port',
        'status','is_radius','api_version','location','remarks'
    ];

    protected $casts = [
        'port' => 'integer',
    ];

    public function hotspotProfiles(){ return $this->hasMany(Hotspot_profile::class); }
    public function hotspotUsers(){ return $this->hasMany(Hotspot_user::class); }
    public function voucherBatches(){ return $this->hasMany(Voucher_batch::class); }
    public function vouchers(){ return $this->hasMany(Voucher::class); }


    public function getHost(): string { return $this->ip_address; }


    public function useTls(): bool {
        return (int)$this->port === 8729;
    }

    public function apiPort(): int {
        return (int)$this->port ?: 8728;
    }
}
