<?php

class MainController extends Controller {

    function index() {
        return "index";
    }

	function errorLog() {
		$this->b();
	}

    function b() {

    }

    function slashArgs($arg1, $arg2) {
        echo $arg1;
        echo $arg2;
    }
}

?>
