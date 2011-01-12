<?php

/**
 * MainController
 *
 */
class MainController extends Controller{

    public function index(){
        $this->view()->cache('home.html', 10);
		$this->view()->load('home.html');

    }


	public function browser() {
	    echo $_SERVER['HTTP_USER_AGENT'];
	}

}

?>
