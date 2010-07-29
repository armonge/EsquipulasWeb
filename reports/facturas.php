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
		DATE_FORMAT(d.fechacreacion,'%d/%m/%Y') as fechacreacion,
		d.escontado
	FROM documentos d
	JOIN tiposdoc td ON td.idtipodoc=d.idtipodoc
	JOIN bodegas b ON b.idbodega=d.idbodega
	JOIN tiposcambio tc ON tc.idtc=d.idtipocambio
	JOIN personasxdocumento pxd ON d.iddocumento = pxd.iddocumento
	JOIN personas p ON p.idpersona=pxd.idpersona AND p.tipopersona = 1
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
	font-family: Arial;
	font-size: 12pt;
	width: 690pt;
	padding:0;
	margin:0;
	margin-left:60pt;
	margin-top:50pt;
	height:900pt;
/* 	border:1px solid #000; */
}

#type {
	text-align: right;
	padding-right:6%;
	margin-top:0;
	clear:both;
}

#header {
	text-align: center;
	height:100pt;
}

#header p,#header h1 {
	margin-bottom: 0;
	margin-top: 0;
}

#header2 h2 {
	display: inline-block
}

#header2 table {
	float: right;
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
	border:0;
}

thead {
	display: table-header-group;
	height:30pt;
}

tbody {
	display: table-row-group;
}

#table {
	height: 425pt;
	border: 1px solid #000;
	width: 100%;
	margin-top:25pt;
	float: left;
}

#authorization {
	border: 1px solid #000;
	padding: 20pt;
	padding-top: 40pt;
	width: 50%;
	margin-top: 5pt;
	display: inline-block;
	text-align: center;
}

#authorization span {
	padding-top: 10pt;
	border-top: 1px solid #000;
}

#details {
	width: 100%;
	table-layout:fixed;
}
#fecha{
    table-layout:fixed;
}
}

#details tbody tr td,#details tbody tr {
	height: 15pt;
}

#totals {
	width: 30%;
	height:144pt;
	float: right;
}
#totals tr{
    height:20pt;
	border:1px solid #000;
	border-top:0;
}
#persons{
    margin-top:20pt;
}
#persons table {
	width:100%;
}
#persons .pwrap{
	width: 46%;
	border:1px solid #000;
	float: right;
	margin: 0 0 36pt 2%;
	height:108pt;
}
.square{
    display: inline-block;
    width: 15pt;
    height: 15pt;
    border: 1px solid #000;
}
@media print {
	#header,thead{
		color:#fff;
	}
	.gray {
		background: #fff;
	}
	th {
		background: #fff;
	}
	.white{
        color:#fff;
	}

	#table,#persons .pwrap,#authorization, #authorization span, #totals tr{
		border:0;
	}
	.square{
        border:0;
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
<h2><span class="white">Factura N&deg; <?php echo $row_rsDocumento["ndocimpreso"] ?></span></h2>
                                        
<table border="0"  rules="none" cellpadding="5"
	cellspacing="0" id="fecha">
	<thead>
		<tr>
			<th style="width:100pt">Fecha</th>
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
<p id="type"><span class="white">CONTADO</span> <span class="square" style="margin-right:50pt"><?php if($row_rsDocumento["escontado"]){ echo "X&nbsp;" ; }else{ echo "&nbsp;&nbsp;"; } ?></span>
<span class="white">CREDITO</span> <span class="square" ><?php if(!$row_rsDocumento["escontado"]){ echo "X&nbsp;" ; }else{ echo "&nbsp;&nbsp;"; }	 ?></span>
</p>

<div id="persons">

<div class="pwrap">
<table border="0" frame="border" rules="none" cellpadding="5"
	cellspacing="0">
	<thead>
		<tr>
			<th>Entregar a:</th>
		</tr>
	</thead>
	<tbody  valign="top">
		<tr>
			<td><?php echo $row_rsDocumento["cliente"]?></td>
		</tr>
	</tbody>
</table>
</div>
<div class="pwrap">
<table border="0" frame="border" rules="none" cellpadding="5"
	cellspacing="0">
	<thead >
		<tr>
			<th>Facturar a:</th>
		</tr>
	</thead>
	<tbody  valign="top">
		<tr>
			<td><?php echo $row_rsDocumento["cliente"]?></td>
		</tr>
	</tbody>
</table>
</div>
</div>
<div id="table">
<table border="0" frame="border" rules="none" cellpadding="5"
	cellspacing="0" summary="Detalle factura" id="details">
	<thead>
		<tr>
			<th style="width:100pt">Item No.</th>
			<th style="width:102pt" >Cantidad</th>
			<th style="width:280pt">Descripci&oacute;n</th>
			<th style="width:100pt" >Precio Unit</th>
			<th>Valor</th>
		</tr>
	</thead>
	<tbody valign="top">
	<?php $color = 1; while ($row_rsArticulos = $rsArticulos->fetch_assoc() ){ $color++;  ?>
		<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
			<td ><?php echo $row_rsArticulos["idarticulo"] ?></td>
			<td ><?php echo $row_rsArticulos["unidades"] ?></td>
			<td style="text-align:left"><?php echo utf8tohtml($row_rsArticulos["descripcion"]) ?></td>
			<td >US$ <?php echo number_format($row_rsArticulos["preciounit"],2) ?></td>
			<td >US$ <?php echo number_format($row_rsArticulos["unidades"]*$row_rsArticulos["preciounit"],2) ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
</div>
<div id="authorization"><span class="white">AUTORIZADO POR</span></div>
<table border="0" frame="border" rules="none" cellpadding="5"	cellspacing="0" summary="Totales factura" id="totals">
	<tbody>
		<tr>
			<td class="white">
               Subtotal
            </td>
            <td>
                US$ <?php echo number_format($row_rsDocumento["subtotal"],2) ?>
			</td>
		</tr>
		<tr>
			<td class="white">IVA</td><td> US$ <?php echo number_format($row_rsDocumento["iva"],2) ?></td>
		</tr>
		<tr>
			<td class="white">Total</td><td> US$ <?php echo number_format($row_rsDocumento["total"],2) ?>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>
