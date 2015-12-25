<?php

/**
 * Class LatherDumpCest
 *
 * @property \Codeception\Module\SmtpMailerHelper $mailer
 */
class LatherDumpCest
{
    /**
     * @var string One password to rule all inboxes
     */
    private $password = 'password';

    use BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'LatherMail';
    }

    protected function sendEmails(AcceptanceTester $I)
    {
        $I->clearMailbox();

        /** @var \Swift_SmtpTransport $transport */
        $transport = $this->mailer->getMailer()->getTransport();

        $transport->setUsername('to' . sq(1) . '@othermailbox.com');
        $transport->setPassword($this->password);
        $this->mailer->sendEmail(
            'from_' . sq(1) . '@somemailbox.com',
            'to' . sq(1) . '@othermailbox.com',
            'Subject ' . sq(1),
            'Body ' . sq(1)
        );

        $transport->setUsername('to' . sq(2) . '@othermailbox.com');
        $transport->setPassword($this->password);
        $this->mailer->sendEmail(
            'from_' . sq(2) . '@somemailbox.com',
            'to' . sq(2) . '@othermailbox.com',
            'Subject ' . sq(2),
            'Body ' . sq(2)
        );
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        \Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['LatherMail']['options']['guzzleOptions'] = [
            'headers' => [
                'X-Mail-Password' => $this->password
            ]
        ];

        $this->_baseBefore($mailer);
    }
}
