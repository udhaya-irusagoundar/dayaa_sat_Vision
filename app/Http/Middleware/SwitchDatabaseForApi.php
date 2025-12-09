<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Exception;

class SwitchDatabaseForApi
{
 public function handle($request, Closure $next)
{
    $companyCode = strtoupper(
        $request->header('company-code') ??
        $request->header('company_code') ??
        $request->company_code ??
        ''
    );

    if (!$companyCode) {
        return response()->json([
            "status" => false,
            "message" => "Company Code header missing"
        ]);
    }

    $company = DB::connection('mysql')
        ->table('companies')
        ->where('company_code', $companyCode)
        ->first();

    if (!$company) {
        return response()->json([
            "status" => false,
            "message" => "Invalid Company Code"
        ]);
    }

    // Switch DB
    DB::purge('multi');
    config(['database.connections.multi.database' => $company->db_name]);
    DB::reconnect('multi');

    /** 🔥 THESE 2 LINES FIX YOUR PROBLEM */
    app()->forgetInstance('db');
    DB::setDefaultConnection('multi');

    return $next($request);
}

}