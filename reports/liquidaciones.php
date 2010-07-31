<?php
require_once "../functions.php";

$iddoc = (int)$_GET["doc"];
if($iddoc){

	$rsDocumento = $dbc->query("
		SELECT
			d.iddocumento,
			d.ndocimpreso,
			DATE_FORMAT(d.fechacreacion,'%d/%m/%Y') as fechacreacion,
			l.procedencia,
			l.totalagencia,
			l.totalalmacen,
			l.fletetotal,
			l.segurototal,
			l.otrosgastos,
			l.porcentajetransporte,
			l.porcentajepapeleria,
			l.peso,
			p.nombre,
			b.nombrebodega,
			valorcosto as iso,
			tc.tasa
		FROM documentos d
		JOIN liquidaciones l ON d.iddocumento = l.iddocumento
        JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
		JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["PROVEEDOR"]}
		JOIN bodegas b ON b.idbodega = d.idbodega
		LEFT JOIN costosxdocumento cxd ON d.iddocumento = cxd.iddocumento
		LEFT JOIN costosagregados ca ON cxd.idcostoagregado = ca.idcostoagregado
		JOIN tiposcambio tc ON d.idtipocambio = tc.idtc
		WHERE ca.idtipocosto = 6 AND d.iddocumento = $iddoc
		GROUP BY d.iddocumento
	");
	$row_rsDocumento = $rsDocumento->fetch_assoc();

	$rsArticulos = $dbc->query("
	         SELECT
            a.idarticulo,
            descripcion,
            unidades,
            costocompra,
            fob,
            flete,
            seguro,
            otrosgastos,
            cif,
            impuestos,
            comision,
            agencia,
            almacen,
            papeleria,
            transporte,
            iddocumento
        FROM vw_articulosprorrateados v
        JOIN vw_articulosdescritos a ON a.idarticulo = v.idarticulo
	WHERE iddocumento = $iddoc
	");



}else{
	die();
}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Liquidaci&oacute;n <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
html{
    border:0;
    padding:0;
    margin:0;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8pt;
	float:left;
	width:1020pt;
	padding:40pt 50pt;
/* 	border:1px solid #000; */
}

.gray {
	background: #f4f4f4;
}

.rigth {
	text-align: right;
}

h2 {
	border-bottom: 4px solid #000000;
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
	table-layout:fixed;
}

thead {
	display: table-header-group;
	border-width: 1px 0;
	border-color: #000;
	border-style: solid;
}

tbody {
	display: table-row-group;
}
table{
    width:100%;
}
@media print {
    .gray {
        background: #fff;
    }
    th {
        background: #fff;
        color:#000;
    }
    thead{
        color:#000;
    }

}
</style>
</head>
<body>
<h1>Llantera Esquipulas</h1>
<div style="width:100%">
<h2>Liquidaci&oacute;n de costos</h2>
<div class="float">

<p><strong>Poliza </strong><?php echo $row_rsDocumento["ndocimpreso"] ?></p>
<p><strong>Procedencia </strong><?php echo $row_rsDocumento["procedencia"] ?></p>
</div>
<div class="float">
<p><strong>Fecha </strong><?php echo $row_rsDocumento["fechacreacion"] ?></p>
<p><strong>Bodega </strong><?php echo $row_rsDocumento["nombrebodega"] ?></p>
</div>
<div class="float">
<p><strong>Tipo Cambio </strong><?php echo $row_rsDocumento["tasa"] ?></p>
<p><strong>Proveedor </strong><?php echo $row_rsDocumento["nombre"] ?></p>

</div>
</div>
<table border="1" frame="border" rules="rows" cellpadding="2"
	cellspacing="0" summary="Reporte de liquidacion">
	<tr>
		<th  style="width:100pt">Articulo</th>
		<th style="width:20pt">Cantidad</th>
		<th style="width:25pt">Precio Unit</th>
		<th style="width:30pt">FOB</th>

		<th style="width:20pt">Flete</th>
		<th style="width:20pt">Seguro</th>
		<th style="width:20pt">Otros Gastos</th>
		<th style="width:25pt">CIF</th>
		<th style="width:25pt">Comisi&oacute;n</th>
		<th style="width:20pt">Agencia</th>
		<th style="width:20pt">Almacen</th>
		<th style="width:25pt">Papeleria</th>
		<th style="width:25pt">Impuestos</th>
		<th style="width:25pt">Acarreo</th>
	</tr>
	<?php $color = 1; 	while ( $row_rsArticulo = $rsArticulos->fetch_assoc() ){	 ?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td style="text-align:left"><?php echo $row_rsArticulo["descripcion"] ?></td>
		<td><?php echo $row_rsArticulo["unidades"] ?></td>
		<td><?php echo number_format($row_rsArticulo["costocompra"], 4) ?></td>
		<td><?php echo number_format($row_rsArticulo["fob"],4) ?></td>

		<td><?php echo number_format($row_rsArticulo["flete"],4)  ?></td>
		<td><?php echo number_format($row_rsArticulo["seguro"],4)  ?></td>
		<td><?php echo number_format($row_rsArticulo["otrosgastos"],4)  ?></td>
		<td><?php echo number_format($row_rsArticulo["cif"],4) ?></td>
		<td><?php echo number_format($row_rsArticulo["comision"],4) ?></td>
		<td><?php echo number_format($row_rsArticulo["agencia"],4) ?></td>
		<td><?php echo number_format($row_rsArticulo["almacen"], 4)?></td>
		<td><?php echo number_format( $row_rsArticulo["papeleria"] ,4    )?></td>

		<td><?php echo number_format($row_rsArticulo["impuestos"], 4)  ?></td>

		<td><?php echo number_format( $row_rsArticulo["transporte"] ,4    )?></td>


	</tr>
	<?php }?>
</table>
	<?php if($row_rsDocumento["observacion"]){ ?>
<h2>Observaciones</h2>
<div><?php echo $row_rsDocumento["observacion"] ?></div>
	<?php } ?>

</body>
</html>
