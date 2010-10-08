<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/
//TODO: Mostar la otra columna y el estado de resultado
require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("contabilidadrep") || $_SESSION["user"]->hasRole("gerencia"))){
    die("Usted no tiene permisos para ver reportes");
}
if(isset($_GET["date"])){
	list($month , $year, $rest) = explode(' ', $_GET["date"], 3);
	$month = (int)$month;
	$year = (int)$year;
	$stamp = strtotime("{$year}-{$month}-01");
	$date = date('Y-m-d',strtotime("{$year}-{$month}-01"));
}else{
	$date = date('Y-m-d');
	$stamp = time();
}
$query = "
SELECT 
	padre.descripcion as categoria, 
	cc.idcuenta, 
	cc.codigo, 
	cc.descripcion, 
	SUM(IFNULL(cxd.monto,0)) as monto
FROM cuentascontables cc
JOIN cuentascontables padre ON padre.idcuenta = cc.padre
JOIN cuentascontables abuelo ON padre.padre = abuelo.idcuenta AND abuelo.padre = 1
JOIN cuentascontables hijos ON hijos.codigo LIKE CONCAT(SUBSTRING_INDEX(cc.codigo,' ',2), \"%\") AND hijos.esdebe = 1
JOIN cuentasxdocumento cxd ON cxd.idcuenta = hijos.idcuenta
JOIN documentos d ON d.iddocumento = cxd.iddocumento "; 
$query .= " AND " . ( $month ?  $month : " MONTH(NOW()) " ) . " = MONTH(d.fechacreacion) "; 
$query .= " AND " . ( $year ?  $year  : " YEAR(NOW()) " ) . " = YEAR(d.fechacreacion) "; 
$query .= " WHERE cc.codigo NOT LIKE '' 
GROUP BY cc.idcuenta
ORDER  BY cc.idcuenta
";
$rsActivo = $dbc->query($query);
$row_rsActivo = $rsActivo->fetch_array(MYSQLI_ASSOC);
$catActivo = $row_rsActivo["categoria"];

$query = "
SELECT 
	padre.descripcion as categoria, 
	cc.idcuenta, 
	cc.codigo, 
	cc.descripcion, 
	SUM(IFNULL(cxd.monto,0)) as monto
FROM cuentascontables cc
JOIN cuentascontables padre ON padre.idcuenta = cc.padre
JOIN cuentascontables abuelo ON padre.padre = abuelo.idcuenta AND abuelo.padre = 1
JOIN cuentascontables hijos ON hijos.codigo LIKE CONCAT(SUBSTRING_INDEX(cc.codigo,' ',2), \"%\") AND hijos.esdebe = 0
JOIN cuentasxdocumento cxd ON cxd.idcuenta = hijos.idcuenta 
JOIN documentos d ON d.iddocumento = cxd.iddocumento ";
$query .= " AND " . ( $month ?  $month : " MONTH(NOW()) " ) . " = MONTH(d.fechacreacion) "; 
$query .= " AND " . ( $year ?  $year  : " YEAR(NOW()) " ) . " = YEAR(d.fechacreacion) "; 
$query .= " WHERE cc.codigo NOT LIKE '' 
GROUP BY cc.idcuenta
ORDER  BY cc.idcuenta
";
$rsPasivo= $dbc->query($query);
$row_rsPasivo= $rsPasivo->fetch_array(MYSQLI_ASSOC);
$catPasivo= $row_rsPasivo["categoria"];
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-es.js"></script>
<title>Llantera Esquipulas: Reporte de Balance General</title>
<style type="text/css">

#m2 a{
    background: url(img/nav-left.png) no-repeat left;
}
#m2 span{
    background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
.float{
	width:50%;
	float:left;
}
strong{
	border-bottom:1px solid #676766;
	margin:10px;
}
table{
	width:100%
}
.ui-datepicker-calendar {
    display: none;
    }
@media print{
html{
    border:0;
    padding:0;
    margin:0;
}
#menu, #uname, #logo{
	display:none;
}
body{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 9pt;
	float:left;
	width: 692pt;
	padding:0;
	margin:0;
	color:#000;
}

.gray, .error, .error2{
	background:#fff;
}
#footer, #header, .ui-widget{
    display:none;
}

thead {
    display: table-header-group;
  }
 #wrap{position:static}
 }
</style>
<script type="text/javascript">
$(function(){
	$('#date').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: false,
        dateFormat: 'mm yy',
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
});
</script>
</head>
<body>
<div id="wrap">

 <?php include "../header.php"?>

<div id="content">
<h1>Balance General</h1>
<form action="reports/balancegeneral.php">
<div class="ui-widget">
    <label for="date">Para: <input type="text" id="date" name="date" value="<?php echo $_GET["date"] ?>" /></label>
    <input type="submit" value="aceptar" />
</div>

</form>
<h2>Balance de general para el mes de <?php echo strftime('%B', $stamp) ?> de <?php echo date('Y',$stamp)?></h2>
<?php if(!$rsActivo->num_rows){?>
	<p>No hay activos para este mes</p>
<?php }else{ ?>
<div class="float">
<h2>Activo</h2>
<strong><?php echo $catActivo ?></strong>
<table border="0" frame="void" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">
<?php do{
	if($catActivo!= $row_rsActivo["categoria"]){ ?>
		</table>
		<strong><?php echo $row_rsActivo["categoria"] ?></strong>
		<table border="0" frame="void" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">		
	<?php }
	$catActivo = $row_rsActivo["categoria"]; 
?>

<tr>
	<td style="width:220px">	<?php echo $row_rsActivo['descripcion'] ?>	</td>
	<td>	C$ <?php echo money_format($row_rsActivo['monto'],2) ?>	</td>
</tr>
<?php }while($row_rsActivo = $rsActivo->fetch_array(MYSQLI_ASSOC)) ?>
</table>
</div>
<?php } ?>

<?php if(!$rsPasivo->num_rows){?>
	<p>No hay pasivo para este mes</p>
<?php }else{ ?>
<div class="float">
<h2>Pasivo</h2>
<strong><?php echo $catPasivo ?></strong>
<table border="0" frame="void" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">
<?php do{
	if($catPasivo!= $row_rsPasivo["categoria"]){ ?>
		</table>
		<strong><?php echo $row_rsPasivo["categoria"] ?></strong>
		<table border="0" frame="void" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">		
	<?php }
	$catPasivo = $row_rsPasivo["categoria"]; 
?>

<tr>
	<td style="width:220px">	<?php echo $row_rsPasivo['descripcion'] ?>	</td>
	<td>	C$ <?php echo money_format($row_rsPasivo['monto'],2) ?>	</td>
</tr>
<?php }while($row_rsPasivo= $rsPasivo->fetch_array(MYSQLI_ASSOC)) ?>
</table>
</div>
<?php } ?>
<?php include "../footer.php" ?></div>
</div>

</body>
</html>
