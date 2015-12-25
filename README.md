# Codeception MailChecker Module

[![Build Status](https://travis-ci.org/johnatannvmd/codeception-mailchecker-module.svg?branch=master)](https://travis-ci.org/johnatannvmd/codeception-mailchecker-module)

This module will let you test emails that are sent during your Codeception
acceptance tests.

It was inspired by the https://github.com/captbaritone/codeception-mailcatcher-module and
https://github.com/fetch/zend-mail-codeception-module/.

It supports several mail providers:

* [MailCatcher](http://mailcatcher.me/)
* [MailDump](https://github.com/ThiefMaster/maildump)
* [ZenMail](https://github.com/zendframework/zend-mail)

## Installation

Add the packages into your `composer.json`. For example we add Guzzle lib for MailCatcher
for MailDump provider:

    {
        "require-dev": {
            "codeception/codeception": "*",
            "johnatannvmd/mailchecker-codeception-module": "1.*",
            "guzzle/guzzle": "3.*"
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

You will then need to rebuild your actor class:

    php codecept.phar build

## Optional Configuration

If you need to specify some special options (e.g. SSL verification or authentication
headers), you can set all of the allowed [Guzzle request options](http://docs.guzzlephp.org/en/latest/request-options.html):

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
                    guzzleOptions:
                        auth: ['yo', 'yo']

You will then need to rebuild your actor class:

    vendor/bin/codecept build

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

Checks that an email does NOT contain a value. It searches the full raw text of the
email: headers, subject line, and body.

Example:

    <?php
    $I->dontSeeInLastEmail('Hit me with those laser beams');
    ?>

* Param $text

### dontSeeInLastEmailTo

Checks that the last email sent to an address does NOT contain a value. It searches the
full raw text of the email: headers, subject line, and body.

Example:

    <?php
    $I->dontSeeInLastEmailTo('admin@example.com', 'But shoot it in the right direction');
    ?>

* Param $email
* Param $text

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

# License

Released under the same licence as Codeception: MIT
