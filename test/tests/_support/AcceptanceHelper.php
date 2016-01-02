<?php
namespace Codeception\Module;

class AcceptanceHelper extends \Codeception\Module
{
    public function getMailProvider($provider)
    {
        $this->getModule('MailChecker')->_reconfigure(['provider' => $provider]);
    }
}