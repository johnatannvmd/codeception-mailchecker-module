<?php
namespace MailChecker\Providers;

use MailChecker\Models\Body;
use MailChecker\Models\Message;
use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

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
    use RawMailProvider;

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->transport->delete('/api/0/messages/');
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        $messages = $this->messages(['recipients.address' => $address]);
        if (is_null($messages)) {
            return null;
        }

        return $messages;
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        $messages = $this->messages();
        if (is_null($messages)) {
            return null;
        }

        return $messages;
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $response = json_decode($this->transport->get('/api/0/messages/')->getBody(), true);

        return $response['message_count'];
    }

    /**
     * @param array $query
     *
     * @return \MailChecker\Models\Message[]
     */
    private function messages(array $query = [])
    {
        $options = [];

        if (!empty($query)) {
            $options['query'] = $query;
        }

        $response = json_decode($this->transport->get('/api/0/messages/', $options)->getBody(), true);

        if (isset($response['message_list'])) {
            return $this->getMessage($response['message_list'][0]['message_raw']);
        }

        return null;
    }
}
