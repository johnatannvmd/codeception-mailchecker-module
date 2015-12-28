<?php

/**
 * Class MailTrapCest
 *
 * @property \Codeception\Module\SmtpMailerHelper $mailer
 */
class MailTrapCest
{
    use BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'MailTrap';
    }

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);

        // Wait between tests. MailTrap drop frequent requests with "Requested action not taken: too many emails per second"
        sleep(1);
    }
}
