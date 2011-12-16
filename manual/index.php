<?php
// include Liber framework
include_once "../framework/Liber.php";

// prepares enviroment to Liber application
Liber::loadApp( realpath('app/').'/' );

// requesting process
Liber::run();

?>
