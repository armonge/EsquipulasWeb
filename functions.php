<?php
/********auto load classes on call***********/
function __autoload($class_name) {
	require_once "classes/{$class_name}.php";
}
/**********set the locale******************/
setlocale(LC_ALL,'es_NI');
/********start the session*****************/
session_start();
session_regenerate_id();

/*********general settings ***********/
#TODO: configure settings for when not localhost
if(($_SERVER["REMOTE_ADDR"]=="127.0.0.1") || ($_SERVER["REMOTE_ADDR"]=="192.168.1.10") || ($_SERVER["REMOTE_ADDR"]=="192.168.1.11")){
	$path = "/srv/http/EsquipulasWeb/";
    require_once('conn.php');
}else{
	$path = "/srv/www/htdocs/";
    require_once("{$basedir}../onlineconn.php");
}
/***********modules************/
$modules = array(
	1=>"COMPRAS",
	2=>"CAJA",
	3=>"CONTABILIDAD",
	4=>"INVENTARIO",
	5=>"ADMINISTRACION",
	6=>"REPORTES",
);
/**********document ids*******************/
$docids = array(
    "IDFACTURA" => 5,
    "IDDEVOLUCION" => 10,
    "IDND" => 11,
    "IDCHEQUE" => 12,
    "IDDEPOSITO" => 13,
    "IDNC" => 14,
    "IDRECIBO" => 18,
    "IDRETENCION" => 19,
    "IDENTRADALOCAL" => 21,
	"IDARQUEO" => 23,
    "IDAJUSTECONTABLE" => 24,
    "IDCONCILIACION" => 25,
    "IDERROR" => 26,
    "IDKARDEX" => 27,
);
/************person types********************/
$persontypes = array(
    "CLIENTE" => 1,
    "PROVEEDOR" => 2,
    "VENDEDOR" => 3,
    "USUARIO" => 4
);
/*************monedas*************************/
$moneda = array(
	"CORDOBA" => 1,
	"DOLAR" => 2
);
/*************db connection ***************/
$dbc = new MySQLI(DBHOST,DBUSER,DBPASS,DB);


/*************validate user******************/
if( ( !isset($_SESSION["user"]) ) || ( !$_SESSION["user"]->isValid() ) ){
	if( basename($_SERVER["SCRIPT_NAME"]) != "login.php"){
		if( isset($_GET["hash"]) ){
			$user = new UserFromHash($_GET["uname"], $_GET["hash"], $dbc);

			if(!$user->isValid()){
				header("Location: {$basedir}login.php");
				die();
			}else{
				$_SESSION["user"] = $user;
			}

		}else{
			header("Location: {$basedir}login.php");
			die();
		}


	}
}


// converts a UTF8-string into HTML entities
//  - $utf8:        the UTF8-string to convert
//  - $encodeTags:  booloean. TRUE will convert "<" to "&lt;"
//  - return:       returns the converted HTML-string
function utf8tohtml($utf8, $encodeTags=true) {
    $result = '';
    for ($i = 0; $i < strlen($utf8); $i++) {
        $char = $utf8[$i];
        $ascii = ord($char);
        if ($ascii < 128) {
            // one-byte character
            $result .= ($encodeTags) ? htmlentities($char) : $char;
        } else if ($ascii < 192) {
            // non-utf8 character or not a start byte
        } else if ($ascii < 224) {
            // two-byte character
            $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
            $i++;
        } else if ($ascii < 240) {
            // three-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $unicode = (15 & $ascii) * 4096 +
                       (63 & $ascii1) * 64 +
                       (63 & $ascii2);
            $result .= "&#$unicode;";
            $i += 2;
        } else if ($ascii < 248) {
            // four-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $ascii3 = ord($utf8[$i+3]);
            $unicode = (15 & $ascii) * 262144 +
                       (63 & $ascii1) * 4096 +
                       (63 & $ascii2) * 64 +
                       (63 & $ascii3);
            $result .= "&#$unicode;";
            $i += 3;
        }
    }
    return $result;
}
/******************last day of month****************/
function lastday($month = '', $year = '' ,$format = 'Ymd') {
   if (empty($month)) {
      $month = date('m');
   }
   if (empty($year)) {
      $year = date('Y');
   }
   
   $result = strtotime("{$year}-{$month}-01");
   if(!$result){
   	return $result;
   }
   
   $result = strtotime('-1 second', strtotime('+1 month', $result));
   return date($format, $result);
}
?>