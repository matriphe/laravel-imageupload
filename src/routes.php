<?php

Route::any('matriphe/imageupload', function() 
{
    $data = [];
    
    echo config('imageupload.library');
    
    if (Request::hasFile('file')) {
        $data['result'] = Imageupload::upload(Request::file('file'));
    }
    
    return view('imageupload::form')->with($data);
});