<?php

namespace Checkitsedo\Esrpackage\Test;

use Checkitsedo\Esrpackage\EsrpackageFacade;
use Checkitsedo\Esrpackage\EsrpackageServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Checkitsedo\Esrpackage\EsrpackageServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [EsrpackageServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Esrpackage' => EsrpackageFacade::class,
        ];
    }
}
