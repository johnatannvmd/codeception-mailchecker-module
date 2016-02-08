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
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $service;

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
        $this->user = $config['options']['user'];
        $this->password = $config['options']['password'];
    }

    private function openMailbox()
    {
        $mailboxResource = imap_open($this->mailbox, $this->user, $this->password);

        if ($mailboxResource === false) {
            throw new MailProviderException('Can not open mailbox: ' . imap_last_error());
        }

        return $mailboxResource;
    }
}
