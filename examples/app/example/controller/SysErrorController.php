<?php

class SysErrorController extends Controller {

    public function index() {
        header('HTTP/1.0 500 Internal Error');

        echo "Oops, an application error was found.";
        echo "<hr>";
        echo Liber::log()->toString();
    }
}
?>

