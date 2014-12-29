<?php
/**
*   Class used to control a number of attempts done by some routine that is needed to be monitored.
*   The meaning of number of tries is a value that is used to compare against limit specified by <em>watch($limit)</em> method, that Attempt object will be blocked if a number of tries exceeds it $limit. .
*   The meaning of wait time is the number of seconds that stay Attempt object blocked before next try based in last time that <em>increase()</em> method was called.
*   @package classes
*/
class Attempt {

    var $Session;

    /**
     * Specifing a $namespace you can watch differents routines.
     * @param string $namespace
     */
    function __construct( $namespace='' ) {
        Liber::loadClass('Session');
        $this->Session = new Session('liber_attempt_'.$namespace);
    }

    /**
     * Start watching until reach a number of $limit specified.
     * @param  integer $limit
     * @return void
     */
    function watch( $limit=3 ) {
        $this->Session->val('limit', $limit);
    }

    /**
     * Indicate if is watching attempts.
     * @return boolean
     */
    function isWatching() {
        return ($this->Session->val('limit') and $this->Session->val('limit') > 0);
    }


    /**
     * Reset the number of attempts made.
     * @return void
     */
    function reset() {
        $this->Session->val('tries', 0);
        $this->Session->val('last_attempt', 0);
    }


    /**
     * Increase by 1 a number of attempt.
     * @param  integer $by If specified this is the value that tries will increase.
     * @return void
     */
    function increase( $by=1 ) {
        if ( $this->isWatching() ) {
            if ( !$by or $by <= 0 ) {
                $by = 1;
            }
            $this->Session->val('tries', $this->Session->val('tries') + $by);
            $this->Session->val('last_attempt', time());
        }
    }


    /**
     * Return a number of seconds that is a next attempt can be made.
     * The wait time is calculated by the sum of sequential numbers that the <em>tries</em> exceeds the <em>limit</em> multiplied by 60 seconds.
     * Example: Limit=5, Tries=7, then waitTime=180
     * Explains: Tries - Limit = 2, Sequential is 1,2, Sum of sequential is 3 (1+2), then 3*60 = 180
     * @return integer
     */
    function waitTime() {
        $tries = $this->Session->val('tries');
        $limit = $this->Session->val('limit');
        if ( !$tries ) {
            return 0;
        }

        $base    = ($tries - $limit);
        $minutes = 0;
        for( $i=1; $i <= $base; $i++ ) {
            $minutes += $i;
        }

        return $minutes*60;
    }


    /**
     * Indicate if attempt is blocked.
     * The blocking depends of number of tries and the wait time.
     * @return boolean
     */
    function isBlocked() {
        if ( $this->isWatching() ) {
            // Blocked by number of tries
            if ( $this->Session->val('tries') > $this->Session->val('limit') ) {
                // Blocked by time between tries
                if ( time() < ($this->Session->val('last_attempt') + $this->waitTime()) ) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Return current number of attempts made.
     * @return integer
     */
    function tries() {
        if ( $this->isWatching() ) {
            return $this->Session->val('tries');
        }

        return 0;
    }

}