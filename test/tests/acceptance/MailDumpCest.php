<?php
class MailDumpCest extends BaseMailChecker
{
    protected function getProvider()
    {
        return 'MailDump';
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
