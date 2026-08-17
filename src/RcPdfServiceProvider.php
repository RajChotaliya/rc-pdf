<?php

namespace RajChotaliya\RcPdf;

use Illuminate\Support\ServiceProvider;

class RcPdfServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rc-pdf.php', 'rc-pdf');

        $this->app->bind('rc-pdf', function ($app) {
            return new RcPdf($app->make('view'), config('rc-pdf', []));
        });

        $this->app->alias('rc-pdf', RcPdf::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rc-pdf');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/rc-pdf.php' => config_path('rc-pdf.php'),
            ], 'rc-pdf-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/rc-pdf'),
            ], 'rc-pdf-views');
        }
    }
}
