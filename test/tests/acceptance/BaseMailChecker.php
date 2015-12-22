<?php
abstract class BaseMailChecker
{
    abstract protected function getProvider();

    public function _before(\Codeception\Module\BaseMailerHelper $mailer)
    {
        $mailer->haveMailProvider($this->getProvider());
    }

    protected function sendEmails(AcceptanceTester $I, \Codeception\Module\BaseMailerHelper $mailer)
    {
        $I->clearMailbox();

        $mailer->sendEmail(
            'from_' . sq(1) . '@somemailbox.com',
            'to' . sq(1) . '@othermailbox.com',
            'Subject ' . sq(1),
            'Body ' . sq(1)
        );

        $mailer->sendEmail(
            'from_' . sq(2) . '@somemailbox.com',
            'to' . sq(2) . '@othermailbox.com',
            'Subject ' . sq(2),
            'Body ' . sq(2)
        );
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function emailCount(AcceptanceTester $I)
    {
        $I->wantTo('Count some email');
        $I->seeEmailCount(2);
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('See in last email');
        $I->seeInLastEmail('Body ' . sq(2));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailSubject(AcceptanceTester $I)
    {
        $I->wantTo('See in last subject');
        $I->seeInLastEmailSubject('Subject ' . sq(2));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailSubject(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last subject');
        $I->dontSeeInLastEmailSubject('Subject ' . sq(1));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email');
        $I->dontSeeInLastEmail('Body ' . sq(1));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('See in last email to ' . 'to' . sq(2) . '@othermailbox.com');
        $I->seeInLastEmailTo('to' . sq(2) . '@othermailbox.com', 'Body ' . sq(2));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email to' . 'to' . sq(2) . '@othermailbox.com');
        $I->dontSeeInLastEmailTo('to' . sq(2) . '@othermailbox.com', 'Body ' . sq(1));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailSubjectTo(AcceptanceTester $I)
    {
        $I->wantTo('See in last email\'s subject to ' . 'to' . sq(2) . '@othermailbox.com');
        $I->seeInLastEmailSubjectTo('to' . sq(2) . '@othermailbox.com', 'Subject ' . sq(2));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailSubjectTo(AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email\'s subject to ' . 'to' . sq(2) . '@othermailbox.com');
        $I->dontSeeInLastEmailSubjectTo('to' . sq(2) . '@othermailbox.com', 'Subject ' . sq(1));
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabMatchesFromLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('Grab matches from last email');
        $result = $I->grabMatchesFromLastEmail('/Body ([_a-z0-9]+)/i');

        $I->assertEquals(sq(2), $result[1], 'Can not find matches in email body');
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabFromLastEmail(AcceptanceTester $I)
    {
        $I->wantTo('Grab from last email');
        $result = $I->grabFromLastEmail('/Body ([_a-z0-9]+)/i');

        $I->assertEquals('Body ' . sq(2), $result, 'Can not find matches in email body');
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabMatchesFromLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('Grab matches from last email to ' . 'to' . sq(1) . '@othermailbox.com');
        $result = $I->grabMatchesFromLastEmailTo('to' . sq(1) . '@othermailbox.com', '/Body ([_a-z0-9]+)/i');

        $I->assertEquals(sq(1), $result[1], 'Can not find matches in email body');
    }

    /**
     * @before sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabFromLastEmailTo(AcceptanceTester $I)
    {
        $I->wantTo('Grab from last email to ' . 'to' . sq(1) . '@othermailbox.com');
        $result = $I->grabFromLastEmailTo('to' . sq(1) . '@othermailbox.com', '/Body ([_a-z0-9]+)/i');

        $I->assertEquals('Body ' . sq(1), $result, 'Can not find matches in email body');
    }
}
