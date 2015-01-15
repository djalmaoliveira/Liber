<?php

class SysErrorController extends Controller {

    function __construct($p) {
        parent::__construct($p);
    }


    public function index() {
        echo "system error"    ;
        exit;
    }


}
?>

