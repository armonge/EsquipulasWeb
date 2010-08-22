<?php
require_once "../functions.php";

$iddoc = (int)$_GET["doc"];
if(!$iddoc){
	die();
}
$query = "
    SELECT
        d.iddocumento,
        d.ndocimpreso,
        p.nombre as cliente,
        d.total* (1-IF(valorcosto IS NULL,0,valorcosto)/100) as subtotal,
        d.total*ca.valorcosto/100 as iva,
        d.total,
        DATE_FORMAT(d.fechacreacion,'%d/%m/%Y') as fechacreacion,
        d.escontado,
        cr.iddocumento as idcredito,
        d.idestado
    FROM documentos d
    JOIN tiposdoc td ON td.idtipodoc=d.idtipodoc
    JOIN bodegas b ON b.idbodega=d.idbodega
    JOIN tiposcambio tc ON tc.idtc=d.idtipocambio
    JOIN personasxdocumento pxd ON d.iddocumento = pxd.iddocumento
    JOIN personas p ON p.idpersona=pxd.idpersona AND p.tipopersona = 1
    LEFT JOIN costosxdocumento cd ON cd.iddocumento=d.iddocumento
    LEFT JOIN costosagregados ca ON ca.idcostoagregado=cd.idcostoagregado
    LEFT JOIN creditos cr ON cd.iddocumento = d.iddocumento
    WHERE d.idtipodoc={$docids["FACTURA"]}
    AND d.iddocumento=$iddoc
";
$rsDocumento = $dbc->query($query);

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php echo $basedir ?>" />
<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
<title>Facturas</title>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<link rel="stylesheet" type="text/css" href="css/flick/jq.ui.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript">
$(function(){
    $('#accept, #deny').button()
});
</script>
<style type="text/css">
#m2 a{
    background: url(img/nav-left.png) no-repeat left;
}
#m2 span{
    background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#header {
    text-align: center;
}
#type {
    text-align: right;
    padding-right:6%;
    margin-top:0;
    clear:both;
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
    clear:both;
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
tbody {
    display: table-row-group;
}
#details {
    width: 100%;
    table-layout:fixed;
}
#fecha{
    table-layout:fixed;
}
#persons table {
    width:100%;
}
#totals {
    width: 30%;
    float: right;
    height:144px;
}
thead {
    display: table-header-group;
}
#totals tr{
    border:1px solid #000;
    border-top:0;
    height:20px;
}
#persons .pwrap{
    width: 46%;
    border:1px solid #000;
    float: right;
    margin: 0 0 36px 2%;
    height:108px;
}
#table {
    height: 425px;
    border: 1px solid #000;
    width: 100%;
    margin-top:25px;
    float: left;
}

#authorization {
    border: 1px solid #000;
    padding: 20px;
    padding-top: 40px;
    width: 50%;
    margin-top: 5px;
    display: inline-block;
    text-align: center;
}

#authorization span {
    padding-top: 10px;
    border-top: 1px solid #000;
}
.square{
    display: inline-block;
    width: 15px;
    height: 15px;
    border: 1px solid #000;
}
#action{
    clear:both;
    text-align:center;
    float:left;
    width:100%;
    margin:10px;
}
#action h2{
    text-align:center;
}
@media print {
    html{
        border:0;
        margin:0;
        padding:0;
    }
    body {
        font-family: Arial;
        font-size: 12pt;
        width: 750pt;
        padding:0;
        margin:0;
        padding-left:60pt;
        padding-top:50pt;
        height:960pt;
        color: #000;
    }
    #header {
        height:100pt;
    }

    thead {
        height:30pt;
    }
    #header,thead, #header h1,.white{
        color:#fff;
    }
    .gray,th {
        background: #fff;
    }
    #table,#persons .pwrap,#authorization, #authorization span, #totals tr{
        border:0;
    }
    .square{
        border:0;
    }
    #footer, #logo, #uname, #menu, .ui-widget, #action{
        display:none;
    }
    #details tbody tr td,#details tbody tr {
        height: 15pt;
    }

    #totals {
        height:144pt;
    }
    #totals tr{
        height:20pt;
    }
    #persons{
        margin-top:20pt;
    }
    #persons .pwrap{
        margin: 0 0 36pt 2%;
        height:108pt;
    }
    .square{
        width: 15pt;
        height: 15pt;
    }
    #table {
        height: 425pt;
        margin-top:25pt;
    }

    #authorization {
        padding: 20pt;
        padding-top: 40pt;
        margin-top: 5pt;
    }

    #authorization span {
        padding-top: 10pt;
    }
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
<div id="header">
<h2><?php echo $row_rsDocumento["idcredito"] ? "Credito": "Regalia" ?></h2>
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
<table border="0" frame="border" rules="lines" cellpadding="5"	cellspacing="0" summary="Totales factura" id="totals">
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
<?php if($_SESSION["user"]->hasRole("gerencia")   && !$row_rsDocumento["estado"]){ ?>
<div id="action">

        <a id="accept" href="<?php echo $base ?>administration/authorizations.php?doc=<?php echo $iddoc ?>"
         onclick="return confirm('¿Realmente desea confirmar esta factura?')" >Autorizar</a>
        <a id="deny" href="<?php echo $base ?>administration/authorizations.php?del=<?php echo $iddoc ?>"
        onclick="return confirm('¿Realmente desea borrar esta factura?')">Denegar</a>
</div>
<?php } ?>

<?php include "../footer.php" ?>
</div>

</div>

</body>
</html>
