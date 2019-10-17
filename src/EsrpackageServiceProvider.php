<?php

namespace Checkitsedo\Esrpackage;

use Illuminate\Support\ServiceProvider;

class EsrpackageServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Esrpackage::class, function () {
            return new Esrpackage();
        });

        $this->app->alias(Esrpackage::class, 'esrpackage');
    }
}
