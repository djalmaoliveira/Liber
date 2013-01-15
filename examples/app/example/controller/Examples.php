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
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo "Example index.";
    }

    public function process($param='') {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo urldecode("processing $param example.");
    }

    public function named() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo $this->params('name');
    }

    public function others() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo "Others";
    }

}

?>