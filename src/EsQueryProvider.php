<?php

namespace Jeffleyd\EsLikeEloquent;

use Illuminate\Support\ServiceProvider;

class EsQueryProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishFiles();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('EsQuery', fn() => new EsQuery);
    }

    protected function publishFiles()
    {
        $this->publishes([
            __DIR__.'/../config/esquery.php' => config_path('esquery.php'),
        ], 'esquery-provider');
    }

}