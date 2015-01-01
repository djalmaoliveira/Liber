<?php

/**
 * Common routines controller.
 */
class BaseController extends Controller {

    protected $module;
    protected $method;
    protected $user;

    function __construct($p) {
        parent::__construct($p);
        $this->method = $p['method'];
        $this->module = $p['module'];
        Liber::loadHelper( array('Url', 'HTML', 'Paginate') );
    }


    protected function isUserLogged() {
        $User = Liber::loadModel('User', 'User' ,  true);
        if ( !($User->hasLogin())  ) {
            Liber::redirect('/user/login');
        }

        $this->user = $User->loggedUser();
    }

    private function resp($status, $msg, $data=array()) {
        $resp['status'] = $status;
        $resp['info']   = $msg;
        if ( $data ) {
            $resp = array_merge($resp, $data);
        }

        Http::contentType('json');
        echo json_encode($resp, true);
    }

    protected function respOk($msg, $data=array()) {
        $this->resp(true, $msg, $data);
    }

    protected function respError($msg, $data=array()) {
        $this->resp(false, $msg, $data);
    }

    /**
     * Register app events.
     * @return void
     */
    protected function log( $msg='' ) {
        $hashtag = "#".get_class($this).'/'.$this->method;
        Liber::log()->add( "$hashtag $msg \r\n" );
    }


}

?>