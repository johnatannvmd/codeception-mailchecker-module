<?php
namespace MailChecker;

use MailChecker\Exceptions\MailProviderNotFoundException;
use MailChecker\Providers\IProvider;

class MailProviderFactory
{
    /**
     * @param $providerName string one of the self::$providers or FDQN to class which
     *                                 extends \MailChecker\Providers\IProvider
     * @param $config array Module configuration
     *
     * @throws \MailChecker\Exceptions\MailProviderNotFoundException
     *
     * @return \MailChecker\Providers\IProvider
     */
    public static function getProvider($providerName, $config)
    {
        $providerClass = '\\MailChecker\\Providers\\' . $providerName;
        if (class_exists('\\MailChecker\\Providers\\' . $providerName)) {
            return new $providerClass($config);
        }

        if (!class_exists($providerName)) {
            throw new MailProviderNotFoundException("Provider '{$providerName}' not found.");
        }

        $providerClass = new $providerName($config);
        if (!($providerClass instanceof IProvider)) {
            throw new MailProviderNotFoundException(
                "Provider '{$providerName}' is not instance of MailChecker\\Providers\\IProvider."
            );
        }

        return $providerClass;
    }
}
