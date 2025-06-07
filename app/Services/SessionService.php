<?php
namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
class SessionService
{
    public function forget_session_sidebar_customer(): void
    {
        Cache::forget('sidebar_customers');
    }
}
