<?php
 
namespace App\Http\Controllers; 
use Illuminate\Http\Request;
use App\Models\Customers;
use Illuminate\Support\Facades\DB;
 
 
class CustomerController extends Controller
{
    // Return the blade view for dashboard
  public function dashboard(Request $request)
{
    // When coming from redirect, keep the ?place=Panapatti filter active
    if ($request->has('place')) {
        $place = $request->query('place');
        if ($place === '' || $place === null) {
            session()->forget('filter_place');
        } else {
            session(['filter_place' => $place]);
        }
    }

    return view('dashboard');
}
    public function edit()
    {
          $this->switchToCompanyDB();
        return view('edit-customer'); // Blade for editing customer
    }
   public function editCustomer($id)
{
      $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;  // 🔥 STOP if redirect

    $customer = Customers::findOrFail($id);

    $year = request()->get('year', date('Y'));

    // Load progress_json
    $progress = json_decode($customer->progress_json, true) ?? [];

    // If selected year missing → create empty 12 months
    if (!isset($progress[$year])) {
        $progress[$year] = [
            "weeksPaid" => array_fill(0, 12, false),
            "paymentDates" => array_fill(0, 12, null),
            "totalAmount" => 0
        ];
    }

    // Give JS only this year's data
    $customer->weeksPaid = $progress[$year]['weeksPaid'];
    $customer->paymentDates = $progress[$year]['paymentDates'];
    $customer->totalAmount = $progress[$year]['totalAmount'];

    return view('edit-customer', compact('customer'));
}


