<?php

/**
 * MainController
 *
 */
class MainController extends Controller{

    function __construct($p) {
        parent::__construct($p);
    }


    public function index(){
        //$this->view()->cache('home.html', 10);
        $this->view()->load('home.html');

    }


    public function browser() {
        echo $_SERVER['HTTP_USER_AGENT'];
    }

    public function error() {
        b(); // this function doesn't exist and log this error
    }

    public function options($opt1='', $opt2='') {
        echo "options selected<br/>";
        echo $opt1;
        echo "<br/>";
        echo $opt2;
    }

}

?>
