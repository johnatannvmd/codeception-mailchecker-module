<?php
namespace MailChecker\Models;

class Message
{
    /**
     * @var \DateTime
     */
    private $date;

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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

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

    /**
     * @param Body $body
     */
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
        $this->from = $this->parseMailAddresses($from)[0];
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $this->parseMailAddresses($to);
    }

    /**
     * @param string $address
     *
     * @return bool
     */
    public function containsTo($address)
    {
        return in_array($address, $this->to, true);
    }

    /**
     * @return string[]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $cc
     */
    public function setCc($cc)
    {
        $this->cc = $this->parseMailAddresses($cc);
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

    /**
     * @param $email
     * @return array|null
     */
    private function parseMailAddresses($email)
    {
        $fromParse = imap_rfc822_parse_adrlist($email, 'none.co');
        if (!is_array($fromParse) || count($fromParse) < 1) {
            return null;
        }

        $result = [];
        foreach ($fromParse as $email) {
            $result[] = $email->mailbox . '@' . $email->host;
        }

        return $result;
    }
}
