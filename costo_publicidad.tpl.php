<?php 
require ("comun/ini.php");
$cod_publicidad = $_REQUEST['id'];
$boton="Guardar";


if(!empty($cod_publicidad)){

	$sql="SELECT * FROM publicidad.tipo_publicidad WHERE cod_publicidad = '$cod_publicidad'";
	$r = $conn->Execute($sql);
	$cod_publicidad= $r->fields('cod_publicidad');
		
	$boton="Actualizar";
	
	$cod_publicidad="  <tr>
    <td>C&oacute;digo: </td>
    <td>$cod_publicidad</td>
  	</tr>";
}

?>

<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod_publicidad?>
  <tr>
    <td>Tipo Publicidad:</td>
    <td><select name="tipo_publicidad">
			<option>Seleccione . . .</option>
			<option value="Publicidad Fija">Publicidad Fija</option>
			<option value="Publicidad Eventual">Publicidad Eventual</option>
		</select></td>
  </tr>
  <tr>
    <td>Descripci&oacute;n:</td>
    <td><input type="text" name="descripcion" id="descripcion" value="<?=$objeto->descripcion?>" size="50">
		<span class="errormsg" id="error_descripcion">*</span>
		<?=$validator->show("error_descrpcion")?>	</td>
  </tr>
  <tr>
    <td>Monto:</td>
    <td><input type="text" name="monto" value="<?=$objeto->monto?>" onkeypress="return(formatoNumero(this, event));">
		<span class="errormsg" id="error_monto">*</span>
		<?=$validator->show("error_monto")?>	</td>
  </tr>
  <tr>
  	<td width="96">Status:</td>
	<td width="401"><input name="status" id="status" type="checkbox" value="1" /></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" onclick="<?=$validator->validate() ?>" /></td>
  </tr>
</table>
<input name="id" type="hidden" value="<?=$objeto->id_publicidad?>" />
<input name="accion" type="hidden" value="<?=$boton?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_div();" src="images/close_div.gif" /></span>
</form>