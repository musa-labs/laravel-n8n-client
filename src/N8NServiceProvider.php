<?php

namespace JPCaparas\N8N;

use Illuminate\Support\ServiceProvider;

class N8NServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(N8NClient::class, function ($app) {
            return new N8NClient;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/n8n.php' => config_path('n8n.php'),
        ], 'config');
    }
}
