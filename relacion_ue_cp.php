<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto relacion_ue_cp
$oRelacion_ue_cp = new relacion_ue_cp;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oRelacion_ue_cp->add($conn, 
									$_REQUEST['escenarios'], 
									$_REQUEST['categorias_programaticas'], 
									$_REQUEST['unidades_ejecutoras'],
									$_REQUEST['descripcion']);
}elseif($accion == 'Actualizar'){
	$msj = $oRelacion_ue_cp->set($conn, 
									$_REQUEST['id'],
									$_REQUEST['escenarios'], 
									$_REQUEST['categorias_programaticas'], 
									$_REQUEST['unidades_ejecutoras'],
									$_REQUEST['descripcion']);
}elseif($accion == 'del'){
	$msj = $oRelacion_ue_cp->del($conn, $_REQUEST['id']);
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cRelacion_ue_cp=$oRelacion_ue_cp->get_all($conn, $escEnEje, $start_record,$page_size);
$pag=new paginator($oRelacion_ue_cp->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Categor&iacute;as Program&aacute;ticas por Unidades Ejecutoras</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Unidad Ejecutora</td>
			<td>Escenario</td>
		</tr>
		<tr>
			<td><?=helpers::superCombo($conn, "SELECT id, (id || ' - ' || descripcion)::varchar AS descripcion FROM unidades_ejecutoras WHERE id_escenario=$escEnEje ORDER BY id",0,
																'busca_ue','busca_ue', '', '', 'id', 'descripcion',
																'', '', '', 'Seleccione...', true)?></td>
			<td><?=helpers::superCombo($conn, 'escenarios',0, 'busca_escenarios', 'busca_escenarios')?></td>
		</tr>
	</table>
	<table>
		<tr>
		  <td>C&oacute;digo</td>
			<td>Categoria Program&aacute;tica</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
      <td><input style="width:80px;" type="text" name="busca_id_cp" id="busca_id_cp" maxlength="10" /></td>
		  <td><?=helpers::superCombo($conn, "SELECT id, (id || ' - ' || descripcion)::varchar AS descripcion FROM categorias_programaticas WHERE id_escenario=$escEnEje",0,
													'busca_cp','busca_cp', '', '', 'id', 'descripcion', '', '50', '',
													'Seleccione...', false)?></td>
			<td><input style="width:300px" type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"><div>

<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<script type="text/javascript">
var t;

function traeUnidadesDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=unidades&escenario=' + escenario;
	var updater = new Ajax.Updater('cont_unidades' , 
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
function traeCategoriasDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=catproPorEsc&escenario=' + escenario;
	var updater = new Ajax.Updater('cont_categorias', 
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
function busca(id_escenario, id_ue, cod_cp, id_cp, descripcion, pagina){
	var url = 'updater_busca_ue_cp.php';
	var pars = '&id_escenario=' + id_escenario + '&id_ue=' + id_ue + '&cod_cp=' + cod_cp + '&id_cp=' + id_cp + '&descripcion=' + descripcion + '&pagina=' + pagina + '&ms='+new Date().getTime();
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

Event.observe('busca_escenarios', "change", function () { 
	busca($F('busca_escenarios'), $F('busca_ue'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_descripcion'),'1'); 
});
Event.observe('busca_ue', "change", function () { 
	busca($F('busca_escenarios'), $F('busca_ue'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_descripcion'),'1'); 
});
Event.observe('busca_id_cp', "keyup", function () {
    if($F('busca_id_cp').length == 10 || $F('busca_id_cp') == '') 
      {busca($F('busca_escenarios'), $F('busca_ue'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_descripcion'),'1');} 
});
Event.observe('busca_cp', "change", function () { 
	busca($F('busca_escenarios'), $F('busca_ue'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_descripcion'),'1'); 
});
Event.observe('busca_descripcion', "keyup", function () {
  clearTimeout(t);
  t = setTimeout("busca($F('busca_escenarios'), $F('busca_ue'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_descripcion'), '1')", 1500); 
});
</script>
<?
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_ue", "unidades_ejecutoras", "*");
$validator->create_message("error_cp", "categorias_programaticas", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
