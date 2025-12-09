<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginPage;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function login_submit(Request $request)
    {
       if ($request->company_code && !$request->username && !$request->password) {
    $company = DB::table('companies')
        ->where('company_code', strtoupper($request->company_code))
        ->first();

    if (!$company) {
        return back()->withErrors(['company_code' => 'Invalid company code']);
    }

    // ❌ Block INACTIVE company here itself
    if ($company->status == 0) {
        return back()->withErrors(['company_code' => 'This company is inactive. Contact Super Admin.']);
    }

    session(['company_code' => strtoupper($request->company_code)]);
    return redirect()->route('login');
}

// Step 2: Validate company + status
$company = DB::table('companies')
    ->where('company_code', strtoupper($request->company_code))
    ->first();

if (!$company) {
    return back()->with('error', 'Invalid Company Code');
}

// ❌ block inactive company
if ($company->status == 0) {
    return back()->with('error', 'This company is inactive. Contact Super Admin.');
}

// store name + code in session 🔥
session([
    'company_name' => $company->company_name,
    'company_logo' => $company->logo,
    'company_code' => strtoupper($request->company_code),
]);
        // Step 3: Switch DB
        config(['database.connections.multi.database' => $company->db_name]);
        DB::setDefaultConnection('multi');
        session(['active_db' => $company->db_name]);

        // Step 4: Validate inputs
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // ---- Admin Login ----
        Auth::shouldUse('admin');
        $admin = DB::table('login_page')
            ->where('username', $request->username)
            ->first();

        // 🔥 FIX → only redirect if admin matched
        if ($admin && Hash::check($request->password, $admin->password)) {

            session([
                'role'     => 'admin',
                'user_id'  => $admin->id,
                'username' => $admin->username,
            ]);

            return redirect()->route('admin.dashboard');
        }

        // ---- Staff Login ----
        Auth::shouldUse('staff');
        $staff = DB::table('staff')
            ->where('username', $request->username)
            ->first();

        if (!$staff) {
            return back()->withErrors(['username' => 'Invalid username'])->withInput();
        }

        if (!Hash::check($request->password, $staff->password)) {
            return back()->withErrors(['password' => 'Incorrect password'])->withInput();
        }

        session([
            'role'     => 'staff',
            'user_id'  => $staff->id,
            'username' => $staff->username,
             'staff_name' => $staff->name  
        ]);
//dd(session()->all());
        return redirect()->route('staff.search');
    }

    public function logout()
    {
        // 1️⃣ get active DB before anything
        $activeDB = session('active_db');
        $role = session('role');

        // 2️⃣ switch DB first (before logout)
        if ($activeDB) {
            config(['database.connections.multi.database' => $activeDB]);
            DB::setDefaultConnection('multi');
        }

        // 3️⃣ now logout safely
        if ($role === 'admin') {
            Auth::shouldUse('admin');
            Auth::guard('admin')->logout();
        } elseif ($role === 'staff') {
            Auth::shouldUse('staff');
            Auth::guard('staff')->logout();
        }

        // 4️⃣ finally clear session
        session()->flush();

        return redirect()->route('login');
    }

}