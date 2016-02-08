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
        $security = null;
        if (isset($providerConfig['smtpSecurity'])) {
            $security = $providerConfig['smtpSecurity'];
        }

        $smtpTransport = \Swift_SmtpTransport::newInstance(
            $providerConfig['smtpHost'],
            $providerConfig['smtpPort'],
            $security
        );

        if (isset($providerConfig['smtpAuth'])) {
            $smtpTransport->setUsername($providerConfig['smtpAuth'][0]);
            $smtpTransport->setPassword($providerConfig['smtpAuth'][1]);
        }
        $this->mailer = \Swift_Mailer::newInstance($smtpTransport);
    }

    public function sendEmail($from, $to, $cc, $subject, $body, $attachmentFilename)
    {
        $this->assertNotNull($this->mailer, 'Set provider first via haveMailProvider');

        $message = \Swift_Message::newInstance($subject, $body, 'text/plain');
        $message->addFrom($from);
        $message->addTo($to);
        $message->addPart("<p>{$body}</p>", 'text/html');
        $message->attach(\Swift_Attachment::newInstance($body, $attachmentFilename, 'application/octet-stream'));
        $message->addCc($cc);

        $this->mailer->send($message);

        $this->debugSection('SmtpMailer', $subject . ' ' . $from . ' -> ' . $to);
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }
}
