<?php #INCOMPLETO
require ("comun/ini.php");
$cod_des=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_des)){

	$sql_col="SELECT * FROM vehiculo.descuento WHERE cod_des=".$cod_des;
	$rs = $conn->Execute($sql_col); 
	$dias=$rs->fields('dias');
	$porcentaje=$rs->fields('porcentaje');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Descuento: </td>
    <td>$cod_des</td>
  	</tr>";
}

?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="96">D&iacute;as:</td>
    <td width="401"><input type="text" name="dias" value="<?=$dias?>" size="4"></td>
  </tr>
  <tr>
    <td>Porcentaje:</td>
    <td><input type="text" name="porcentaje" value="<?=$porcentaje?>" size="5" maxlength="3">%</td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_des" type="hidden" value="<?=$cod_des?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>