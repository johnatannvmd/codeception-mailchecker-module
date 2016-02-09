<?php
namespace MailChecker;

use MailChecker\Models\Message;

class Util
{
    public static function messageSortByDate(Message $messageA, Message $messageB)
    {
        $sortKeyA = $messageA->getDate()->getTimestamp();
        $sortKeyB = $messageB->getDate()->getTimestamp();

        return $sortKeyA > $sortKeyB ? -1 : 1;
    }
}
