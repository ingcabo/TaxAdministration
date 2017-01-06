<?
require ("comun/ini.php");
// Creando el objeto tipos_documentos
$oTiposDocumentos = new tipos_documentos;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oTiposDocumentos->add($conn, $_REQUEST['id_nuevo'],
									$_REQUEST['momentos_presupuestarios'], 
									$_REQUEST['abreviacion'], 
									$_REQUEST['descripcion'], 
									$_REQUEST['observacion'], 
									$_REQUEST['colocar_op']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oTiposDocumentos->set($conn, $_REQUEST['id_nuevo'],
									$_REQUEST['id'],
									$_REQUEST['momentos_presupuestarios'], 
									$_REQUEST['abreviacion'], 
									$_REQUEST['descripcion'], 
									$_REQUEST['observacion'], 
									$_REQUEST['colocar_op']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oTiposDocumentos->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cTiposDocumentos=$oTiposDocumentos->get_all($conn, $start_record,$page_size);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Tipos de Documentos</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>C&oacute;digo</td>
			<td>Momento Presupuestario</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td><input style="width:80px" type="text" name="busca_id" id="busca_id" /></td>
			<td><?=helpers::combo_ue_cp($conn, 'momentos_presupuestarios','','','','busca_mp','busca_mp')?></td>
			<td><input type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script>
function busca(id, id_mp, descripcion){
	var url = 'updater_busca_tipdoc.php';
	var pars = '&id='+ id + '&id_mp=' + id_mp + '&descripcion=' + descripcion;
	var updater = new Ajax.Updater('busqueda', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
} 

Event.observe('busca_id', "keyup", function () { 
	busca($F('busca_id'), $F('busca_mp'), $F('busca_descripcion')); 
});
Event.observe('busca_mp', "change", function () { 
	busca($F('busca_id'), $F('busca_mp'), $F('busca_descripcion')); 
});
Event.observe('busca_descripcion', "keyup", function () { 
	busca($F('busca_id'), $F('busca_mp'), $F('busca_descripcion')); 
});
</script>
<?
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_mp", "momentos_presupuestarios", "*");
$validator->create_message("error_abrv", "abreviacion", "*");
$validator->create_message("error_op", "colocar_op", "*", 2);
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
