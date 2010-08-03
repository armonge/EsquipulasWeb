<?php
require_once "../functions.php";

$iddoc = (int)$_GET["doc"];
if($iddoc){

	$rsDocumento = $dbc->query("
		SELECT 
		    d.ndocimpreso,
		    DATE(d.fechacreacion) as Fecha,
		    c.descripcion,d.total,p.nombre		
		FROM documentos d 
		JOIN conceptos c ON c.idconcepto=d.idconcepto
		JOIN personasxdocumento pd ON d.iddocumento=pd.iddocumento
		JOIN personas p ON p.idpersona=pd.idpersona
		WHERE d.idtipodoc= 12 AND p.tipopersona = 2
		ORDER BY CAST(d.ndocimpreso AS SIGNED)

	");
	$row_rsDocumento = $rsDocumento->fetch_assoc();

	$rsCuentasContables = $dbc->query("
	         SELECT 
		    c.codigo,c.descripcion,
		    IF (cd.monto<0,@valor:=cd.monto,'---') as Debe, 
		    IF (cd.monto>0,cd.monto,'---') as Haber
		 FROM cuentascontables c 
		 join cuentasxdocumento cd on cd.idcuenta=c.idcuenta
		join documentos d on d.iddocumento=cd.iddocumento
		WHERE d.iddocumento = $iddoc and d.idtipodoc=12
		order by cd.nlinea
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
<title>Llantera Esquipulas: Cheque <?php echo $row_rsDocumento["ndocimpreso"] ?></title>
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
	width: 50%
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
<div style="width:80%">
<h2>Cheques</h2>
<p align="right"><strong>Fecha </strong><?php echo $row_rsDocumento["Fecha"] ?></p>
<p><strong>Cheque# </strong><?php echo $row_rsDocumento["ndocimpreso"] ?> 
<p align="right"><strong>	Total </strong><?php echo $row_rsDocumento["total"] ?></p>
</p>
<p><strong>Paguese a la orden de </strong><?php echo $row_rsDocumento["nombre"] ?>	</p>
<p>&nbsp;</p>
<p><strong>EN CONCEPTO </strong><?php echo $row_rsDocumento["descripcion"] ?></p>
<div class="float">
<table border="1" frame="border" rules="rows" cellpadding="2"
	cellspacing="0" summary="Reporte de Cheques">
<p>&nbsp;</p>
<tr>
	  <th  style="width:30pt">Codigo</th>
		<th style="width:20pt">Descripcion</th>
		<th style="width:30pt">Debe</th>
		<th style="width:30pt">Haber</th>

<?php $color = 1; 	
while ( $row_rsCuentasContables = $rsCuentasContables->fetch_assoc() ){	 ?>

<tr <?php if($color % 2 == 0 ){ echo "class='gray'"; } ?>>
<td style="text-align:left"><?php echo $row_rsCuentasContables["codigo"] ?></td>
<td style="text-align:left"><?php echo $row_rsCuentasContables["descripcion"] ?></td>
<td><?php echo $row_rsCuentasContables["Debe"]  ?></td>
<td><?php echo$row_rsCuentasContables["Haber"] ?></td>
</p>

<table>
<?php }?>
</body>

</html>
