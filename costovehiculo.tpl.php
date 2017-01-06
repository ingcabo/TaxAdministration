<?php #INCOMPLETO
require ("comun/ini.php");
$cod_esp=$_REQUEST['id'];
$boton="Guardar";


if(!empty($cod_esp)){

	$sql_col="SELECT * FROM vehiculo.esp_costo_veh WHERE cod_esp=".$cod_esp;
	$rs = $conn->Execute($sql_col);
	$cod_veh= $rs->fields('cod_veh');
	$mes=$rs->fields('mes');
	$anio=$rs->fields('anio');
	$monto=$rs->fields('monto');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo: </td>
    <td>$cod_esp</td>
  	</tr>";
}

?>

<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td>Tipo Veh&iacute;culo:</td>
    <td><?=helpers::combo($conn, 'tipo_veh_segun_gaceta', $cod_veh, '', 'cod_veh', 'cod_veh', '', '', 'SELECT cod_veh as id, descripcion FROM vehiculo.tipo_veh_segun_gaceta WHERE status=1')?></td>
  </tr>
  <tr>
    <td>A&ntilde;o:</td>
    <td><input type="text" name="anio" value="<?=$anio?>" size="5" maxlength="4" onKeyPress="noAlpha(this)"></td>
  </tr>
  <tr>
    <td>Monto:</td>
    <td><input type="text" name="monto" value="<?=number_format($monto, 2, ',', '.')?>" onKeyPress=""></td>
  </tr>
  <tr>
    <td>Fecha Desde:</td>
    <td>
	<input value="<?=muestrafecha($rs->fields['fecha_desde'])?>" size="12" name="desde" type="text" />

	</td>
  </tr>
  <tr>
    <td width="96">Fecha Hasta: </td>
    <td width="401"><input value="<?=muestrafecha($rs->fields['fecha_hasta'])?>" size="12" name="hasta" type="text" /></td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="cod_esp" type="hidden" value="<?=$cod_esp?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>