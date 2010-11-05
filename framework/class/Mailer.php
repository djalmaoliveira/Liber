<?php
/**
*   @package core.class
*/



/**
*   A simple Mailer class, for sending e-mails.
*   It is capable to send text or html messages.
*/
class Mailer {
    
    private $aMail      = Array('subject'=>'', 'body'=>'','headers'=>Array());
    private $files      = Array();
    
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
    *   Set ReplyTo email address.
    *   You can use: reply('name', 'email') or reply('email')        
    *   @param String $name
    *   @param String $email
    */
    public function reply($name, $email='') {
        $reply = '';
        if ( func_num_args() == 1 ) {
            $reply = $name;  
        } else {
            $reply  = "$name <$email>";
        }
        $this->header('Reply-To', $reply);
    }    

    /**
    *   Add attachment files.
    *   Usage:  ->file('/path/to/file');
    *           ->file(Array('/path/file1', '/path/file2'));
    *   @param String | Array $files
    */
    public function file($files=null) {
        if ( is_array($files) ) {
            $this->files = $this->files + $files;
        } else {
            $this->files[] = $files;
        }
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
            $headers .= $header.': '.$value."\n";
        }
        
        $headers .= "MIME-Version: 1.0\n";
        $message_header = "Content-Type: text/".($this->aMail['html']?'html':'plain')."; charset=".$this->aMail['charset']."\nContent-Transfer-Encoding: 8bit";
       
        // attachments
        if ( count($this->files) > 0 ) {
            $boundary = 'Multipart_Boundary_x'.md5(time()).'x';
            $headers  .= 'Content-Type: multipart/mixed; boundary="'."{$boundary}".'"'."\n";
            $this->aMail['body'] = "This is a multi-part message in MIME format.\n\n" . "--{$boundary}\n" .$message_header."\n\n".$this->aMail['body']."\n\n";
            $attachs = '';
            foreach( $this->files as $filepath ) {
                if ( file_exists($filepath) ) {
                    $name = basename($filepath);
                    $h  = "--{$boundary}\n";
                    $h .= "Content-Type: application/octet-stream; name=\"$name\"\n";
                    $h .= "Content-Disposition: attachment; filename=\"$name\"\n";
                    $h .= "Content-Transfer-Encoding: base64\n\n";
                    $h .= chunk_split(base64_encode(file_get_contents($filepath)))."\n";
                    
                    $attachs .= $h;
                }
            }
            $attachs .= "--{$boundary}--\n";
            $this->aMail['body'] .= $attachs;
        } else {
            $headers = $headers.$message_header;
        }
        
        return mail($to, $this->aMail['subject'], $this->aMail['body'], $headers);
    }    




}


?>
