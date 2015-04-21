<?php namespace Matriphe\Format;

use Illuminate\Support\ServiceProvider;

class FormatServiceProvider extends ServiceProvider {

  protected $defer = false;

  public function boot()
  {

  }

  public function register()
  {
    $this->app['format'] = $this->app->share(function($app)
    {
      return new Format();
    });
  }

}
