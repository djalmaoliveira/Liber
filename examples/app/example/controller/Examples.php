<?php

/**
 * Examples Controller
 *
 */
class Examples extends Controller{

    function __construct($p) {
        parent::__construct($p);
    }


    public function index() {
        echo "Example index.";
    }

    public function process($param='') {
        echo urldecode("processing $param example.");
    }

    public function named() {
        echo $this->params('name');
    }

}

?>