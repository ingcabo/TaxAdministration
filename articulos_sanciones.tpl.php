<?php 
require ("comun/ini.php");
$cod_articulo=$_REQUEST['id'];
$boton="Guardar";

if(!empty($cod_articulo)){

	$sql="SELECT * FROM publicidad.articulos_sanciones WHERE cod_articulo=".$cod_articulo;
	$rs = $conn->Execute($sql); 
	$descripcion=$rs->fields('descripcion');
	$monto=$rs->fields('monto');
	$status=$rs->fields('status');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Art&iacute;culo: </td>
    <td>$cod_articulo</td>
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
    <td width="96">Monto:</td>
    <td width="401"><input align="right" type="text" name="monto" value="<?=muestrafloat($monto)?>" size="16" onkeypress="return(formatoNumero(this, event));"></td>
  </tr>
  <tr>
    <td>Activo:</td>
    <td><input type="checkbox" name="status" value="1" <?php if($status==1){ echo "checked"; }  ?>></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_articulo" type="hidden" value="<?=$cod_articulo?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_div();" src="images/close_div.gif" /></span>
</form>