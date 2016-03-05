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
        $message->setSubject($headers['subject']);
        $message->setFrom($headers['from']);

        if (strpos($headers['to'], ', ') !== false) {
            $headers['to'] = explode(', ', $headers['to']);
        }
        if (is_array($headers['to'])) {
            $message->setTo($headers['to']);
        } else {
            $message->addTo($headers['to']);
        }

        if (strpos($headers['cc'], ', ') !== false) {
            $headers['cc'] = explode(', ', $headers['cc']);
        }
        if (is_array($headers['cc'])) {
            $message->setCc($headers['cc']);
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
            $attachment->setFilename($messageAttachment->getFilename());

            $message->addAttachment($attachment);
        }

        return $message;
    }
}
