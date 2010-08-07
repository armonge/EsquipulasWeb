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
    d.fechacreacion,
    p.nombre as proveedor,
    d.observacion,
    (@subtotalc:=( ( d.total / (1 + (ca.valorcosto / 100)  ) ) )  * tc.tasa  ) AS subtotalC ,
    ( @subtotalc * (  (ca.valorcosto / 100) ) )  AS ivaC,
    d.total * tc.tasa AS totalC,
    d.total AS totalD,
    escontado as tipopago
FROM documentos d
JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
JOIN personas p ON pxd.idpersona = p.idpersona AND p.tipopersona = {$persontypes["PROVEEDOR"]}
JOIN articulosxdocumento axd ON axd.iddocumento = d.iddocumento
JOIN costosxdocumento cxd ON cxd.iddocumento = d.iddocumento
JOIN costosagregados ca ON cxd.idcostoagregado = ca.idcostoagregado
JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
WHERE d.idtipodoc = {$docids["ENTRADALOCAL"]} AND d.iddocumento = $iddoc
");
$row_rsDocumento = $rsDocumento->fetch_assoc();

$rsArticulos = $dbc->query("
SELECT
    a.descripcion,
    axd.unidades as cantidad,
    CONCAT('C$',FORMAT((axd.costounit * tc.tasa),4)) as precioC,
    CONCAT('US$', axd.costounit) as precioD,
    CONCAT('C$',FORMAT(((axd.unidades * axd.costounit) * tc.tasa),4)) as totalC,
    CONCAT('US$',FORMAT((axd.unidades * axd.costounit),4)) as totalD
FROM documentos d
JOIN articulosxdocumento axd ON axd.iddocumento=d.iddocumento
JOIN vw_articulosdescritos a ON axd.idarticulo = a.idarticulo
JOIN tiposcambio tc ON tc.idtc = d.idtipocambio
WHERE d.idtipodoc = {$docids["ENTRADALOCAL"]} AND axd.iddocumento = $iddoc
");


?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=UTF-8" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Llantera Esquipulas: Entrada No <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	width: 750pt;
}

.gray {
	background: #f4f4f4;
}

.rigth {
	text-align: right;
}

h2 {
	border-bottom: 4px solid #000000;
	clear: both;
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
    width:100%;
}

.error {
	background: #C00000;
	color:#fff;
	background-image:"../flag-red.png";
	background-repeat:no-repeat;
	background-position:left;
}
.error2{
	background: #F3FF05;
	color:#000;
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
<?php if($iddoc){ ?>
<h2>Entradas Locales</h2>
<p><strong>Numero de Entrada:</strong> <?php echo $row_rsDocumento["ndocimpreso"] ?>
<br />
<strong>Fecha del documento: </strong><?php echo $row_rsDocumento["fechacreacion"] ?> <br />
<strong>Proveedor: </strong><?php echo $row_rsDocumento["proveedor"] ?></p>
<table border="1" frame="border" rules="all" cellpadding="5" cellspacing="1"	summary="Reporte de partida contable">
	<col width="400" />
	<thead>
	<tr>
		<th>Articulo</th>
		<th>Unidades</th>
		<th>Precio C$</th>
		<th>Precio US$</th>
		<th>Total C$</th>
		<th>Total US$</th>
	</tr>
	</thead>
	<tbody>
	<?php $color = 1; while ($row_rsArticulos = $rsArticulos->fetch_assoc() ){ $color++;  ?>
	<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td><?php echo $row_rsArticulos["descripcion"] ?></td>
		<td><?php echo $row_rsArticulos["cantidad"] ?></td>
		<td><?php echo $row_rsArticulos["precioC"] ?></td>
		<td><?php echo $row_rsArticulos["precioD"] ?></td>
		<td><?php echo $row_rsArticulos["totalC"] ?></td>
		<td><?php echo $row_rsArticulos["totalD"] ?></td>
		
	</tr>
	<?php } ?>
	<tr <?php $color++; if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td colspan="4"></td>
		<td class="rigth">Subtotal C$</td>
		<td>C$ <?php echo number_format($row_rsDocumento["subtotalC"],2) ?></td>
	</tr>
	<tr <?php $color++; if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td colspan="4"></td>
		<td class="rigth">IVA C$</td>
		<td>C$ <?php echo number_format($row_rsDocumento["ivaC"],2) ?></td>
	</tr>
	<tr <?php $color++; if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
		<td colspan="4"></td>
		<td class="rigth">TOTAL C$</td>
		<td>C$ <?php echo number_format($row_rsDocumento["totalC"],2) ?></td>
	</tr>
	<tr <?php $color++; if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
        <td colspan="4"></td>
        <td class="rigth">TOTAL US$</td>
        <td>US$ <?php echo number_format($row_rsDocumento["totalD"],2) ?></td>
    </tr>
	</tbody>
</table>
	<?php if($row_rsDocumento["observacion"]){ ?>
<h2>Observaciones</h2>
<div><?php echo $row_rsDocumento["observacion"] ?></div>
	<?php } ?>

	<?php }else{ ?>
	<h1>Lista de Entradas Locales</h1>
		<table border="1" frame="border" rules="all" cellpadding="5"
	cellspacing="1" summary="Reporte de liquidacion">
	<thead>
		<tr>
			<th>Numero de Liquidacion</th>
			<th>Fecha</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<?php while($row_rsEntrada = $rsEntradas->fetch_assoc()){?>
		<tr>
			<td><a href="entradaslocales.php?doc=<?php echo $row_rsEntrada["iddocumento"] ?>"><?php echo $row_rsEntrada["ndocimpreso"]?></a></td>
			<td><?php echo $row_rsEntrada["fechacreacion"]?></td>
			<td>C$ <?php echo number_format($row_rsEntrada["total"], 4 ) ?></td>
		</tr>
		<?php } ?>
	</tbody>
	</table>

	<?php } ?>
</body>
</html>
