<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;
    protected $table = 'deals';

    protected $fillable = [
        'title',
        'lead_id',
        'client_id',
        'stage_id',
        'amount',
        'expected_close_date',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_close_date' => 'date', // Y-m-d
    ];

    public function stage()  { return $this->belongsTo(\App\Models\Deal_stage::class, 'stage_id'); }
    public function lead()   { return $this->belongsTo(\App\Models\Lead::class, 'lead_id'); }
    public function client() { return $this->belongsTo(\App\Models\Client::class, 'client_id'); }
    public function user()   { return $this->belongsTo(\App\Models\Admin::class, 'user_id'); }
}
