<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginPage;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
 public function handleCompanyCode(Request $request)
{
    $companyCode = strtoupper(
        $request->header('Company-Code') ??
        $request->header('company-code') ??
        $request->header('company_code') ??
        $request->company_code ??
        ''
    );

    if (!$companyCode) {
        return response()->json([
            'status' => false,
            'message' => 'Company Code missing'
        ], 422);
    }

    $company = DB::connection('mysql')
        ->table('companies')
        ->where('company_code', $companyCode)
        ->first();

    if (!$company) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid company code.'
        ], 400);
    }
    if ($company->status == 0) {
    return response()->json([
        'status' => false,
        'message' => 'Company disabled. Contact Admin.'
    ], 403);
}


    DB::purge('multi');
    config(['database.connections.multi.database' => $company->db_name]);
    DB::reconnect('multi');

    app()->forgetInstance('db');
    DB::setDefaultConnection('multi');

    return response()->json([
        'status'       => true,
        'message'      => 'Company code valid. Proceed login',
        'company_code' => $companyCode,
        'active_db'    => $company->db_name
    ]);
}

    public function login(Request $request)
{
    $request->validate([
       
        'username'     => 'required',
        'password'     => 'required',
    ]);

   $companyCode = strtoupper(
    $request->company_code ?? $request->header('company-code')
);


    // Check company manually
    $company = DB::connection('mysql')
        ->table('companies')
        ->where('company_code', $companyCode)
        ->first();

    if (!$company) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid company code.'
        ], 400);
    }
    // Block inactive company
if ($company->status == 0) {
    return response()->json([
        'status' => false,
        'message' => 'Company disabled. Contact Admin.'
    ], 403);
}


    // Switch DB
  /*  DB::purge('multi');
    config(['database.connections.multi.database' => $company->db_name]);
    DB::reconnect('multi');
    DB::setDefaultConnection('multi');
    */

    // ---- Login Check ----
    $admin = LoginPage::where('username', $request->username)->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
    return response()->json([
        'status'       => true,
        'role'         => 'admin',
        'message'      => 'Admin login successful',
        'token'        => $admin->createToken('mobile')->plainTextToken,
        'active_db'    => $company->db_name,
        'company_code' => $companyCode,

        // ⭐ Added Admin Details
        'admin' => [
            'id'           => $admin->id,
            'username'     => $admin->username,
            'name'         => $admin->name ?? null,
            'mobile' => $admin->mobile ?? null,
        ]
    ]);
}


    $staff = Staff::where('username', $request->username)->first();

    if ($staff && Hash::check($request->password, $staff->password)) {

    return response()->json([
        'status'       => true,
        'role'         => 'staff',
        'message'      => 'Staff login successful',
        'token'        => $staff->createToken('mobile')->plainTextToken,
        'active_db'    => $company->db_name,
        'company_code' => $companyCode,

        // ⭐ Added Staff Details
        'staff' => [
            'id'           => $staff->id,
            'username'     => $staff->username,
            'name'         => $staff->name,
            'mobile' => $staff->mobile,
        ]
    ]);
}


    return response()->json([
        'status'  => false,
        'message' => 'Invalid username or password.',
    ], 401);
}
}