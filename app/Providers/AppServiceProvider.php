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
        if (!empty(config('app.ngrok_url')) && request()->server->has('HTTP_X_FORWARDED_HOST')) {
            $this->app['url']->forceRootUrl(config('app.ngrok_url'));
            $this->app['url']->forceScheme('https');
        }

        // 強制使用 HTTPS
        if (config('app.force_https', false)) {
            $this->app['url']->forceScheme('https');
        }
    }
}
