<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;


class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }
public function boot()
{
    if (session()->has('active_db')) {
        config(['database.connections.multi.database' => session('active_db')]);
        \DB::setDefaultConnection('multi');
    }
}



}