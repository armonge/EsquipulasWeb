<?php
/********auto load classes on call***********/
function __autoload($class_name) {
	require_once "classes/{$class_name}.php";
}
/**********set the locale******************/
setlocale(LC_ALL,'es_NI');
/*********set the numeric precission********/
bcscale(4);
/********start the session*****************/
session_start();
session_regenerate_id();

/*********general settings ***********/
#TODO: configure settings for when not localhost
if(($_SERVER["REMOTE_ADDR"]=="127.0.0.1") || ($_SERVER["REMOTE_ADDR"]=="localhost") || ($_SERVER["REMOTE_ADDR"]=="192.168.2.200")){
	$path = "/srv/http/EsquipulasWeb/";
	require_once('conn.php');
	$local = True;
}else{
	$path = "/srv/www/htdocs/";
	require_once("{$path}../onlineconn.php");
	$local = False;
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
     
    "ANULACION" => 2,
    "FACTURA" => 5,
    "LIQUIDACION" => 7,
    "NOTACREDITO" => 10,
    "NOTADEBITO" => 11,
    "CHEQUE" => 12,
    "DEPOSITO" => 13,
    "RECIBO" => 18,
    "RETENCION" => 19,
    "ENTRADALOCAL" => 21,
    "ARQUEO" => 23,
    "AJUSTECONTABLE" => 24,
    "CONCILIACION" => 25,
    "ERROR" => 26,
    "KARDEX" => 27,
    "CIERREMENSUAL" => 28,
    "CIERREANUAL" => 29,
    "PAGO" => 30
);
/************person types********************/
$persontypes = array(
    "CLIENTE" => 1,
    "PROVEEDOR" => 2,
    "VENDEDOR" => 3,
    "USUARIO" => 4,
    "SUPERVISOR" => 5,
    "CONTADOR" => 6
);
/************estados de documentos************/
$docstates = array(
    "CONFIRMADO"=>1,
    "PENDIENTE"=>3,
    "ANULADO"=>2,
    "INCOMPLETO"=>4,
    "PENDIENTEANULACION" => 5
);
/*************monedas*************************/
$moneda = array(
	"CORDOBA" => 1,
	"DOLAR" => 2
);
/*************cuentas contables*************/
$accounts = array(
    "VENTASNETAS" =>173,
    "CXCCLIENTE" => 14,
    "INVENTARIO" =>22,
    "COSTOSVENTAS" =>182,
    "IMPUESTOSXPAGAR" =>133,
    "RETENCIONPAGADA" => 35,
    "INVENTARIO" => 22,
    "COSTOSVENTAS" => 182,
    "IMPUESTOSXPAGAR" => 133,
    "CAJA" => 5,
    "BMN" => 7,
    "BME" => 10
);
/*************costs ******************/
$costs = array(
    "IVA" => 1,
    "ISC" => 2,
    "DAI" => 3,
    "SPE" => 4,
    "TSIM" => 5,
    "ISO" => 6,
    "COMISION" => 7,
    "RETENCIONFUENTE" => 8,
    "RETENCIONSERVICIOS" => 9
);
/*************db connection ***************/
/* @var $dbc mysqli */
$dbc = new MySQLIEsquipulas(DBHOST,DBUSER,DBPASS,DB);


/*************validate user******************/
if(!isset($nologin) or $nologin == False){
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
}

/*********converts a UTF-8 string into HTML entities************/
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
/****************numbers to letters**********/
/*!
 @function num2letras ()
 @abstract Dado un número lo devuelve escrito.
 @param $num number - Número a convertir.
 @param $fem bool - Forma femenina (true) o no (false).
 @param $dec bool - Con decimales (true) o no (false).
 @result string - Devuelve el número escrito en letra.

 */

