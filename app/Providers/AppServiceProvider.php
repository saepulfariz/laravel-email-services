<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\LdapProvider;
use Laravel\Socialite\Facades\Socialite;

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
        Socialite::extend('ldap', function ($app) {
            $config = $app['config']['services.ldap'];

            return Socialite::buildProvider(LdapProvider::class, $config);
        });

        \Illuminate\Pagination\Paginator::useTailwind();
    }
}
