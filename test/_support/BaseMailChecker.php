<?php
namespace Codeception\Module;

/**
 * Class BaseMailChecker
 */
trait BaseMailChecker
{
    /**
     * @var \Codeception\Module\BaseMailerHelper
     */
    protected $mailer;

    /**
     * @return string Mail provider name
     */
    abstract protected function getProvider();

    protected function getFromAddress()
    {
        return 'from' . sqs('from') . '@somemailbox.com';
    }

    protected function getToFirstAddress()
    {
        return 'to' . sqs('toFirst') . '@othermailbox.com';
    }

    protected function getToSecondAddress()
    {
        return 'to' . sqs('toSecond') . '@othermailbox.com';
    }

    protected function getToThirdAddress()
    {
        return 'to' . sqs('toThird') . '@othermailbox.com';
    }

    protected function getCcFirstAddress()
    {
        return 'cc' . sqs('toFirst') . '@othermailbox.com';
    }

    protected function getCcSecondAddress()
    {
        return 'cc' . sqs('toSecond') . '@othermailbox.com';
    }

    protected function getCcThirdAddress()
    {
        return 'cc' . sqs('toThird') . '@othermailbox.com';
    }

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
    public function sendEmails(\AcceptanceTester $I)
    {
        $this->mailer->sendEmail(
            $this->getFromAddress(),
            $this->getToFirstAddress(),
            [$this->getCcFirstAddress(), $this->getCcSecondAddress()],
            'Subject ' . sqs('first'),
            'Body ' . sqs('first'),
            'file' . sqs('first') . '.ext'
        );

        sleep(1);

        $this->mailer->sendEmail(
            $this->getFromAddress(),
            $this->getToFirstAddress(),
            [$this->getCcFirstAddress(), $this->getCcThirdAddress()],
            'Subject ' . sqs('second'),
            'Body ' . sqs('second'),
            'file' . sqs('second') . '.ext'
        );

        sleep(1);

        $this->mailer->sendEmail(
            $this->getFromAddress(),
            $this->getToSecondAddress(),
            $this->getCcSecondAddress(),
            'Subject ' . sqs('third'),
            'Body ' . sqs('third'),
            'file' . sqs('third') . '.ext'
        );

        sleep(1);

        $this->mailer->sendEmail(
            $this->getFromAddress(),
            $this->getToThirdAddress(),
            [$this->getCcFirstAddress(), $this->getCcSecondAddress()],
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
    public function emailCount(\AcceptanceTester $I)
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
    public function seeInLastEmail(\AcceptanceTester $I)
    {
        $I->wantTo('See in last email');
        $I->seeInLastEmail('Body ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailSubject(\AcceptanceTester $I)
    {
        $I->wantTo('See in last subject');
        $I->seeInLastEmailSubject('Subject ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailSubject(\AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last subject');
        $I->dontSeeInLastEmailSubject('Subject ' . sqs('first'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmail(\AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email');
        $I->dontSeeInLastEmail('Body ' . sqs('first'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('See in last email to ' . $this->getToFirstAddress());
        $I->seeInLastEmailTo($this->getToFirstAddress(), 'Body ' . sqs('second'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email to ' . $this->getToFirstAddress());
        $I->dontSeeInLastEmailTo($this->getToFirstAddress(), 'Body ' . sqs('last'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeInLastEmailSubjectTo(\AcceptanceTester $I)
    {
        $I->wantTo('See in last email\'s subject to ' . $this->getToFirstAddress());
        $I->seeInLastEmailSubjectTo($this->getToFirstAddress(), 'Subject ' . sqs('second'));
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeInLastEmailSubjectTo(\AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email\'s subject to ' . $this->getToFirstAddress());
        $I->dontSeeInLastEmailSubjectTo($this->getToFirstAddress(), 'Subject ' . sqs('last'));
    }


    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeAttachmentFilenameInLastEmail(\AcceptanceTester $I)
    {
        $I->wantTo('See in last email\'s attachment');
        $I->seeAttachmentFilenameInLastEmail('file' . sqs('last') . '.ext');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeAttachmentFilenameInLastEmail(\AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email\'s attachment');
        $I->dontSeeAttachmentFilenameInLastEmail('file' . sqs('third') . '.ext');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeAttachmentFilenameInLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('See in last email\'s attachment to ' . $this->getToFirstAddress());
        $I->seeAttachmentFilenameInLastEmailTo($this->getToFirstAddress(), 'file' . sqs('second') . '.ext');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function dontSeeAttachmentFilenameInLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('Do not see in last email\'s attachment to ' . $this->getToFirstAddress());
        $I->dontSeeAttachmentFilenameInLastEmailTo($this->getToFirstAddress(), 'file' . sqs('first') . '.ext');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeAttachmentsCountInLastEmail(\AcceptanceTester $I)
    {
        $I->wantTo('Count attachments in the last email');
        $I->seeAttachmentsCountInLastEmail(1);
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeAttachmentsCountInLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('Count attachments in the last email to ' . $this->getToFirstAddress());
        $I->seeAttachmentsCountInLastEmailTo($this->getToFirstAddress(), 1);
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeCcInLastEmail(\AcceptanceTester $I)
    {
        $I->wantTo('See CC in last email');
        $I->seeCcInLastEmail($this->getCcFirstAddress());
        $I->seeCcInLastEmail($this->getCcSecondAddress());
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function seeCcInLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('See CC in last email to ' . $this->getToFirstAddress());
        $I->seeCcInLastEmailTo($this->getToFirstAddress(), $this->getCcFirstAddress());
        $I->seeCcInLastEmailTo($this->getToFirstAddress(), $this->getCcThirdAddress());
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabMatchesFromLastEmail(\AcceptanceTester $I)
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
    public function grabFromLastEmail(\AcceptanceTester $I)
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
    public function grabMatchesFromLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('Grab matches from last email to ' . $this->getToFirstAddress());
        $result = $I->grabMatchesFromLastEmailTo($this->getToFirstAddress(), '/Body ([_a-z0-9]+)/i');

        $I->assertEquals(sqs('second'), $result[1], 'Can not find matches in email body');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function grabFromLastEmailTo(\AcceptanceTester $I)
    {
        $I->wantTo('Grab from last email to ' . $this->getToFirstAddress());
        $result = $I->grabFromLastEmailTo($this->getToFirstAddress(), '/Body ([_a-z0-9]+)/i');

        $I->assertEquals('Body ' . sqs('second'), $result, 'Can not find matches in email body');
    }

    /**
     * @depends sendEmails
     *
     * @param \AcceptanceTester $I
     */
    public function clearMailbox(\AcceptanceTester $I)
    {
        $I->wantTo('Clear mail box');

        $I->clearMailbox();
        $I->seeEmailCount(0);
    }
}
