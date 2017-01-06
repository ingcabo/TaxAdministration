<?php 

require ("comun/ini.php");
/*
include('adodb/adodb.inc.php');
$conn = &ADONewConnection('postgres');
$DBuser = "puser";
$DBpass = "123";
$DBname = "presupuestos";
$DBserver="172.31.0.19";
$conn->Connect($DBserver, $DBuser, $DBpass, $DBname); 
*/

$cod_col=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_col)){

	$sql_col="SELECT * FROM vehiculo.colores WHERE cod_col=".$cod_col;
	$rs = $conn->Execute($sql_col); 
	$descripcion=$rs->fields('descripcion');
	$status=$rs->fields('status');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Color: </td>
    <td>$cod_col</td>
  	</tr>";
	

}
?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="96">Descripci&oacute;n:</td>
    <td width="401"><input type="text" name="descripcion" value="<?=$descripcion?>" size="25"></td>
  </tr>
  <tr>
    <td>Activo:</td>
    <td><input type="checkbox" name="status" value="1" <?php if($status==1){ echo "checked"; }  ?>></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_col" type="hidden" value="<?=$cod_col?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>
