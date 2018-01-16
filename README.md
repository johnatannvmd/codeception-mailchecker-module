# Codeception MailChecker Module

This repo is abandoned. Please use https://github.com/captbaritone/codeception-mailcatcher-module instead or any other similar project.

[![Build Status](https://travis-ci.org/johnatannvmd/codeception-mailchecker-module.svg?branch=master)](https://travis-ci.org/johnatannvmd/codeception-mailchecker-module)
[![Coverage Status](https://coveralls.io/repos/johnatannvmd/codeception-mailchecker-module/badge.svg?branch=master&service=github)](https://coveralls.io/github/johnatannvmd/codeception-mailchecker-module?branch=master)

This module will let you test emails that are sent during your Codeception
acceptance tests.

It was inspired by the https://github.com/captbaritone/codeception-mailcatcher-module and
https://github.com/fetch/zend-mail-codeception-module/.

It supports several mail testing tools:

* [MailCatcher](http://mailcatcher.me/)
* [MailDump](https://github.com/ThiefMaster/maildump)
* [ZendMail](https://github.com/zendframework/zend-mail)
* [LatherMail](https://github.com/reclosedev/lathermail)
* [MailHog](https://github.com/mailhog/MailHog)
* [Mailtrap](https://mailtrap.io)
* [Imap Server] included

## Installation

Add the packages into your `composer.json`. For example we add Guzzle lib for MailCatcher
for MailDump provider:

    {
        "require-dev": {
            "codeception/codeception": "*",
            "johnatannvmd/mailchecker-codeception-module": "1.*"
        }
    } 

Tell Composer to download the package:

    php composer.phar update

Then enable it in your `acceptance.suite.yml` configuration and set the url and
port of your site's MailCatcher installation:

    class_name: WebGuy
    modules:
        enabled:
            - MailChecker
        config:
            MailChecker:
                provider: MailCatcher
                options:
                    url: 'http://project.dev'
                    port: '1080'

## Optional Configuration

If you need to specify some special options (e.g. SSL verification or authentication
headers), you can set all of the allowed [Guzzle request options](http://docs.guzzlephp.org/en/latest/request-options.html):

    class_name: WebGuy
    modules:
        enabled:
            - MailChecker
        config:
            MailChecker:
                provider: MailDump
                options:
                    url: 'http://project.dev'
                    port: '1080'
                    guzzleOptions:
                        auth: ['yo', 'yo']

## Example Usage

    <?php

    $I = new WebGuy($scenario);
    $I->wantTo('Get a password reset email');

    // Cleared old emails from MailCatcher
    $I->clearMailbox();

    // Reset 
    $I->amOnPage('forgotPassword.php');
    $I->fillField("input[name='email']", 'user@example.com');
    $I->click("Submit");
    $I->see("Please check your email");

    $I->seeInLastEmail("Please click this link to reset your password");

## Actions

### clearMailbox

Clears the emails in providers's list. This is prevents seeing emails sent
during a previous test. You probably want to do this before you trigger any
emails to be sent

Example:

    <?php
    // Clears all emails
    $I->clearMailbox();
    ?>

### seeInLastEmail

Checks that an email contains a value. It searches the full raw text of the
email: headers, subject line, and body.

Example:

    <?php
    $I->seeInLastEmail('Thanks for signing up!');
    ?>

* Param $text

### seeInLastEmailTo

Checks that the last email sent to an address contains a value. It searches the
full raw text of the email: headers, subject line, and body.

This is useful if, for example a page triggers both an email to the new user,
and to the administrator.

Example:

    <?php
    $I->seeInLastEmailTo('user@example.com', 'Thanks for signing up!');
    $I->seeInLastEmailTo('admin@example.com', 'A new user has signed up!');
    ?>

* Param $email
* Param $text

### dontSeeInLastEmail

Checks that an email does NOT contain a value. It searches the full raw
text of the email: headers, subject line, and body.

Example:

    <?php
    $I->dontSeeInLastEmail('Hit me with those laser beams');
    ?>

* Param $text

### dontSeeInLastEmailTo

Checks that the last email sent to an address does NOT contain a value.
It searches the full raw text of the email: headers, subject line, and body.

Example:

    <?php
    $I->dontSeeInLastEmailTo('admin@example.com', 'But shoot it in the right direction');
    ?>

* Param $email
* Param $text

### seeAttachmentFilenameInLastEmail

Checks that the last email have attachment with following filename.

Example:

    <?php
    $I->seeAttachmentFilenameInLastEmail('expected_journey.ext');
    ?>

* Param $expectedFilename

### dontSeeAttachmentFilenameInLastEmail)

Checks that the last email does NOT have attachment with following filename.

Example:

    <?php
    $I->dontSeeAttachmentFilenameInLastEmail('unexpected_journey.ext');
    ?>

* Param $unexpectedFilename

### seeAttachmentFilenameInLastEmailTo

Checks that the last sent to an address have attachment with following
filename.

Example:

    <?php
    $I->seeAttachmentFilenameInLastEmailTo('admin@example.com', 'expected_journey.ext');
    ?>

* Param $address
* Param $expectedFilename

### dontSeeAttachmentFilenameInLastEmailTo

Checks that the last sent to an address does NOT have attachment with
following filename.

Example:

    <?php
    $I->dontSeeAttachmentFilenameInLastEmailTo('admin@example.com', 'unexpected_journey.ext');
    ?>

* Param $address
* Param $unexpectedFilename

### seeAttachmentsCountInLastEmail

Asserts that a certain number of attachments found in the last email.

Example:

    <?php
    $I->seeAttachmentsCountInLastEmail(1);
    ?>

* Param $exected

### seeAttachmentsCountInLastEmailTo

Asserts that a certain number of attachments found in the last email to a
given address.

Example:

    <?php
    $I->seeAttachmentsCountInLastEmailTo('admin@example.com', 1);
    ?>

* Param $address
* Param $expected

### seeCcInLastEmail

Look for the expected CC address in the last sent email.

Example:

    <?php
    $I->seeCcInLastEmail('cc@example.com');
    ?>

* Param $expectedAddress

### seeCcInLastEmailTo

Look for the expected CC address in the last sent email to a given address.

Example:

    <?php
    $I->seeCcInLastEmailTo('admin@example.com', 'cc@example.com');
    ?>

* Param $address
* Param $expectedAddress

### grabMatchesFromLastEmail

Extracts an array of matches and sub-matches from the last email based on
a regular expression. It searches the full raw text of the email: headers,
subject line, and body. The return value is an array like that returned by
`preg_match()`.

Example:

    <?php
    $matches = $I->grabMatchesFromLastEmail('@<strong>(.*)</strong>@');
    ?>

* Param $regex

### grabFromLastEmail

Extracts a string from the last email based on a regular expression.
It searches the full raw text of the email: headers, subject line, and body.

Example:

    <?php
    $match = $I->grabFromLastEmail('@<strong>(.*)</strong>@');
    ?>

* Param $regex

### grabMatchesFromLastEmailTo

Extracts an array of matches and sub-matches from the last email to a given
address based on a regular expression. It searches the full raw text of the
email: headers, subject line, and body. The return value is an array like that
returned by `preg_match()`.

Example:

    <?php
    $matchs = $I->grabMatchesFromLastEmailTo('user@example.com', '@<strong>(.*)</strong>@');
    ?>

* Param $email
* Param $regex

### grabFromLastEmailTo

Extracts a string from the last email to a given address based on a regular
expression.  It searches the full raw text of the email: headers, subject
line, and body.

Example:

    <?php
    $match = $I->grabFromLastEmailTo('user@example.com', '@<strong>(.*)</strong>@');
    ?>

* Param $email
* Param $regex

### seeEmailCount

Asserts that a certain number of emails have been sent since the last time
`clearMailbox()` was called.

Example:

    <?php
    $match = $I->seeEmailCount(2);
    ?>

* Param $count

# Docker

Now you can build all modules at once by:
```
docker-compose build
```

# License

Released under the same licence as Codeception: MIT
