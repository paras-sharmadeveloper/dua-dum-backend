<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * Access is actually enforced by the 'auth.basic' middleware in
     * config/horizon.php, which checks credentials against the existing
     * `users` table before a request ever reaches this gate — so anyone who
     * got this far already authenticated as a real admin user.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn () => true);
    }
}
