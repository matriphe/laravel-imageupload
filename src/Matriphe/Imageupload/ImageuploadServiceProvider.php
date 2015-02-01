<?php namespace Matriphe\Imageupload;

use Illuminate\Support\ServiceProvider;

class ImageuploadServiceProvider extends ServiceProvider {

	protected $defer = false;

	public function boot()
	{
		$this->package('matriphe/imageupload');
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
