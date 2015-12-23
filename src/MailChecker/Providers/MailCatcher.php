<?php
namespace MailChecker\Providers;

use MailChecker\Providers\BaseProviders\GuzzleBasedProvider;

class MailCatcher implements IProvider
{
    use GuzzleBasedProvider;

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
        $ids = [];
        $messages = $this->messages();
        if (empty($messages)) {
            return [];
        }

        foreach ($messages as $message) {
            foreach ($message['recipients'] as $recipient) {
                if (strpos($recipient, $address) !== false) {
                    $ids[] = $message['id'];
                }
            }
        }

        if (count($ids) > 0) {
            return $this->emailFromId(max($ids));
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

        $last = array_shift($messages);

        return $this->emailFromId($last['id']);
    }

    /**
     * @inheritdoc
     */
    public function messages()
    {
        $response = $this->transport->get('/messages');
        $messages = json_decode($response->getBody()->getContents(), true);

        usort($messages, ['\\MailChecker\\Util', 'messageSortByCreatedAt']);

        return $messages;
    }

    /**
     * @param string $id
     *
     * @return array
     */
    private function emailFromId($id)
    {
        $response = $this->transport->get("/messages/{$id}.json");
        $message = json_decode($response->getBody()->getContents(), true);
        $message['source'] = quoted_printable_decode($message['source']);

        return $message;
    }
}
