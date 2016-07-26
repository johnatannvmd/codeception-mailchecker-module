<?php

/**
 * Class FailCest
 */
class FailCest
{
    public function notExistsMailProvider(AcceptanceTester $I)
    {
        $catch = false;

        try {
            $I->getMailProvider('FiledMailProvider');
        } catch (\MailChecker\Exceptions\MailProviderNotFoundException $e) {
            $I->assertContains('not found', $e->getMessage());
            $catch = true;
        }

        $I->assertTrue($catch);
    }

    public function wrongMailProvider(AcceptanceTester $I)
    {
        $catch = false;

        try {
            $I->getMailProvider('WrongProvider');
        } catch (\MailChecker\Exceptions\MailProviderNotFoundException $e) {
            $I->assertContains('instance', $e->getMessage());
            $catch = true;
        }

        $I->assertTrue($catch);
    }

    public function rightMailProvider(AcceptanceTester $I)
    {
        $catch = false;

        try {
            $I->getMailProvider('RightProvider');
        } catch (\MailChecker\Exceptions\MailProviderNotFoundException $e) {
            $catch = true;
        }

        $I->assertFalse($catch);
    }
}

class WrongProvider
{

}

class RightProvider implements \MailChecker\Providers\IProvider
{

    public function clear()
    {
    }

    public function lastMessageTo($address)
    {
    }

    public function lastMessage()
    {
    }

    public function messagesCount()
    {
    }
}
