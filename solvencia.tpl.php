<?php 
require ("comun/ini.php");
$id=$_REQUEST['id'];
$boton="Guardar";

if(!empty($id)){

	$sql_col="SELECT * FROM vehiculo.solvencia WHERE id=".$id;
	$rs = $conn->Execute($sql_col); 
	$anio=$rs->fields('anio');
	$fecha_desde=$rs->fields('fecha_desde');
	$fecha_hasta=$rs->fields('fecha_hasta');
	$monto_normal=$rs->fields('monto_normal');
	$monto_habilitado=$rs->fields('monto_habilitado');
	
	$boton="Actualizar";
	
	$cod="  <tr>
    <td>C&oacute;digo Solvencia: </td>
    <td>$id</td>
  	</tr>";
}

?>
<form name="form1" action="" method="post">
<table width="507" border="0">
<?=$cod?>
  <tr>
    <td width="130">A&ntilde;o:</td>
    <td width="367"><input type="text" name="anio" value="<?=$anio?>" size="5" maxlength="4"></td>
  </tr>
  <tr>
    <td>Fecha Desde :</td>
    <td>
		<input value="<?=muestrafecha($fecha_desde)?>" size="12" name="fecha_desde" type="text" />
		<a href="#" id="boton1" onclick="return false;"><img border="0" src="images/calendarA.png" width="20" height="20" /></a>  
	  <script type="text/javascript">
				new Zapatec.Calendar.setup({
					firstDay          : 1,
					weekNumbers       : true,
					showOthers        : false,
					showsTime         : false,
					timeFormat        : "24",
					step              : 2,
					range             : [1900.01, 2999.12],
					electric          : false,
					singleClick       : true,
					inputField        : "fecha_desde",
					button            : "boton1",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
				</script></td>
  </tr>
  <tr>
    <td>Fecha Hasta: </td>
    <td><input value="<?=muestrafecha($fecha_hasta)?>" size="12" name="fecha_hasta" type="text" />
	<a href="#" id="boton2" onclick="return false;"><img border="0" src="images/calendarA.png" width="20" height="20" /></a>  
				<script type="text/javascript">
				new Zapatec.Calendar.setup({
					firstDay          : 1,
					weekNumbers       : true,
					showOthers        : false,
					showsTime         : false,
					timeFormat        : "24",
					step              : 2,
					range             : [1900.01, 2999.12],
					electric          : false,
					singleClick       : true,
					inputField        : "fecha_hasta",
					button            : "boton2",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
				</script></td>
  </tr>
  <tr>
    <td>Monto Normal: </td>
    <td><input type="text" name="monto_normal" value="<?=number_format($monto_normal, 2, ',', '.')?>"></td>
  </tr>
  <tr>
    <td>Monto Habilitado:</td>
    <td><input type="text" name="monto_habilitado" value="<?=number_format($monto_habilitado, 2, ',', '.')?>"><strong></strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="accion" type="submit" value="<?=$boton?>" /></td>
  </tr>
</table>

<input name="id" type="hidden" value="<?=$id?>" />
<span style="position:absolute;top:4px;right:5px;cursor:pointer;">
<img onclick="close_dv();" src="images/close_div.gif" /></span>
</form>