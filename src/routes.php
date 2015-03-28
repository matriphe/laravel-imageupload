<?php

Route::any('matriphe/imageupload', function() 
{
    if (Request::hasFile('file')) {
        $result = Imageupload::upload(Request::file('file'));
    }
    
    return view('imageupload::form');
});