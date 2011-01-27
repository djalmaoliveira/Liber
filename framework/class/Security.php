<?php
/**
*   @package core.class
*/


/**
*   Class with some useful method used with security implementations.
*/
class Security {

    /**
    *   Generate and/or return a token in a current session.
    *   @param boolean $renew
    *   @return String
    */
    function token($renew=false) {
        Liber::loadClass('Session');
        $oSession = new Session('security');
        if ( $renew or !($oSession->val('token')) ) {
            $oSession->val('token', uniqid('liber'));
        }
        return $oSession->val('token');
    }

    /**
    *   Verify if $token specified is the same in current session.
    *   @param String $token
    *   @return boolean
    */
    function validToken($token) {
        Liber::loadClass('Session');
        $oSession = new Session('security');
        return ($token==$oSession->val('token'));
    }

}


?>