<?php
class ZendMailCest
{
    use BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'ZendMail';
    }

    public function _before(\Codeception\Module\ZendMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}