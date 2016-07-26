<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Exceptions\MessageNotFoundException;
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
        try {
            $this->transport->delete('/messages');
        } catch (\Exception $e) {
            throw new MailProviderException($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function lastMessageTo($address)
    {
        $lastMessage = null;
        $messages = $this->getMessages();

        foreach ($messages as $message) {
            if ($message->containsTo($address)) {
                $lastMessage = $message;
            }
        }

        if (is_null($lastMessage)) {
            throw new MessageNotFoundException();
        }

        return $lastMessage;
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        $messages = $this->getMessages();

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
     * @return \MailChecker\Models\Message[]
     * @throws \MailChecker\Exceptions\MessageNotFoundException
     */
    private function getMessages()
    {
        $response = json_decode($this->transport->get('/messages')->getBody()->getContents(), true);

        if (!empty($response)) {
            return array_map(function ($rawMessage) {
                return $this->getMessage(
                    $this->transport->get("/messages/{$rawMessage['id']}.source")->getBody()->getContents()
                );
            }, $response);
        }

        throw new MessageNotFoundException();
    }
}
