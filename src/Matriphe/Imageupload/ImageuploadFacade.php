<?php namespace Matriphe\Imageupload;

use Illuminate\Support\Facades\Facade;

class ImageuploadFacade extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'imageupload';
    }
    
    public function register()
    {
        $this->app->bind('imageupload', function ($app) {
            return new Imageupload;
        });
    }  

}