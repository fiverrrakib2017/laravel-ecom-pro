<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    use HasFactory;

    protected $connection = 'radius'; // <-- IMPORTANT
    protected $table = 'radacct';
    public $timestamps = false;

    protected $fillable = [
        'radacctid', 'acctsessionid', 'acctuniqueid', 'username',
        'groupname', 'realm', 'nasipaddress', 'nasportid', 'nasporttype',
        'acctstarttime', 'acctstoptime', 'acctsessiontime', 'acctauthentic',
        'connectinfo_start', 'connectinfo_stop', 'acctinputoctets', 'acctoutputoctets',
        'calledstationid', 'callingstationid', 'acctterminatecause',
        'servicetype', 'framedprotocol', 'framedipaddress'
    ];
}
