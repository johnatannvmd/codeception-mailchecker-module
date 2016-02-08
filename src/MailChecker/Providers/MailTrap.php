<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;
use MailChecker\Providers\BaseProviders\RawMailProvider;

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
     * @var array
     */
    private $defaultInbox;

    use GuzzleBasedProvider {
        __construct as baseConstruct;
    }
    use RawMailProvider;

    public function __construct(array $config)
    {
        $config['options']['guzzleOptions']['headers'] = [
            'Api-Token' => $config['options']['apiToken']
        ];

        $this->baseConstruct($config);

        $this->defaultInbox = $this->getDefaultInbox();
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->transport->patch('/api/v1/inboxes/' . $this->defaultInbox['id'] . '/clean');
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        $inboxInfo = json_decode($this->transport->get("/api/v1/inboxes/{$this->defaultInbox['id']}")->getBody(), true);

        if ($inboxInfo == false) {
            throw new MailProviderException('Can not decode answer from MailTrap. ');
        }

        return $inboxInfo['emails_count'];
    }

    /**
     * @inheritdoc
     */
    public function lastMessageFrom($address)
    {
        $messages = $this->messages(['search' => $address]);
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
     * @param null $search
     *
     * @return \MailChecker\Models\Message[]|null
     */
    private function messages($search = null)
    {
        $options = [];

        if (!is_null($search)) {
            $options['query'] = $search;
        }

        $response = json_decode(
            $this->transport->get("/api/v1/inboxes/{$this->defaultInbox['id']}/messages", $options)->getBody(),
            true
        );

        if (!empty($response)) {
            return $this->getMessage($this->transport->get($response[0]['raw_path'])->getBody());
        }

        return null;
    }

    private function getDefaultInbox()
    {
        $inboxes = json_decode($this->transport->get('/api/v1/inboxes')->getBody(), true);
        if ($inboxes === false) {
            throw new MailProviderException('Can not decode answer from MailTrap. ');
        }

        foreach ($inboxes as $inbox) {
            if ($inbox['name'] === $this->config['options']['defaultInbox']) {
                return $inbox;
            }
        }

        throw new MailProviderException("Inbox with name: '{$this->config['options']['defaultInbox']}' not found");
    }
}