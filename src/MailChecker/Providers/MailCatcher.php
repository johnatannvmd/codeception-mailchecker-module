<?php
namespace MailChecker\Providers;

use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Class MailCatcher
 *
 * Config example:
 * ```
 * MailCatcher:
 *   options:
 *     url: 'http://127.0.0.1'
 *     port: '1080'
 * ```
 *
 * @package MailChecker\Providers
 */
class MailCatcher implements IProvider
{
    use GuzzleBasedProvider;
    use RawMailProvider;

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->transport->delete('/messages');
    }

    /**
     * @inheritdoc
     */
    public function lastMessageFrom($address)
    {
        $lastMessage = null;
        $messages = $this->messages();
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
        $messages = $this->messages();

        if (is_null($messages)) {
            return null;
        }

        return array_pop($messages);
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $messages = json_decode($this->transport->get('/messages')->getBody()->getContents(), true);

        return count($messages);
    }

    /**
     * @return \MailChecker\Models\Message[]|null
     */
    private function messages()
    {
        $response = json_decode($this->transport->get('/messages')->getBody()->getContents(), true);

        if (!empty($response)) {
            return array_map(function ($rawMessage) {
                return $this->getMessage($this->transport->get("/messages/{$rawMessage['id']}.source")->getBody()->getContents());
            }, $response);
        }

        return null;
    }
}
