<?php

class ImageuploadCustomFilenameTest extends AbstractImageuploadTest
{
    /**
     * Use custom filename as new filename
     *
     * @var mixed
     * @access protected
     */
    protected $newfilename = 'custom';

    protected $customFilename = 'custom_file_name';

    protected function additionaAssertion($result)
    {
        $basename = pathinfo($result['filename'], PATHINFO_FILENAME);

        $this->assertSame($this->customFilename.'.jpg', $result['filename']);

        return $this;
    }
}
