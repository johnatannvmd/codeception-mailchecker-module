<?php

namespace Codeception\Module;

use Codeception\Module;
use MailChecker\MailProviderFactory;
use MailChecker\MessageAdapter;

class MailChecker extends Module
{
    use MessageAdapter;

    /**
     * @var \MailChecker\Providers\IProvider
     */
    protected $provider;

    /**
     * @var array
     */
    protected $config = ['provider', 'options'];

    /**
     * @var array
     */
    protected $requiredFields = ['provider'];

    /**
     * Codeception hook used after configuration is loaded
     *
     * @throws \MailChecker\Exceptions\MailProviderNotFoundException
     */
    public function _initialize()
    {
        $this->provider = MailProviderFactory::getProvider($this->config['provider'], $this->config);
    }

    /**
     * @inheritdoc
     */
    protected function onReconfigure()
    {
        $this->_initialize();
    }

    /**
     * Clear all emails from provider. You probably want to do this before
     * you do the thing that will send emails
     **/
    public function clearMailbox()
    {
        $this->provider->clear();
    }

    /**
     * Look for a string in the most recent email
     *
     * @param $expected
     */
    public function seeInLastEmail($expected)
    {
        $this->seeInEmail(
            $this->provider->lastMessage(),
            $expected
        );
    }

    /**
     * Look for the absence of a string in the most recent email
     *
     * @param $unexpected
     */
    public function dontSeeInLastEmail($unexpected)
    {
        $this->dontSeeInEmail(
            $this->provider->lastMessage(),
            $unexpected
        );
    }

    /**
     * Look for a string in the most recent email subject
     *
     * @param $expected
     */
    public function seeInLastEmailSubject($expected)
    {
        $this->seeInEmailSubject(
            $this->provider->lastMessage(),
            $expected
        );
    }

    /**
     * Look for the absence of a string in the most recent email subject
     *
     * @param $expected
     */
    public function dontSeeInLastEmailSubject($expected)
    {
        $this->dontSeeInEmailSubject(
            $this->provider->lastMessage(),
            $expected
        );
    }

    /**
     * Look for a string in the most recent email sent to $address
     *
     * @param $address
     * @param $expected
     */
    public function seeInLastEmailTo($address, $expected)
    {
        $this->seeInEmail(
            $this->provider->lastMessageTo($address),
            $expected
        );
    }

    /**
     * Look for the absence of a string in the most recent email sent to $address
     *
     * @param $address
     * @param $unexpected
     */
    public function dontSeeInLastEmailTo($address, $unexpected)
    {
        $this->dontSeeInEmail(
            $this->provider->lastMessageTo($address),
            $unexpected
        );
    }

    /**
     * Look for a string in the most recent email subject sent to $address
     *
     * @param $address
     * @param $expected
     */
    public function seeInLastEmailSubjectTo($address, $expected)
    {
        $this->seeInEmailSubject(
            $this->provider->lastMessageTo($address),
            $expected
        );
    }

    /**
     * Look for the absence of a string in the most recent email subject sent to $address
     *
     * @param $address
     * @param $unexpected
     */
    public function dontSeeInLastEmailSubjectTo($address, $unexpected)
    {
        $this->dontSeeInEmailSubject(
            $this->provider->lastMessageTo($address),
            $unexpected
        );
    }

    /**
     * Checks that the last email have attachment with following filename.
     *
     * @param $expectedFilename
     */
    public function seeAttachmentFilenameInLastEmail($expectedFilename)
    {
        $this->seeInEmailAttachment(
            $this->provider->lastMessage(),
            $expectedFilename
        );
    }

    /**
     * Checks that the last email does NOT have attachment with following filename.
     *
     * @param $unexpectedFilename
     */
    public function dontSeeAttachmentFilenameInLastEmail($unexpectedFilename)
    {
        $this->dontSeeInEmailAttachment(
            $this->provider->lastMessage(),
            $unexpectedFilename
        );
    }

    /**
     * Checks that the last sent to an address have attachment with following filename.
     *
     * @param $address
     * @param $expectedFilename
     */
    public function seeAttachmentFilenameInLastEmailTo($address, $expectedFilename)
    {
        $this->seeInEmailAttachment(
            $this->provider->lastMessageTo($address),
            $expectedFilename
        );
    }

    /**
     * Checks that the last sent to an address does NOT have attachment with following filename.
     *
     * @param $address
     * @param $unexpectedFilename
     */
    public function dontSeeAttachmentFilenameInLastEmailTo($address, $unexpectedFilename)
    {
        $this->dontSeeInEmailAttachment(
            $this->provider->lastMessageTo($address),
            $unexpectedFilename
        );
    }

    /**
     * Asserts that a certain number of attachments found in the last email.
     *
     * @param $expected
     */
    public function seeAttachmentsCountInLastEmail($expected)
    {
        $this->assertEquals($expected, $this->getNumberOfAttachments($this->provider->lastMessage()));
    }

    /**
     * Asserts that a certain number of attachments found in the last email to a given address.
     *
     * @param $address
     * @param $expected
     */
    public function seeAttachmentsCountInLastEmailTo($address, $expected)
    {
        $this->assertEquals($expected, $this->getNumberOfAttachments($this->provider->lastMessageTo($address)));
    }

    /**
     * @param $expectedAddress
     */
    public function seeCcInLastEmail($expectedAddress)
    {
        $this->seeCcInEmail($this->provider->lastMessage(), $expectedAddress);
    }

    /**
     * @param $address
     * @param $expectedAddress
     */
    public function seeCcInLastEmailTo($address, $expectedAddress)
    {
        $this->seeCcInEmail($this->provider->lastMessageTo($address), $expectedAddress);
    }

    /**
     * Look for a regex in the email source and return it's matches
     *
     * @param $regex
     *
     * @return array
     */
    public function grabMatchesFromLastEmail($regex)
    {
        return $this->grabMatchesFromEmail(
            $this->provider->lastMessage(),
            $regex
        );
    }

    /**
     * Look for a regex in the email source and return it
     *
     * @param $regex
     *
     * @return string
     */
    public function grabFromLastEmail($regex)
    {
        return $this->grabMatchesFromLastEmail($regex)[0];
    }

    /**
     * Look for a regex in most recent email sent to $address email source and
     * return it's matches
     *
     * @param $address
     * @param $regex
     *
     * @return array
     */
    public function grabMatchesFromLastEmailTo($address, $regex)
    {
        return $this->grabMatchesFromEmail(
            $this->provider->lastMessageTo($address),
            $regex
        );
    }

    /**
     * Look for a regex in most recent email sent to $address email source and
     * return it
     *
     * @param $address string
     * @param $regex string
     *
     * @return string
     */
    public function grabFromLastEmailTo($address, $regex)
    {
        return $this->grabMatchesFromLastEmailTo($address, $regex)[0];
    }

    /**
     * Test email count equals expected value
     *
     * @param $expected int
     */
    public function seeEmailCount($expected)
    {
        $this->assertEquals($expected, $this->provider->messagesCount());
    }
}
