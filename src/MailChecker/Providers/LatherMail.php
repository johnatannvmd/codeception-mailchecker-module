<?php
namespace MailChecker\Providers;

use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;

/**
 * X-Mail-Password - same as SMTP password
 * X-Mail-Inbox - same as SMTP user. Optional, work with all inboxes if not specified
 *
 * Config example:
 * ```
 * LatherMail:
 *   options:
 *     url: 'http://127.0.0.1'
 *     port: '1080'
 *     user: 'smtp-inbox'
 *     password: 'smtp-password'
 * ```
 *
 * @package MailChecker\Providers
 */
class LatherMail implements IProvider
{
    use GuzzleBasedProvider;

    public function clear()
    {
        $this->transport->delete('/api/0/messages/');
    }

    public function lastMessageFrom($address)
    {
        $ids = [];
        $messages = $this->messages();
        if (empty($messages)) {
            return [];
        }

        foreach ($messages as $message) {
            foreach ($message['recipients'] as $recipient) {
                if (strpos($recipient['address'], $address) !== false) {
                    $ids[] = $message['_id'];
                }
            }
        }

        if (count($ids) > 0) {
            return $this->emailFromId(max($ids));
        }

        return [];
    }

    public function lastMessage()
    {
        $messages = $this->messages();
        if (empty($messages)) {
            return [];
        }

        $last = array_shift($messages);

        return $this->emailFromId($last['_id']);
    }

    public function messages()
    {
        $response = json_decode($this->transport->get('/api/0/messages/')->getBody()->getContents(), true);

        if (isset($response['message_list'])) {
            $messages = $response['message_list'];
        } else {
            return [];
        }

        return $messages;
    }

    private function emailFromId($id)
    {
        $response = json_decode($this->transport->get("/api/0/messages/{$id}")->getBody()->getContents(), true);

        $message = $response['message_info'];
        $message['source'] = $message['message_raw'];

        return $message;
    }
}
