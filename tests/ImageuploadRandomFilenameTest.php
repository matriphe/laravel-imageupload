<?php


class ImageuploadRandomFilenameTest extends AbstractImageuploadTest
{
    /**
     * Use random filename as new filename
     *
     * @var mixed
     * @access protected
     */
    protected $newfilename = 'random';

    protected function additionaAssertion($result)
    {
        $basename = pathinfo($result['filename'], PATHINFO_FILENAME);

        $this->assertSame(16, strlen($basename));
        $this->assertTrue((bool) preg_match('/[a-zA-Z0-9]{16}/', $basename));

        return $this;
    }
}