function num2letras($num, $fem = true, $dec = true) {
	//if (strlen($num) > 14) die("El n&uacute;mero introducido es demasiado grande");
	$matuni[2]  = "dos";
	$matuni[3]  = "tres";
	$matuni[4]  = "cuatro";
	$matuni[5]  = "cinco";
	$matuni[6]  = "seis";
	$matuni[7]  = "siete";
	$matuni[8]  = "ocho";
	$matuni[9]  = "nueve";
	$matuni[10] = "diez";
	$matuni[11] = "once";
	$matuni[12] = "doce";
	$matuni[13] = "trece";
	$matuni[14] = "catorce";
	$matuni[15] = "quince";
	$matuni[16] = "dieciseis";
	$matuni[17] = "diecisiete";
	$matuni[18] = "dieciocho";
	$matuni[19] = "diecinueve";
	$matuni[20] = "veinte";
	$matunisub[2] = "dos";
	$matunisub[3] = "tres";
	$matunisub[4] = "cuatro";
	$matunisub[5] = "quin";
	$matunisub[6] = "seis";
	$matunisub[7] = "sete";
	$matunisub[8] = "ocho";
	$matunisub[9] = "nove";

	$matdec[2] = "veint";
	$matdec[3] = "treinta";
	$matdec[4] = "cuarenta";
	$matdec[5] = "cincuenta";
	$matdec[6] = "sesenta";
	$matdec[7] = "setenta";
	$matdec[8] = "ochenta";
	$matdec[9] = "noventa";
	$matsub[3]  = 'mill';
	$matsub[5]  = 'bill';
	$matsub[7]  = 'mill';
	$matsub[9]  = 'trill';
	$matsub[11] = 'mill';
	$matsub[13] = 'bill';
	$matsub[15] = 'mill';
	$matmil[4]  = 'millones';
	$matmil[6]  = 'billones';
	$matmil[7]  = 'de billones';
	$matmil[8]  = 'millones de billones';
	$matmil[10] = 'trillones';
	$matmil[11] = 'de trillones';
	$matmil[12] = 'millones de trillones';
	$matmil[13] = 'de trillones';
	$matmil[14] = 'billones de trillones';
	$matmil[15] = 'de billones de trillones';
	$matmil[16] = 'millones de billones de trillones';

	$num = trim((string)@$num);
	if ($num[0] == '-') {
		$neg = 'menos ';
		$num = substr($num, 1);
	}else
	$neg = '';
	while ($num[0] == '0') $num = substr($num, 1);
	if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
	$zeros = true;
	$punt = false;
	$ent = '';
	$fra = '';
	for ($c = 0; $c < strlen($num); $c++) {
		$n = $num[$c];
		if (! (strpos(".,'''", $n) === false)) {
			if ($punt) break;
			else{
				$punt = true;
				continue;
			}

		}elseif (! (strpos('0123456789', $n) === false)) {
			if ($punt) {
				if ($n != '0') $zeros = false;
				$fra .= $n;
			}else

			$ent .= $n;
		}else

		break;

	}
	$ent = '     ' . $ent;
	if ($dec and $fra and ! $zeros) {
		$fin = ' punto';
		for ($n = 0; $n < strlen($fra); $n++) {
			if (($s = $fra[$n]) == '0')
			$fin .= ' cero';
			elseif ($s == '1')
			$fin .= $fem ? ' una' : ' un';
			else
			$fin .= ' ' . $matuni[$s];
		}
	}else
	$fin = '';
	if ((int)$ent === 0) return 'Cero ' . $fin;
	$tex = '';
	$sub = 0;
	$mils = 0;
	$neutro = false;
	while ( ($num = substr($ent, -3)) != '   ') {
		$ent = substr($ent, 0, -3);
		if (++$sub < 3 and $fem) {
			$matuni[1] = 'una';
			$subcent = 'as';
		}else{
			$matuni[1] = $neutro ? 'un' : 'uno';
			$subcent = 'os';
		}
		$t = '';
		$n2 = substr($num, 1);
		if ($n2 == '00') {
		}elseif ($n2 < 21)
		$t = ' ' . $matuni[(int)$n2];
		elseif ($n2 < 30) {
			$n3 = $num[2];
			if ($n3 != 0) $t = 'i' . $matuni[$n3];
			$n2 = $num[1];
			$t = ' ' . $matdec[$n2] . $t;
		}else{
			$n3 = $num[2];
			if ($n3 != 0) $t = ' y ' . $matuni[$n3];
			$n2 = $num[1];
			$t = ' ' . $matdec[$n2] . $t;
		}
		$n = $num[0];
		if ($n == 1) {
			$t = ' ciento' . $t;
		}elseif ($n == 5){
			$t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
		}elseif ($n != 0){
			$t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
		}
		if ($sub == 1) {
		}elseif (! isset($matsub[$sub])) {
			if ($num == 1) {
				$t = ' mil';
			}elseif ($num > 1){
				$t .= ' mil';
			}
		}elseif ($num == 1) {
			$t .= ' ' . $matsub[$sub] . '?n';
		}elseif ($num > 1){
			$t .= ' ' . $matsub[$sub] . 'ones';
		}
		if ($num == '000') $mils ++;
		elseif ($mils != 0) {
			if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
			$mils = 0;
		}
		$neutro = true;
		$tex = $t . $tex;
	}
	$tex = $neg . substr($tex, 1) . $fin;
	return ucfirst($tex);
}

?>