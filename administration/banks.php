<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
    if(!$_SESSION["user"]->hasRole("root")){
        die("Usted no tiene permisos para administrar Bancos");
    }
    if(isset($_POST["add"])){
        $bname = $dbc->real_escape_string(trim($_POST["bname"]));
        if($bname){
            $query = "INSERT INTO bancos (descripcion) VALUES ('$bname')";
            $dbc->query($query);
            $status = "<p>Se a&ntilde;adio un banco</p>";
        }
    }

    $rsBanks = $dbc->query("
    SELECT b.idbanco, b.descripcion
    FROM bancos b
    ");
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
<script type="text/javascript" src="js/jq.js"></script>
<script type="text/javascript">
$(function(){
	$("#add").click(function(e){
		$(".addbank").toggle()
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
</style>

</head>
<body>
<div id="wrap">
<?php include "../header.php"?>
<div id="content">
	<div id="left-column">
		<h1>Bancos</h1>
		<?php echo $status ?>
		<ul>
			<?php while($row_rsBank = $rsBanks->fetch_array(MYSQLI_ASSOC)){ ?>
				  <li><?php echo $row_rsBank["descripcion"]?>
				  <?php
				  $rsAccounts = $dbc->query("
				  SELECT cb.idcuentabancaria, cb.ctabancaria, m.moneda
				  FROM cuentasbancarias
				  JOIN tiposmoneda m ON m.idtipomoneda = cb.idtipomoneda
				  ");
				  if($rsAccounts->num_rows){ ?>
				  	<ul>
				  	<?php while($row_rsAccount = $rsAccounts->fetch_array(MYSQLI_ASSOC)){ ?>
				  		<li>
				  			<?php echo $row_rsAccount["ctabancaria"]?>
				  		</li>
				  	<?php } ?>
				  	</ul>
				  	<?php }?>
				  </li>
			<?php } ?>
		</ul>
		<a href="#" id="add">A&ntilde;adir Banco</a>
		<div class="hide addbank">
			<form class="cform" method="post" action="administration/banks.php">
				<p>
					<label><span>Nombre del Banco:</span> <input type="text" name="bname" /></label>
				</p>
				<p>
					<input type="submit" value="Aceptar" />
					<input type="hidden" name="add" value="yes" />
				</p>
			</form>
		</div>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>