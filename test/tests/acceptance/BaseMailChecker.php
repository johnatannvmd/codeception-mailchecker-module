<?php
trait BaseMailChecker
{
    /**
     * @var \Codeception\Module\BaseMailerHelper
     */
    protected $mailer;

    abstract protected function getProvider();

    public function _before(\Codeception\Module\BaseMailerHelper $mailer)
    {
        $this->mailer = $mailer;

        $this->mailer->haveMailProvider($this->getProvider());
    }

    /**
     * @before clearMailbox
     *
     * @param \AcceptanceTester $I
     */
    public function sendEmails(AcceptanceTester $I)
    {
        $this->mailer->sendEmail(
            'from_' . sqs('from') . '@somemailbox.com',
            'to' . sqs('toFirst') . '@othermailbox.com',
            'cc' . sqs('ccFirst') . '@othermailbox.com',
            'Subject ' . sqs('first'),
            'Body ' . sqs('first'),
            'file' . sqs('first') . '.ext'
        );

        $this->mailer->sendEmail(
            'from_' . sqs('from') . '@somemailbox.com',
            'to' . sqs('toFirst') . '@othermailbox.com',
            'cc' . sqs('ccFirst') . '@othermailbox.com',
            'Subject ' . sqs('second'),
            'Body ' . sqs('second'),
            'file' . sqs('second') . '.ext'
        );

        $this->mailer->sendEmail(
            'from_' . sqs('from') . '@somemailbox.com',
            'to' . sqs('toSecond') . '@othermailbox.com',
            'cc' . sqs('ccSecond') . '@othermailbox.com',
            'Subject ' . sqs('third'),
            'Body ' . sqs('third'),
            'file' . sqs('third') . '.ext'
        );

        $this->mailer->sendEmail(
            'from_' . sqs('from') . '@somemailbox.com',
            'to' . sqs('toThird') . '@othermailbox.com',
            'cc' . sqs('ccThird') . '@othermailbox.com',
            'Subject ' . sqs('last'),
            'Body ' . sqs('last'),
            'file' . sqs('last') . '.ext'
        );
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     *
     * @group smoke
     */
    public function emailCount(AcceptanceTester $I)
    {
        $I->wantTo('Count some email');
        $I->seeEmailCount(4);
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     *
     * @group smoke
     */
    public function seeInLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('See in last email');
        $I->seeInLastEmail('Body ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailSubject(AcceptanceTester $I)
    {
        $I->wantTo('See in last subject');
        $I->seeInLastEmailSubject('Subject ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailSubject(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last subject');
        $I->dontSeeInLastEmailSubject('Subject ' . sqs('first'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email');
        $I->dontSeeInLastEmail('Body ' . sqs('first'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('See in last email to ' . 'to' . sqs('toFirst') . '@othermailbox.com');
        $I->seeInLastEmailTo('to' . sqs('toFirst') . '@othermailbox.com', 'Body ' . sqs('second'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email to ' . 'to' . sqs('toFirst'). '@othermailbox.com');
        $I->dontSeeInLastEmailTo('to' . sqs('toFirst') . '@othermailbox.com', 'Body ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailSubjectTo(AcceptanceTester $I)
    {
        $I->wantTo('See in last email\'s subject to ' . 'to' . sqs('toFirst') . '@othermailbox.com');
        $I->seeInLastEmailSubjectTo('to' . sqs('toFirst') . '@othermailbox.com', 'Subject ' . sqs('second'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailSubjectTo(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email\'s subject to ' . 'to' . sqs('toFirst') . '@othermailbox.com');
        $I->dontSeeInLastEmailSubjectTo('to' . sqs('toFirst') . '@othermailbox.com', 'Subject ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabMatchesFromLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('Grab matches from last email');
        $result = $I->grabMatchesFromLastEmail('/Body ([_a-z0-9]+)/i');

        $I->assertEquals(sqs('last'), $result[1], 'Can not find matches in email body');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabFromLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('Grab from last email');
        $result = $I->grabFromLastEmail('/Body ([_a-z0-9]+)/i');

        $I->assertEquals('Body ' . sqs('last'), $result, 'Can not find matches in email body');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabMatchesFromLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('Grab matches from last email to ' . 'to' . sqs('toFirst') . '@othermailbox.com');
        $result = $I->grabMatchesFromLastEmailTo('to' . sqs('toFirst') . '@othermailbox.com', '/Body ([_a-z0-9]+)/i');

        $I->assertEquals(sqs('second'), $result[1], 'Can not find matches in email body');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabFromLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('Grab from last email to ' . 'to' . sqs('toFirst') . '@othermailbox.com');
        $result = $I->grabFromLastEmailTo('to' . sqs('toFirst') . '@othermailbox.com', '/Body ([_a-z0-9]+)/i');

        $I->assertEquals('Body ' . sqs('second'), $result, 'Can not find matches in email body');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function clearMailbox(AcceptanceTester $I)
    {
        $I->wantTo('Clear mail box');

        $I->clearMailbox();
        $I->seeEmailCount(0);
    }
}
