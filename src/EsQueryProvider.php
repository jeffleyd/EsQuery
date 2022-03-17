<?php

namespace Jeffleyd\EsLikeEloquent;

use Illuminate\Support\ServiceProvider;

abstract class EsQueryProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishFiles();
    }

    protected function publishFiles()
    {
        $this->publishes([
            __DIR__.'/../config/esquery.php' => config_path('esquery.php'),
        ], 'esquery-provider');
    }

}