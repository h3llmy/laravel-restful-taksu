<?php

namespace Taksu;

use Illuminate\Support\ServiceProvider;

class RestfulServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'restful-migrations');
    }
}
