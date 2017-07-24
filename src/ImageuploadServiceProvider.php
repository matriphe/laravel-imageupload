<?php

namespace Matriphe\Imageupload;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;

class ImageuploadServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('imageupload.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/2017_07_24_024410_create_image_upload_table.php' => database_path('migrations/2017_07_24_024410_create_image_upload_table.php'),
        ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'imageupload');

        $this->app->singleton('imageupload', function ($app) {
            return new Imageupload(new ImageManager());
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
