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

        $headers = array_change_key_case($parser->getHeaders());

        $message = new Message();
        $message->setDate(new \DateTime($headers['date']));
        $message->setSubject($headers['subject']);
        $message->setFrom($headers['from']);
        if (is_array($headers['to'])) {
            foreach ($headers['to'] as $to) {
                $message->addTo($to);
            }
        } else {
            $message->addTo($headers['to']);
        }

        if (is_array($headers['cc'])) {
            foreach ($headers['cc'] as $cc) {
                $message->addCc($cc);
            }
        } else {
            $message->addCc($headers['cc']);
        }

        foreach ($parser->parts as $part) {
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
            $attachment->setId($messageAttachment->getContentID());
            $attachment->setType($messageAttachment->getContentType());
            $attachment->setFilename($messageAttachment->getFilename());

            $message->addAttachment($attachment);
        }

        return $message;
    }
}
