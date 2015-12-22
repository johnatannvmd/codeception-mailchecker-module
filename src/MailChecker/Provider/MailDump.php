<?php
namespace MailChecker\Provider;

class MailDump implements IProvider
{
    use GuzzleBasedProvider;

    public function clear()
    {
        $this->transport->delete('/messages/');
    }

    public function lastMessageFrom($address)
    {
        $ids = [];
        $messages = $this->messages();
        if(empty($messages)) {
            return [];
        }

        foreach($messages as $message) {
            foreach($message['recipients'] as $recipients) {
                foreach($recipients as $recipient) {
                    if(strpos($recipient, $address) !== false) {
                        $ids[] = $message['id'];
                    }
                }
            }
        }

        if(count($ids) > 0) {
            return $this->emailFromId(max($ids));
        }

        return [];
    }

    public function lastMessage()
    {
        $messages = $this->messages();
        if(empty($messages)) {
            return [];
        }

        $last = array_shift($messages);

        return $this->emailFromId($last['id']);
    }

    public function messages()
    {
        $response = json_decode($this->transport->get('/messages')->getBody()->getContents(), true);

        if(isset($response['messages'])) {
            $messages = $response['messages'];
        } else {
            return [];
        }

        usort($messages, ['\\MailChecker\\Util', 'messageSortByCreatedAt']);

        return $messages;
    }

    private function emailFromId($id)
    {
        $response = $this->transport->get("/messages/{$id}.json");
        $message = json_decode($response->getBody()->getContents(), true);
        $message['source'] = $this->transport->get("/messages/{$id}.source")->getBody()->getContents();

        return $message;
    }
}
