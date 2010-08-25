<?php
/**
*   @package core.class
*/



/**
*   A simple Mailer class, for sending e-mails.
*   It is capable to send text or html messages.
*/
class Mailer {
    
    private $aMail = Array('headers'=>Array());

    function __construct() {
        $this->html();
        $this->charset();
    }

    /**
    *   Return Array of data stored on current object Mailer.
    *   @return Array
    */
    public function toArray() {
        return $this->aMail;        
    }    
    
    /**
    *   Set current charset of mail, default value 'utf-8'.
    *   @param String $charset
    */
    public function charset($charset='utf-8') {
        $this->aMail['charset'] = $charset;
    }    

    /**
    *   Set if mail is a html message, default value 'false'.
    *   @param boolean $isHtml
    */
    public function html($isHtml = false) {
        $this->aMail['html'] = $isHtml;
    }    
    
    /**
    *   Set the subject of mail.
    *   @param String $subject
    */
    public function subject($subject) {
        $this->aMail['subject'] = trim($subject);
    }    
    
    /**
    *   Set mail body.
    *   @param String $body
    */
    public function body($body) {
        $this->aMail['body'] = trim($body);
    }    
    
    /**
    *   Set headers of mail, like Reply-to, From. 
    *   @param String $header
    *   @param String $value
    */
    public function header($header, $value) {
        $this->aMail['headers'][$header] = $value;
    }    
    
    
    /**
    *   Add one or more destinations to mail.
    *   @param String $to
    */
    public function addTo($to) {
        $this->aMail['to'][] = trim($to);
    }    
    
    /**
    *   Set original sender to mail.
    *   You can use: from('name', 'email') or from('email')        
    *   @param String $name
    *   @param String $email
    */
    public function from($name, $email='') {
        $from = '';
        if ( func_num_args() == 1 ) {
            $from = $name;  
        } else {
            $from  = "$name <$email>";
        }
        $this->header('From', $from);
    }    
    
    /**
    *   Try to deliver the mail to MTA.
    *   Return true if get, or false otherwise.        
    *   @return boolean
    */
    public function send() {
        $to = implode(' , ', $this->aMail['to']);
        // prepare headers
        $headers = '';
        foreach( $this->aMail['headers'] as $header => $value ) {
            $headers .= $header.': '.$value."\r\n";
        }

        // html
        $headers .= 'MIME-Version: 1.0'."\r\n"."Content-type: text/".($this->aMail['html']?'html':'plain')."; charset=".$this->aMail['charset']."\r\n";        
        
        return mail($to, $this->aMail['subject'], $this->aMail['body'],$headers);
    }    



}


?>
