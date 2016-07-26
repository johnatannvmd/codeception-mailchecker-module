<?php
namespace Codeception\Module;

use Codeception\Configuration;
use Codeception\Module;

abstract class BaseMailerHelper extends Module
{
    abstract public function _init(array $providerConfig);

    abstract public function sendEmail($from, $to, $cc, $subject, $body, $attachmentFilename);

    final public function haveMailProvider($provider)
    {
        $providers = $this->getProviders();
        $this->assertTrue(isset($providers[$provider]), "Provider {$provider} not found in acceptance suite config");
        $this->getModule('MailChecker')->_reconfigure(array_merge(['provider' => $provider], $providers[$provider]));

        $this->_init($providers[$provider]);
    }

    protected function getProviders()
    {
        return Configuration::suiteSettings('acceptance', Configuration::config())['modules']['config']['providers'];
    }
}
