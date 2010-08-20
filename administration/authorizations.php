<?php
require_once "../functions.php";
if(!$_SESSION["user"]->hasRole("gerencia")){
    die("Usted no tiene permisos para administrar conceptos");
}
$authid = (int)$_GET["doc"];
if($authid){
    $result = $dbc->query("
    CALL spAutorizarFactura($authid,{$_SESSION["user"]->uid},
    173,
    14,
    22,
    182,
    133
    )"
    );
    if($result){
        $print = "<p>La factura se ha autorizado</p>";
    }else{
        $print = "<p class'error'>Hubo un error al autorizar la factura</p>";
    }
}
$delid = (int)$_GET["del"];
if($delid){
    //denegar la factura
}

$query = "
SELECT
    d.iddocumento,
    d.idtipodoc,
    td.descripcion,
    p.nombre
FROM documentos d
JOIN tiposdoc td ON d.idtipodoc = td.idtipodoc
JOIN creditos cr ON cr.iddocumento = d.iddocumento
JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["CLIENTE"]}
WHERE d.idestado = {$docstates["PENDIENTE"]}
ORDER BY d.idtipodoc
";
$rsCreditInvoices = $dbc->query($query);

$query = "
SELECT
    d.iddocumento,
    d.idtipodoc,
    td.descripcion,
    p.nombre
FROM documentos d
JOIN tiposdoc td ON d.idtipodoc = td.idtipodoc
LEFT JOIN creditos cr ON cr.iddocumento = d.iddocumento
JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
JOIN personas p ON p.idpersona = pxd.idpersona AND p.tipopersona = {$persontypes["CLIENTE"]}
WHERE d.idestado = {$docstates["PENDIENTE"]} AND cr.iddocumento IS NULL
ORDER BY d.idtipodoc
";
$rsInvoiceRoyalties = $dbc->query($query);

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
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript">
$(function(){
});
</script>
<style type="text/css">
#m3 a{
    background: url(img/nav-left.png) no-repeat left;
}
#m3 span{
    background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
    <?php echo $print ?>
    <h1>Documentos pendientes de autorizaci&oacute;n</h1>
    <?php if($rsCreditInvoices->num_rows){ ?>
    <h2>Creditos de factura</h2>
    <ul>
        <?php while($row_rsDocument = $rsCreditInvoices->fetch_array(MYSQLI_ASSOC)){ ?>
            <li><a href="<?php echo $base ?>reports/facturas.php?doc=<?php echo $row_rsDocument["iddocumento"] ?>"><?php echo $row_rsDocument["descripcion"] ?> para <?php echo $row_rsDocument["nombre"] ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    <?php if($rsInvoiceRoyalties->num_rows){ ?>
    <h2>Regalias de factura</h2>
    <ul>
        <?php while($row_rsDocument = $rsInvoiceRoyalties->fetch_array(MYSQLI_ASSOC)){ ?>
            <li><a href="<?php echo $base ?>reports/facturas.php?doc=<?php echo $row_rsDocument["iddocumento"] ?>"><?php echo $row_rsDocument["descripcion"] ?> para <?php echo $row_rsDocument["nombre3"] ?></a></li>
        <?php } ?>
    </ul>
    <?php } ?>

    <?php if(!( $rsInvoiceRoyalties->num_rows + $rsCreditInvoices->num_rows)){ ?>
    
        <p>No hay documentos pendientes de autorizaci&oacute;n</p>
    <?php } ?>

    


</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>