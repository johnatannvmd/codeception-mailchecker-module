<?php
namespace MailChecker;

use MailChecker\Exceptions\MailProviderNotFoundException;
use MailChecker\Providers\IProvider;

class MailProviderFactory
{
    private static $providers = [
        'MailCatcher' => '\\MailChecker\\Providers\\MailCatcher',
        'MailDump' => '\\MailChecker\\Providers\\MailDump',
        'ZendMail' => '\\MailChecker\\Providers\\ZendMail'
    ];

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
        if (isset(self::$providers[$providerName])) {
            return new self::$providers[$providerName]($config);
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
