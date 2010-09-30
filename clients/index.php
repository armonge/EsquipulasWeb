<?php
require_once "../functions.php";
try{
    $del = (int)$_GET["del"];
    if($del){
        $dbc->query("UPDATE personas  SET activo = IF(activo = 0,1,0) WHERE idpersona = $del AND tipopersona = 1 LIMIT 1");
        if($dbc->affected_rows){
                $status="Se ha cambiado el estado de un cliente";
        }
    }

    $rsClients = $dbc->query("
    SELECT
    p.idpersona,
    p.nombre,
    p.activo,
    SUM(IF(fact.iddocumento IS NOT NULL, fact.total , 0) ) totalfacturas,
    SUM(IF(rec.iddocumento IS NOT NULL, rec.total , 0) ) totalrecibos
    FROM personas p
    LEFT JOIN personasxdocumento pxd ON pxd.idpersona = p.idpersona AND pxd.idaccion = {$persontypes["CLIENTE"]}
    LEFT JOIN documentos fact ON fact.iddocumento = pxd.iddocumento AND fact.idtipodoc = {$docids["FACTURA"]} AND fact.idestado = {$docstates["CONFIRMADO"]}
    LEFT JOIN documentos rec ON rec.iddocumento = pxd.iddocumento AND rec.idtipodoc = {$docids["RECIBO"]} AND rec.idestado = {$docstates["CONFIRMADO"]}
    WHERE p.tipopersona = {$persontypes["CLIENTE"]}
    GROUP BY p.idpersona
    ORDER BY activo DESC
    ");
    $row_rsClient=$rsClients->fetch_array(MYSQLI_ASSOC);
    $estadoClientes = $row_rsClient["activo"];
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
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m5 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m5 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
</style>
<script type="text/javascript">
$(function(){
    $("#content li").corner();
});
</script >
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	    <h1>Clientes</h1>
		<?php echo $status ?>
	    <?php if($rsClients->num_rows){ ?>
            <?php echo $estadoClientes == 0 ?  "<p>Clientes Inactivos</p>" :"" ; ?> 
            
		<ul>
		<?php do{ 
				if($estadoClientes != $row_rsClient["activo"]){
						echo "</ul>Clientes Inactivos<ul>";
				}
				$estadoClientes = $row_rsClient["activo"];
		?>
				<li>
                    <a href="clients/details.php?id=<?php echo $row_rsClient["idpersona"] ?>">
                        <?php echo $row_rsClient["nombre"] ?>
                    </a> <br />
                    <?php
                    $saldo = $row_rsClient["totalfacturas"] - $row_rsClient["totalrecibos"];
                    if($saldo > 0){ ?>
                        Este cliente tiene  un saldo de US$<?php echo $saldo ?>
                    <?php } ?>
                    
                </li>
		<?php }while($row_rsClient = $rsClients->fetch_array(MYSQLI_ASSOC)) ?>
		</ul>
	    <?php }else{ ?>
		No hay ning&uacute;n cliente
	    <?php } ?>
		<p>
			<a href="clients/add.php">A&ntilde;adir Cliente</a>
		</p>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
