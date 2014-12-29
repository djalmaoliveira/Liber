<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class AttemptTest extends PHPUnit_Framework_TestCase {



    function SetUp() {
        Liber::loadClass('Attempt');
    }

    function tearDown() {
    }


    function testNameSpacesOnDifferentInstances() {
        $A = new Attempt('r1');
        $A->reset();
        $A->watch(5);

        $A1 = new Attempt('r1');
        $A2 = new Attempt('r2');
        $this->assertTrue($A1->isWatching(), "Attempt should be watching with same namespace." );
        $this->assertFalse($A2->isWatching(), "Attempt should not be watching with different namespace." );
    }

    function testLimitsAndTries() {
        $A = new Attempt('r1');
        $A->reset();
        $A->watch(5);
        $A->increase();
        $A->increase();

        $this->assertEquals(2, $A->tries(), "Number of tries should be 2." );
        $this->assertFalse($A->isBlocked(), "Attempt should not be blocked." );

        $A->increase(4);

        $this->assertTrue($A->isBlocked(), "Attempt should be blocked." );
    }

    function testNotExpectedUse() {
        $A = new Attempt('r1');

        $this->assertFalse($A->isWatching(), "Attempt should not be watching." );
        $A->increase();
        $this->assertEquals(0, $A->tries(), "Number of tries should be 0." );
        $this->assertFalse($A->isBlocked(), "Attempt should not be blocked." );
        $this->assertEquals(0, $A->waitTime(), "Wait time should be 0." );
    }


    function testWaitTime() {
        $A = new Attempt('r1');
        $A->reset();
        $A->watch(2);
        $A->increase(3);

        $this->assertEquals(60, $A->waitTime(), "Should be wait for 60 seconds." );
        $A->increase();
        $this->assertEquals(180, $A->waitTime(), "Should be wait for 180 seconds." );
    }


}
?>
