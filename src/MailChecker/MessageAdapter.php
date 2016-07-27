<?php
namespace MailChecker;

use MailChecker\Models\Message;

/**
 * Class MessageAdapter
 * @package MailChecker
 *
 * @method assertContains($expect, $actual, $message)
 * @method assertNotContains($expect, $actual, $message)
 * @method assertNotEmpty($actual, $message)
 * @method assertEquals($expected, $actual, $message = '')
 * @method fail($message)
 */
trait MessageAdapter
{
    /**
     * Look for a string in an email subject
     *
     * @param Message $email
     * @param $expected
     */
    protected function seeInEmailSubject(Message $email, $expected)
    {
        $this->assertContains($expected, $email->getSubject(), "Email Subject Contains");
    }

    /**
     * Look for the absence of a string in an email subject
     *
     * @param Message $email
     * @param $unexpected
     */
    protected function dontSeeInEmailSubject(Message $email, $unexpected)
    {
        $this->assertNotContains($unexpected, $email->getSubject(), "Email Subject Does Not Contain");
    }

    /**
     * Look for a string in an email
     *
     * @param Message $email
     * @param $expected
     * @throws \MailChecker\Exceptions\MessageNotFoundException
     */
    protected function seeInEmail(Message $email, $expected)
    {
        $this->assertContains($expected, $email->getBody()[0]->getBody(), "Email Contains");
    }

    /**
     * Look for the absence of a string in an email
     *
     * @param Message $email
     * @param $unexpected
     */
    protected function dontSeeInEmail(Message $email, $unexpected)
    {
        $this->assertNotContains($unexpected, $email->getBody()[0]->getBody(), "Email Does Not Contain");
    }

    /**
     * Look for filename in a attachments of a given message
     *
     * @param \MailChecker\Models\Message $email
     * @param $expectedFilename
     */
    protected function seeInEmailAttachment(Message $email, $expectedFilename)
    {
        $this->assertContains($expectedFilename, $email->getAttachmentsFilesNames(), 'Email Contains Attachment');
    }

    /**
     * Look for the absence of a filename in a attachments of a given message
     *
     * @param \MailChecker\Models\Message $email
     * @param $unexpectedFilename
     */
    protected function dontSeeInEmailAttachment(Message $email, $unexpectedFilename)
    {
        $this->assertNotContains(
            $unexpectedFilename,
            $email->getAttachmentsFilesNames(),
            'Email Does Not Contains Attachment'
        );
    }

    /**
     * @param \MailChecker\Models\Message $email
     *
     * @return int
     */
    protected function getNumberOfAttachments(Message $email)
    {
        return count($email->getAttachments());
    }

    protected function seeCcInEmail(Message $message, $address)
    {
        $this->assertContains($address, $message->getCc(), 'Email\'s CC contains');
    }

    /**
     * Return the matches of a regex against the raw email
     *
     * @param Message $email
     * @param $regex
     *
     * @return array
     */
    protected function grabMatchesFromEmail(Message $email, $regex)
    {
        $matches = [];

        preg_match($regex, $email->getBody()[0]->getBody(), $matches);
        $this->assertNotEmpty($matches, "No matches found for {$regex}");

        return $matches;
    }

    /**
     * Look for expected address in CC field of the given email
     *
     * @param Message $email
     * @param $expectedAddress
     */
    protected function seeCcInEmail(Message $email, $expectedAddress)
    {
        $this->assertContains($expectedAddress, $email->getCc(), 'Email Contains In CC');
    }
}
