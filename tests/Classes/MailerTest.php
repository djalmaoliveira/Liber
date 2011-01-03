<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class MailerTest extends PHPUnit_Framework_TestCase {



    function SetUp() {
        Liber::loadClass('Mailer');
    }

    function tearDown() {
    }


    function testSendMailText() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('teste subject');
        $m->body('teste body');
        $m->send();
    }


    function testSendMailHtmlk() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('teste subject');
        $m->body('teste <strong>body</strong>');
        $m->html(true);
        $m->send();
    }

    function testSendMailTextWithFile() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('teste subject');
        $m->body('teste body');
        $m->file('/home/usuario/a.php');
        $m->send();
    }

    function testSendMailHtmlWithFile() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('teste subject');
        $m->body('teste <strong>body</strong>');
        $m->file('/home/usuario/a.php');
        $m->html(true);
        $m->send();
    }

    function testMultipleReplyAddresses() {
        $m = new Mailer;

        $replys = Array('r1@localhost.home', 'r2@localhost.home', 'Liber'=>'test@localhost.home');
        $m->addTo("test@localhost.home");
        $m->reply($replys);
        $m->subject('test-replys');
        $m->body('body');
        $this->assertTrue($m->send(), 'Cannot send mail with multiple replys.');
    }


    function testMultipleFromAddresses() {
        $m = new Mailer;

        $froms = Array('r1@localhost.home', 'r2@localhost.home', 'Liber'=>'test@localhost.home');
        $m->addTo("test@localhost.home");
        $m->from($froms);
        $m->subject('test-froms');
        $m->body('body');
        $this->assertTrue($m->send(), 'Cannot send mail with multiple froms.');
    }

    function testAddressesSpaceSeparated() {
        $m = new Mailer;

        $m->addTo("test@localhost.home");
        $m->subject('test-spaces');
        $m->body('body');
        $this->assertFalse($m->send(), 'Send mail with spaces among addresses.');
    }

    function testToAddressesWithSpaceSeparated() {
        $m = new Mailer;

        $m->addTo("test@localhost.home, test2");
        $m->subject('test-spaces-into-field');
        $m->body('body');
        $this->assertFalse($m->send(), 'Send mail with To Addresses With Space Separated.');
    }


}
?>
