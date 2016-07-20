<?php
namespace Codeception\Module;

use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\FileOptions;
use Zend\Mime\Mime;

class ZendMailerHelper extends BaseMailerHelper
{
    /**
     * @var File
     */
    private $transport;

    public function _init(array $providerConfig)
    {
        $transportOptions = new FileOptions(
            [
                'path' => $providerConfig['options']['path'],
                'callback' => function () use ($providerConfig) {
                    return uniqid() . '.' . $providerConfig['options']['extension'];
                }
            ]
        );
        $this->transport = new File($transportOptions);
    }

    public function sendEmail($from, $to, $cc, $subject, $body, $attachmentFilename)
    {
        $message = new Message();
        $message->setFrom($from);
        $message->setTo($to);
        $message->setCc($cc);
        $message->setSubject($subject);

        $mimeMessage = new \Zend\Mime\Message();

        $part = new \Zend\Mime\Part($body);
        $part->setType(Mime::TYPE_TEXT);
        $part->setCharset('UTF-8');
        $mimeMessage->addPart($part);

        $part = new \Zend\Mime\Part('<p>' . $body . '<p>');
        $part->setType(Mime::TYPE_HTML);
        $part->setCharset('UTF-8');
        $mimeMessage->addPart($part);

        $part = new \Zend\Mime\Part($body);
        $part->setType(Mime::TYPE_OCTETSTREAM);
        $part->setEncoding(Mime::ENCODING_BASE64);
        $part->setFileName($attachmentFilename);
        $part->setDisposition(Mime::DISPOSITION_ATTACHMENT);
        $mimeMessage->addPart($part);

        $message->setBody($mimeMessage);

        $this->transport->send($message);

        $this->debugSection('ZendMailer', $subject . ' ' . $from . ' -> ' . $to);
    }
}
