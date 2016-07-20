<?php

/**
 * Class MailCatcherCest
 */
class MailCatcherCest
{
    use \Codeception\Module\BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'MailCatcher';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
