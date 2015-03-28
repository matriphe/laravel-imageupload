<?php namespace Matriphe\Imageupload;

use Illuminate\Support\ServiceProvider;

class ImageuploadServiceProvider extends ServiceProvider {

	protected $defer = false;

	public function boot()
	{
		include __DIR__.'/../../routes.php';
		
		$this->loadViewsFrom(__DIR__.'/../../views', 'imageupload');
		$this->mergeConfigFrom( __DIR__.'/../../config/config.php', 'imageupload');

        $this->publishes([
            __DIR__.'/../../views' => base_path('resources/views/vendor/imageupload'),
            __DIR__.'/../../config/config.php' => config_path('imageupload.php'),
        ]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['imageupload'] = $this->app->share(function($app)
        {
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
		return array();
	}

}
