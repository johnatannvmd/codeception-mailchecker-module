<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Exceptions\MessageNotFoundException;
use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Class MailHog
 *
 * Config example:
 * ```
 * MailHog:
 *   options:
 *     url: 'http://127.0.0.1'
 *     port: '1080'
 * ```
 *
 * @package MailChecker\Providers
 */
class MailHog implements IProvider
{
    use GuzzleBasedProvider;
    use RawMailProvider;

    /**
     * @inheritdoc
     */
    public function clear()
    {
        try {
            $this->transport->delete('/api/v1/messages');
        } catch (\Exception $e) {
            throw new MailProviderException($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $messages = json_decode($this->transport->get('/api/v2/messages')->getBody(), true);

        if ($messages === false) {
            throw new MailProviderException('Wrong answer from API');
        }

        return (int)$messages['total'];
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        foreach ($this->getMessages() as $message) {
            if ($message->containsTo($address)) {
                return $message;
            }
        }

        throw new MessageNotFoundException();
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        return $this->getMessages()[0];
    }

    /**
     * @return \MailChecker\Models\Message[]
     * @throws \MailChecker\Exceptions\MessageNotFoundException
     */
    private function getMessages()
    {
        $response = json_decode($this->transport->get('/api/v2/messages')->getBody(), true);

        if (isset($response['items']) && !empty($response['items'])) {
            return array_map(function ($rawMessage) {
                return $this->getMessage($rawMessage['Raw']['Data']);
            }, $response['items']);
        }

        throw new MessageNotFoundException();
    }
}
