<?php

use Carbon\Carbon;

class ImageuploadTimestampFilenameTest extends AbstractImageuploadTest
{
    /**
     * Use timestamp filename as new filename
     *
     * @var mixed
     * @access protected
     */
    protected $newfilename = 'timestamp';

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
        $this->assertSame('1500786542.jpg', $result['filename']);

        return $this;
    }
}
