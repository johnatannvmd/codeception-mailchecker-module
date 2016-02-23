<?php
include 'vendor/autoload.php';

function sendEmail()
{
    $smtpTransport = \Swift_SmtpTransport::newInstance('127.0.0.1', 1028);
    $smtpTransport->setUsername('first@127.0.0.1');
    $smtpTransport->setPassword(getenv('IMAP_SMTP_PWD'));
    $mailer = Swift_Mailer::newInstance($smtpTransport);

    $message = Swift_Message::newInstance('test subject', 'some body');
    $message->setFrom('first@localhost');
    $message->setTo('admin@127.0.0.1');

    $mailer->send($message);
}

try {
    sendEmail();
    exit(0);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
