<?php
namespace MailChecker\Models;

use PhpMimeMailParser\Charset;

class Body
{
    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var string
     */
    private $body;

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = strtolower($charset);
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = strtolower($encoding);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $this->decodeCharset($this->decodeContentTransfer($body));
    }

    private function decodeContentTransfer($input)
    {
        if ($this->getEncoding() == 'base64') {
            return base64_decode($input);
        } elseif ($this->getEncoding() == 'quoted-printable') {
            return quoted_printable_decode($input);
        }

        return $input; //8bit, 7bit, binary
    }

    private function decodeCharset($input)
    {
        if (in_array($this->getCharset(), ['utf-8', 'us-ascii'])) {
            return $input;
        }

        $charset = new Charset();
        return $charset->decodeCharset($input, $this->getCharset());
    }
}
