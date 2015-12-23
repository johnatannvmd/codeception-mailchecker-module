<?php

namespace Codeception\Module;

use Codeception\Module;
use MailChecker\MailProviderFactory;

class MailChecker extends Module
{
    /**
     * @var \MailChecker\Provider\IProvider
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
        $email = $this->provider->lastMessage();
        $this->seeInEmail($email, $expected);
    }

    /**
     * Look for a string in the most recent email subject
     *
     * @param $expected
     */
    public function seeInLastEmailSubject($expected)
    {
        $email = $this->provider->lastMessage();
        $this->seeInEmailSubject($email, $expected);
    }

    /**
     * Look for the absence of a string in the most recent email subject
     *
     * @param $expected
     */
    public function dontSeeInLastEmailSubject($expected)
    {
        $email = $this->provider->lastMessage();
        $this->dontSeeInEmailSubject($email, $expected);
    }

    /**
     * Look for the absence of a string in the most recent email
     *
     * @param $unexpected
     */
    public function dontSeeInLastEmail($unexpected)
    {
        $email = $this->provider->lastMessage();
        $this->dontSeeInEmail($email, $unexpected);
    }

    /**
     * Look for a string in the most recent email sent to $address
     *
     * @param $address
     * @param $expected
     */
    public function seeInLastEmailTo($address, $expected)
    {
        $email = $this->provider->lastMessageFrom($address);
        $this->seeInEmail($email, $expected);
    }

    /**
     * Look for the absence of a string in the most recent email sent to $address
     *
     * @param $address
     * @param $unexpected
     */
    public function dontSeeInLastEmailTo($address, $unexpected)
    {
        $email = $this->provider->lastMessageFrom($address);
        $this->dontSeeInEmail($email, $unexpected);
    }

    /**
     * Look for a string in the most recent email subject sent to $address
     *
     * @param $address
     * @param $expected
     */
    public function seeInLastEmailSubjectTo($address, $expected)
    {
        $email = $this->provider->lastMessageFrom($address);
        $this->seeInEmailSubject($email, $expected);
    }

    /**
     * Look for the absence of a string in the most recent email subject sent to $address
     *
     * @param $address
     * @param $unexpected
     */
    public function dontSeeInLastEmailSubjectTo($address, $unexpected)
    {
        $email = $this->provider->lastMessageFrom($address);
        $this->dontSeeInEmailSubject($email, $unexpected);
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
        $email = $this->provider->lastMessage();
        $matches = $this->grabMatchesFromEmail($email, $regex);

        return $matches;
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
        $matches = $this->grabMatchesFromLastEmail($regex);

        return $matches[0];
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
        $email = $this->provider->lastMessageFrom($address);
        $matches = $this->grabMatchesFromEmail($email, $regex);

        return $matches;
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
        $matches = $this->grabMatchesFromLastEmailTo($address, $regex);

        return $matches[0];
    }

    /**
     * Test email count equals expected value
     *
     * @param $expected int
     */
    public function seeEmailCount($expected)
    {
        $messages = $this->provider->messages();
        $count = count($messages);
        $this->assertEquals($expected, $count);
    }

    /**
     * Look for a string in an email subject
     *
     * @param $email
     * @param $expected
     */
    protected function seeInEmailSubject($email, $expected)
    {
        $this->assertContains($expected, $email['subject'], "Email Subject Contains");
    }

    /**
     * Look for the absence of a string in an email subject
     *
     * @param $email
     * @param $unexpected
     */
    protected function dontSeeInEmailSubject($email, $unexpected)
    {
        $this->assertNotContains($unexpected, $email['subject'], "Email Subject Does Not Contain");
    }

    /**
     * Look for a string in an email
     *
     * @param $email
     * @param $expected
     */
    protected function seeInEmail($email, $expected)
    {
        $this->assertContains($expected, $email['source'], "Email Contains");
    }

    /**
     * Look for the absence of a string in an email
     *
     * @param $email
     * @param $unexpected
     */
    protected function dontSeeInEmail($email, $unexpected)
    {
        $this->assertNotContains($unexpected, $email['source'], "Email Does Not Contain");
    }

    /**
     * Return the matches of a regex against the raw email
     *
     * @param $email
     * @param $regex
     *
     * @return array
     */
    protected function grabMatchesFromEmail($email, $regex)
    {
        $matches = [];

        preg_match($regex, $email['source'], $matches);
        $this->assertNotEmpty($matches, "No matches found for $regex");

        return $matches;
    }
}
