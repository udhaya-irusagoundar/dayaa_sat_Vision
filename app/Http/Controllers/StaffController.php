<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Customers;
use Illuminate\Support\Facades\Hash;
use Validator;

class StaffController extends Controller
{
    // Show staff page
   public function index() {
    return view('staff_list');
}
    // Get list of staff (for AJAX DataTable)
    public function list() {
     $staffs = Staff::orderBy('id', 'desc')->get();

        return response()->json($staffs);
    }

    // Store new staff
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'mobile' => 'required|digits:10',
            'username' => 'required|string|max:50|unique:staff,username',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','errors'=>$validator->errors()]);
        }

        $staff = Staff::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'username' => $request->username,
            'password' => Hash::make($request->password),
              'role' => 'staff'  
        ]);

      return response()->json([
    'status' => 'success',
    'message' => 'Staff saved successfully!',
    'staffList' => Staff::all()
]);

    }

    // Edit staff
    public function edit($id) {
        $staff = Staff::find($id);
        if(!$staff){
            return response()->json(['status'=>'error','message'=>'Staff not found']);
        }
        return response()->json($staff);
    }

    // Update staff
    public function update(Request $request, $id) {
        $staff = Staff::find($id);
        if(!$staff){
            return response()->json(['status'=>'error','message'=>'Staff not found']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'mobile' => 'required|digits:10',
            'username' => 'required|string|max:50|unique:staff,username,'.$staff->id,
            'password' => 'nullable|string|min:6',
             'role' => 'staff'  
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','errors'=>$validator->errors()]);
        }

        $staff->name = $request->name;
        $staff->mobile = $request->mobile;
        $staff->username = $request->username;
        if($request->password){
            $staff->password = Hash::make($request->password);
        }
        $staff->save();

       return response()->json([
    'status' => 'success',
    'message' => 'Staff update successfully!',
    'staffList' => Staff::all()
]);

    }

    // Delete staff
    public function destroy($id) {
        $staff = Staff::find($id);
        if(!$staff){
            return response()->json(['status'=>'error','message'=>'Staff not found']);
        }
        $staff->delete();
        return response()->json(['status'=>'success','message'=>'Staff deleted successfully.']);
    }
 public function staffReport(Request $request)
{
    // Convert dd/mm/yyyy or yyyy-mm-dd to yyyy-mm-dd
    function formatDate($date) {
        if (!$date) return null;
        if (strpos($date, '/') !== false) {
            $parts = explode('/', $date);
            return $parts[2] . "-" . $parts[1] . "-" . $parts[0];
        }
        return $date;
    }

    $from = $request->from;
    $to   = $request->to;

    $customers = \App\Models\Customers::select('paymentDates')->get();
    $staffList = \App\Models\Staff::select('username', 'name')->get()->keyBy('username');

    $report = [];

    foreach ($customers as $customer) {
        $paymentData = json_decode($customer->paymentDates, true);
        if (!is_array($paymentData)) continue;

        foreach ($paymentData as $month) {

            // skip null months
            if (!is_array($month) || count($month) == 0) continue;

            // pick only latest payment inside the month
            $payment = end($month);

            if (!isset($payment['date']) || !isset($payment['amount']) || !isset($payment['paid_by']))
                continue;

            $paymentDate = formatDate($payment['date']);
            $fromDate = formatDate($from);
            $toDate = formatDate($to);

            // if only from given -> treat as exact day
            if ($from && !$to) $toDate = $fromDate;
            if (!$from && $to) $fromDate = $toDate;

            // final date range filter
            if ($fromDate && $toDate) {
                if ($paymentDate < $fromDate || $paymentDate > $toDate) continue;
            }

            $username = strtolower($payment['paid_by']);
            $amount = floatval($payment['amount']);
            $staffName = $staffList[$username]->name ?? ucfirst($username);

            if (!isset($report[$staffName])) {
                $report[$staffName] = 0;
            }

            // latest payment only counted
            $report[$staffName] += $amount;
        }
    }

    arsort($report); // sort by highest amount (optional)
    return view('staff_report', compact('report', 'from', 'to'));
}

}