<?php

class SysErrorController extends Controller {

    function __construct( $p=array('method' => '') ) {
        parent::__construct($p);
        Liber::loadHelper(array('HTML','Url'));
    }


    public function index() {
        header('HTTP/1.0 500 Not Found');

        if ( !Http::ajax() ) {
            Liber::redirect('/error?url='.urlencode(url_current_(true)));
        }
    }


    public function error() {
        header('HTTP/1.0 500 Not Found');
        $this->view()->template('default.html');
        $this->view()->load('error.html');
        exit;
    }

}
?>