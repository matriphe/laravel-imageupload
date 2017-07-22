<?php

namespace Matriphe\Imageupload;

use Illuminate\Support\ServiceProvider;

class ImageuploadServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'imageupload');

        $this->publishes([
            __DIR__.'/config/config.php' => config_path('imageupload.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     */
    public function register()
    {
        $this->app->singleton('imageupload', function ($app) {
            return new Imageupload();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
           
        ];
    }
}
