<?php
/**
* class EsquipulasException
* @package exceptions
* @author Andrés Reyes Monge <armonge@gmail.com>
*/
class EsquipulasException extends Exception{
    protected $details;
    public function __construct($message = null, $code = 0, Exception $previous = null){
       parent::__construct($message , $code , $previous );
       $this->details = "
	=============== DETAILS ================
    Line : " . $this->line . "
	Time: " . date("r") . "
	IP: " . $_SERVER['REMOTE_ADDR'] . "
	Browser: " . $_SERVER['HTTP_USER_AGENT'] . "
	File: " . $_SERVER['PHP_SELF'] . "
	Server: " . $_SERVER['SERVER_NAME'] . "
	Method: " . $_SERVER['REQUEST_METHOD'] . "
	Query: " . $_SERVER['QUERY_STRING'] . "
	HTTP Language: " . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . "
	Referer: " . $_SERVER['HTTP_REFERER'] . "
	URI: $basedir" . $_SERVER['REQUEST_URI'] .	"
        ============= GET  VARIABLES =============

        ". print_r($_GET, true) . "

	    ============= POST VARIABLES ==============

	    " . print_r($_POST, true) ."

	    ============= COOKIE VARIABLES ==============

	    " . print_r($_COOKIE, true);
    }
    
    public function __toString(){
        return 
            __CLASS__ . ":
            Message: {$this->message}
            {$this->details}
            "
        ;
    }
    public function mail($mail){
        mail($mail, "Error", $this, "From:Esquipulas AutoMail<automail@esquipulas.com>");
    }
}
?>
