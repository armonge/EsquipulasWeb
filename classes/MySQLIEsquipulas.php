<?php
/**
* class MySQLIEsquipulas
* @package sql
* @author Andrés Reyes Monge <armonge@gmail.com>
*/
class MySQLIEsquipulas extends MySQLI{
    /**
    * 
    /
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
    /**
    * Función que ejecuta smart_quote en un elemento
    *
    *
    * @param mixed $item
    * @param mixed $key
    */
    public function smart_quote_array(&$item, $key){
        $item = $this->smart_quote($item);
    }

    
    /**
    * Inserta una fila en la base de datos
    *
    * Esta funciónn toma el nombre de una tabla y un arreglo de la forma $key=>$value para generar una
    * consulta de tipo insert y luego la ejecuta,
    * Automaticamente escapa todos los caracteres que sean necesario y añade comillas a las strings
    * Es responsabilidad del programador escapar los valores que introduce como $keys y el nombre de la
    * tabla
    *
    * @param array $table
    * @param array $data
    */
    public function simple_insert($table, $data){
        $keys = array_keys($data);
        $values = array_values($data);


        array_walk($values, array(&$this, "smart_quote_array"));

        
        $query = "INSERT INTO $table ( " . implode(" , ", $keys) ." ) VALUES ( ". implode(" , " ,$values ) . " ) ";

        $this->query($query);
        
    }
    /**
    * Realiza un update sobre una fila de la base de datos
    *
    * Esta funciónn toma el nombre de una tabla y dos arreglos de la forma $key=>$value para generar una
    * consulta de tipo update y luego la ejecuta,
    * Automaticamente escapa todos los caracteres que sean necesario y añade comillas a las strings
    * Es responsabilidad del programador escapar los valores que introduce como $keys y el nombre de la
    * tabla
    *
    * @param array $table
    * @param array $data
    */
    public function simple_update($table, $data, $limit){
        array_walk($data, array(&$this, "smart_quote_array"));
        array_walk($limit, array(&$this, "smart_quote_array"));

        if(! ( count($data) && count($limit) ) ){
            throw new EsquipulasException("No \$data y \$limit no pueden estar vacios");
        }
        
        $newdata = array();
        foreach($data as $key => $value){
            $newdata[] = $key . " = " . $value ;
        }

        $query .= 

        $newlimit = array();
        foreach($limit as $key => $value){
            $newlimit[] = $key . " = " . $value ;
        }

        $query = "UPDATE $table SET " . implode(" , ", $newdata) . " WHERE " . implode(" , ", $newlimit)  . " LIMIT 1 ";

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