<?php

namespace App\Http\Middleware;

use App\Models\Pop_branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         /* Check if the admin is authenticated*/
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();

        /*If admin is linked to a pop_id, check the status of that branch*/
        if (!is_null($admin->pop_id)) {
            $popBranch = Pop_branch::find($admin->pop_id);

            /* If the branch doesn't exist or is inactive*/
            if (!$popBranch || $popBranch->status != 1) {
                Auth::guard('admin')->logout(); 
                return redirect()->route('admin.login')->withErrors(['Your Account is Blocked']);
            }
        }
        return $next($request);
    }
}
