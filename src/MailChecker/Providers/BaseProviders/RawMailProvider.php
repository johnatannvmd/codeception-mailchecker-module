<?php
namespace MailChecker\Providers\BaseProviders;

use MailChecker\Models\Attachment;
use MailChecker\Models\Body;
use MailChecker\Models\Message;
use PhpMimeMailParser\Parser as MailParser;

trait RawMailProvider
{
    /**
     * @param $rawMessage
     *
     * @return \MailChecker\Models\Message
     */
    protected function getMessage($rawMessage)
    {
        $parser = new MailParser();
        $parser->setText($rawMessage);

        $headers = array_change_key_case($parser->getHeaders(), CASE_LOWER);

        $message = new Message();
        try {
            $message->setDate(new \DateTime($headers['date']));
        } catch (\Exception $e) {
            // Can't recognize date time format
            // TODO add config option for date time format parsing
            $message->setDate(new \DateTime());
        }
        $message->setSubject($headers['subject']);
        $message->setFrom($headers['from']);
        $message->setTo($headers['to']);
        if (isset($headers['cc'])) {
            $message->setCc($headers['cc']);
        }

        foreach ($parser->getParts() as $part) {
            if (in_array($part['content-type'], ['text/plain', 'text/html'])) {
                $body = new Body();
                $body->setContentType($part['content-type']);
                $body->setCharset(isset($part['content-charset']) ? $part['content-charset'] : null);
                $body->setEncoding(isset($part['transfer-encoding']) ? $part['transfer-encoding'] : null);
                $start = $part['starting-pos-body'];
                $end = $part['ending-pos-body'];
                $body->setBody(substr($rawMessage, $start, $end - $start));

                $message->addBody($body);
            }
        }

        /** @var \PhpMimeMailParser\Attachment $messageAttachment */
        foreach ($parser->getAttachments() as $messageAttachment) {
            $attachment = new Attachment();
            $attachment->setType($messageAttachment->getContentType());
            $attachment->setFilename($messageAttachment->getFilename());

            $message->addAttachment($attachment);
        }

        return $message;
    }
}
