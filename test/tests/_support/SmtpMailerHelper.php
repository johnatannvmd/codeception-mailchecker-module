<?php
namespace Codeception\Module;

class SmtpMailerHelper extends BaseMailerHelper
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function _init(array $providerConfig)
    {
        $smtpTransport = \Swift_SmtpTransport::newInstance($providerConfig['smtpHost'], $providerConfig['smtpPort']);
        if(isset($providerConfig['smtpAuth'])) {
            $smtpTransport->setUsername($providerConfig['smtpAuth'][0]);
            $smtpTransport->setPassword($providerConfig['smtpAuth'][1]);
        }
        $this->mailer = \Swift_Mailer::newInstance($smtpTransport);
    }

    public function sendEmail($from, $to, $subject, $body)
    {
        $this->assertNotNull($this->mailer, 'Set provider first via haveMailProvider');

        $message = \Swift_Message::newInstance($subject, $body);
        $message->addFrom($from);
        $message->addTo($to);

        $this->mailer->send($message);
    }
}