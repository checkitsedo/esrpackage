<?php

namespace Checkitsedo\Esrpackage;

use Illuminate\Support\Facades\Facade;

class EsrpackageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'esrpackage';
    }
}
