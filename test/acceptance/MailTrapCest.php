<?php

/**
 * Class MailTrapCest
 *
 * @property \Codeception\Module\SmtpMailerHelper $mailer
 */
class MailTrapCest
{
    use \Codeception\Module\BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'MailTrap';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
