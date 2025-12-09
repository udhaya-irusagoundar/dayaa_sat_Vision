<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function index()
    {
        $companies = DB::table('companies')->orderBy('id','DESC')->get();
        return view('superadmin.company_list', compact('companies'));
    }

    public function create()
    {
        return view('superadmin.create_company');
    }

    public function store(Request $request)
{
    $request->validate([
        'company_name' => 'required|min:3|max:30',
        'company_code' => [
            'required',
            'unique:companies,company_code',
            'regex:/^(?=(?:.*[A-Za-z]){2})(?=(?:.*\d){2})[A-Za-z0-9]{4}$/'
        ],
        'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ], [
        'company_name.min' => 'Company name must be at least 3 characters.',
        'company_name.max' => 'Company name cannot exceed 30 characters.',
        'company_code.regex' => 'Code must contain 2 letters & 2 numbers (Example: A1B2 or AB12 or 12AB)',
    ]);

    $logoName = null;
    if ($request->hasFile('logo')) {
        $logoName = time() . '.' . $request->logo->extension();
        $request->logo->move(public_path('uploads/company_logos'), $logoName);
    }

    $dbName = strtolower("cable_" . $request->company_code);

    DB::table('companies')->insert([
        'company_name' => $request->company_name,
        'company_code' => strtoupper($request->company_code),
        'db_name' => $dbName,
        'status' => 1,
        'logo' => $logoName,   // ⭐ saved here
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Company added successfully!');
}

   public function dashboard()
{
    $companies = DB::table('companies')->get();

    $totalRevenue = 0;

    foreach ($companies as $company) {

        DB::purge("dynamic");

        config([
            "database.connections.dynamic" => [
                "driver"   => "mysql",
                "host"     => env("DB_HOST"),
                "port"     => env("DB_PORT"),
                "database" => $company->db_name,
                "username" => env("DB_USERNAME"),
                "password" => env("DB_PASSWORD"),
                "charset"  => "utf8mb4",
                "collation"=> "utf8mb4_unicode_ci",
            ]
        ]);

        DB::reconnect("dynamic");

        try {
            // sum of collection from each company's customer table
            $companyRevenue = DB::connection("dynamic")
                ->table("customers")
                ->sum("totalAmount");

            $totalRevenue += $companyRevenue;
        } catch (\Exception $e) {
            // ignore if db not exists
            $totalRevenue += 0;
        }
    }

    $totalCompanies = DB::table('companies')->count();
    $activeCompanies = DB::table('companies')->where('status', 1)->count();
    $inactiveCompanies = DB::table('companies')->where('status', 0)->count();

    return view('superadmin.dashboard', compact(
        'totalCompanies', 'activeCompanies', 'inactiveCompanies', 'totalRevenue'
    ));
}
public function login_submit(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $admin = DB::table('admins')
        ->where('username', $request->username)
        ->where('role', 'super_admin')
        ->first();

    if (!$admin) {
        return back()->with('error', 'Invalid Username')->withInput();
    }

    if (!Hash::check($request->password, $admin->password)) {
        return back()->with('error', 'Invalid Password')->withInput();
    }

    // Set session for Super Admin Middleware
    session(['superadmin_logged' => true]);

    return redirect()->route('superadmin.dashboard');
}

public function toggleStatus($id)
{
    $company = DB::table('companies')->where('id', $id)->first();
    $newStatus = $company->status == 1 ? 0 : 1;

    DB::table('companies')->where('id', $id)->update([
        'status' => $newStatus,
        'updated_at' => now()
    ]);

    return back()->with('success', 'Company status updated!');
}


public function delete($id)
{
    DB::table('companies')->where('id', $id)->delete();
    return back()->with('success', 'Company deleted successfully!');
}

public function edit($id)
{
    $company = DB::table('companies')->where('id', $id)->first();
    return view('superadmin.edit_company', compact('company'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'company_name' => 'required|min:3|max:30',
        'company_code' => [
            'required',
            'unique:companies,company_code,' . $id,
            'regex:/^(?=(?:.*[A-Za-z]){2})(?=(?:.*\d){2})[A-Za-z0-9]{4}$/'
        ],
        'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $company = DB::table('companies')->where('id', $id)->first();
    $logoName = $company->logo;

    if ($request->hasFile('logo')) {
        if ($logoName && file_exists(public_path('uploads/company_logos/' . $logoName))) {
            unlink(public_path('uploads/company_logos/' . $logoName));
        }
        $logoName = time() . '.' . $request->logo->extension();
        $request->logo->move(public_path('uploads/company_logos'), $logoName);
    }

    DB::table('companies')->where('id', $id)->update([
        'company_name' => $request->company_name,
        'company_code' => strtoupper($request->company_code),
        'logo' => $logoName,        // ⭐ keep / update
        'updated_at' => now()
    ]);

    return redirect()->route('company.index')->with('success', 'Company updated successfully!');
}

public function showReports(Request $request)
{
    $from = $request->from_date ? \Carbon\Carbon::parse($request->from_date) : null;
    $to   = $request->to_date   ? \Carbon\Carbon::parse($request->to_date)   : null;

    $companies = DB::table('companies')->orderBy('id','DESC')->get();

    foreach ($companies as $company) {

        DB::purge("dynamic");

        config([
            "database.connections.dynamic" => [
                "driver"   => "mysql",
                "host"     => env("DB_HOST"),
                "port"     => env("DB_PORT"),
                "database" => $company->db_name,
                "username" => env("DB_USERNAME"),
                "password" => env("DB_PASSWORD"),
                "charset"  => "utf8mb4",
                "collation"=> "utf8mb4_unicode_ci",
            ]
        ]);

        DB::reconnect("dynamic");

        try {
            $company->total_customers = DB::connection("dynamic")->table("customers")->count();
            $company->total_staff     = DB::connection("dynamic")->table("staff")->count();

            // Case 1 → No date filter → Current month calculation
            if (!$from || !$to) {
                $from = now()->startOfMonth();
                $to   = now()->endOfMonth();
            }

            $customers = DB::connection("dynamic")->table("customers")->get();
            $filtered = 0;

            foreach ($customers as $customer) {

                if (!$customer->progress_json) continue;
                $json = json_decode($customer->progress_json, true);

                $monthCollected = 0;
                $monthWiseLatest = [];

                foreach ($json as $yearKey => $yearData) {
                    if (!isset($yearData['paymentDates'])) continue;

                    foreach ($yearData['paymentDates'] as $monthArr) {
                        if (!is_array($monthArr)) continue;

                        foreach ($monthArr as $payment) {

                            $payDate = \Carbon\Carbon::createFromFormat("d/m/Y", $payment['date']);

                            // always filter based on selected date range
                           if ($payDate->between($from->startOfDay(), $to->endOfDay())) {
                                $monthName = $payment["month"];
                                $dateKey = $payment["date"];
                                // keep only last payment of each date of each month
                                $monthWiseLatest[$monthName][$dateKey] = $payment["amount"];
                            }
                        }
                    }
                }

                // From each month → pick latest DATE → pick latest PAYMENT
                foreach ($monthWiseLatest as $m => $dates) {
                    $latestDate = array_key_last($dates);
                    $latestAmount = $dates[$latestDate]; // take only last payment, no sum
                    $monthCollected += $latestAmount;
                }

                $filtered += $monthCollected;
            }

            $company->total_collected = $filtered;

        } catch (\Exception $e) {

            \Log::error("DB Err for ".$company->db_name." : ".$e->getMessage());
            $company->total_customers = 0;
            $company->total_staff     = 0;
            $company->total_collected = 0;
        }
    }

    return view("superadmin.reports", compact("companies"));
}

}