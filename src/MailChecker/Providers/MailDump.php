<?php
namespace MailChecker\Providers;

use MailChecker\Models\Message;
use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Class MailDump
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
class MailDump implements IProvider
{
    use GuzzleBasedProvider;
    use RawMailProvider;

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->transport->delete('/messages/');
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $messages = json_decode($this->transport->get('/messages/')->getBody(), true);

        return count($messages['messages']);
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        $lastMessage = null;
        $messages = $this->getMessages();
        if (is_null($messages)) {
            return null;
        }

        foreach ($messages as $message) {
            if ($message->containsTo($address)) {
                $lastMessage = $message;
            }
        }

        return $lastMessage;
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

        return array_pop($messages);
    }

    /**
     * @return Message[]|null
     */
    private function getMessages()
    {
        $response = json_decode($this->transport->get('/messages/')->getBody(), true);

        if (isset($response['messages']) && !empty($response['messages'])) {
            return array_map(function ($rawMessage) {
                return $this->getMessage($this->transport->get("/messages/{$rawMessage['id']}.source")->getBody());
            }, $response['messages']);
        }

        return null;
    }
}
