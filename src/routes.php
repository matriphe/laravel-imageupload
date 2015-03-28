<?php
Route::any('matriphe/imageupload', function() 
{
    $data = [];
    
    if (Input::hasFile('file')) {
        $data['result'] = Imageupload::upload(Input::file('file'));
    }
    
    return View::make('imageupload::form')->with($data);
});