<?php
namespace MailChecker\Providers;

use MailChecker\Exceptions\MailProviderException;
use MailChecker\Exceptions\MessageNotFoundException;
use MailChecker\Providers\BaseProviders\RawMailProvider;

/**
 * Class ZendMail
 *
 * Config example:
 * ```
 * ZendMail:
 *   options:
 *     path: 'path/to/the/mail'
 *     extension: 'mail_ext'
 * ```
 *
 * @package MailChecker\Providers
 */
class ZendMail implements IProvider
{
    use RawMailProvider;

    /**
     * @var string
     */
    private $path;

    /**
     * @var
     */
    private $extension;

    /**
     * ZendMail constructor.
     *
     * @param $config
     *
     * @throws \MailChecker\Exceptions\MailProviderException
     */
    public function __construct($config)
    {
        if (!isset($config['options']['path'], $config['options']['extension'])) {
            throw new MailProviderException('ZendMail provider can not find path or extension options in config');
        }

        $this->path = realpath($config['options']['path']);
        if (!is_dir($this->path)) {
            throw new MailProviderException('ZendMail path is not a directory or does not exists');
        }
        $this->extension = $config['options']['extension'];
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $emails = glob($this->path . '/*.' . $this->extension);
        foreach ($emails as $email) {
            if (is_file($email)) {
                $result = unlink($email);
                if ($result === false) {
                    throw new MailProviderException('Could not clear ZendMail inbox directory');
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function messagesCount()
    {
        return count(glob($this->path . '/*.' . $this->extension));
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
        $messages = glob($this->path . '/*.' . $this->extension);
        if (empty($messages)) {
            throw new MessageNotFoundException();
        }

        return array_map(
            function ($message) {
                return $this->getMessage(file_get_contents($message));
            },
            $messages
        );
    }
}
