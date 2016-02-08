<?php

class ImapMailCest
{
    use BaseMailChecker {
        _before as _baseBefore;
    }

    protected function getProvider()
    {
        return 'ImapMail';
    }

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

    public function _before(\Codeception\Module\SmtpMailerHelper $mailer)
    {
        $this->_baseBefore($mailer);
    }
}
