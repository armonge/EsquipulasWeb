<?php
/**
* @package reporting
* @author Andrés Reyes Monge <armonge@gmail.com>
*/require_once "../functions.php";
if(!($_SESSION["user"]->hasRole("contabilidadrep") || $_SESSION["user"]->hasRole("gerencia"))){
    die("Usted no tiene permisos para ver reportes");
}
if(isset($_GET["date"])){
	list($month , $year, $rest) = explode(' ', $_GET["date"], 3);
	$stamp = strtotime("{$year}-{$month}-01");
	$date = date('Y-m-d',strtotime("{$year}-{$month}-01"));
}else{
	$date = date('Y-m-d');
	$stamp = time();
}

$query = "
SELECT 
	base.codigo, 
	base.descripcion, 
	FORMAT(base.saldoanterior,4) AS 'saldoanterior',
	FORMAT(base.credito,4) AS 'credito',
	FORMAT(base.debito,4) AS 'debito', 
	base.saldoanterior + base.debito - base.credito AS saldomes
FROM 
(SELECT 
	cc.codigo, 
	cc.descripcion, 
	SUM(IF(d.fechacreacion < DATE_ADD(LAST_DAY(DATE_SUB('$date', INTERVAL 1 MONTH)), INTERVAL 1 DAY),cxd.monto,0)) as 'saldoanterior', 
	SUM(IF(d.fechacreacion >=  DATE_ADD(LAST_DAY(DATE_SUB('$date', INTERVAL 1 MONTH)), INTERVAL 1 DAY), IF(cxd.monto > 0, cxd.monto,0),0)) as 'debito',
	ABS(SUM(IF(d.fechacreacion >=  DATE_ADD(LAST_DAY(DATE_SUB('$date', INTERVAL 1 MONTH)), INTERVAL 1 DAY), IF(cxd.monto < 0, cxd.monto,0),0))) as 'credito'
FROM cuentasxdocumento cxd
JOIN cuentascontables cc ON cc.idcuenta = cxd.idcuenta
JOIN documentos d ON cxd.iddocumento = d.iddocumento
WHERE d.fechacreacion <= LAST_DAY('$date')
GROUP BY cxd.idcuenta
ORDER BY cc.codigo 
) as base
 " ;
$rsCuentas = $dbc->query($query);

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
<title>Llantera Esquipulas: Balance de comprobacion</title>
<style type="text/css" media="print">
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
	width: 750pt;
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
</style>
<style type="text/css">
table{
	width:100%;
	text-align:center;
}
#m2 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m2 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
.ui-datepicker-calendar {
    display: none;
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
<form action="reports/balancecomprobacion.php">
<div class="ui-widget">
    <label for="date">Para: <input type="text" id="date" name="date" value="<?php echo $_GET["date"] ?>" /></label>
    <input type="submit" value="aceptar" />
</div>

</form>
<h1>Llantera Esquipulas</h1>
<h2>Balance de comprobacion en c&oacute;rdobas para el mes de <?php echo strftime('%B', $stamp) ?> de <?php echo date('Y',$stamp)?></h2>
<?php if(! ($rsCuentas->num_rows >  0) ) { ?>
	<p>No existen movimientos contables para el periodo seleccionado</p>
<?php }else{?>
<table border="1" frame="border" rules="all" cellpadding="3" cellspacing="0"	summary="Reporte de partida contable">
	<thead>
	<tr>
		<th>Codigo Contable</th>
		<th>Descripci&oacute;n</th>
		<th>Saldo Mes Anterior</th>
		<th>Movimiento D&eacute;bito</th>
		<th>Movimiento Cr&eacute;dito</th>
		<th>Saldo Deudor</th>
		<th>Saldo Acreedor</th>
	</tr>
	</thead>

	<tbody>
	<?php
	$color = 1;
	while ($row = $rsCuentas->fetch_assoc()){
		$color++;
		?>
	<tr class='<?php if ( ( bcadd($deber, $credito) != '0') ||( $deber=='0' ) ) { echo " error "; } if($color % 2 == 0 ){ echo " gray "; } ?>  pass' >
		<td><?php echo $row["codigo"]?></td>
		<td style="text-align:left;"><?php echo utf8tohtml($row["descripcion"] )?></td>
		<td><?php echo $row["saldoanterior"]?></td>
		<td><?php echo $row["debito"]?></td>
		<td><?php echo $row["credito"]?></td>
		<td><?php echo $row["saldomes"] > 0 ? number_format($row["saldomes"],4) : "0.0000" ;?></td>
		<td><?php echo $row["saldomes"] < 0 ? number_format(abs($row["saldomes"]),4) : "0.0000" ;?></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<?php } ?>
<?php include "../footer.php" ?></div>
</div>

</body>
</html>
