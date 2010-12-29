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

}
?>
