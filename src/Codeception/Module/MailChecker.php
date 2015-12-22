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

    public function _initialize()
    {
        $this->provider = MailProviderFactory::getProvider($this->config['provider'], $this->config);
    }

    protected function onReconfigure()
    {
        $this->_initialize();
    }

    /**
     * Clear provider mail box emails
     *
     * Clear all emails from provider. You probably want to do this before
     * you do the thing that will send emails
     **/
    public function clearMailbox()
    {
        $this->provider->clear();
    }

    /**
     * See In Last Email
     *
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
     * See In Last Email subject
     *
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
     * Don't See In Last Email subject
     *
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
     * Don't See In Last Email
     *
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
     * See In Last Email To
     *
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
     * Don't See In Last Email To
     *
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
     * See In Last Email Subject To
     *
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
     * Don't See In Last Email Subject To
     *
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
     * Grab Matches From Last Email
     *
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
     * Grab From Last Email
     *
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
     * Grab Matches From Last Email To
     *
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
     * Grab From Last Email To
     *
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
     * See In Subject
     *
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
     * Don't See In Subject
     *
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
     * See In Email
     *
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
     * Don't See In Email
     *
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
     * Grab From Email
     *
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
