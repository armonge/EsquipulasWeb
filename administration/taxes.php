<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("root")){
	die("Usted no tiene permisos para administrar Impuestos");
}
if(isset($_POST["update"])){
	$taxtype = (int)$_POST["type"];
	$value = (float)$_POST["value"];
	switch( $taxtype){
		case 1:
		case 4:
		case 6:
		case 8:
		case 10:
			$query = "UPDATE costosagregados SET activo = 0 WHERE idtipocosto = $taxtype AND activo = 1 LIMIT 1";
			$dbc->query($query);
			$query = "INSERT INTO costosagregados (valorcosto, fechaestablecido, activo, idtipocosto) 
			VALUES ($value, NOW(), 1, $taxtype)";
			$dbc->query($query);
		break;
		case 5:
			$factorpeso = (float)$_POST["factorpeso"];
			
			$query = "UPDATE costosagregados SET activo = 0 WHERE idtipocosto = $taxtype AND activo = 1 LIMIT 1";
			$dbc->query($query);
			$query = "INSERT INTO costosagregados (valorcosto, fechaestablecido, activo, idtipocosto) 
			VALUES ($value, NOW(), 1, $taxtype)";
			$dbc->query($query);
			$insertedId = $dbc->insert_id;			
			
			$query = "INSERT INTO tsim (idtsim, factorpeso ) 
			VALUES ($insertedId, $factorpeso)";
			$dbc->query($query);
			
		break;
		
	}
}
$rsTaxes = $dbc->query("
SELECT 
	SUM(IF(ca.idtipocosto = 1, ca.valorcosto, 0)) iva,
	SUM(IF(ca.idtipocosto = 4, ca.valorcosto, 0)) spe,
	SUM(IF(ca.idtipocosto = 5, ca.valorcosto, 0)) tsim,
	SUM(IF(ca.idtipocosto = 5, ts.factorpeso, 0)) as factorpeso,
	SUM(IF(ca.idtipocosto = 6, ca.valorcosto, 0)) iso,
	SUM(IF(ca.idtipocosto = 8, ca.valorcosto, 0)) retencionprof,
	SUM(IF(ca.idtipocosto = 10, ca.valorcosto, 0)) retencionf
FROM costosagregados ca
JOIN tiposcosto tc ON tc.idtipocosto = ca.idtipocosto
LEFT JOIN tsim ts ON ts.idtsim = ca.idcostoagregado
WHERE ca.idtipocosto IN ( 1,4,5, 6, 8,10)
AND activo = 1
");
$row_rsTax = $rsTaxes->fetch_array(MYSQLI_ASSOC);
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<title>Llantera Esquipulas: Administraci&oacute;n</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.validate.js"></script>
<script type="text/javascript" src="js/messages_es.js"></script>
<script type="text/javascript">
$(function(){
	$("#iva").validate();
	$("#spe").validate();
	$("#tsim").validate();
	$("#retencionprof").validate();
	$("#retencionf").validate();
});
</script>
<style type="text/css">
#m3 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m3 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
h2{
	float:left;
	clear:both;
}
.tax{
display:inline-block;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
		<h1>Impuestos</h1>
		<div class="tax">
		<h2>IVA</h2>
			<form id="iva" action="administration/taxes.php" method="post" class="cforms">
				<p>
					<label><span>Valor:</span> <input type="text" class="required number"  name="value" value="<?php echo $row_rsTax["iva"]?>" /></label>
				</p>
				<p>
					<input type="submit" value="aceptar" />
					<input type="hidden" name="type" value="1" />
					<input type="hidden" name="update" value="yes" />
				</p>
			</form>
			</div>
			<div class="tax">
		<h2>SPE</h2>
			<form id="spe" action="administration/taxes.php" method="post" class="cforms">
				<p>
					<label><span>Valor:</span> <input type="text" name="value" class="required number"  value="<?php echo $row_rsTax["spe"]?>" /></label>
				</p>
				<p>
					<input type="submit" value="aceptar" />
					<input type="hidden" name="type" value="4" />
					<input type="hidden" name="update" value="yes" />
				</p>
			</form>
			</div>
			<div class="tax">
			<h2>ISO</h2>
			<form id="iso" action="administration/taxes.php" method="post" class="cforms">
				<p>
					<label><span>Valor:</span> <input type="text" name="value" class="required number"  value="<?php echo $row_rsTax["iso"]?>" /></label> 
				</p>
				<p>
					<input type="submit" value="aceptar" />
					<input type="hidden" name="type" value="6" />
					<input type="hidden" name="update" value="yes" />
				</p>
			</form>
			</div>
			<div class="tax">
		<h2>TSIM</h2>
			<form id="tsim" action="administration/taxes.php" method="post" class="cforms">
				<p>
					<label><span>Valor:</span> <input type="text" name="value" class="required number"  value="<?php echo $row_rsTax["tsim"]?>" /></label>
				</p>
				<p>
					<label><span>Factor Peso:</span> <input type="text" name="factorpeso" class="required digit"  value="<?php echo $row_rsTax["factorpeso"]?>" /></label>
				</p>
				<p>
					<input type="submit" value="aceptar" />
					<input type="hidden" name="type" value="5" />
					<input type="hidden" name="update" value="yes" />
				</p>
			</form>
			</div>
			<div class="tax">
			<h2>Retenci&oacute;n por servicios profesionales</h2>
			<form id="retencionprof" action="administration/taxes.php" method="post" class="cforms">
				<p>
					<label><span>Valor:</span> <input type="text" name="value" class="required number"  value="<?php echo $row_rsTax["retencionprof"]?>" /></label>
				</p>
				<p>
					<input type="submit" value="aceptar" />
					<input type="hidden" name="type" value="8" />
					<input type="hidden" name="update" value="yes" />
				</p>
			</form>
			</div>
			<div class="tax">
			<h2>Retenci&oacute;n en la fuente</h2>
			<form id="retencionf" action="administration/taxes.php" method="post" class="cforms">
				<p>
					<label><span>Valor:</span> <input type="text" name="value" class="required number"  value="<?php echo $row_rsTax["retencionf"]?>" /></label>
				</p>
				<p>
					<input type="submit" value="aceptar" />
					<input type="hidden" name="type" value="10" />
					<input type="hidden" name="update" value="yes" />
				</p>
			</form>
			</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>