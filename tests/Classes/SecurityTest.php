<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class SecurityTest extends PHPUnit_Framework_TestCase {



    function SetUp() {
        Liber::loadClass('Security');
    }

    function tearDown() {
    }


    function testFirstToken() {
        $s = new Security;
        $token = $s->token();
        $this->assertTrue( !empty($token), "Token should have some value." );
    }

    function testRenewToken() {
        $s = new Security;
        $token = $s->token();
        $newToken = $s->token(true);
        $this->assertNotEquals($token, $newToken, "Tokens should be different." );
    }



}
?>
