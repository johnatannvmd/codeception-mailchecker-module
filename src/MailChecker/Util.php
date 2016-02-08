<?php
namespace MailChecker;

use MailChecker\Models\Message;

class Util
{
    /**
     * @param $messageA
     * @param $messageB
     *
     * @return int
     */
    public static function messageSortByCreatedAt($messageA, $messageB)
    {
        $sortKeyA = strtotime($messageA['created_at']) . $messageA['id'];
        $sortKeyB = strtotime($messageB['created_at']) . $messageB['id'];

        return ($sortKeyA < $sortKeyB) ? -1 : 1;
    }

    public static function messageSortByDate(Message $messageA, Message $messageB)
    {
        $sortKeyA = $messageA->getDate()->getTimestamp() + (int)$messageA->getId();
        $sortKeyB = $messageB->getDate()->getTimestamp() + (int)$messageB->getId();

        return $sortKeyA > $sortKeyB ? -1 : 1;
    }
}
