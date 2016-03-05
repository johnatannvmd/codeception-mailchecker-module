<?php
namespace MailChecker\Providers;

use MailChecker\Models\Message;
use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Class MailHog
 *
 * Config example:
 * ```
 * MailDump:
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
        $this->transport->delete('/api/v1/messages');
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $messages = json_decode($this->transport->get('/api/v2/messages')->getBody(), true);

        return (int)$messages['total'];
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        $messages = $this->getMessages();
        if (is_null($messages)) {
            return null;
        }

        foreach ($messages as $message) {
            if ($message->containsTo($address)) {
                return $message;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        $messages = $this->getMessages();

        if (is_null($messages)) {
            return null;
        }

        return $messages[0];
    }

    /**
     * @return Message[]|null
     */
    private function getMessages()
    {
        $response = json_decode($this->transport->get('/api/v2/messages')->getBody(), true);

        if (isset($response['items']) && !empty($response['items'])) {
            return array_map(function ($rawMessage) {
                return $this->getMessage($rawMessage['Raw']['Data']);
            }, $response['items']);
        }

        return null;
    }
}
