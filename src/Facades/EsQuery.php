<?php

namespace Facades;

use Illuminate\Support\Facades\Facade;

class EsQuery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'EsQuery';
    }
}