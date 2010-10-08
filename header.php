<a href="./" id="logo">
	<img src="img/logo.jpg" width="280" height="75" alt="Inicio" />
</a>
<?php if(isset($_SESSION["user"]) ){ ?>
<p id="uname">
	Bienvenido <?php echo $_SESSION["user"]->uname ?> <br/>
	<a href="login.php?close=yes">Cerrar Sesión</a>
</p>
<?php } ?>
<ul id="menu">
	<li id="m1"><a href="./"><span>Inicio</span></a></li>
	<?php if(isset($_SESSION["user"])){ ?> 
	<li id="m2"><a href="reports/"><span>Reportes</span></a></li>
	<?php } 
	if(isset($_SESSION["user"]) &&  $_SESSION["user"]->hasRole("gerencia")){ ?>
	<li id="m5"><a href="clients/"><span>Clientes</span></a></li>
	<?php } 
	if(isset($_SESSION["user"]) && $_SESSION["user"]->hasRole("gerencia")){ ?>
	
	<li id="m3"><a href="administration/"><span>Administraci&oacute;n</span></a></li>
	
	<?php } ?>
	<li id="m4" class="help"><a href="help/"><span>Ayuda</span></a></li>
</ul>