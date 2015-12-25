<?php
require_once 'acceptance/BaseMailChecker.php';
require_once '_support/BaseMailerHelper.php';

date_default_timezone_set('Etc/GMT');

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

\Codeception\Configuration::$defaultSuiteSettings['modules']['config']['providers']['ZendMail'] = [
    'options' => [
        'path' => 'tests/_output',
        'extension' => 'eml'
    ]
];
