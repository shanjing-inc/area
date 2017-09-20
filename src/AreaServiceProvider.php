<?php

namespace Temporaries\Area;

use Illuminate\Support\ServiceProvider;

class AreaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            Console\GenerateCommand::class,
        ]);
    }
}