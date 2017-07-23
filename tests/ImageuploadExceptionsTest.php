<?php

use Intervention\Image\AbstractDriver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Matriphe\Imageupload\Imageupload;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Matriphe\Imageupload\ImageuploadException;

class ImageuploadExceptionsTest extends TestCase
{
    /**
     * The setUp.
     *
     * @access public
     */
    public function setUp()
    {
        parent::setUp();

        $driver = Mockery::mock(AbstractDriver::class);
        $image = Mockery::mock(Image::class, [
            'save' => $driver,
        ]);
        $this->intervention = Mockery::mock(ImageManager::class, [
            'configure' => ['driver' => 'gd'],
            'make' => $image,
        ]);
        $this->imageupload = new Imageupload($this->intervention);
        $this->uploadedFile = Mockery::mock(UploadedFile::class, [
            'getMimeType' => 'text/plain',
            'getClientOriginalName' => 'test.txt',
            'getRealPath' => '/tmp/test.txt',
            'getClientOriginalExtension' => 'txt',
            'getSize' => 1024, // 1 KB
        ]);
    }

    /**
     * The tearDown.
     *
     * @access public
     */
    public function tearDown()
    {
        Mockery::close();
        
        parent::tearDown();
    }
    
    public function testTargetUploadDirectoryNotExists()
    {
        File::shouldReceive('isDirectory')->atLeast()->times(1)->andReturn(false);
        
        $this->setExpectedException(ImageuploadException::class);
        
        $result = $this->imageupload->upload($this->uploadedFile);
    }
    
    public function testTargetUploadDirectoryNotWriteable()
    {
        File::shouldReceive('isDirectory')->once()->andReturn(true);
        File::shouldReceive('isWritable')->once()->andReturn(false);
        
        $this->setExpectedException(ImageuploadException::class);
        
        $result = $this->imageupload->upload($this->uploadedFile);
    }
    
    public function testCannotCreateTargetUpload()
    {
        File::shouldReceive('isDirectory')->once()->andReturn(false);
        File::shouldReceive('makeDirectory')->once()->andReturn(false);
        
        $this->setExpectedException(ImageuploadException::class);
        
        $result = $this->imageupload->upload($this->uploadedFile);
    }
    
    public function testFileNotImage()
    {
        File::shouldReceive('isDirectory')->once()->andReturn(false);
        File::shouldReceive('makeDirectory')->once()->andReturn(false);
        
        $this->setExpectedException(ImageuploadException::class);
        
        $result = $this->imageupload->upload($this->uploadedFile);
    }
}