<?php #tipo vehículo según gaceta
require ("comun/ini.php");
$cod_veh=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_veh)){

	$sql_col="SELECT * FROM vehiculo.tipo_veh_segun_gaceta WHERE cod_veh=".$cod_veh;
	$rs = $conn->Execute($sql_col); 
	$descripcion=$rs->fields('descripcion');
	$status=$rs->fields('status');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Tipo Veh&iacute;culo: </td>
    <td>$cod_veh</td>
  	</tr>";
}

?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="96">Descripci&oacute;n:</td>
    <td width="401"><input type="text" name="descripcion" value="<?=$descripcion?>" size="60"></td>
  </tr>
  <tr>
    <td>Activo:</td>
    <td><input type="checkbox" name="status" value="1" <?php if($status==1){ echo "checked"; }  ?>></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_veh" type="hidden" value="<?=$cod_veh?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>