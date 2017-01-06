<?php 
require ("comun/ini.php");
$cod_tip=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_tip)){

	$sql="SELECT * FROM vehiculo.tipo WHERE cod_tip=".$cod_tip;
	$rs = $conn->Execute($sql); 
	$descripcion=$rs->fields('descripcion');
	$status=$rs->fields('status');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Tipo: </td>
    <td>$cod_tip</td>
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

<input name="cod_tip" type="hidden" value="<?=$cod_tip?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>
