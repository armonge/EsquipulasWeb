<?php
require_once "functions.php";
if(isset($_GET["close"])){
	session_destroy();
}
if(isset($_POST["loginUsername"])){
	 $loginUsername = $_POST["loginUsername"] ;
	 $loginPassword = $_POST["loginPassword"];
	 $user = new UserFromPasswd($_POST["loginUsername"], $_POST["loginPassword"],$dbc);
	 $_SESSION["user"]=$user;

	 if($_POST["js"] == "yes"){
		 if($user->isValid()){
		    $result["success"] = true;
		 }else{
		    $result["success"] = false;
		    $result["errors"]["reason"] = "Autenticación fallida intente de nuevo.";
		 }
		echo json_encode($result);
		die();
	 }else{
	 	if($user->isValid()){
	 		header("Location: index.php");
	 		die();
	 	}else{
	 		$error = "Autenticaci&oacute;n fallida";
	 	}
	 }
}
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<base href="<?php echo $basedir ?>" />
	<link rel="shortcut icon" href="<?php echo $basedir ?>favicon.ico" />
	<title>Autenticaci&oacute;n</title>
	<meta http-equiv="Content-Type"	content="application/xhtml+xml; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	<script type="text/javascript" src="js/jq.js"></script>
	<script type="text/javascript">
	$(function(){

		$("form").submit(function(){
			$.ajaxSetup({
				type: "POST",
				url:"login.php",
				dataType:"json",


			});
			$.post(
				"login.php",{
					loginUsername:$("#name").val(),
					loginPassword:$("#passwd").val(),
					js:"yes"
				},function(data){
					if(!data.success){
						alert(data.errors.reason)
					}else{
						window.location = "<?php echo $basedir ?>";
					}
				},
				"json"
				);
			return false;
		});
	});
	</script>
	</head>
	<body>
		<form action="login.php" method="post" class="cforms">
			<p>
				<label><span>Usuario</span><input id="name" type="text" name="loginUsername" /></label>
			</p>
			<p>
				<label><span>Contrase&ntilde;a</span><input id="passwd" type="password" name="loginPassword" /></label>
			</p>
			<p>
				<input type="submit" value="Aceptar" />
			</p>
		</form>
	</body>
</html>