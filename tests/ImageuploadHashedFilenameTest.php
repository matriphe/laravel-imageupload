<?php

use Carbon\Carbon;

class ImageuploadHashedFilenameTest extends AbstractImageuploadTest
{
    /**
     * Use hashed filename as new filename
     *
     * @var mixed
     * @access protected
     */
    protected $newfilename = 'hash';

    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2017-07-23 12:09:02', 'Asia/Jakarta'));
    }

    public function tearDown()
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function additionaAssertion($result)
    {
        $this->assertSame('8d0c9f97bd1b1ae3d85d240f2a7e9c35.jpg', $result['filename']);

        return $this;
    }
}
