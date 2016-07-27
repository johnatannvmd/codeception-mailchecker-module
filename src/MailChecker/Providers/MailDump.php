<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Exceptions\MessageNotFoundException;
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
        try {
            $this->transport->delete('/messages/');
        } catch (\Exception $e) {
            throw new MailProviderException($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $messages = json_decode($this->transport->get('/messages/')->getBody(), true);

        if ($messages === false) {
            throw new MailProviderException('Wrong answer from API');
        }

        return count($messages['messages']);
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
     * @return \MailChecker\Models\Message[]
     * @throws \MailChecker\Exceptions\MessageNotFoundException
     */
    private function getMessages()
    {
        $response = json_decode($this->transport->get('/messages/')->getBody(), true);

        if (isset($response['messages']) && !empty($response['messages'])) {
            return array_map(function ($rawMessage) {
                return $this->getMessage($this->transport->get("/messages/{$rawMessage['id']}.source")->getBody());
            }, $response['messages']);
        }

        throw new MessageNotFoundException();
    }
}
