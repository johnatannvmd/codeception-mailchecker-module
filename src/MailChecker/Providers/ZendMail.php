<?php
namespace MailChecker\Providers;

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
     * @throws \Exception
     */
    public function __construct($config)
    {
        if (!isset($config['options']['path'], $config['options']['extension'])) {
            throw new \Exception('ZendMail provider can not find path or extension options in config.');
        }

        $this->path = realpath($config['options']['path']);
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
                unlink($email);
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
     * @return \MailChecker\Models\Message[]|null
     */
    private function getMessages()
    {
        $messages = glob($this->path . '/*.' . $this->extension);
        if (empty($messages)) {
            return null;
        }

        return array_map(
            function ($message) {
                return $this->getMessage(file_get_contents($message));
            },
            $messages
        );
    }
}
