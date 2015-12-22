<?php
class MailCatcherCest extends BaseMailChecker
{
    protected function getProvider()
    {
        return 'MailCatcher';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        parent::_before($mailer);
    }

    protected function sendEmails(AcceptanceTester $I, \Codeception\Module\SmtpMailerHelper $mailer)
    {
        parent::sendEmails($I, $mailer);
    }
}