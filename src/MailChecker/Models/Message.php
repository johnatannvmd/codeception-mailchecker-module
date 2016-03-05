<?php
namespace MailChecker\Models;

class Message
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var Body[]
     */
    private $body = [];

    /**
     * @var string
     */
    private $from;

    /**
     * @var string[]
     */
    private $to = [];

    /**
     * @var string[]
     */
    private $cc = [];

    /**
     * @var Attachment[]
     */
    private $attachments = [];


    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return Body[]
     */
    public function getBody()
    {
        return $this->body;
    }

    public function addBody(Body $body)
    {
        $this->body[] = $body;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string[]
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string[] $to
     */
    public function setTo(array $to)
    {
        $this->to = $to;
    }

    /**
     * @param string $to
     */
    public function addTo($to)
    {
        $this->to[] = $to;
    }

    /**
     * @param string $address
     *
     * @return bool
     */
    public function containsTo($address)
    {
        foreach ($this->to as $to) {
            if ($to == $address) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string[] $cc
     */
    public function setCc(array $cc)
    {
        $this->cc = $cc;
    }

    /**
     * @param string $cc
     */
    public function addCc($cc)
    {
        $this->cc[] = $cc;
    }

    /**
     * @return \MailChecker\Models\Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return \Generator|string[]
     */
    public function getAttachmentsFilesNames()
    {
        foreach ($this->getAttachments() as $attachment) {
            yield $attachment->getFilename();
        }
    }

    /**
     * @param \MailChecker\Models\Attachment $attachment
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;
    }
}
