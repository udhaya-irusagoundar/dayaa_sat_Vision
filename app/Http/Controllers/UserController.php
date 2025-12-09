<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginPage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
   public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|confirmed|min:5',
    ]);

    // 1) Make sure DB is switched (via middleware or here)
    $activeDb = session('active_db');
    if (!$activeDb) {
        return response()->json(['status' => 'error', 'message' => 'No active database. Please login again.']);
    }
    config(['database.connections.multi.database' => $activeDb]);
    DB::setDefaultConnection('multi');

    // 2) Get logged-in user
    $userId = session('user_id');  // ⭐ from login session
    if (!$userId) {
        return response()->json(['status' => 'error', 'message' => 'Not logged in.']);
    }

    $user = LoginPage::on('multi')->find($userId);
    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'User not found.']);
    }

    // 3) Check current password (hashed)
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['status' => 'error', 'message' => 'Current password is incorrect.']);
    }

    // 4) Save new hashed password
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['status' => 'success', 'message' => 'Password changed successfully!']);
}
}