<?php

namespace Matriphe\Imageupload;

use Exception;

class ImageuploadException extends Exception
{
     public function __construct($message, $code = null, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        
        return [
            'is_error' => true,
            'error' => $message,
        ];
    }
}