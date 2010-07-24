<?php
require_once "../functions.php";

$iddoc = (int)$_GET["doc"];

$rsDocumento = $dbc->query("
	SELECT
		d.iddocumento,
		d.ndocimpreso,
		p.nombre as cliente,
		d.total* (1-IF(valorcosto IS NULL,0,valorcosto)/100) as subtotal,
		d.total*ca.valorcosto/100 as iva,
		d.total,
		d.observacion,
		d.fechacreacion,
		b.nombrebodega as Bodega,
		tc.tasa as 'Tipo de Cambio Oficial',
		tc.tasabanco as 'Tipo de Cambio Banco',
		d.idtipodoc
	FROM documentos d
	JOIN tiposdoc td ON td.idtipodoc=d.idtipodoc
	JOIN bodegas b ON b.idbodega=d.idbodega
	JOIN tiposcambio tc ON tc.idtc=d.idtipocambio
	JOIN personas p ON p.idpersona=d.idpersona
	LEFT JOIN costosxdocumento cd ON cd.iddocumento=d.iddocumento
	LEFT JOIN costosagregados ca ON ca.idcostoagregado=cd.idcostoagregado
	WHERE d.idtipodoc=5
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
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Devoluci&oacute;n No <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

.gray {
	background: #f4f4f4;
}

.rigth {
	text-align: right;
}

h2 {
	border-bottom: 4px solid #000000;
	clear:both;
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
	float: left;
	clear: both;
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
</style>
</head>
<body>
<h1>Llantera Esquipulas</h1>
<h2>Factura #: <?php echo $row_rsDocumento["ndocimpreso"] ?></h2>
<p>
Fecha del documento: <?php echo $row_rsDocumento["fechacreacion"] ?> <br />
Cliente: <?php echo $row_rsDocumento["cliente"] ?></p>
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1"	summary="Reporte de partida contable">
<thead>
	<tr>
		<th>Articulo</th>
		<th>Unidades</th>
		<th>Precio de venta</th>
		<th>Total</th>
	</tr>
	</thead>
	<tbody>
	<?php $color = 1; while ($row_rsArticulos = $rsArticulos->fetch_assoc() ){ $color++;  ?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td><?php echo $row_rsArticulos["descripcion"] ?></td>
		<td><?php echo $row_rsArticulos["unidades"] ?></td>
		<td>US$ <?php echo number_format($row_rsArticulos["preciounit"],4) ?></td>
		<td>US$ <?php echo number_format($row_rsArticulos["unidades"]*$row_rsArticulos["preciounit"],4) ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2"></td>
		<td>Subtotal</td>
		<td>US$ <?php echo number_format($row_rsDocumento["subtotal"],4) ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
		<td>TOTAL</td>
		<td>US$ <?php echo number_format($row_rsDocumento["iva"],4) ?></td>
	</tr>
	<tr>
		<td colspan="2"></td>
		<td>TOTAL</td>
		<td>US$ <?php echo number_format($row_rsDocumento["total"],4) ?></td>
	</tr>
	</tbody>
</table>
<?php if($row_rsDocumento["observacion"]){ ?>
<h2>Observaciones</h2>
<div><?php echo $row_rsDocumento["observacion"] ?></div>
<?php } ?>
</body>
</html>
