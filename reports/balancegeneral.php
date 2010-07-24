<?php
//TODO: Mostar la otra columna y el estado de resultado
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("contabilidadrep")){
	die("Usted no tiene permisos para ver este reporte");
}
$query = "
SELECT padre.descripcion as categoria, cc.idcuenta, cc.codigo, cc.descripcion, SUM(IFNULL(cxd.monto,0)) as monto
FROM cuentascontables cc
JOIN cuentascontables padre ON padre.idcuenta = cc.padre
JOIN cuentascontables abuelo ON padre.padre = abuelo.idcuenta AND abuelo.padre = 1
JOIN cuentascontables hijos ON hijos.codigo LIKE CONCAT(SUBSTRING_INDEX(cc.codigo,' ',2), \"%\") AND hijos.esdebe = 1
JOIN cuentasxdocumento cxd ON cxd.idcuenta = hijos.idcuenta 
LEFT JOIN documentos d ON d.iddocumento = cxd.iddocumento AND MONTH(NOW()) = MONTH(d.fechacreacion) AND YEAR(d.fechacreacion) = YEAR(NOW())
WHERE cc.codigo NOT LIKE '' 
GROUP BY cc.idcuenta
ORDER  BY cc.idcuenta
";
$rsActivo = $dbc->query($query);
$row_rsActivo = $rsActivo->fetch_array(MYSQLI_ASSOC);
$catActivo = $row_rsActivo["categoria"];

$query = "
SELECT padre.descripcion as categoria, cc.idcuenta, cc.codigo, cc.descripcion, SUM(IFNULL(cxd.monto,0)) as monto
FROM cuentascontables cc
JOIN cuentascontables padre ON padre.idcuenta = cc.padre
JOIN cuentascontables abuelo ON padre.padre = abuelo.idcuenta AND abuelo.padre = 1
JOIN cuentascontables hijos ON hijos.codigo LIKE CONCAT(SUBSTRING_INDEX(cc.codigo,' ',2), \"%\") AND hijos.esdebe = 0
JOIN cuentasxdocumento cxd ON cxd.idcuenta = hijos.idcuenta 
LEFT JOIN documentos d ON d.iddocumento = cxd.iddocumento AND MONTH(NOW()) = MONTH(d.fechacreacion) AND YEAR(d.fechacreacion) = YEAR(NOW())
WHERE cc.codigo NOT LIKE '' 
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
.float{
	width:50%;
	float:left;
}
strong{
	border-bottom:1px solid #676766;
	margin:10px;
}
</style>
<script type="text/javascript">
$(function(){

});
</script>
</head>
<body>
<div id="wrap">

 <?php include "../header.php"?>

<div id="content">
<h1>Balance General</h1>
<div class="float">
<h2>Activo</h2>
<strong><?php echo $catActivo ?></strong>
<table border="0" frame="border" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">
<?php do{
	if($catActivo!= $row_rsActivo["categoria"]){ ?>
		</table>
		<strong><?php echo $row_rsActivo["categoria"] ?></strong>
		<table border="0" frame="border" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">		
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


<div class="float">
<h2>Pasivo</h2>
<strong><?php echo $catPasivo ?></strong>
<table border="0" frame="border" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">
<?php do{
	if($catPasivo!= $row_rsPasivo["categoria"]){ ?>
		</table>
		<strong><?php echo $row_rsPasivo["categoria"] ?></strong>
		<table border="0" frame="border" rules="none" cellpadding="3" cellspacing="1"	summary="Activos">		
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
<?php include "../footer.php" ?></div>
</div>

</body>
</html>
