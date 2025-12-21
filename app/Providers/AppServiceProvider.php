<?php

namespace App\Providers;

use App\Models\LeaveType;
use App\Observers\LeaveTypeObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
    public function boot(): void
    {
        //
        Paginator::useBootstrapFive();

        // Register model observers
        LeaveType::observe(LeaveTypeObserver::class);

        //  if (app()->environment('production', 'local')) {
        //     URL::forceScheme('https');
        // }
    }
}
