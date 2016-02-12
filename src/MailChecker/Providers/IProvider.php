<?php
namespace MailChecker\Providers;

use MailChecker\Models\Message;

interface IProvider
{
    /**
     * Clears messages in provider
     *
     * @return void
     */
    public function clear();

    /**
     * Get messages count from provider
     *
     * @return int
     */
    public function messagesCount();

    /**
     * Get last message from provider by given email address
     *
     * @param $address
     *
     * @return Message|null
     */
    public function lastMessageTo($address);

    /**
     * Get last message from provider
     *
     * @return Message|null
     */
    public function lastMessage();
}
