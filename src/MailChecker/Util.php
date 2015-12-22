<?php
namespace MailChecker;

class Util
{
    /**
     * @param $messageA
     * @param $messageB
     *
     * @return int
     */
    static function messageSortByCreatedAt($messageA, $messageB)
    {
        $sortKeyA = $messageA['created_at'] . $messageA['id'];
        $sortKeyB = $messageB['created_at'] . $messageB['id'];

        return ($sortKeyA > $sortKeyB) ? -1 : 1;
    }
}