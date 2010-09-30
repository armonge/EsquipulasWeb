<?php
class MySQLIEsquipulas extends MySQLI{
    public function real_escape_string( $escapestr ){

        if (get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }

        $escapestr = strip_tags($escapestr);
        $escapestr = trim($escapestr);
        return parent::real_escape_string($escapestr);
    }
    public function smart_quote($string){
        $string = $this->real_escape_string($string);
        if(!is_numeric($string)){
            $string = " '" . $string . "' ";
        }
        return $string;
    }
    public function smart_quote_array(&$item, $key){
        $item = $this->smart_quote($item);
    }
    public function simple_insert($table, $data){
        $keys = array_keys($data);
        $values = array_values($data);


        array_walk($values, array(&$this, "smart_quote_array"));



        
        $query = "INSERT INTO $table ( " . implode(" , ", $keys) ." ) VALUES ( ". implode(" , " ,$values ) . " ) ";

        $this->query($query);
        
    }
    public function query($query){
        if($this->debug){
            echo "<h1>$query</h1>";
        }

        $result= parent::query($query);
        if (!$result){
            throw new EsquipulasSQLException($this->error,$query);
        }else{
            return $result;
        }
    }
}
?>