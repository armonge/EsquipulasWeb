<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
if(!$_SESSION["user"]->hasRole("gerencia")){
	die("Usted no tiene permisos para entrar a administraci&oacute;n");
}
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
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<style type="text/css">
#m3 a{
	background: url(img/nav-left.png) no-repeat left;
}
#m3 span{
	background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#content ul{
    list-style:none;
    width:90%;
    margin:auto;
    padding:0;
    text-align:center;
}
#content li{
    list-style-image:none;
    width:80px;
    margin:10px;
    text-align:center;
    display:inline-block;

    zoom:1;
    *display:inline;
}
#content li img{
	display:block;
	margin:auto;
}
</style>
</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
		<h1>Administraci&oacute;n</h1>
		<ul>
            <li>
                <a href="administration/authorizations.php" title="Autorizaciones">
                <img src="img/authorizations.png" alt="Autorizaciones" />
                Autorizaciones
                </a>
            </li>
            <li>
                <a href="administration/exchangerates.php" title="Tipos de Cambio">
                <img src="img/exchange-rates.png" alt="Tipos de Cambio" />
                Tipos de Cambio
                </a>
            </li>
            <?php if($_SESSION["user"]->hasRole("root")){ ?>
			<li>
			    <a href="administration/users.php" title="Administraci&oacute;n de Usuarios">
				<img src="img/system-users.png" alt="Administraci&oacute;n de Usuarios" />
				Usuarios
			    </a>
			</li>
			<li>
			    <a href="administration/salesmen.php" title="Administraci&oacute;n de Vendedores">
				<img src="img/resource-group.png" alt="Administraci&oacute;n de Vendedores" />
				Vendedores
			    </a>
			</li>
			<li>
                <a href="administration/bankaccounts.php" title="Administraci&oacute;n de Cuentas Bancarias">
                <img src="img/view-bank-account-savings.png" alt="Administraci&oacute;n de Cuentas Bancarias" />
                Cuentas Bancarias
                </a>
            </li>
            <li>
                <a href="administration/banks.php" title="Administraci&oacute;n de Bancos">
                <img src="img/institution.png" alt="Administraci&oacute;n de Bancos" />
                Bancos
                </a>
            </li>

			<li>
			    <a href="administration/warehouses.php" title="Bodegas">
				<img src="img/warehouses.png" alt="Bodegas" />
				Bodegas
			    </a>
			</li>
			<li>
			    <a href="administration/pos.php" title="Puntos de Caja">
				<img src="img/pos.png" alt="Puntos de Caja" />
				Puntos de Caja
			    </a> 
			</li>
			<li>
			    <a href="administration/taxes.php" title="Impuestos">
				<img src="img/taxes.png" alt="Impuestos" />
				Impuestos
			    </a>
			</li>
            <li>
                <a href="administration/concepts.php" title="Conceptos">
                <img src="img/concepts.png" alt="Conceptos" />
                Conceptos
                </a>
            </li>
            <?php } ?>
		</ul>
</div>

<?php include "../footer.php" ?>
</div>
</body>
</html>