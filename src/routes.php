<?php
Route::any('matriphe/imageupload', function() 
{
    $data = [];
    
    echo config('imageupload.library');
    
    if (Input::hasFile('file')) {
        $data['result'] = Imageupload::upload(Input::file('file'));
    }
    
    return return View::make('imageupload::form');->with($data);
});