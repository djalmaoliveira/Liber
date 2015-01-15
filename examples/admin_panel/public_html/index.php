<?php

// include Liber framework
require "../../../framework/Liber.php";

// prepares enviroment to Liber application
Liber::loadApp( realpath('../app/').'/' );

// security settings
header("Content-Security-Policy: script-src 'self' 'unsafe-inline'; frame-src 'self' ; style-src 'self' 'unsafe-inline'");
header("X-Frame-Options: SAMEORIGIN;");
ini_set('session.cookie_httponly', true);
ini_set('session.use_strict_mode', true);

// requesting process
Liber::run();

?>