<?php

/**
 * MainController
 *
 */
class MainController extends Controller{

    var $oTPL;

    function __construct() {
     	Liber::loadHelper('Url');
        $this->view()->template('default.html');
    }


    public function index(){

		// setup application
        if ( !file_exists(Liber::conf('APP_ROOT').Liber::conf('ASSETS_DIR').'app') ) {
            $this->setup();
        }

        $aData['titulo'] = 'Liber Manual';

        $this->view()->load("index.html",$aData);
    }

	public function setup() {
    	$oSetup = Liber::loadClass('Setup', true);
    	$oSetup->publishAsset();
	}


	public function mailer() {

    	Liber::loadHelper('Form');
        $this->view()->load("mailer.html");
	}

	public function mailersend() {
	    $oMailer = Liber::loadClass('Mailer',true);

	    $oMailer->addTo(    Http::post('to') );
	    $oMailer->from(     Http::post('from') );
	    $oMailer->subject(  Http::post('subject') );
	    $oMailer->body(     Http::post('body') );
	    $oMailer->html(     Http::post('html'));

	    if ( $oMailer->send() ) {
	       $out = "Mail sent.<br/>";
	    } else {
	       $out =  "Problem sending mail.<br/> <pre>";
	    }

        $aData['title'] = "Email data requested ";
        $aData['content'] = $out."<br/>".print_r( $oMailer->toArray() , true);
        $this->view()->load("output.html",$aData);

	}


	public function http() {
    	Liber::loadHelper('Form');
        $this->view()->load("http.html");
	}

	public function liber() {
        $this->view()->load("liber.html");
	}


	public function log() {
        $this->view()->load("log.html");
	}

	public function viewfiles() {
        $this->view()->load("viewfiles.html");
	}

	public function controllerfiles() {
        $this->view()->load("controllerfiles.html");
	}

    public function session() {
        $this->view()->load("session.html");
    }

    public function cart() {
        $aData['cart'] = Liber::loadClass('Cart', true);

        if ( Http::get('add') == 'true' ) {
            $aData['cart']->insert(Array('#'=>'4548', 'product'=>'Liber Manual'));
        }
        if ( Http::get('clear') == 'true' ) {
            $aData['cart']->clear();
        }

        $this->view()->load("cart.html", $aData);
    }


    public function minify() {
        Liber::loadHelper('Url');
        $this->view()->load("minify.html");
    }


    public function helper_form() {
        Liber::loadHelper('Form');
        $this->view()->load("helper_form.html");
    }

    public function helper_url() {
        Liber::loadHelper('Url');
        $this->view()->load("helper_url.html");
    }

    public function routes() {
        Liber::loadHelper('Url');
        $this->view()->load("routes.html");
    }

}

?>