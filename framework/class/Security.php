<?php
/**
*   Class with some useful method used with security implementations.
*   .
*   @package classes
*/
class Security {

	protected $oSession;
    protected $start_delay = 0;
    protected $delay_time = 0;

	function __construct() {
        Liber::loadClass('Session');
		$this->oSession = new Session('security');
	}

    /**
    *   Generate and/or return a token in a current session.
    *   @param boolean $renew
    *   @return String
    */
    function token($renew=false) {
        if ( $renew or !($this->oSession->val('token')) ) {
            $this->oSession->val('token', uniqid('liber'));
        }
        return $this->oSession->val('token');
    }

    /**
    *   Verify if $token specified is the same in current session.
    *   @param String $token
    *   @return boolean
    */
    function validToken($token) {
        return ($token==$this->oSession->val('token'));
    }

	/**
	*	Start watching monitor by client.
    *   Return the initial values that should be monitored.
    *   By default watch change for IP and AGENT.
	*	@param Array $options - Options that should be verified, can be 'ip', 'agent'.
    *   @return  array
	*/
	function clientWatch( $options=array('ip', 'agent') ) {
		$monitors = array();
		if ( in_array('ip', $options) ) {
			$monitors['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		if ( in_array('agent', $options) ) {
			$monitors['agent'] = ($_SERVER['HTTP_USER_AGENT']);
		}

		$this->oSession->val('monitor', $monitors);
        return $monitors;
	}

	/**
	*	Return Array of changes and current values detected since clientWatch() method call.
	*	@return Array
	*/
	function clientChanged() {
		$changes = array();
		if ( !$this->oSession->val('monitor') ) { return $changes; }

        foreach( $this->oSession->val('monitor') as $type => $value ) {
			switch ($type) {

				case 'ip':
					if ( $_SERVER['REMOTE_ADDR'] != $value ) {
						$changes[$type] = $_SERVER['REMOTE_ADDR'];
					}
				break;

				case 'agent':
					if ( $_SERVER['HTTP_USER_AGENT'] != $value ) {
						$changes[$type] = $_SERVER['HTTP_USER_AGENT'];
					}
				break;
			}
		}
		return $changes;
	}

    /**
     * Start a $delay time in seconds from start until stopDelay() called point.
     * It is used to avoid timing attack.
     * It is cannot do perfect sleep time protection in some cases, like a busy server or busy routines.
     * <code>
     * $Sec = Liber::loadlClass('Security',true);
     * $Sec->startDelay( 5 );
     *
     * some_routines_to_protected_by_time_elapsed_less_than_5_seconds();
     *
     * $Sec->stopDelay();
     * </code>
     * @param  integer $delay
     * @return void
     */
    function startDelay( $delay ) {
        $this->delay_time  = $delay;
        $this->start_delay = microtime(true);
    }

    /**
     * Sleep running script until the predefined startDelay().
     * Sleep only if the start delay time wasn't exceeded.
     * @return void
     */
    function stopDelay() {
        $wait = $this->start_delay + $this->delay_time;
        time_sleep_until($wait);
    }
}


?>