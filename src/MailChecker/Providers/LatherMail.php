<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Exceptions\MessageNotFoundException;
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
        try {
            $this->transport->delete('/api/0/messages/');
        } catch (\Exception $e) {
            throw new MailProviderException($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        return $this->getLastMessage(['recipients.address' => $address]);
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        return $this->getLastMessage();
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $response = json_decode($this->transport->get('/api/0/messages/')->getBody(), true);

        if ($response === false) {
            throw new MailProviderException('Wrong answer from API');
        }

        return (int)$response['message_count'];
    }

    /**
     * @param array $query
     *
     * @return \MailChecker\Models\Message
     * @throws \MailChecker\Exceptions\MessageNotFoundException
     */
    private function getLastMessage(array $query = [])
    {
        $options = [];

        if (!empty($query)) {
            $options['query'] = $query;
        }

        $response = json_decode($this->transport->get('/api/0/messages/', $options)->getBody(), true);

        if (isset($response['message_list']) && !empty($response['message_list'])) {
            return $this->getMessage($response['message_list'][0]['message_raw']);
        }

        throw new MessageNotFoundException();
    }
}
