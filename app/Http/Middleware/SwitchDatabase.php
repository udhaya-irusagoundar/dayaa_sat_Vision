<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SwitchDatabase
{
    public function handle($request, Closure $next)
    {
        if (session()->has('active_db')) {

            // get company details to confirm status
            $company = DB::table('companies')
                ->where('db_name', session('active_db'))
                ->first();

            // ❌ company not found OR inactive
            if (!$company || $company->status == 0) {

                // remove active DB session
                session()->forget('active_db');

                return redirect()->route('company.index')
                    ->with('error', 'This company is inactive. Access denied.');
            }

            // ✔ ACTIVE → allow DB switch
            DB::purge('multi');
            Config::set('database.connections.multi.database', session('active_db'));
            DB::reconnect('multi');
            app()->forgetInstance('db');
            DB::setDefaultConnection('multi');
        }

        return $next($request);
    }
}