<?php #INCOMPLETO
require ("comun/ini.php");
$cod_tas=$_REQUEST['id'];
$boton="Guardar";
$mes=$_REQUEST['mes'];
$anio=$_REQUEST['anio'];
$monto=$_REQUEST['monto'];

if(!empty($cod_tas)){

	$sql_col="SELECT * FROM vehiculo.tasa_bancaria WHERE cod_tas=".$cod_tas;
	$rs = $conn->Execute($sql_col); 
	$mes=$rs->fields('mes');
	$anio=$rs->fields('anio');
	$monto=$rs->fields('monto');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Tasa: </td>
    <td>$cod_tas</td>
  	</tr>";
}

?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="96">Mes:</td>
    <td width="401"><input type="text" name="mes" value="<?=$mes?>" size="4" maxlength="2" onKeyPress="noAlpha(this)"></td>
  </tr>
  <tr>
    <td>A&ntilde;o:</td>
    <td><input type="text" name="anio" value="<?=$anio?>" size="5" maxlength="4" onKeyPress="noAlpha(this)"></td>
  </tr>
  <tr>
    <td>Tasa:</td>
    <td><input type="text" name="monto" value="<?=number_format($monto, 2, ',', '.')?>" onKeyPress=""></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_tas" type="hidden" value="<?=$cod_tas?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>