<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
    $id = (int)$_GET["id"];
    if(!$id){
        die();
    }
    $query = "
    SELECT p.idpersona, p.nombre, p.telefono, p.email, p.ruc,p.activo, SUM(d.total) as total
    FROM personas p
    JOIN personasxdocumento pxd ON pxd.idpersona = p.idpersona
    JOIN documentos d ON d.iddocumento = pxd.iddocumento
    WHERE p.idpersona = $id AND p.tipopersona = 3
    ";
    $rsDetails = $dbc->query($query);
    $row_rsDetails = $rsDetails->fetch_array(MYSQLI_ASSOC);

    $query="
    SELECT
    d.iddocumento, d.ndocimpreso, UNIX_TIMESTAMP(d.fechacreacion)*1000 as stamp, d.total
    FROM documentos d
    JOIN personasxdocumento pxd ON pxd.iddocumento = d.iddocumento
    JOIN personas p ON p.idpersona = pxd.idpersona 
    WHERE p.idpersona = $id AND d.idtipodoc = 5
    ORDER BY DATE(d.fechacreacion)
    ";
    $rsTransactions = $dbc->query($query);
}catch(EsquipulasException $ex){
    if($local){
        die($ex);
    }else{
        $ex->mail(ADMINMAIL);
        header("Location: {$basedir}error.php ");
        die();
    }
}
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
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.corner.js"></script>
<script type="text/javascript" src="js/jq.flot.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m5 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m5 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#content span{
    width:20%;
    display:inline-block;
}
h2:hover{
    color:blue;
    cursor:pointer;
}
#canvas{
    width:90%;
    height:300px;
}
</style>
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
    <h1>Vendedores: <?php echo $row_rsDetails["nombre"] ?></h1>
	<p>
		<a href="administration/salesman.php?edit=<?php echo $id ?>">Editar</a> /
		<?php if($row_rsDetails["activo"] == 1){ ?>
		<a href="administration/salesmen.php?del=<?php echo $id ?>" onclick="return confirm('Realmente desea establecer este cliente como inactivo?')" title="Esto solo borrara al vendedor de la lista de vendedores pero no eliminara los registros de sus transacciones">Desactivar vendedor</a>
		<?php }else{ ?>
		<a href="administration/salesmen.php?del=<?php echo $id ?>" onclick="return confirm('Realmente desea establecer este cliente como activo?')" >Activar cliente</a>
		<?php } ?>
	</p>
	<p>
	<span><strong>Telef&oacute;no:</strong> <?php echo $row_rsDetails["telefono"] ?></span>
	<span><strong>e-mail:</strong> <?php echo $row_rsDetails["email"] ?></span>
	<span><strong>RUC/Id:</strong> <?php echo $row_rsDetails["ruc"] ?></span>
	<span><strong>Total de Ventas:</strong> US$<?php echo number_format($row_rsDetails["total"],4) ?></span>
    </p>
    <?php if($rsTransactions->num_rows){ ?>
	<h2>Ultimas Compras</h2>
	<div id="canvas">
	</div>
    <?php } ?>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
