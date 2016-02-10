<?php
array_map(
    function($item) {
        if(getenv($item) === false) {
            throw new Exception("Environment variable {$item} was not set.");
        }
    },
    [
        'MAIL_SERVICE_HOST', 'MAIL_TRAP_USER', 'MAIL_TRAP_PASSWORD', 'MAIL_TRAP_APITOKEN',
        'IMAP_HOST', 'IMAP_PORT', 'IMAP_USER_FIRST', 'IMAP_USER_SECOND', 'IMAP_USER_THIRD', 'IMAP_PWD',
        'IMAP_SMTP_HOST', 'IMAP_SMTP_PORT', 'IMAP_SMTP_PWD', 'IMAP_SMTP_USER'
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
        'path' => 'tests/_output',
        'extension' => 'eml'
    ]
];

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['ImapMail'] = [
    'options' => [
        'host' => getenv('IMAP_HOST'),
        'port' => getenv('IMAP_PORT'),
        'service' => 'pop3',
        'credentials' => [
            getenv('IMAP_USER_FIRST') => getenv('IMAP_PWD'),
            getenv('IMAP_USER_SECOND') => getenv('IMAP_PWD'),
            getenv('IMAP_USER_THIRD') => getenv('IMAP_PWD'),
        ],
        'flags' => 'novalidate-cert',
    ],
    'smtpHost' => getenv('IMAP_SMTP_HOST'),
    'smtpPort' => getenv('IMAP_SMTP_PORT'),
    'smtpAuth' => [getenv('IMAP_SMTP_USER'), getenv('IMAP_SMTP_PWD')]
];
