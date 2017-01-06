<?
require ("comun/ini.php");
// Creando el objeto cargos
$oCargos = new cargos();
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oCargos->add($conn, $_POST['id_nuevo'], $_POST['descripcion']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oCargos->set($conn, $_POST['id_nuevo'], $_POST['id'], $_POST['descripcion']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oCargos->del($conn, $_POST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cCargos=$oCargos->get_all($conn);
require ("comun/header_popup.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />

<span class="titulo_maestro">Agregar Fianzas</span>
<div id="formulario">
<form name="form1" method="post">
<table>
<tr>
	<td>Tipos de fianza:</td>
	<td>
	<?=helpers::combo_ue_cp($conn, 
															'tipos_fianzas', 
															$objeto->id_tipo_fianza)?>
		<span class="errormsg" id="error_tf">*</span>
		<?=$validator->show("error_tf")?>
	</td>
	<td>Entidad:</td>
	<td>
	<?=helpers::combo_ue_cp($conn, 
															'entidades', 
															$objeto->id_tipo_fianza)?>
		<span class="errormsg" id="error_tf">*</span>
		<?=$validator->show("error_tf")?>
	</td>
</tr>
</table>
<input style="float:right" name="boton" type="button" value="<?=$boton?>" onclick="<?=$validator->validate() ?>" />
<input name="id" type="hidden" value="<?=$objeto->id?>" />
<input name="accion" type="hidden" value="<?=$boton?>" />
<div style="position:absolute; top:4px; right:5px; cursor:pointer;">
<img onclick="close_div();" src="images/close_div.gif" /></div>
</form>
<p class="errormsg">(*) Campo requerido</p>
</div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<?
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer_popup.php");
?>