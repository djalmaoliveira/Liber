<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class MailerTest extends PHPUnit_Framework_TestCase {

    var $file;

    function SetUp() {
        Liber::loadClass('Mailer');
        $this->file = __FILE__;
    }

    function tearDown() {
    }


    function testSendMailText() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('test as plain text');
        $m->body('teste body');
        $this->assertTrue($m->send(), 'Mail not sent');
    }


    function testSendMailHtml() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('teste as html');
        $m->body('<html><body>teste <strong>body</strong></body></html>');
        $m->html(true);
        $this->assertTrue($m->send(), 'Mail not sent');
    }

    function testSendMailTextWithFile() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('test as plain text with attach');
        $m->body('teste body');
        $m->file($this->file);
        $this->assertTrue($m->send(), 'Mail not sent');
    }

    function testSendMailHtmlWithFile() {
        $m = new Mailer;

        $m->addTo('test@localhost.home');
        $m->from('from_test@localhost.home');
        $m->subject('test as html text with attach');
        $m->body('teste <strong>body</strong>');
        $m->file($this->file);
        $m->html(true);
        $this->assertTrue($m->send(), 'Mail not sent');
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

    function testAddressesSpaceSepareted() {
        $m = new Mailer;

        $m->addTo("test@localhost.home");
        $m->subject('test-spaces');
        $m->body('body');
        $this->assertTrue($m->send(), 'Send mail with spaces among addresses.');
    }

    function testToAddressesWithSpaceSepareted() {
        $m = new Mailer;

        $m->addTo("test@localhost.home, test2@localhost.home");
        $m->subject("You shouldn't see this mail'");
        $m->body('body');
        $this->assertFalse($m->send(), 'Send mail with To Addresses With Space Separated.');
    }

    function testToMultipleAddressesArray() {
        $m = new Mailer;

        $m->to(Array("test@localhost.home","test2@localhost.home"));
        $m->subject("Send mail to with two addresses Array.");
        $m->body('body');
        $this->assertTrue($m->send(), 'Send mail to with two addresses Array.');
    }


    function testAddToWithEmptyAndInvalidAddress() {
        $m = new Mailer;

        $m->addTo( Array('', 'asdasdas', "test@localhost.home" ) );
        $m->from('teste@localhost.home');
        $m->subject('test-empty_to');
        $m->body('trying to send to 3 mails address');
        $this->assertTrue($m->send(), 'Cannot send mail with multiple froms.');
    }

}
?>
