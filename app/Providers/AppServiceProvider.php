<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth; // 👈 Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // app/Providers/AppServiceProvider.php


    public function boot(): void
    {
        Blade::if('can', function ($roles) {
            if (!Auth::check()) return false;

            $userRole = Auth::user()->role;
            $roles = is_array($roles) ? $roles : func_get_args();

            return in_array($userRole, $roles);
        });
    }
}
