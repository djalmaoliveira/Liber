<?php

// include Liber framework
require "../../framework/Liber.php";

// prepares enviroment to Liber application
Liber::loadApp( realpath('example/').'/' );

// requesting process
Liber::run();

?>