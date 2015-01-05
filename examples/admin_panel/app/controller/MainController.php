<?php

Liber::loadController('BaseController');

/**
 * MainController
 *
 */
class MainController extends BaseController {

    var $url_base;

    function __construct($p) {
        parent::__construct($p);
        Liber::loadHelper(array('Url', 'HTML'));
        $this->view()->template('default.html');
        $this->url_base = url_to_('/', true);
    }

    // after deploy setup application
    public function setup() {
        $oSetup = Liber::loadClass('Setup', true);

        if ( !file_exists(Liber::conf('APP_ROOT').Liber::conf('ASSETS_DIR').'app') ) {
            $oSetup->publishAsset();
        }
    }

    public function index() {
        $this->isUserLogged();
        $data['url'] = url_to_('/user', true).'/';
        $this->view()->load( "index.html", $data );
    }

}

?>