<?
require ("comun/ini.php");
require ("comun/header.php");
if($_SESSION['EmpresaL']==-1){
	?><script>alert("El Modulo RRHH no funcionara correctamente, no se ha sido selecionada ninguna Empresa");</script> <?
}
?>
<div style="margin-left:auto; margin-right:auto; padding-left:20px">
<br />
<h1>Bienvenido al SCG3</h1>
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
</div>

<? require ("comun/footer.php"); ?>