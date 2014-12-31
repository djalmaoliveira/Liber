<?php

// include Liber framework
require "../../../framework/Liber.php";

// prepares enviroment to Liber application
Liber::loadApp( realpath('../app/').'/' );


header("Content-Security-Policy: script-src 'self' 'unsafe-inline'; frame-src 'self' ; style-src 'self' 'unsafe-inline'");
header("X-Frame-Options: SAMEORIGIN;");


// requesting process
Liber::run();

?>