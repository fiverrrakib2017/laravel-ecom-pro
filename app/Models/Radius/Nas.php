<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nas extends Model
{
    use HasFactory;

    protected $connection = 'radius'; // <-- IMPORTANT
    protected $table ='nas';
    public $timestamps = false;

    protected $fillable = [
        'id', 'nasname'	, 'shortname' ,	'type'	, 'ports',	'secret	server',	'community','	description'

    ];
}
