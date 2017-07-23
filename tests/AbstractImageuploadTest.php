<?php

use Matriphe\Imageupload\Imageupload;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Orchestra\Testbench\TestCase;
use Intervention\Image\ImageManager;
use Intervention\Image\Image;
use Intervention\Image\AbstractDriver;

abstract class AbstractImageuploadTest extends TestCase
{
    /**
     * Set new filename config
     * 
     * @var mixed
     * @access protected
     */
    protected $newfilename;
    
    /**
     * Custom filename for custom config.
     * 
     * @var mixed
     * @access protected
     */
    protected $customFilename;
    
    /**
     * Set thumbnail name using suffix instead of path.
     * 
     * (default value: false)
     * 
     * @var bool
     * @access protected
     */
    protected $suffix = true;
    
    /**
     * The setUp.
     * 
     * @access public
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        
        $driver = Mockery::mock(AbstractDriver::class);
        $image = Mockery::mock(Image::class, [
            'save' => $driver,
            'width' => 1234,
            'height' => 567,
            'exif' => null,
            'filesize' => 1024,
            'fit' => null,
            'resize' => null,
        ]);
        $this->intervention = Mockery::mock(ImageManager::class, [
            'configure' => ['driver' => 'gd'],
            'make' => $image,
        ]);
        $this->imageupload = new Imageupload($this->intervention);
        $this->uploadedFile = Mockery::mock(UploadedFile::class, [
            'getMimeType' => 'image/jpeg',
            'getClientOriginalName' => 'test.jpg',
            'getRealPath' => '/tmp/test.jpg',
            'getClientOriginalExtension' => 'jpg',
            'getSize' => (1024 * 1024 * 1024), // 1 MB
        ]);
    }

    /**
     * The tearDown.
     * 
     * @access public
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * Define environment setup.
     * 
     * @access protected
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('imageupload.dimensions', [
            'square50' => [50, 50, true],
            'square100' => [100, 100, true],
            'size50' => [50, 50, false],
            'size100' => [100, 100, false],
        ]);
        $app['config']->set('imageupload.newfilename', $this->newfilename);
        $app['config']->set('imageupload.suffix', $this->suffix);
    }
    
    /**
     * Invoke method for testing private and public function.
     * 
     * @access protected
     * @param mixed &$object
     * @param string $methodName
     * @param array $parameters (default: array())
     * @return class
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
    
        return $method->invokeArgs($object, $parameters);
    }
    
    /**
     * Mock upload directory upload.
     * 
     * @access protected
     * @return void
     */
    protected function mockTargetUploadPathExistsAndWriteable()
    {
        File::shouldReceive('isDirectory')->atLeast()->times(1)->andReturn(true);
        File::shouldReceive('isWritable')->atLeast()->times(1)->andReturn(true);
        
        return $this;
    }
    
    /**
     * Assert output array.
     * 
     * @access protected
     * @param array $result
     * @return void
     */
    protected function imageuploadResultIsValid($result)
    {
        $this->assertTrue(is_array($result));
        
        $this->assertArrayHasKey('original_filename', $result);
        $this->assertArrayHasKey('original_filepath', $result);
        $this->assertArrayHasKey('original_filedir', $result);
        $this->assertArrayHasKey('original_extension', $result);
        $this->assertArrayHasKey('original_mime', $result);
        $this->assertArrayHasKey('original_filesize', $result);
        $this->assertArrayHasKey('original_width', $result);
        $this->assertArrayHasKey('original_height', $result);
        $this->assertArrayHasKey('exif', $result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('dir', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('basename', $result);
        $this->assertArrayHasKey('dimensions', $result);
        
        return $this;
    }
    
    protected function checkThumbnailPath($result)
    {
        if (empty($result['dimensions'])) {
            return $this;
        }
        
        foreach ($result['dimensions'] as $key => $dimension) {
            $this->assertTrue($this->checkThumbnailSuffix($key, $dimension['filepath'], $result['basename']));
        }
        
        return $this;
    }
    
    /**
     * Check thumb nail path based on suffix.
     * 
     * @access protected
     * @param string $key
     * @param string $filepath
     * @param string $basename
     * @return boolean
     */
    protected function checkThumbnailSuffix($key, $filepath, $basename)
    {
        $filename = pathinfo($filepath, PATHINFO_BASENAME);
        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        
        if ($this->suffix) {
            return ($filename == implode('_', [$basename, $key]).'.'.$extension);
        }
        
        $thumbnailFilename = implode('/', [$key, $filename]);
        $checkedFilepath = substr($filepath, (strlen($thumbnailFilename) * -1));
        
        return ($checkedFilepath == $thumbnailFilename);
    }
    
   /**
     * Set additional mock assertion.
     * 
     * @access protected
     * @return void
     */
    protected function additionaMockAssertion()
    {
        return $this;
    }
    
    /**
     * Set additional assertion.
     * 
     * @access protected
     * @param array $result
     * @return void
     */
    protected function additionaAssertion($result)
    {
        return $this;
    }
    
     /**
     * @test
     */
    public function testImageuploadSuccessReturnArray()
    {
        $this->mockTargetUploadPathExistsAndWriteable();
        $this->additionaMockAssertion();
        
        $result = $this->imageupload->upload($this->uploadedFile, $this->customFilename);
        
        $this->imageuploadResultIsValid($result);
        $this->checkThumbnailPath($result);
        $this->additionaAssertion($result);
        
        //dump($result);
    }
}
