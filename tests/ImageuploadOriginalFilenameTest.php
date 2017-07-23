<?php

class ImageuploadOriginalFilenameTest extends AbstractImageuploadTest
{
    /**
     * Use original filename as new filename
     *
     * @var mixed
     * @access protected
     */
    protected $newfilename = 'original';

    protected function additionaAssertion($result)
    {
        $this->assertSame('test.jpg', $result['filename']);

        return $this;
    }
}
