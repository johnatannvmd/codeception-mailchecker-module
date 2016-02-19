<?php
array_map(
    function($item) {
        if(getenv($item) === false) {
            throw new Exception("Environment variable {$item} was not set.");
        }
    },
    [
        'MAIL_SERVICE_HOST', 'MAIL_TRAP_USER', 'MAIL_TRAP_PASSWORD', 'MAIL_TRAP_APITOKEN',
        'IMAP_PWD', 'IMAP_SMTP_PWD'
    ]
);

require_once 'acceptance/BaseMailChecker.php';
require_once '_support/BaseMailerHelper.php';

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers'] = [];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['MailDump'] = [
    'options' => [
        'url' => getenv('MAIL_SERVICE_HOST'),
        'port' => '1080'
    ],
    'smtpHost' => '127.0.0.1',
    'smtpPort' => '1025'
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['MailCatcher'] = [
    'options' => [
        'url' => getenv('MAIL_SERVICE_HOST'),
        'port' => '1081'
    ],
    'smtpHost' => '127.0.0.1',
    'smtpPort' => '1026'
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['LatherMail'] = [
    'options' => [
        'url' => getenv('MAIL_SERVICE_HOST'),
        'port' => '1082'
    ],
    'smtpHost' => '127.0.0.1',
    'smtpPort' => '1027'
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['MailTrap'] = [
    'options' => [
        'url' => 'https://mailtrap.io',
        'port' => '443',
        'apiToken' => getenv('MAIL_TRAP_APITOKEN'),
        'defaultInbox' => 'Demo inbox'
    ],
    'smtpHost' => 'mailtrap.io',
    'smtpPort' => '2525', // for cram-md5
    'smtpAuth' => [getenv('MAIL_TRAP_USER'), getenv('MAIL_TRAP_PASSWORD')]
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['ZendMail'] = [
    'options' => [
        'path' => 'test/_output',
        'extension' => 'eml'
    ]
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['ImapMail'] = [
    'options' => [
        'host' => '127.0.0.1',
        'port' => '4143', // 4110 for pop3
        'service' => 'imap',
        'credentials' => [
            'first@127.0.0.1' => getenv('IMAP_PWD'),
            'second@127.0.0.1' => getenv('IMAP_PWD'),
            'third@127.0.0.1' => getenv('IMAP_PWD'),
        ],
        'flags' => 'novalidate-cert',
    ],
    'smtpHost' => '127.0.0.1',
    'smtpPort' => '1028',
    'smtpAuth' => ['admin@127.0.0.1', getenv('IMAP_SMTP_PWD')]
];
