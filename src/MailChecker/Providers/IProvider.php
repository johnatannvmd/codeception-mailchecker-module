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
     * Get last message from provider by given email address
     *
     * @param $address
     *
     * @return Message|null
     */
    public function lastMessageFrom($address);

    /**
     * Get last message from provider
     *
     * @return Message|null
     */
    public function lastMessage();

    /**
     * Get messages count from provider
     *
     * @return int
     */
    public function messagesCount();
}
