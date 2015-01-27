<?php

class NotFoundController {

    public function index() {
        header('HTTP/1.0 404 Not Found');

        echo '<h1>ERROR 404 not found</h1>';
        echo 'Trying to access: '.$_SERVER['REQUEST_URI'];
        echo '<p>This is handler by an internal Route as defined in "APP_PATH/config/config.php"</p>

<p>Your error document needs to be more than 512 bytes in length. If not IE will display its default error page.</p>

<p>Give some helpful comments other than 404 :(
Also check out the links page for a list of URLs available.</p>';
    }


}
?>

