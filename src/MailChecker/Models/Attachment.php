<?php
namespace MailChecker\Models;

class Attachment
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }
}