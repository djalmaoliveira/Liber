<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class MailerTest extends PHPUnit_Framework_TestCase {



    function SetUp() {

    }

    function tearDown() {
    }


    function testSendMailText() {
        $m = Liber::loadClass('Mailer', true);

        $m->addTo('test@localhost');
        $m->from('from_test@localhost');
        $m->subject('teste subject');
        $m->body('teste body');
        $m->send();
    }


    function testSendMailHtmlk() {
        $m = Liber::loadClass('Mailer', true);

        $m->addTo('test@localhost');
        $m->from('from_test@localhost');
        $m->subject('teste subject');
        $m->body('teste <strong>body</strong>');
        $m->html(true);
        $m->send();
    }

    function testSendMailTextWithFile() {
        $m = Liber::loadClass('Mailer', true);

        $m->addTo('test@localhost');
        $m->from('from_test@localhost');
        $m->subject('teste subject');
        $m->body('teste body');
        $m->file('/home/usuario/a.php');
        $m->send();
    }

    function testSendMailHtmlWithFile() {
        $m = Liber::loadClass('Mailer', true);

        $m->addTo('test@localhost');
        $m->from('from_test@localhost');
        $m->subject('teste subject');
        $m->body('teste <strong>body</strong>');
        $m->file('/home/usuario/a.php');
        $m->html(true);
        $m->send();
    }


}
?>
