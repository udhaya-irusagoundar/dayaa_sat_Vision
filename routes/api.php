<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MobileCustomerController;

// API Routes
Route::post('/company-code', [LoginController::class, 'handleCompanyCode']);
 Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['switch_db_api'])->group(function () {

    Route::get('/debug-db', function(){
        return response()->json([
            "db" => DB::connection()->getDatabaseName(),
            "headers" => request()->headers->all(),
        ]);
    });

   
    Route::get('/customers/search', [MobileCustomerController::class, 'search']);
    Route::get('/customers/details/{id}', [MobileCustomerController::class, 'details']);
    Route::post('/customers/update/{id}', [MobileCustomerController::class, 'updatePayment']);
    Route::get('/today-collections', [MobileCustomerController::class, 'todayCollections']);
});