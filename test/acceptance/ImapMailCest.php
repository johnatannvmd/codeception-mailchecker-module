<?php

/**
 * Class ImapMailCest
 */
class ImapMailCest
{
    use \Codeception\Module\BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'ImapMail';
    }

    protected function getFromAddress()
    {
        return 'admin@0.0.0.0';
    }

    protected function getToFirstAddress()
    {
        return 'first@0.0.0.0';
    }

    protected function getToSecondAddress()
    {
        return 'second@0.0.0.0';
    }

    protected function getToThirdAddress()
    {
        return 'third@0.0.0.0';
    }

    protected function getCcFirstAddress()
    {
        return 'cc-first@0.0.0.0';
    }

    protected function getCcSecondAddress()
    {
        return 'cc-second@0.0.0.0';
    }

    protected function getCcThirdAddress()
    {
        return 'cc-third@0.0.0.0';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
