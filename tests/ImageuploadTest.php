<?php 

use Mockery as m;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Matriphe\Imageupload\Imageupload;

class ImageuploadTest extends PHPUnit_Framework_TestCase {

    public function testBasicUpload()
    {
        $request = m::mock(Request::class, [
            'hasFile' => true
        ]);
        $file = m::mock(UploadedFile::class, [
            'getClientOriginalName' => 'test.jpg'
        ]);
        $upload = new Imageupload;
        
        $file->shouldReceive('move')
            ->once()
            ->with($upload->uploadpath, 'test.jpg');
    }


}