 public function update(Request $request, $id)
{
      $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;  // 🔥 STOP if redirect

    try {

        $data = json_decode($request->getContent(), true);

        // ------------------- Validation -------------------
        $validated = validator($data, [
            'customerNumber' => 'required|size:16|regex:/^[A-Z0-9]+$/|unique:customers,customerNumber,' . $id,
            'name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'place' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'mobileNumber' => 'required|digits:10',
        ])->validate();

        $customer = Customers::findOrFail($id);

        // ------------------- Load Progress JSON -------------------
        $progress = json_decode($customer->progress_json, true) ?? [];
        $year = $data['year'] ?? date('Y');

        // If year is missing → initialize
        if (!isset($progress[$year])) {
            $progress[$year] = [
                "baseAmount" => $customer->amount ?? 0,
                "weeksPaid" => array_fill(0, 12, false),
                "paymentDates" => array_fill(0, 12, null),
                "totalAmount" => 0,
                "last_paid_by" => null
            ];
        }

        // ------------------- Update Weekly Paid Status -------------------
        $progress[$year]['weeksPaid'] = json_decode($data['weeksPaid'], true);

        // ------------------- Update Payment Dates -------------------
        $progress[$year]['paymentDates'] = json_decode($data['paymentDates'], true);

$paymentDates = $progress[$year]['paymentDates'];

$today = date('d/m/Y');
$currentUser = session('role') === 'admin' ? 'admin' : session('username');

foreach ($paymentDates as $monthIndex => $monthData) {
    if (is_array($monthData)) {
        foreach ($monthData as $i => $p) {

            $payDate = $p['date'] ?? null;
            $oldPaidBy = $p['paid_by'] ?? null;
            $paymentDates[$monthIndex][$i]['paid_by'] = $currentUser;

        }
    }
}

$progress[$year]['paymentDates'] = $paymentDates;


        // ------------------- Update Total Amount -------------------
        $progress[$year]['totalAmount'] = $data['totalAmount'] ?? 0;

        // ------------------- Save last paid by -------------------
     // Update last_paid_by only when a new payment is added
//if ($progress[$year]['paymentDates'] != json_decode($customer->paymentDates, true)) {
    //$progress[$year]['last_paid_by'] = $currentUser;
//}
$progress[$year]['last_paid_by'] = $currentUser;

        // ------------------- ADMIN ONLY: Update base amount -------------------
        if (session('role') === 'admin') {
            $progress[$year]['baseAmount'] = $data['baseAmount'] ?? $progress[$year]['baseAmount'];
            $customer->amount = $data['baseAmount'] ?? $customer->amount;
        }

        // ------------------- Save to DB -------------------
        $customer->customerNumber = $validated['customerNumber'];
        $customer->name = $validated['name'];
        $customer->mobileNumber = $validated['mobileNumber'];
        $customer->place = $validated['place'];

        $customer->progress_json = json_encode($progress);
        $customer->totalAmount = $progress[$year]['totalAmount'];
        $customer->weeksPaid = json_encode($progress[$year]['weeksPaid']);
        $customer->paymentDates = json_encode($progress[$year]['paymentDates']);
       $customer->last_paid_by = $progress[$year]['last_paid_by'] ?? $customer->last_paid_by;
// BACKUP — only new payments (no duplicates)
// 1) TAKE OLD BEFORE UPDATE
$oldProgress = json_decode($customer->progress_json, true) ?? [];
$oldPayments = $oldProgress[$year]['paymentDates'] ?? [];
$newPayments = $progress[$year]['paymentDates'];

// 2) INSERT ONLY NEW PAYMENTS IN BACKUP TABLE
foreach ($newPayments as $monthIndex => $payments) {
    if (is_array($payments)) {
        foreach ($payments as $p) {

            $exists = false;

            if (isset($oldPayments[$monthIndex])) {
                foreach ($oldPayments[$monthIndex] as $op) {
                    if ($op['amount'] == $p['amount'] && $op['date'] == $p['date']) {
                        $exists = true;
                        break;
                    }
                }
            }

            if (!$exists) {
                DB::table('customer_payment_logs')->insert([
                    'customer_id'       => $customer->id,
                    'customer_name'     => $customer->name,
                    'customer_boxnumber'=> $customer->customerNumber,
                    'year'              => $year,
                    'month'             => $p['month'],
                    'amount'            => $p['amount'],
                    'date'              => date('Y-m-d', strtotime($p['date'])),
                    'paid_by'           => $p['paid_by'] ?? $currentUser,
                    'created_at'        => now(),
                ]);
            }
        }
    }
}
    

        $customer->save();   // ⭐ FINAL SAVE

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'redirect' => route('admin.dashboard') . '?place=' . urlencode($validated['place']),
        ]);

    } catch (\Exception $e) {

        \Log::error("Update Error: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Server error'], 500);
    }
}
  // -------------------- Store --------------------
 public function store(Request $request)
{
    $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;

    try {
        $validated = $request->validate([
            'customerNumber' => [
                'required',
                'unique:customers,customerNumber',
                'min:16',
                'max:16',
                'regex:/^[A-Z0-9]+$/'
            ],
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'mobileNumber' => ['required', 'digits:10'],
            'place' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'amount' => ['required', 'numeric', 'min:1']
        ]);

        $year = date('Y');

        // 🔹 Arrays with correct format
        $weeksArray        = array_fill(0, 12, false);
        $paymentDatesArray = array_fill(0, 12, null); // ⭐ REQUIRED FIX

        // 🔹 Progress JSON for multi-year
        $progress = [
            $year => [
                "baseAmount"   => $validated['amount'],
                "weeksPaid"    => $weeksArray,
                "paymentDates" => $paymentDatesArray,
                "totalAmount"  => 0,
                "last_paid_by" => 'admin'
            ]
        ];

        $customer = Customers::create([
            'customerNumber' => $validated['customerNumber'],
            'name'           => $validated['name'],
            'mobileNumber'   => $validated['mobileNumber'],
            'place'          => $validated['place'],
            'amount'         => $validated['amount'],

            'progress_json'  => json_encode($progress),
            'weeksPaid'      => json_encode($weeksArray),
            'paymentDates'   => json_encode($paymentDatesArray),
            'totalAmount'    => 0,
            'last_paid_by'   => 'admin',
        ]);

        return response()->json([
            'status'   => 'success',
            'customer' => $customer
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'validation_error',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        \Log::error('Customer Store Error: ' . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function destroy($id)
{
      $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;  // 🔥 STOP if redirect

    $customer = Customers::find($id);
    if ($customer) {
        $customer->delete();
        return response()->json(['status' => 'success']);
    }
    return response()->json(['status' => 'error']);
}
public function getList(Request $request)
{
      $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;  // 🔥 STOP if redirect

    try {
        $place = $request->query('place');

        if ($place === null || $place === '' || strtolower($place) === 'all') {
            session()->forget('filter_place');
            $selectedPlace = '';
        } else {
            session(['filter_place' => $place]);
            $selectedPlace = $place;
        }

        $query = Customers::orderBy('id', 'desc');

        if (!empty($selectedPlace)) {
            $query->where('place', $selectedPlace);
        }

        $customers = $query->get()->map(function ($c) {

            // ---------------------------
            // READ progress_json properly
            // ---------------------------
          $year = request()->get('year', date('Y'));
 // current year
            $progress = json_decode($c->progress_json, true) ?? [];

            // If this year missing → create default
            if (!isset($progress[$year])) {
                $progress[$year] = [
                    "weeksPaid" => array_fill(0, 12, false),
                    "paymentDates" => array_fill(0, 12, null),
                    "totalAmount" => 0,
                    "last_paid_by" => $c->last_paid_by
                ];
            }

            $weeksPaid = $progress[$year]['weeksPaid'];
            $paymentDates = $progress[$year]['paymentDates'];
            $totalAmount = $progress[$year]['totalAmount'];

            // ⭐ FIX: INJECT paid_by FOR ALL PAYMENTS
            foreach ($paymentDates as $monthIndex => $monthPayments) {

                if (is_array($monthPayments)) {
                    foreach ($monthPayments as $i => $p) {

                        if (!isset($p['paid_by']) || empty($p['paid_by'])) {
                            $paymentDates[$monthIndex][$i]['paid_by'] =
                                $progress[$year]['last_paid_by']
                                ?? $c->last_paid_by
                                ?? 'admin';
                        }
                    }
                }
            }

            // ---------------------------
            // Month progress calculation
            // ---------------------------
            $paidMonths = count(array_filter($weeksPaid));
            $weekProgress = round(($paidMonths / 12) * 100);

            return [
                'id' => $c->id,
                'customerNumber' => $c->customerNumber,
                'name' => $c->name,
                'mobileNumber' => $c->mobileNumber,
                'place' => $c->place,

                'weeksPaid' => $weeksPaid,
                'paymentDates' => $paymentDates, // ⭐ FIXED ARRAY SENT TO FRONTEND
                'week_progress' => $weekProgress,
                'paidWeeks' => $paidMonths,

                'totalAmount' => $totalAmount,

                'last_paid_by' => $progress[$year]['last_paid_by']
                    ?? $c->last_paid_by
                    ?? null,

                'created_at' => $c->created_at,
            ];
        });

        return response()->json([
            'customers' => $customers,
            'selectedPlace' => $selectedPlace,
        ]);

    } catch (\Exception $e) {
        \Log::error('Customer List Error: ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

public function staffSearch(Request $request)
{
     $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;  // 🔥 STOP if redirect

   // dd(session()->all());
    $search = $request->get('search');

    $query = Customers::query();

    if ($search) {
        $query->where('customerNumber', 'LIKE', "%$search%")
              ->orWhere('name', 'LIKE', "%$search%")
              ->orWhere('place', 'LIKE', "%$search%");
    }

    $customers = $query->orderBy('customerNumber')->limit(50)->get();

    return view('staff.search', compact('customers', 'search'));
}

public function addCustomer() {
    return view('admin.add-customer'); // if you have a separate add page
}
 
public function show($id) {
       $dbCheck = $this->switchToCompanyDB();
    if ($dbCheck) return $dbCheck;  // 🔥 STOP if redirect

    $customer = Customers::findOrFail($id);
    return response()->json($customer);
}
private function switchToCompanyDB()
{
    $db = session('active_db');

    if (!$db) {
        return redirect()->route('login')->with('error', 'Session expired, please login again.');
    }

    DB::purge('mysql');
    config(['database.connections.mysql.database' => $db]);
    DB::reconnect('mysql');
}


 
}