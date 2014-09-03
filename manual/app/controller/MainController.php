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


    public function minify($type='') {
        Liber::loadHelper('Url');
        Liber::loadClass('Minify');

        switch ($type) {
            case 'css':
                $css_file = file_get_contents( Liber::conf('APP_PATH').'assets/css/main.css' );
                echo Minify::css($css_file);
            break;

            case 'js':
                $js_file = file_get_contents(Liber::conf('APP_PATH').'assets/js/jquery.js');
                echo Minify::js($js_file);
            break;

            case 'html':
                $html_file = file_get_contents(url_to_('/minify', true));
                echo Minify::html($html_file);
            break;

            case '':
                $this->view()->load("minify.html");
            break;
        }



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