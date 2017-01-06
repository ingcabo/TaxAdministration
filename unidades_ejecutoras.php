<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto unidades_ejecutoras
$oUE = new unidades_ejecutoras;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oUE->add($conn, 
							$_REQUEST['id_nuevo'], 
							$_REQUEST['escenario'], 
							$_REQUEST['descripcion'], 
							$_REQUEST['responsable']);
}elseif($accion == 'Actualizar'){
	$msj = $oUE->set($conn, 
							$_REQUEST['id_nuevo'],
							$_REQUEST['id'],
							$_REQUEST['escenario'], 
							$_REQUEST['id_escenario'],
							$_REQUEST['descripcion'], 
							$_REQUEST['responsable']);
}elseif($accion == 'del'){
	$msj = $oUE->del($conn, $_REQUEST['id'], $_REQUEST['id_escenario']);
}
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Unidades Ejecutoras</span>
<div id="formulario">
<a href="#" onclick="updater(0)">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>C&oacute;digo</td>
			<td>Escenario</td>
			<td>Descripci&oacute;n</td>
			<td>Responsable</td>
		</tr>
		<tr>
			<td><input style="width:80px" type="text" name="busca_id" id="busca_id" maxlength="2" /></td>
			<td><?=helpers::combo_ue_cp($conn, 'escenarios','','','id','busca_escenarios','busca_escenarios')?></td>
			<td><input type="text" name="busca_descripcion" id="busca_descripcion" /></td>
			<td><input type="text" name="busca_responsable" id="busca_responsable" /></td>
<!--			<td><input type="button"  value="Buscar" id="busca_boton" /></td> -->
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
var t;

function busca(id, id_escenario, descripcion, responsable, pagina){
	var url = 'updater_busca_ue.php';
	var pars = '&id='+ id + '&id_escenario=' + id_escenario + '&descripcion=' + descripcion + '&responsable=' + responsable + '&pagina=' + pagina;
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

Event.observe(window, "keypress", function (e) { if(e.keyCode == Event.KEY_RETURN){ validate(); } });

Event.observe('busca_id', "keyup", function () {
  if ($F('busca_id').length == 2 || $F('busca_id') == '')  
	   busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), $F('busca_responsable'), '1'); 
});
Event.observe('busca_escenarios', "change", function () {
  busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), $F('busca_responsable'), '1'); 
});
Event.observe('busca_descripcion', "keyup", function () { 
  clearTimeout(t);
	t = setTimeout("busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), $F('busca_responsable'), '1')", 1500); 
});
Event.observe('busca_responsable', "keyup", function () { 
  clearTimeout(t);
	t = setTimeout("busca($F('busca_id'), $F('busca_escenarios'), $F('busca_descripcion'), $F('busca_responsable'), '1')", 1500); 
});

</script>
<?
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_esc", "escenario", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->create_message("error_resp", "responsable", "*");
$validator->print_script();
require ("comun/footer.php");
?>
