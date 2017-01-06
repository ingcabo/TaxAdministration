<?
set_time_limit(0);
require ("comun/ini.php");
// Creando el objeto escenarios
//usuarios::chequea_permiso('1', 'escenarios.php');
$oEscenario = new escenarios;
$del = $_REQUEST['del'];
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$oEscenario->add($conn, $_REQUEST['id_nuevo'],
						$_REQUEST['escenarios'], 
						$_REQUEST['descripcion'], 
						$_REQUEST['ano'], 
						$_REQUEST['detalle'], 
						guardafloat($_REQUEST['factor']), 
						$_REQUEST['formulacion']);
		
}elseif($accion == 'Actualizar'){
	$oEscenario->set($conn, $_REQUEST['id_nuevo'],
						$_REQUEST['id'],
						$_REQUEST['escenarios'], 
						$_REQUEST['descripcion'], 
						$_REQUEST['ano'], 
						$_REQUEST['detalle'], 
						guardafloat($_REQUEST['factor']), 
						$_REQUEST['formulacion']);
		
}elseif($accion == 'del'){
	$oEscenario->del($conn, $_REQUEST['id']);
		
}elseif($accion=='Aprobar'){
	//die('ano '.$_REQUEST['ano']);
	$oEscenario->aprobar($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['ano'], $_REQUEST['detalle'], $escEnEje, $anoCurso);

}
#LLENO LA VARIABLE CON EL MENSAJE DE LA OPERACION REALIZADA#
	$msj = $oEscenario->msj; 
	
$cEscenario=$oEscenario->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Escenarios</span>
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
			<td>Escenario Base</td>
			<td>A&ntilde;o</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td><input style="width:80px" type="text" name="busca_id" id="busca_id" /></td>
			<td><?=helpers::combo_ue_cp($conn, 'escenarios','','','id','busca_base','busca_base')?></td>
			<td><input type="text" name="busca_anio" id="busca_anio" /></td>
			<td><input type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
	</table>

</fieldset></div>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
var t;

function busca(id, id_base, anio, descripcion){
	var url = 'updater_busca_escenario.php';
	var pars = '&id='+ id + '&id_base=' + id_base + '&anio=' + anio + '&descripcion=' + descripcion;
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

//Event.observe(window, "keypress", function (e) { if(e.keyCode == Event.KEY_RETURN){ validate(); } });

Event.observe('busca_id', "keyup", function () {
  if ($F('busca_id').length == 4 || $F('busca_id') == '')  
	   busca($F('busca_id'), $F('busca_base'), $F('busca_anio'), $F('busca_descripcion')); 
});
Event.observe('busca_base', "change", function () { 
	busca($F('busca_id'), $F('busca_base'), $F('busca_anio'), $F('busca_descripcion')); 
});
Event.observe('busca_anio', "keyup", function () {
  if ($F('busca_anio').length == 4 || $F('busca_anio') == '')  
	   busca($F('busca_id'), $F('busca_base'), $F('busca_anio'), $F('busca_descripcion')); 
});
Event.observe('busca_descripcion', "keyup", function () {
  clearTimeout(t); 
	t = setTimeout("busca($F('busca_id'), $F('busca_base'), $F('busca_anio'), $F('busca_descripcion'))", 1500); 
});
</script>
<?
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_ano", "ano", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->create_message("error_factor", "factor", "*");
$validator->print_script();
require ("comun/footer.php");
?>
