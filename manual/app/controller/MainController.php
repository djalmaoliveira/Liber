<?php

/**
 * MainController
 *
 */
class MainController extends Controller{

    var $oTPL;

    function __construct() {
     	Liber::loadHelper('Url');
        $this->oTPL = $this->view()->template();
    }


    public function index(){

		// setup application
        if ( !file_exists(Liber::conf('APP_ROOT').Liber::conf('ASSETS_DIR').'app') ) {
            $this->setup();
        }

        $aData['titulo'] = 'Liber Manual';

        $this->oTPL->load("index.html",$aData);
    }

	public function setup() {
    	$oSetup = Liber::loadClass('Setup', true);
    	$oSetup->publishAsset();
	}


	public function mailer() {

    	Liber::loadHelper('Form');
        $this->oTPL->load("mailer.html");
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
        $this->oTPL->load("output.html",$aData);

	}


	public function http() {
    	Liber::loadHelper('Form');
        $this->oTPL->load("http.html");
	}

	public function liber() {
        $this->oTPL->load("liber.html");
	}


	public function log() {
        $this->oTPL->load("log.html");
	}

	public function viewfiles() {
        $this->oTPL->load("viewfiles.html");
	}

	public function controllerfiles() {
        $this->oTPL->load("controllerfiles.html");
	}

    public function session() {
        $this->oTPL->load("session.html");
    }

    public function cart() {
        $aData['cart'] = Liber::loadClass('Cart', true);

        if ( Http::get('add') == 'true' ) {
            $aData['cart']->insert(Array('#'=>'4548', 'product'=>'Liber Manual'));
        }
        if ( Http::get('clear') == 'true' ) {
            $aData['cart']->clear();
        }

        $this->oTPL->load("cart.html", $aData);
    }


    public function minify() {
        Liber::loadHelper('Url');
        $this->oTPL->load("minify.html");
    }


    public function helper_form() {
        Liber::loadHelper('Form');
        $this->oTPL->load("helper_form.html");
    }

    public function helper_url() {
        Liber::loadHelper('Url');
        $this->oTPL->load("helper_url.html");
    }

    public function routes() {
        Liber::loadHelper('Url');
        $this->oTPL->load("routes.html");
    }

}

?>