<?php
namespace MailChecker\Providers;

use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;

/**
 * Class MailTrap
 *
 * Config example:
 * ```
 * MailTrap:
 *   options:
 *     url: 'https://mailtrap.io'
 *     port: '80'
 *     apiToken: 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
 *     defaultInbox: 'Name of the Inbox'
 * ```
 *
 * @package MailChecker\Providers
 */
class MailTrap implements IProvider
{
    /**
     * @var string
     */
    private $inboxId;

    use GuzzleBasedProvider {
        __construct as baseConstruct;
    }

    public function __construct(array $config)
    {
        $config['options']['guzzleOptions']['headers'] = [
            'Api-Token' => $config['options']['apiToken']
        ];

        $this->baseConstruct($config);
    }

    /**
     * Clears messages in provider
     *
     * @return void
     */
    public function clear()
    {
        $this->transport->patch('/api/v1/inboxes/' . $this->getInboxId() . '/clean');
    }

    /**
     * Get last message from provider by given email address
     *
     * @param $address
     *
     * @return array
     */
    public function lastMessageFrom($address)
    {
        $ids = [];
        $messages = $this->messages();
        if (empty($messages)) {
            return [];
        }

        foreach ($messages as $message) {
            if (strpos($message['to_email'], $address) !== false) {
                $ids[] = $message['id'];
            }
        }

        if (count($ids) > 0) {
            return $this->emailFromId(max($ids));
        }

        return [];
    }

    /**
     * Get last message from provider
     *
     * @return array
     */
    public function lastMessage()
    {
        $messages = $this->messages();

        $last = array_shift($messages);

        return $this->emailFromId($last['id']);
    }

    /**
     * Get all messages from provider
     *
     * @return array
     */
    public function messages()
    {
        $messages = json_decode(
            $this->transport->get("/api/v1/inboxes/{$this->getInboxId()}/messages")->getBody()->getContents(),
            true
        );

        usort($messages, ['\\MailChecker\\Util', 'messageSortByCreatedAt']);

        return $messages;
    }

    private function getInboxId()
    {
        if ($this->inboxId !== null) {
            return $this->inboxId;
        }

        $inboxes = json_decode($this->transport->get('/api/v1/inboxes')->getBody()->getContents(), true);
        if ($inboxes === false) {
            throw new \Exception('Can not decode answer from MailTrap');
        }

        foreach ($inboxes as $inbox) {
            if ($inbox['name'] === $this->config['options']['defaultInbox']) {
                return $this->inboxId = $inbox['id'];
            }
        }

        throw new \Exception("Inbox with name: \"{$this->config['options']['defaultInbox']}\" does not found");
    }

    private function emailFromId($id)
    {
        $response = $this->transport->get("/api/v1/inboxes/{$this->getInboxId()}/messages/{$id}");
        $message = json_decode($response->getBody()->getContents(), true);
        $message['source'] = $this->transport->get($message['raw_path'])->getBody()->getContents();

        return $message;
    }
}