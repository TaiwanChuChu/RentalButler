<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // 支援 ngrok 便於測試
        if (!empty(env('NGORK_URL')) && request()->server->has('HTTP_X_FORWARDED_HOST')) {
            $this->app['url']->forceRootUrl(env('NGORK_URL'));
            $this->app['url']->forceScheme('https');
        }
    }
}
