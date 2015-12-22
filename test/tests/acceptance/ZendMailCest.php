<?php
class ZendMailCest extends BaseMailChecker
{
    protected function getProvider()
    {
        return 'ZendMail';
    }

    public function _before(\Codeception\Module\ZendMailerHelper $mailer)
    {
        parent::_before($mailer);
    }

    protected function sendEmails(AcceptanceTester $I, \Codeception\Module\ZendMailerHelper $mailer)
    {
        parent::sendEmails($I, $mailer);
    }
}
