<?php 
require ("comun/ini.php");
$cod_ins=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_ins)){

	$sql="SELECT * FROM publicidad.inspector WHERE cod_ins=".$cod_ins;
	$rs = $conn->Execute($sql); 
	$nombre=$rs->fields('nombre');
	$apellido=$rs->fields('apellido');
	$cedula=$rs->fields('cedula');
	$status=$rs->fields('status');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Inspector: </td>
    <td>$cod_ins</td>
  	</tr>";
}

?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="96">C&eacute;dula:</td>
    <td width="401"><input type="text" name="cedula" value="<?=$cedula?>" size="12"></td>
  </tr>
  <tr>
    <td width="96">Nombre:</td>
    <td width="401"><input type="text" name="nombre" value="<?=$nombre?>" size="12"></td>
  </tr>
  <tr>
    <td width="96">Apellido:</td>
    <td width="401"><input type="text" name="apellido" value="<?=$apellido?>" size="12"></td>
  </tr>
  <tr>
    <td>Activo:</td>
    <td><input type="checkbox" name="status" value="1" <?php if($status==1){ echo "checked"; }  ?>></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_ins" type="hidden" value="<?=$cod_ins?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_div();" src="images/close_div.gif" /></span>
</form>