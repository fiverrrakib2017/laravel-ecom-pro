<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

// class CompanyMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle($request, Closure $next)
//     {
//         $host = $request->getHost();
//         $subdomain = explode('.', $host)[0];

//         $tenant = DB::table('tenants')->where('subdomain', $host)->first();

//         if ($tenant) {
//             Config::set('database.connections.tenant', [
//                 'driver' => 'mysql',
//                 'host' => env('DB_HOST', '127.0.0.1'),
//                 'port' => env('DB_PORT', '3306'),
//                 'database' => $tenant->db_name,
//                 'username' => $tenant->db_user,
//                 'password' => $tenant->db_pass,
//             ]);

//             DB::purge('tenant');
//             DB::reconnect('tenant');
//             DB::setDefaultConnection('tenant');
//         } else {
//             DB::setDefaultConnection('mysql');
//         }

//         return $next($request);
//     }
// }



class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawHost = $request->getHost();
        $host    = Str::lower($rawHost);
        $host    = Str::before($host, ':');
        $host    = Str::startsWith($host, 'www.') ? Str::after($host, 'www.') : $host;

        $parts      = explode('.', $host);
        $subdomain  = $parts[0] ?? null;

        $tenant = Cache::store('redis')->remember("tenant:lookup:{$host}", now()->addMinutes(5), function () use ($host, $subdomain) {

            return DB::table('tenants')
                ->where('subdomain', $host)
                ->first();
        });

        if (!$tenant) {
            DB::setDefaultConnection('mysql');
            return $next($request);
        }

        $tenantConnection = [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => $tenant->db_name,
            'username'  => $tenant->db_user,
            'password'  => $tenant->db_pass,

            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'prefix_indexes' => true,
            'strict'    => true,
            'engine'    => null,
            'options'   => extension_loaded('pdo_mysql') ? array_filter([
                \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ];

        Config::set('database.connections.tenant', $tenantConnection);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');

        app()->instance('tenant', $tenant);

        // Cache prefix:
        $tenantPrefix = 'tenant_' . ($tenant->id ?? $subdomain ?? 'unknown') . '_';
        config(['cache.prefix' => $tenantPrefix . (config('cache.prefix') ?? 'cache_')]);

        return $next($request);
    }
}
