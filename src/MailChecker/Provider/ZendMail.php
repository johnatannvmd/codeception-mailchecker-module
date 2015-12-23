<?php
namespace MailChecker\Provider;

use Zend\Mail\Message;

class ZendMail implements IProvider
{
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
    public function lastMessageFrom($address)
    {
        $messagesFrom = [];
        $messages = $this->messages();

        if (empty($messages)) {
            return [];
        }

        foreach ($messages as $message) {
            if ($message->getTo()->has($address)) {
                $messagesFrom[] = $message;
            }
        }

        if (!empty($messagesFrom)) {
            /** @var \Zend\Mail\Message $lastMessage */
            $lastMessage = max($messagesFrom);

            return [
                'subject' => $lastMessage->getSubject(),
                'source' => $lastMessage->getBodyText()
            ];
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function lastMessage()
    {
        $messages = $this->messages();

        if (empty($messages)) {
            return [];
        }

        /** @var \Zend\Mail\Message $lastMessage */
        $lastMessage = end($messages);

        return [
            'subject' => $lastMessage->getSubject(),
            'source' => $lastMessage->getBodyText()
        ];
    }

    /**
     * @inheritdoc
     * @return \Zend\Mail\Message[]
     */
    public function messages()
    {
        $messages = glob($this->path . '/*.' . $this->extension);

        return array_map(
            function ($message) {
                return Message::fromString(file_get_contents($message));
            },
            $messages
        );
    }
}
