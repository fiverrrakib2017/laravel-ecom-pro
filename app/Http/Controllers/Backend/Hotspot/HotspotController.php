<?php

namespace App\Http\Controllers\Backend\Hotspot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HotspotController extends Controller
{
    public function hotspot_dashbaord(){
         return view('Backend.Pages.Hotspot.Dashboard');
    }
}
