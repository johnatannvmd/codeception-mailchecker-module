<?php
array_map(
    function ($item) {
        if (getenv($item) === false) {
            throw new Exception("Environment variable {$item} was not set.");
        }
    },
    [
        'MAIL_SERVICE_HOST',
        'MAIL_TRAP_USER',
        'MAIL_TRAP_PASSWORD',
        'MAIL_TRAP_APITOKEN'
    ]
);

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

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['MailHog'] = [
    'options' => [
        'url' => getenv('MAIL_SERVICE_HOST'),
        'port' => '1083'
    ],
    'smtpHost' => '127.0.0.1',
    'smtpPort' => '1028'
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
        'host' => '0.0.0.0',
        'port' => '3993',
        'service' => 'imap',
        'credentials' => [
            'first@0.0.0.0' => 'first@0.0.0.0',
            'second@0.0.0.0' => 'second@0.0.0.0',
            'third@0.0.0.0' => 'third@0.0.0.0',
        ],
        'flags' => 'ssl/novalidate-cert',
    ],
    'smtpHost' => '0.0.0.0',
    'smtpPort' => '3465'
];
