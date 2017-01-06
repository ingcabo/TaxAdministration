<?php 
require ("comun/ini.php");
$cod_cambio=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_cambio)){

	$sql_col="SELECT * FROM vehiculo.tipo_cambio WHERE cod_cambio=".$cod_cambio;
	$rs = $conn->Execute($sql_col); 
	$descripcion=$rs->fields('descripcion');
	$status=$rs->fields('status');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Cambio: </td>
    <td>$cod_cambio</td>
  	</tr>";
}
?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="96">Tipos de Cambios:</td>
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

<input name="cod_cambio" type="hidden" value="<?=$cod_cambio?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>