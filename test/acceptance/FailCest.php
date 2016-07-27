<?php

/**
 * Class FailCest
 */
class FailCest
{
    public function notExistsMailProvider(AcceptanceTester $I)
    {
        $I->expectException(\MailChecker\Exceptions\MailProviderNotFoundException::class, function () use ($I) {
            $I->getMailProvider('FiledMailProvider');
        });
    }

    public function wrongMailProvider(AcceptanceTester $I)
    {
        $I->expectException(\MailChecker\Exceptions\MailProviderHasBadInterfaceException::class, function () use ($I) {
            $I->getMailProvider('WrongProvider');
        });
    }

    public function rightMailProvider(AcceptanceTester $I)
    {
        $I->getMailProvider('RightProvider');
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
