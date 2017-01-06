<?
require ("lib/config.php");
$msj="";
$validator=new validator("form1");
$validator->create_message("id_login","login","(*) Campo requerido");
$validator->create_message("id_pwd","pwd","(*) Campo requerido");
// CEPV.070706.SN VALIDACION DE EMPRESA "RRHH"
$q = "SELECT * FROM rrhh.parametrossistema";
//die($q);
$r = $conn->Execute($q);
if(!$r->EOF){
	$MultiEmpresa=$r->fields['multi_empresa'];
	$EmpresaPred=$r->fields['emp_pred'];
}
//var_dump($_GET);
// CEPV.070706.EN 
if ($_GET['ac']) {
	$usuario=new usuarios;
	$txtlogin=$_REQUEST['login'];
	$txtpwd=$_REQUEST['pwd'];
	// CEPV.070706.SN VALIDACION DE EMPRESA "RRHH"
	$txtempresa=$_REQUEST['Empresa'];
	if($MultiEmpresa==1){
		$EmpresaEnv=$txtempresa;
	}else{
		$EmpresaEnv=-1;
		$_SESSION['EmpresaL']= ($EmpresaPred) ? $EmpresaPred : -1;
	}
	// CEPV.070706.EN 
	if ($usuario->access_login($conn, $txtlogin, $txtpwd,$MultiEmpresa,$EmpresaEnv)) {
		header("Location: frames.php");
	}else {
		$space ="<br>";
		if($MultiEmpresa==1)
			$msj="Nombre de usuario y/o contrase&ntilde;a y/o empresa incorrectos";
		else
			$msj="Nombre de usuario y/o contrase&ntilde;a incorrectos";
	}
}
if ($_GET['st']) {
	$msj = "Usuario Inactivo";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SCG3</title>
<head><link rel="shortcut icon" href="images/favicon.ico" ></head>
<link href="css/estilos.css" type="text/css" rel="stylesheet">
<script src="js/prototype.js" type="text/javascript"></script>
<script src="js/scriptaculous.js" type="text/javascript"></script>
<? $validator->print_script(); ?>
</head>
<body >
<div id="contenedor">

	<div id="cabecera"><!-- <img src="images/banner.jpg" width="880" />--></div>
	<div id="cuerpo">
		<div id="principal">	
<br /><br />
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>

<br /><br /><br />

      <form action="?ac=lg" method="POST" name="form1" >
        <div id="msj_login">Por favor introduzca su nombre de usuario y contrase&ntilde;a:</div>
        <table id="login" border="0" cellpadding="1" cellspacing="0" align="center">
          <tr>
            <td>Login:&nbsp;</td>
            <td><input name="login" id="login" type="text" size="23" maxlength="23" class="textbox"/>
              &nbsp;
              <?=$validator->show("id_login");?></td>
          </tr>
          <tr>
            <td><div align="right" class="logintext">Password:&nbsp;</div></td>
            <td ><input type="password" name="pwd" size="23" class="textbox"/>
              &nbsp;
              <?=$validator->show("id_pwd");?></td>
          </tr>
		  <? if($MultiEmpresa==1){ // CEPV.070706.SN VALIDACION DE EMPRESA "RRHH" 	 ?>
          <tr>
            <td>Empresa:&nbsp;</td>
            <td ><?=helpers::combonomina($conn, 'rrhh.empresa',$EmpresaPred,'','int_cod','Empresa','int_cod','emp_nom','Empresa','','','');?></td>
          </tr>
		  <? } // CEPV.070706.EN ?>
          <tr>
            <td></td>
            <td><input name="enviar" type="button" class="boton_login" style="cursor:pointer" value="Login" onclick="<?=$validator->validate()?>"/></td>
          </tr>
        </table>
        <div align="center">
          <br>
          <?= $space ?>
        </div>
      </form>

<br /><br /><br />


		</div>
	</div>
	<div id="pie">2006</div>
</div>

<script type="text/javascript">
<?=(!empty($msj)) ? "Effect.Appear('msj',{duration:1.2});\n" : ""?>
Event.observe(window, "keypress", function (e) { if(e.keyCode == Event.KEY_RETURN){ validate(); } });
Event.observe(window, "load", function () { document.form1.login.focus(); });
</script>

</body>
</html>
