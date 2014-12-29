<?php

class NotFoundController extends Controller {

    public function index() {
        Liber::loadHelper(array('HTML','Url'));

        Http::statusCode(404);

        $this->view()->template('default.html');
        $this->view()->load( 'notfound.html' );
    }


}
?>