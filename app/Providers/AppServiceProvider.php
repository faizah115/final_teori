<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tighten\Ziggy\ZiggyServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register Ziggy service provider so Blade directive @routes works
        $this->app->register(ZiggyServiceProvider::class);
    }

    public function boot()
    {
        // No additional boot logic needed for now
    }
}
