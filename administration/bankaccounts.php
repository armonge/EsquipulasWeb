<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
    if(!$_SESSION["user"]->hasRole("root")){
        die("Usted no tiene permisos para administrar cuentas bancarias");
    }
    if(isset($_POST["add"])){
        $bankid = (int)$_POST["bank"];
        $currency = (int)$_POST["currency"];
        $name = $dbc->real_escape_string($_POST["account_name"]);

        $rsBank =  $dbc->query("SELECT descripcion FROM bancos WHERE idbanco = $bankid LIMIT 1");


        if(!($bankid || $currency || $name || in_array($currency, $moneda) || $rsBank->num_rows )){
            die("Hubo un error al a&ntilde;adir la cuenta bancaria");
        }else{

            $row_rsBank = $rsBank->fetch_assoc();
            $bankname = $row_rsBank["descripcion"];

            $padre = $currency == 1 ? $accounts["BMN"] : $accounts["BME"] ;

            $query = "
            SELECT
            CONCAT_WS(' ',SUBSTR(codigo,1,11), LPAD(MAX(SUBSTR(codigo,13,3))+1,3,0),'000') as code
            FROM esquipulasdb.cuentascontables
            WHERE padre = $padre
            LIMIT 1;
            ";
            $rsCode = $dbc->query($query);
            $row_rsCode = $rsCode->fetch_assoc();
            $code = $row_rsCode["code"];

            $description = ( $currency == 1 ? "BMN" : "BME") . " " . $bankname . " " . $name ;


            $dbc->simple_insert("cuentascontables", array(
                "padre"=>$padre,
                "codigo"=>$code,
                "descripcion"=>$description,
                "esdebe"=>1
            ));

            $dbc->simple_insert("cuentasbancarias",array(
                "idcuentacontable"=>$dbc->insert_id,
                "idbanco"=>$bankid,
                "idtipomoneda"=>$currency,
                "ctabancaria"=>$name,
                "fechaapertura" => "CURDATE()",
                "seriedoc"=>1
            ));
        }
    }
    $query = "
    SELECT
        cb.idcuentacontable ,
        cb.ctabancaria,
        b.descripcion as banco,
        cc.descripcion ncontable,
        cc.codigo,
        m.moneda
    FROM cuentasbancarias cb
    JOIN bancos b ON cb.idbanco = b.idbanco
    JOIN cuentascontables cc ON cc.idcuenta = cb.idcuentacontable
    JOIN tiposmoneda m ON m.idtipomoneda = cb.idtipomoneda;";
    $rsAccounts = $dbc->query($query);

    $query ="
    SELECT idbanco, descripcion FROM bancos
    ";
    $rsBanks = $dbc->query($query);
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
<link type="text/css" href="css/flick/jq.ui.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript" src="js/jq.ui.js"></script>
<script type="text/javascript">
$(function(){
    $("#add_account").click(function(){
        $(".hide").toggle();
        return false;
    });
});
</script>
<style type="text/css">
#m3 a{
    background: url(img/nav-left.png) no-repeat left;
}
#m3 span{
    background:  #99AB63 url(img/nav-right.png) no-repeat right;
}
#concepts, #docs{
    width:200px;
    height:100px;
}
#concepts{
    width:350px;
}
#add{
    font-weight:bold;
    font-size:14px;
}
.cforms{
    float:left;
}
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
<h1>Cuentas Bancarias</h1>
<?php if(!$rsAccounts->num_rows){ ?>
    <h2>No hay cuentas bancarias</h2>
<?php } else{ ?>
<ul>
    <?php while($row_rsAccounts = $rsAccounts->fetch_assoc()){ ?>
        <li>
            <h2><?php echo $row_rsAccounts["ctabancaria"] . " - " . $row_rsAccounts["banco"] ?></h2>
            <p>
                <strong>Cuenta Contable:</strong> <?php echo $row_rsAccounts["ncontable"] ?> <br />
                <strong>Codigo:</strong> <?php echo $row_rsAccounts["codigo"] ?> <br />
                <strong>Moneda:</strong> <?php echo $row_rsAccounts["moneda"] ?> <br />
            </p>
        </li>
    <?php } ?>
</ul>
<?php } ?>
<a href="#" id="add_account">A&ntilde;adir cuenta</a>
<div class="hide">
    <form method="post" class="cforms" action="<?php echo $base ?>administration/bankaccounts.php">
        <p>
            <label> <span>Nombre de la cuenta: </span><input type="text" name="account_name" /></label>
        </p>
        <p>
            <span>Moneda</span> 
            <label><input type="radio" name="currency" value="1" /> <span> Cordoba </span></label>
            <label><input type="radio" name="currency" value="2" /> <span> Dolar </span></label>
        </p>
        <p>
            <span>Banco</span>
            <select name="bank">
                <?php while($row_rsBank = $rsBanks->fetch_assoc()){ ?>
                    <option value="<?php echo $row_rsBank["idbanco"] ?>"><?php echo $row_rsBank["descripcion"] ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <input type="hidden" name="add" value="yes" />
            <input type="submit" value="Aceptar" />
        </p>
    </form>
</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>