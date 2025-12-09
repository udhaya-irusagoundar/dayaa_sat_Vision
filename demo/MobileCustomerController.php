<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    // 📌 Get customer full details
    public function details($id)
    {
        $customer = Customers::find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found'], 404);
        }

        return response()->json([
            'status' => true,
            'customer' => [
                'id' => $customer->id,
                'customerNumber' => $customer->customerNumber,
                'name' => $customer->name,
                'mobileNumber' => $customer->mobileNumber,
                'place' => $customer->place,
                'amount' => $customer->amount,
                'weeksPaid' => json_decode($customer->weeksPaid, true),
                'paymentDates' => json_decode($customer->paymentDates, true),
                'totalAmount' => $customer->totalAmount,
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

    // 🔥 Merge new payments month-wise
    foreach ($newPayments as $m => $monthPayments) {
        if (!is_array($monthPayments)) continue;

        if (!isset($existingPayments[$m]) || !is_array($existingPayments[$m])) {
            $existingPayments[$m] = [];
        }

        foreach ($monthPayments as $p) {
            $p['paid_by'] = $paid_by;
            $existingPayments[$m][] = $p;
        }
    }

    // ⭐ Recalculate TOTAL — only latest payment per month
    $finalTotal = 0;
    foreach ($existingPayments as $m => $monthPayments) {
        if (is_array($monthPayments) && count($monthPayments) > 0) {
            $latest = end($monthPayments);
            if (isset($latest['amount'])) {
                $finalTotal += floatval($latest['amount']);
            }
        }
    }

    // Save to DB
    $customer->weeksPaid = json_encode($weeksPaid);
    $customer->paymentDates = json_encode($existingPayments);
    $customer->totalAmount = $finalTotal;
    $customer->last_paid_by = $paid_by;

    // Update progress JSON
    $year = date('Y');
    $progress = json_decode($customer->progress_json, true) ?? [];
    $progress[$year] = [
        "baseAmount" => $customer->amount,
        "weeksPaid" => $weeksPaid,
        "paymentDates" => $existingPayments,
        "totalAmount" => $finalTotal,
        "last_paid_by" => $paid_by,
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
            if (!is_array($monthPay) || count($monthPay) === 0) continue;

            $last = end($monthPay); // 🔥 last payment of that month
            if ($last['date'] === $today && strtolower($last['paid_by']) === $staff) {
                $total += floatval($last['amount']);

                $collections[] = [
                    'customerId' => $c->id,
                    'customerNumber' => $c->customerNumber,
                    'name' => $c->name,
                     'place'=>$c->place,
                    'amount' => $last['amount'],
                    'date' => $last['date'],
                    'paid_by' => $last['paid_by']
                ];
            }
        }
    }

    return response()->json([
        'status' => true,
        'total' => $total,
        'collections' => $collections
    ]);
}

}