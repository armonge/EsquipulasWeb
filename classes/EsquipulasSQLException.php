<?php
/**
* class EsquipulasSQLException
* @package exceptions
*/
class EsquipulasSQLException extends EsquipulasException{
    public function __construct($message, $query){
	parent::__construct($message);
	$this->query =$query;
	
    }
    public function __toString(){
        return __CLASS__ . ":
Message: {$this->message}
{$this->details}
======================QUERY==========================
{$this->query}"
        ;
    }
}
?>
