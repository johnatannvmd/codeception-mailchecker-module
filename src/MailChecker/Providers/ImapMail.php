<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Models\Message;
use MailChecker\Providers\BaseProviders\ImapLibBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;
use MailChecker\Util;

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
    use ImapLibBasedProvider;
    use RawMailProvider;

    public function clear()
    {
        foreach ($this->credentials as $email => $password) {
            codecept_debug('Clear mail box: ' . $email);

            $this->clearMailBox($this->openMailbox($email));
        }
    }

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
            return null;
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
            return null;
        }

        return $messages[0];
    }

    /**
     * @param null $from
     *
     * @return \MailChecker\Models\Message[]
     * @throws \MailChecker\Exceptions\MailProviderException
     */
    private function messages($from = null)
    {
        $messages = [];

        foreach ($this->credentials as $email => $password) {
            if (!is_null($from) && $email != $from) {
                continue;
            }

            $mailboxResource = $this->openMailbox($email);

            $messages = array_merge($messages, $this->parseMessages($mailboxResource));
        }

        usort($messages, ['\\MailChecker\\Util', 'messageSortByDate']);

        return $messages;
    }

    private function parseMessages($mailboxResource)
    {
        $messages = imap_sort($mailboxResource, SORTARRIVAL, 0, SE_UID, 'ALL');
        if ($messages === false) {
            return [];
        }

        return array_map(function ($messageId) use ($mailboxResource) {
            return $this->getMessage(
                imap_fetchheader($mailboxResource, $messageId, FT_UID) .
                "\r\n\r\n" .
                imap_body($mailboxResource, $messageId, FT_UID | FT_PEEK)
            );
        }, $messages);
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
}
