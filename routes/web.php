<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::match(['GET','POST'], '/login_submit', [LoginController::class, 'login_submit'])->name('login_submit');


// SUPER ADMIN LOGIN + DASHBOARD
Route::get('/superadmin/login', function () {
    return view('superadmin.login');
})->name('superadmin.login');
// SUPER ADMIN
Route::get('/superadmin/company', [SuperAdminController::class, 'index'])->name('company.index');
Route::get('/superadmin/company/create', [SuperAdminController::class, 'create'])->name('company.create');
Route::post('/superadmin/company/store', [SuperAdminController::class, 'store'])->name('company.store');
Route::get('/superadmin/company/edit/{id}', [SuperAdminController::class, 'edit'])->name('company.edit');
Route::get('/superadmin/company/toggle/{id}', [SuperAdminController::class, 'toggleStatus'])
    ->name('company.toggle');
Route::get('/superadmin/company/delete/{id}', [SuperAdminController::class, 'delete'])->name('company.delete');
Route::post('/superadmin/company/update/{id}', [SuperAdminController::class, 'update'])->name('company.update');

Route::post('/superadmin/login/submit', [SuperAdminController::class, 'login_submit'])
    ->name('superadmin.login.submit');

Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])
    ->name('superadmin.dashboard')
    ->middleware('superadmin');
   Route::get('/superadmin/reports', [SuperAdminController::class, 'showReports'])->name('reports');

Route::post('/superadmin/logout', function () {
    session()->forget('superadmin_logged');
    session()->flush();
    return redirect()->route('superadmin.login');
})->name('superadmin.logout');


Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    return "<h3> All caches cleared successfully!</h3>";
});

// ===============================
// ADMIN ROUTES
// ===============================
Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('admin.dashboard');

    // Customer CRUD
    Route::get('customers/list', [CustomerController::class, 'getList'])->name('admin.customers.list');
    Route::post('customers/store', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::delete('customers/destroy/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('customers/show/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/add', [CustomerController::class, 'addCustomer'])->name('admin.customers.add');
    Route::get('customers/edit/{id}', [CustomerController::class, 'editCustomer'])->name('customers.edit');
    Route::post('customers/update/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('customers/update-week/{id}', [CustomerController::class, 'updateWeekPayment'])
        ->name('customers.updateWeek');
    Route::get('staff/search', [CustomerController::class, 'staffSearch'])->name('staff.search');

    // ============================
    // STAFF CRUD (FIXED)
    // ============================
    Route::get('staff', [StaffController::class,'index'])->name('admin.staff_list');
    Route::get('staff/list', [StaffController::class,'list'])->name('admin.staff.list');
    Route::post('staff/store', [StaffController::class,'store'])->name('admin.staff.store');
    Route::get('staff/edit/{id}', [StaffController::class,'edit'])->name('admin.staff.edit');
    Route::post('staff/update/{id}', [StaffController::class,'update'])->name('admin.staff.update');
    Route::delete('staff/destroy/{id}', [StaffController::class,'destroy'])->name('admin.staff.destroy');
    Route::get('staff-report', [App\Http\Controllers\StaffController::class, 'staffReport'])
    ->name('admin.staff.report');
  Route::get('/staff/search', [CustomerController::class, 'staffSearch'])->name('staff.search');




});
    
// ===============================
// ADMIN ROUTES
// ===============================
Route::middleware(['switchdb'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('admin.dashboard');

    // Customer CRUD
    Route::get('customers/list', [CustomerController::class, 'getList'])->name('admin.customers.list');
    Route::post('customers/store', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::delete('customers/destroy/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('customers/show/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/add', [CustomerController::class, 'addCustomer'])->name('admin.customers.add');
    Route::get('customers/edit/{id}', [CustomerController::class, 'editCustomer'])->name('customers.edit');
    Route::post('customers/update/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('customers/update-week/{id}', [CustomerController::class, 'updateWeekPayment'])
        ->name('customers.updateWeek');

    Route::get('/staff/search', [CustomerController::class, 'staffSearch'])->name('staff.search');

    // STAFF CRUD
    Route::get('staff', [StaffController::class,'index'])->name('admin.staff_list');
    Route::get('staff/list', [StaffController::class,'list'])->name('admin.staff.list');
    Route::post('staff/store', [StaffController::class,'store'])->name('admin.staff.store');
    Route::get('staff/edit/{id}', [StaffController::class,'edit'])->name('admin.staff.edit');
    Route::post('staff/update/{id}', [StaffController::class,'update'])->name('admin.staff.update');
    Route::delete('staff/destroy/{id}', [StaffController::class,'destroy'])->name('admin.staff.destroy');
    Route::get('staff-report', [StaffController::class, 'staffReport'])
    ->name('admin.staff.report');
    // Password change
Route::post('/change-password', [UserController::class, 'changePassword'])->name('change.password');

});


Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
});