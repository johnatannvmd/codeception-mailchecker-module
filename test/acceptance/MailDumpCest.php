<?php
class MailDumpCest
{
    use \MailChecker\TestKit\BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'MailDump';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
