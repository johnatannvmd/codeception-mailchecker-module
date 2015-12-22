<?php
namespace Codeception\Module;

use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\FileOptions;

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

    public function sendEmail($from, $to, $subject, $body)
    {
        $message = new Message();
        $message->setFrom($from);
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setBody($body);

        $this->transport->send($message);
    }
}