<?php

namespace Facades;

use Illuminate\Support\Facades\Facade;

class EsQ extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'EsQuery';
    }
}