<?php
require_once "../functions.php";

$iddoc = (int)$_GET["doc"];
if(!$iddoc){
	die();
}
$rsDocumento = $dbc->query("
	SELECT
		d.iddocumento,
		d.ndocimpreso,
		p.nombre as cliente,
		d.total* (1-IF(valorcosto IS NULL,0,valorcosto)/100) as subtotal,
		d.total*ca.valorcosto/100 as iva,
		d.total,
		d.observacion,
		DATE_FORMAT(d.fechacreacion,'%d/%m/%Y') as fechacreacion,
		b.nombrebodega as Bodega,
		tc.tasa as 'Tipo de Cambio Oficial',
		tc.tasabanco as 'Tipo de Cambio Banco',
		d.idtipodoc,
		d.escontado
	FROM documentos d
	JOIN tiposdoc td ON td.idtipodoc=d.idtipodoc
	JOIN bodegas b ON b.idbodega=d.idbodega
	JOIN tiposcambio tc ON tc.idtc=d.idtipocambio
	JOIN personasxdocumento pxd ON d.iddocumento = pxd.iddocumento
	JOIN personas p ON p.idpersona=pxd.idpersona AND p.tipopersona != 4
	LEFT JOIN costosxdocumento cd ON cd.iddocumento=d.iddocumento
	LEFT JOIN costosagregados ca ON ca.idcostoagregado=cd.idcostoagregado
	WHERE d.idtipodoc={$docids["IDFACTURA"]}
	AND d.iddocumento=$iddoc
");
$row_rsDocumento = $rsDocumento->fetch_assoc();

$rsArticulos = $dbc->query("
	SELECT
		ad.idarticulo,
		ad.descripcion,
		-a.unidades as unidades,
		a.precioventa as 'preciounit',
		-a.unidades*a.precioventa as 'total'
	FROM articulosxdocumento a
	JOIN vw_articulosdescritos ad on a.idarticulo=ad.idarticulo
	WHERE a.precioventa IS NOT NULL
	AND a.iddocumento=$iddoc
");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Devoluci&oacute;n No <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	width:680pt;
	padding:20pt 50pt;
}
#type{
	text-align:right;
}
#header{
	text-align:center;
}
#header p, #header h1{
	margin-bottom:0;
	margin-top:0;
}
#header2 h2{
	display:inline-block
}
#header2 table{
	float:right;
}
.gray {
	background: #f4f4f4;
}

.rigth {
	text-align: right;
}


.noborder {
	border: 0;
}

th {
	background: #4f4f4f;
	color: #fff;
}

.float {
	float: left;
	width: 33%
}

p {
	margin: 10px;
}

table {
	text-align: center;
}
thead {
	    display:table-header-group;
	    border-width: 1px 0;
	    border-color:#000;
	    border-style:solid;
}
tbody {
    display:table-row-group;
}
#authorization{
	border:1px solid #000;
	padding:20pt;
	padding-top:40pt;
	width:50%;
	margin-top:5pt;
	display:inline-block;
	text-align:center;
}
#authorization span{
	padding-top:10pt;
	border-top:1px solid #000;
}
#details{
	width:100%;
}
#details tbody tr td, #details tbody tr{
	height:10pt;
}
#totals{
	width:30%;
	float:right;
}
#persons table{
	float:left;
	width:46%;
	margin:2%;
}
@media print{
	.gray {
		background: #fff;
	}
	th {
		background: #fff;
		color: #000;
	}
}
</style>
</head>
<body>
<div id="header">
<h1>DISTRIBUIDORA DE LLANTAS ESQUIPULAS, S.A.</h1>
<p>Semaforos del Mayoreo 100 Mts Sur mano Izquierda</p>
<p>Tel: 2233-1542 - 2233-1226 - 2252-0933 - 2252-0944 - 2233-1642</p>
<p>E-mail:llantasesquipulas@hotmail.com</p>
<p><b>RUC: 050404-9515</b></p>
</div>
<div id="header2">
<h2>Factura N&deg; <?php echo $row_rsDocumento["ndocimpreso"] ?></h2>

<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1">
<thead>
<tr>
	<th>Fecha</th>
	<th>FACTURA No.</th>
</tr>
</thead>
<tbody>
<tr>
	<td><?php echo $row_rsDocumento["fechacreacion"]?></td>
	<td><?php echo $row_rsDocumento["ndocimpreso"]?></td>
</tr>
</tbody>
</table>
</div>
<p id="type">
CONTADO <?php if($row_rsDocumento["escontado"]){ ?><img src="img/checkbox.png" alt="checked"/> <?php }else{echo '<span style="display:inline-block;width:15pt;height:15pt;border:1px solid #000;"></span>'; } ?> 
CREDITO <?php if(!$row_rsDocumento["escontado"]){ ?><img src="img/checkbox.png" alt="checked"/><?php }else{echo '<span style="display:inline-block;width:15pt;height:15pt;border:1px solid #000;"></span>'; } ?>
</p>

<div id="persons">
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1">
<thead>
<tr>
	<th>Facturar a:</th>
</tr>
</thead>
<tbody>
<tr>
<td>
	<?php echo $row_rsDocumento["cliente"]?>
</td>
</tr>
</tbody>
</table>
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1">
<thead>
<tr>
	<th>Entregar a:</th>
</tr>
</thead>
<tbody>
<tr>
<td>
	<?php echo $row_rsDocumento["cliente"]?>
</td>
</tr>
</tbody>
</table>
</div>
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1"	summary="Detalle factura" id="details">
<thead>
	<tr>
		<th>Item No.</th>
		<th>Cantidad</th>
		<th>Descripcion</th>
		<th>Precio Unit</th>
		<th>Valor</th>
	</tr>
	</thead>
	<tbody valign="top">
	<?php $color = 1; while ($row_rsArticulos = $rsArticulos->fetch_assoc() ){ $color++;  ?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?> >
		<td><?php echo $row_rsArticulos["idarticulo"] ?></td>
		<td><?php echo $row_rsArticulos["unidades"] ?></td>
		<td><?php echo $row_rsArticulos["descripcion"] ?></td>
		<td>US$ <?php echo number_format($row_rsArticulos["preciounit"],4) ?></td>
		<td>US$ <?php echo number_format($row_rsArticulos["unidades"]*$row_rsArticulos["preciounit"],4) ?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<div  id="authorization">
	<span>AUTORIZADO POR</span>
</div>		
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1"	summary="Totales factura" id="totals">
<tbody>
	<tr>
		<td>
			Subtotal US$ <?php echo number_format($row_rsDocumento["subtotal"],4) ?>
		</td>
	</tr>
	<tr>
		<td>
			IVA US$ <?php echo number_format($row_rsDocumento["iva"],4) ?>
		</td>
	</tr>
	<tr>
		<td>
			Total US$ <?php echo number_format($row_rsDocumento["total"],4) ?>
		</td>
	</tr>
	<tr>
		<td>
			Balance Final US$ <?php echo number_format($row_rsDocumento["total"],4) ?>
		</td>
	</tr>
	</tbody>
</table>
</body>
</html>
