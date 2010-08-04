<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("contabilidadrep")){
	die("Usted no tiene permisos para ver este reporte");
}
$start = $dbc->real_escape_string($_GET["from"]);
$end = $dbc->real_escape_string($_GET["to"]);

$query = "
SELECT
	d.iddocumento,
	DATE(d.fechacreacion) as fecha,
	GROUP_CONCAT(p.nombre) as cliente,
	d.anulado,
	t.codigodoc,
	d.ndocimpreso as ndoc,
	c.codigo as codigocuenta,
	dx.monto,
	c.esdebe,
	d.total,
	c.descripcion
FROM documentos d
JOIN cuentasxdocumento dx ON dx.iddocumento=d.iddocumento
JOIN cuentascontables c ON dx.idcuenta=c.idcuenta
LEFT JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento 
LEFT JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona != 4
JOIN tiposdoc t ON  d.idtipoDoc=t.idtipodoc
 " ;
if(!$start && !$end){
	$query .="WHERE YEAR(d.fechacreacion) = YEAR(CURDATE()) AND MONTH(d.fechacreacion) = MONTH(CURDATE())";
}elseif ($start && $end){
	$query  .= " WHERE DATE(d.fechacreacion) >=  '$start' AND DATE(d.fechacreacion) <= '$end'";
}elseif ($start){
	$query  .= " WHERE DATE(d.fechacreacion) >=  '$start' ";
}elseif ($end){
	$query  .= " WHERE DATE(d.fechacreacion) <= '$end' ";
}
$query .= "GROUP BY d.iddocumento, c.idcuenta ORDER BY d.iddocumento ";


$rsMovimientos = $dbc->query($query);

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
<title>Llantera Esquipulas: Reporte de Partida Contable</title>
<style type="text/css" media="print">
html,body{
	color:#000;
	font-size:9pt;
	width:auto;
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
#m2 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m2 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>
<script type="text/javascript">
$(function(){
var dates = $('#from, #to').datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	dateFormat: 'yy-mm-dd',
	onSelect: function(selectedDate) {
		var option = this.id == "from" ? "minDate" : "maxDate";
		var instance = $(this).data("datepicker");
		var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
		dates.not(this).datepicker("option", option, date);
	}
});
});
</script>
</head>
<body>
<div id="wrap">

 <?php include "../header.php"?>

<div id="content">
<form action="reports/partidacontable.php">
<div class="ui-widget">
    <label for="from">Desde: <input type="text" id="from" name="from" value="<?php echo $_GET["from"] ?>" /></label>
    <label for="to">hasta: <input type="text" id="to" name="to" value="<?php echo $_GET["to"] ?>" /></label>
    <input type="submit" value="aceptar" />
</div>

</form>
<h1>Llantera Esquipulas</h1>
<h2>Reporte de Partida Contable</h2>
<?php if(! ($rsMovimientos->num_rows >  0) ) { ?>
	<p>No existen movimientos contables para el periodo seleccionado</p>
<?php }else{?>
<?php if ($start && $end){ ?> 
	<p>Desde <?php echo date("d/m/Y", strtotime($start)) ?> hasta <?php echo date("d/m/Y", strtotime($end)) ?></p> 
<?php }else{ ?>
	<p>Desde <?php echo date("01/m/Y") ?> hasta <?php echo date("d/m/Y") ?></p>
<?php } ?>
<table border="1" frame="border" rules="all" cellpadding="3" cellspacing="1"	summary="Reporte de partida contable">
	<thead>
	<tr>
		<th>Fecha</th>
		<th>Cliente /<br /> Proveedor</th>
		<th>Tipo Doc</th>
		<th>Numero Doc</th>
		<th>Codigo Cuenta</th>
		<th>Nombre Cuenta</th>
		<th>Debito</th>
		<th>Credito</th>
	</tr>
	</thead>

	<tbody>
	<?php
	$color = 1;
	$iddoc = 0;
	$deber = '0';
	$credito = '0';
	while ($row = $rsMovimientos->fetch_assoc()){
		$color++;
		?>

	<?php  if ($row["iddocumento"] != $iddoc){
		if($total){
			?>

	<tr class='<?php if ( ( bcadd($deber, $credito) != '0') ||( $deber=='0' ) ) { echo " error "; } if($color % 2 == 0 ){ echo " gray "; } ?>  pass' >
		<td colspan="5"></td>
		<td><strong>Total Documento</strong></td>
		<td><?php echo number_format(abs($deber),4) ?></td>
		<td><?php echo number_format(abs($credito),4) ?></td>
	</tr>
	<?php	$deber = 0;	$credito = 0;	$color++; }	?>


	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td><?php echo $row["fecha"]?></td>
		<td><?php echo $row["anulado"] ? "anulado":  $row["cliente"]  ?></td>
		<td><?php echo $row["codigodoc"]  ?></td>
		<td><?php echo htmlentities($row["ndoc"])  ?></td>

		<?php } else{		?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td colspan="4"></td>
		<?php }?>
		<td><?php echo $row["codigocuenta"] ?></td>
		<td><?php echo $row["descripcion"]  ?></td>
		<td <?php if ($row["monto"]==0){echo 'class="error2"'; } ?>><?php echo $row["monto"] < 0 ? "" : number_format($row["monto"],4) ?></td>
		<td <?php if ($row["monto"]==0){echo 'class="error2"'; } ?>><?php echo $row["monto"] > 0 ? "" : number_format(abs($row["monto"]),4) ?></td>
		<?php
		$deber +=  $row["monto"] < 0 ? 0 : $row["monto"];
		$credito +=  $row["monto"] > 0 ? 0 : $row["monto"];
		?>
	</tr>


	<?php 	$iddoc = $row["iddocumento"]; $total = $row["total"]; } ?>


	<tr class='<?php if (bcadd($deber,$credito) != 0){ echo " error "; } if($color % 2 == 0 ){ echo " gray "; } ?>  pass' >
		<td colspan="5"></td>
		<td><strong>Total Documento</strong></td>
		<td><?php echo number_format(abs($deber),4) ?></td>
		<td><?php echo number_format(abs($credito),4) ?></td>
	</tr>
	</tbody>
</table>
<?php } ?>
<?php include "../footer.php" ?></div>
</div>

</body>
</html>
