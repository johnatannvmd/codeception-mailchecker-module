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
        sendEmails as _baseSendEmails;
    }

    protected function getProvider()
    {
        return 'LatherMail';
    }

    /**
     * @before clearMailbox
     *
     * @param \AcceptanceTester $I
     */
    public function sendEmails(AcceptanceTester $I)
    {
        /** @var \Swift_SmtpTransport $transport */
        $transport = $this->mailer->getMailer()->getTransport();

        $transport->setUsername($this->getToFirstAddress());
        $transport->setPassword($this->password);

        $this->_baseSendEmails($I);
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
