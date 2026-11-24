<?php

namespace App\Providers;

use App\Contracts\CartTokenGenerator;
use App\Support\UuidCartTokenGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartTokenGenerator::class, UuidCartTokenGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
