<?php

class ImapMailCest
{
    use \MailChecker\TestKit\BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'ImapMail';
    }

    protected function getFromAddress()
    {
        return 'admin@127.0.0.1';
    }

    protected function getToFirstAddress()
    {
        return 'first@127.0.0.1';
    }

    protected function getToSecondAddress()
    {
        return 'second@127.0.0.1';
    }

    protected function getToThirdAddress()
    {
        return 'third@127.0.0.1';
    }

    protected function getCcFirstAddress()
    {
        return 'cc-first@127.0.0.1';
    }

    protected function getCcSecondAddress()
    {
        return 'cc-second@127.0.0.1';
    }

    protected function getCcThirdAddress()
    {
        return 'cc-third@127.0.0.1';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
