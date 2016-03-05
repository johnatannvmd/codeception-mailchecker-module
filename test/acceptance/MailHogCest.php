<?php
class MailHogCest
{
    use \MailChecker\TestKit\BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'MailHog';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
