<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    // 🔍 Check ADMIN table
    $admin = \App\Models\LoginPage::where('username', $request->username)->first();

    if ($admin) {
        if ($request->password == $admin->password) {
            return response()->json([
                'status' => true,
                'message' => 'Admin login successful',
                'role' => 'admin',
                'data' => [
                    'id' => $admin->id,
                    'username' => $admin->username
                ]
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid password'
        ], 401);
    }

    // 🔍 Check STAFF table
    $staff = \App\Models\Staff::where('username', $request->username)->first();

    if (!$staff) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid username'
        ], 401);
    }

    if (!\Hash::check($request->password, $staff->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid password'
        ], 401);
    }

    return response()->json([
        'status' => true,
        'message' => 'Staff login successful',
        'role' => 'staff',
        'data' => [
            'id' => $staff->id,
            'name' => $staff->name,
            'username' => $staff->username,
            'mobile' => $staff->mobile,
            'password' => $request->password 
        ]
    ], 200);
}

    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}