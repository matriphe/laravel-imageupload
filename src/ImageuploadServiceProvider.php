<?php

namespace Matriphe\Imageupload;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;
use Carbon\Carbon;

class ImageuploadServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('imageupload.php'),
        ], 'config');
        
        if (! class_exists('CreateImageUploadTable')) {
            $stub = '/../database/migrations/create_image_upload_table.php.stub';
            $time = Carbon::now()->format('Y_m_d_His');
            $migrationFile = database_path('migrations/'.$time.'_create_image_upload_table.php');
            
            $this->publishes([__DIR__.$stub => $time], 'migrations');
        }
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
