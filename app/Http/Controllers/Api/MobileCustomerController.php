<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Customers;

class MobileCustomerController extends Controller
{
    // 🔍 Search Customer
    public function search(Request $request)
    {
       


        $search = $request->query('search');

        $query = Customers::query();

        if ($search) {
            $query->where('customerNumber', 'LIKE', "%$search%")
                  ->orWhere('name', 'LIKE', "%$search%")
                  ->orWhere('place', 'LIKE', "%$search%");
        }

        $customers = $query->orderBy('customerNumber')->limit(50)->get([
            'id', 'customerNumber', 'name', 'mobileNumber', 'place'
        ]);

        return response()->json(['status' => true, 'customers' => $customers]);
    }

  public function details($id)
{
    $customer = Customers::find($id);

    if (!$customer) {
        return response()->json(['status' => false, 'message' => 'Customer not found'], 404);
    }

    $weeksPaid = json_decode($customer->weeksPaid, true);
    $paymentDates = json_decode($customer->paymentDates, true);

    // Base amount never changes
    $baseAmount = $customer->amount;

    // Month-wise paid values
    $amountPaid = [];
    foreach ($paymentDates as $m => $month) {
        if (is_array($month) && count($month) > 0) {
            $amountPaid[$m] = $month[count($month) - 1]['amount']; // last paid amount of this month
        } else {
            $amountPaid[$m] = null; // unpaid month
        }
    }

    // Calculate total only using latest paid value of each month
    $total = 0;
    foreach ($paymentDates as $month) {
        if ($month !== null && count($month) > 0) {
            $total += floatval($month[count($month) - 1]['amount']);
        }
    }

    return response()->json([
        'status' => true,
        'customer' => [
            'id' => $customer->id,
            'customerNumber' => $customer->customerNumber,
            'name' => $customer->name,
            'mobileNumber' => $customer->mobileNumber,
            'place' => $customer->place,

            // 👇 VERY IMPORTANT — base amount constant
            'amount' => $baseAmount,       

            // 👇 Month-wise updated paid amount
            'amountPaid' => $amountPaid,   

            'weeksPaid' => $weeksPaid,
            'paymentDates' => $paymentDates,
            'totalAmount' => $total
        ]
    ]);
}


public function updatePayment(Request $request, $id)
{
   
    $customer = Customers::find($id);
    if (!$customer) {
        return response()->json(['status' => false, 'message' => 'Customer not found'], 404);
    }

    // Validate staff login
    $staff = \App\Models\Staff::where('username', $request->username)->first();
    if (!$staff || !\Hash::check($request->password, $staff->password)) {
        return response()->json(['status' => false, 'message' => 'Invalid staff username or password'], 401);
    }

    $paid_by = strtolower($staff->username);

    // Decode request values
    $weeksPaid = is_string($request->weeksPaid) ? json_decode($request->weeksPaid, true) : $request->weeksPaid;
    $newPayments = is_string($request->paymentDates) ? json_decode($request->paymentDates, true) : $request->paymentDates;

    // Existing DB payments
    $existingPayments = json_decode($customer->paymentDates, true) ?? [];

    // Merge new payments month-wise
foreach ($newPayments as $m => $monthPayments) {
    if (!is_array($monthPayments)) continue;

    if (!isset($existingPayments[$m]) || !is_array($existingPayments[$m])) {
        $existingPayments[$m] = [];
    }

    foreach ($monthPayments as $p) {
        $existingPayments[$m][] = [
            "month"   => $p["month"],
            "amount"  => $p["amount"],
            "date"    => $p["date"],
            "paid_by" => $paid_by   // 🔥 same as WEB
        ];
    }
}


    // ⭐ Recalculate TOTAL — only latest payment per month
   $finalTotal = 0;
$lastPaidBy = $customer->last_paid_by;

foreach ($existingPayments as $monthPayments) {
    if (is_array($monthPayments) && count($monthPayments) > 0) {
        $lastPayment = end($monthPayments); // last entry
        $finalTotal += floatval($lastPayment["amount"]);
        $lastPaidBy = $lastPayment["paid_by"];
    }
}


    // Save to DB
  $customer->weeksPaid = json_encode($weeksPaid);
$customer->paymentDates = json_encode($existingPayments);
$customer->totalAmount = $finalTotal;
$customer->last_paid_by = $lastPaidBy;

    // Update progress JSON
    $year = date('Y');
    $progress = json_decode($customer->progress_json, true) ?? [];
    $progress[$year] = [
    "baseAmount"   => $customer->amount,
    "weeksPaid"    => $weeksPaid,
    "paymentDates" => $existingPayments,
    "totalAmount"  => $finalTotal,
    "last_paid_by" => $lastPaidBy,
];
$customer->progress_json = json_encode($progress);

    $customer->save();

    return response()->json([
        'status' => true,
        'message' => 'Payment updated successfully',
        'paid_by' => $paid_by,
        'totalAmount' => $customer->totalAmount
    ]);
}
public function todayCollections(Request $request)
{
    $staff = strtolower($request->query('username'));
    if (!$staff) {
        return response()->json(['status' => false, 'message' => 'username required'], 422);
    }

    $customers = Customers::all();
    $today = date('d/m/Y');

    $collections = [];
    $total = 0;

    foreach ($customers as $c) {

        $payments = json_decode($c->paymentDates, true);
        if (!is_array($payments)) continue;

        foreach ($payments as $monthPay) {

            if (!is_array($monthPay) || count($monthPay) == 0) continue;

            // ⭐ pick last payment of this month
            $lastPayment = end($monthPay);

            // format staff names to lowercase
            $paidBy = strtolower($lastPayment['paid_by'] ?? '');

            // ⭐ check ONLY today's last payment
            if ($lastPayment['date'] === $today && $paidBy === $staff) {

                $amount = floatval($lastPayment['amount']);
                $total += $amount;

                $collections[] = [
                    'customerId'      => $c->id,
                    'customerNumber'  => $c->customerNumber,
                    'name'            => $c->name,
                    'place'           => $c->place,
                    'amount'          => $amount,
                    'date'            => $lastPayment['date'],
                    'paid_by'         => $lastPayment['paid_by']
                ];
            }
        }
    }

    return response()->json([
        'status'      => true,
        'total'       => $total,
        'collections' => $collections
    ]);
}

}