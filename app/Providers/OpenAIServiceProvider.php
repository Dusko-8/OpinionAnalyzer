<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OpenAIService;
class OpenAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(\App\Services\OpenAIService::class, function ($app) {
            return new OpenAIService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
