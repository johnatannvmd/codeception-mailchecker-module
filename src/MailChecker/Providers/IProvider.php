<?php
namespace MailChecker\Providers;

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
     * @return array
     */
    public function lastMessageFrom($address);

    /**
     * Get last message from provider
     *
     * @return array
     */
    public function lastMessage();

    /**
     * Get all messages from provider
     *
     * @return array
     */
    public function messages();
}
