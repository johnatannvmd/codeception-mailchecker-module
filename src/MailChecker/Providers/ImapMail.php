<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Models\Message;
use MailChecker\Providers\BaseProviders\ImapLibBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Config example:
 * ```
 * MailChecker:
 *   provider: 'ImapMail'
 *   options:
 *     host: '0.0.0.0'
 *     port: '993'
 *     service: 'imap'|'pop3' # default: imap
 *     user: 'username'
 *     password: 'password'
 *     folder: 'test' # optional
 *     flags: string|null # optional. Flags from http://php.net/manual/ru/function.imap-open.php
 *                                     without leading slash, i.e. 'novalidate-cert'
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
        $mailboxResource = $this->openMailbox();

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
     * Get messages count from provider
     *
     * @return int
     */
    public function messagesCount()
    {
    }

    public function lastMessageFrom($address)
    {
        $messages = $this->messages($address);

        if (empty($messages)) {
            return [];
        }

        return array_shift($messages);
    }

    public function lastMessage()
    {
        $messages = $this->messages();

        if (empty($messages)) {
            return [];
        }

        return array_shift($messages);
    }

    public function messages($from = null)
    {
        $mailboxResource = $this->openMailbox();

        $messages = imap_sort($mailboxResource, SORTARRIVAL, 1, SE_UID,
            is_null($from) ? 'ALL' : 'FROM "' . $from . '"');

        if ($messages === false) {
            return [];
        }

        return array_map(function ($messageId) use ($mailboxResource) {
            $rawMessage = imap_rfc822_parse_headers(imap_fetchheader(
                    $mailboxResource,
                    $messageId,
                    FT_UID | FT_PREFETCHTEXT)
            );

            $message = new Message($messageId);
            $message->setDate(new \DateTime($rawMessage->Date));
            $message->setSubject($rawMessage->Subject);
            $message->setFrom($rawMessage->fromaddress);
            $message->setTo($rawMessage->reply_toaddress);

            $messageStructure = imap_fetchstructure($mailboxResource, $messageId, FT_UID);

            $body = [];
            if (property_exists($messageStructure, 'parts')) {
                foreach ($messageStructure->parts as $partNumber => $part) {
                    $rawBody = imap_fetchbody($mailboxResource, $messageId, $partNumber + 1, FT_UID);
                    if ($part->encoding == 4) {
                        $rawBody = quoted_printable_decode($rawBody);
                    } elseif ($part->encoding == 3) {
                        $rawBody = base64_decode($rawBody);
                    }
                    $body[] = $rawBody;
                }
            } else {
                $body[] = imap_fetchbody($mailboxResource, $messageId, null, FT_UID);
            }

            $message->setBody($body);

            return $message;
        }, $messages);
    }
}
