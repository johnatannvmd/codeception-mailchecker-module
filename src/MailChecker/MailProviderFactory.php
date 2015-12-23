<?php
namespace MailChecker;

use MailChecker\Exceptions\MailProviderNotFoundException;
use MailChecker\Provider\IProvider;

class MailProviderFactory
{
    private static $providers = [
        'MailCatcher' => '\\MailChecker\\Provider\\MailCatcher',
        'MailDump' => '\\MailChecker\\Provider\\MailDump',
        'ZendMail' => '\\MailChecker\\Provider\\ZendMail'
    ];

    /**
     * @param $providerName string one of the self::$providers or FDQN to class which
     *                                 extends \MailChecker\Provider\IProvider
     * @param $config array Module configuration
     *
     * @throws \MailChecker\Exceptions\MailProviderNotFoundException
     *
     * @return \MailChecker\Provider\IProvider
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
                "Provider '{$providerName}' is not instance of MailChecker\\Provider\\IProvider."
            );
        }

        return $providerClass;
    }
}
