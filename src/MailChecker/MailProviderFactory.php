<?php
namespace MailChecker;

use MailChecker\Exceptions\MailProviderHasBadInterfaceException;
use MailChecker\Exceptions\MailProviderNotFoundException;
use MailChecker\Providers\IProvider;

class MailProviderFactory
{
    /**
     * @param $providerName string one of the self::$providers or FDQN to class which
     *                             extends \MailChecker\Providers\IProvider
     * @param $config array Module configuration
     *
     * @throws \MailChecker\Exceptions\MailProviderNotFoundException
     * @throws \MailChecker\Exceptions\MailProviderHasBadInterfaceException
     *
     * @return \MailChecker\Providers\IProvider
     */
    public static function getProvider($providerName, $config)
    {
        if (class_exists('\\MailChecker\\Providers\\' . $providerName)) {
            $providerName = '\\MailChecker\\Providers\\' . $providerName;
        }

        if (!class_exists($providerName)) {
            throw new MailProviderNotFoundException("Provider '{$providerName}' not found");
        }

        $providerClass = new $providerName($config);
        if (!($providerClass instanceof IProvider)) {
            throw new MailProviderHasBadInterfaceException(
                "Provider '{$providerName}' should implement MailChecker\\Providers\\IProvider interface"
            );
        }

        return $providerClass;
    }
}
