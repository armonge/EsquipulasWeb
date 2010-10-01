<?php
/**
* @package administration
*/
require_once "../functions.php";
try{
    if(!$_SESSION["user"]->hasRole("root")){
        die("Usted no tiene permisos para administrar puntos de venta");
    }
    if(isset($_GET["del"])){
        $delid = (int)$_GET["del"];
        if($delid){
            $query="UPDATE cajas SET activo = 0 WHERE idcaja = $delid LIMIT 1";
            $dbc->query($query);
            if($dbc->affected_rows == 1){
                $status = "La caja se ha borrado con exito";
            }
        }
    }
    if(isset($_POST["add"])){
        $name = $dbc->real_escape_string(trim($_POST["posname"]));
        if($name){
            $query = "INSERT INTO cajas (descripcion) VALUES ('$name')";
            $dbc->query($query);
            $status = "<p>Se a&ntilde;adio una Caja</p>";
        }
    }

    $rsPos = $dbc->query("
            SELECT idcaja, descripcion
            FROM cajas
            WHERE activo = 1
    ")
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
		<h1>Cajas</h1>
		<?php echo $status ?>
		<ul>
			<?php while($row_rsPos = $rsPos->fetch_array(MYSQLI_ASSOC)){ ?>
				  <li>
					<?php echo $row_rsPos["descripcion"]?> <br />
					<a href="administration/pos.php?del=<?php echo $row_rsPos["idcaja"] ?>" onclick="return confirm('Desea realmente borrar este punto de caja?')" class="del" >Borrar</a>
				  </li>
			<?php } ?>
		</ul>
		<form action="administration/pos.php" method="post" class="cform">
			<p>
				<label>Nombre: <input type="text" name="posname" /></label>
			</p>
			
			<p>
				<input type="submit" value="Aceptar"/>
				<input type="hidden" value="yes" name="add" />
			</p>
			
		</form>
	</div>
</div>
<?php include "../footer.php" ?>
</div>
</body>
</html>
