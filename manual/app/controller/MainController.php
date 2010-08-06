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
        $aData['titulo'] = 'Mailer Class';
        $this->oTPL->load("mailer.html",$aData);		
	}
	
	public function mailersend() {
	    $oMailer = Liber::loadClass('Mailer',true);
	    
	    $oMailer->addTo(    Input::post('to') );
	    $oMailer->from(     Input::post('from') );
	    $oMailer->subject(  Input::post('subject') );
	    $oMailer->body(     Input::post('body') );
	    $oMailer->html(     Input::post('html'));

	    if ( $oMailer->send() ) {
	       $out = "Mail sent.<br/>";
	    } else {
	       $out =  "Problem sending mail.<br/> <pre>";
	    }

        $aData['title'] = "Email data requested ";
        $aData['content'] = $out."<br/>".print_r( $oMailer->toArray() , true);
        $this->oTPL->load("output.html",$aData);		
    
	}


	public function input() {
    	Liber::loadHelper('Form');
        $aData['titulo'] = 'Input Class';
        $this->oTPL->load("input.html",$aData);		
	}


}

?>
