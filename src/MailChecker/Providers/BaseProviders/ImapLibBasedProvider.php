<?php
namespace MailChecker\Providers\BaseProviders;

use MailChecker\Exceptions\MailProviderException;

trait ImapLibBasedProvider
{
    /**
     * @var string
     */
    private $mailbox;

    /**
     * @var string
     */
    private $service;

    /**
     * @var string[]
     */
    private $credentials = [];

    public function __construct(array $config)
    {
        if (isset($config['options']['service']) && $config['options']['service'] == 'pop3') {
            $this->service = '/pop3';
        } else {
            $this->service = '/imap';
        }

        if (isset($config['options']['flags']) && $config['options']['flags'] !== null) {
            $flags = '/' . $config['options']['flags'];
        } else {
            $flags = '';
        }

        if (isset($config['options']['folder'])) {
            $folder = '.' . $config['options']['folder'];
        } else {
            $folder = '';
        }

        $this->mailbox = "{{$config['options']['host']}:{$config['options']['port']}{$this->service}{$flags}}INBOX{$folder}";

        $this->credentials = $config['options']['credentials'];
    }

    /**
     * @param $email
     *
     * @return resource
     * @throws \MailChecker\Exceptions\MailProviderException
     */
    private function openMailbox($email)
    {
        if (!isset($this->credentials[$email])) {
            throw new MailProviderException("Email address: '{$email}' does not found in credentials config");
        }

        $mailboxResource = imap_open($this->mailbox, $email, $this->credentials[$email]);

        if ($mailboxResource === false) {
            throw new MailProviderException('Can not open mailbox: ' . imap_last_error());
        }

        return $mailboxResource;
    }
}
