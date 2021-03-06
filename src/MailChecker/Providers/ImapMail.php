<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Exceptions\MessageNotFoundException;
use MailChecker\Models\Message;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Config example:
 *
 * ```
 * MailChecker:
 *   provider: 'ImapMail'
 *   options:
 *     host: '0.0.0.0'
 *     port: '993'
 *     service: 'imap'|'pop3' # optional. Default: imap
 *     credentials:
 *       username1: password1
 *       username2: password2
 *     folder: 'test' # optional. Default: ''
 *     flags: string # optional. Default: '' Flags from http://php.net/manual/ru/function.imap-open.php
 *                                           without leading slash, i.e. 'novalidate-cert'
 * ```
 *
 * @package MailChecker\Providers
 */
class ImapMail implements IProvider
{
    use RawMailProvider;

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

        $this->mailbox = "{{$config['options']['host']}:{$config['options']['port']}" .
            "{$this->service}{$flags}}{$folder}";

        $this->credentials = $config['options']['credentials'];
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        foreach ($this->credentials as $email => $password) {
            $this->clearMailBox($this->openMailbox($email));
        }
    }

    /**
     * Count messages from all mailboxes
     *
     * @inheritdoc
     */
    public function messagesCount()
    {
        $total = 0;

        foreach ($this->credentials as $email => $password) {
            $mailboxResource = $this->openMailbox($email);

            $searchResult = imap_search($mailboxResource, 'ALL', SE_UID);
            if ($searchResult !== false) {
                $total += count($searchResult);
            }
        }

        return $total;
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        $messages = $this->messages($address);
        if (empty($messages)) {
            throw new MessageNotFoundException();
        }

        return $messages[0];
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        $messages = $this->messages();
        if (empty($messages)) {
            throw new MessageNotFoundException();
        }

        return $messages[0];
    }

    /**
     * Collect messages from all mailboxes
     *
     * @param string|null $to
     *
     * @return \MailChecker\Models\Message[]
     * @throws \MailChecker\Exceptions\MailProviderException
     */
    private function messages($to = null)
    {
        $messages = [];

        foreach ($this->credentials as $email => $password) {
            if (!is_null($to) && $email != $to) {
                continue;
            }

            $mailboxResource = $this->openMailbox($email);

            $messages = array_merge($messages, $this->parseMessages($mailboxResource));
        }

        usort($messages, [$this, 'messageSortByDate']);

        return $messages;
    }

    /**
     * @param $mailboxResource
     * @return array
     */
    private function parseMessages($mailboxResource)
    {
        return array_map(function ($messageId) use ($mailboxResource) {
            return $this->getMessage(
                imap_fetchheader($mailboxResource, $messageId, FT_UID) .
                "\r\n\r\n" .
                imap_body($mailboxResource, $messageId, FT_UID | FT_PEEK)
            );
        }, imap_sort($mailboxResource, SORTARRIVAL, 0, SE_UID, 'ALL')); // Always return array
    }

    /**
     * @param resource $mailboxResource
     *
     * @throws \MailChecker\Exceptions\MailProviderException
     */
    private function clearMailBox($mailboxResource)
    {
        $messages = imap_search($mailboxResource, 'ALL', SE_UID);
        if (empty($messages)) {
            return;
        }

        foreach ($messages as $messageId) {
            if ($this->service == 'pop3') {
                $status = imap_setflag_full($mailboxResource, $messageId, '\\Deleted', ST_UID);
            } else {
                $status = imap_delete($mailboxResource, $messageId, FT_UID);
            }

            if ($status === false) {
                throw new MailProviderException('Can not set "Delete" flag: ' . imap_last_error());
            }
        }

        $status = imap_expunge($mailboxResource);

        if ($status === false) {
            throw new MailProviderException('Can not expunge mailbox: ' . imap_last_error());
        }
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

        imap_timeout(IMAP_OPENTIMEOUT, 5);
        codecept_debug('Open email box: ' . $this->mailbox);
        $mailboxResource = imap_open($this->mailbox, $email, $this->credentials[$email], OP_SILENT);

        if ($mailboxResource === false) {
            throw new MailProviderException('Can not open mailbox: ' . imap_last_error());
        }

        return $mailboxResource;
    }

    public function messageSortByDate(Message $messageA, Message $messageB)
    {
        $sortKeyA = $messageA->getDate()->getTimestamp();
        $sortKeyB = $messageB->getDate()->getTimestamp();

        return $sortKeyA > $sortKeyB ? -1 : 1;
    }
}
