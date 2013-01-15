<?php

/**
 * MainController
 *
 */
class MainController extends Controller{

    function __construct($p) {
        parent::__construct($p);
        Liber::loadHelper('Url');
    }


    public function index(){
        //$this->view()->cache('home.html', 10);
        $this->view()->load('home.html');

    }


    public function browser() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo $_SERVER['HTTP_USER_AGENT'];
    }

    // catch error
    public function error() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        b(); // this function doesn't exist and log this error
    }

    // URL /params/one/two
    public function params($param1='', $param2='') {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo "Params passed:<br/>";
        echo $param1;
        echo "<br/>";
        echo $param2;
    }

    public function direct() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo 'Direct routing.';
    }

}

?>