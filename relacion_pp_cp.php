<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto relacion_pp_cp

$oRelacion_pp_cp = new relacion_pp_cp;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oRelacion_pp_cp->add($conn,
								$_REQUEST['escenarios'],
								$_REQUEST['categorias_programaticas'], 
								$_REQUEST['partidas_presupuestarias'],
								$_REQUEST['asignaciones'],
								guardafloat($_REQUEST['presupuesto_original']),
								guardafloat($_REQUEST['aumentos']),
								guardafloat($_REQUEST['disminuciones']),
								guardafloat($_REQUEST['compromisos']),
								guardafloat($_REQUEST['causados']),
								guardafloat($_REQUEST['pagados']),
								guardafloat($_REQUEST['disponible']),
								$_REQUEST['aingresos'],
								$_REQUEST['agastos'],
								escenarios::get_ano($conn, $_REQUEST['escenarios']));
}elseif($accion == 'Actualizar'){
	$msj = $oRelacion_pp_cp->set($conn, 
								$_REQUEST['id'],
								$_REQUEST['escenarios'], 
								$_REQUEST['categorias_programaticas'], 
								$_REQUEST['partidas_presupuestarias'],
								$_REQUEST['asignaciones'],
								guardafloat($_REQUEST['presupuesto_original']),
								guardafloat($_REQUEST['aumentos']),
								guardafloat($_REQUEST['disminuciones']),
								guardafloat($_REQUEST['compromisos']),
								guardafloat($_REQUEST['causados']),
								guardafloat($_REQUEST['pagados']),
								guardafloat($_REQUEST['disponible']),
								$_REQUEST['aingresos'],
								$_REQUEST['agastos'],
								escenarios::get_ano($conn, $_REQUEST['escenarios']));
}elseif($accion == 'del'){
	$msj = $oRelacion_pp_cp->del($conn, $_REQUEST['id']);
}


require ("comun/header.php");
//$select->print_script();
?>
<? if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Partidas Presupuestarias por Categor&iacute;as Program&aacute;ticas</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="2">Escenario</td>
		</tr>
		
		<tr>
			<td colspan="2"><?=helpers::superCombo($conn, 'escenarios',0,'busca_escenarios','busca_escenarios')?></td>
		</tr>
		
		<tr>
			<td>C&oacute;digo de Categor&iacute;a</td>
			<td>Categoria Program&aacute;tica</td>
		</tr>
		
		<tr>
			<td><input type="text" name="busca_id_cp" id="busca_id_cp" maxlength="10" /></td>
			<td>
				<?=helpers::superCombo($conn, 
												"SELECT id, (id || ' - ' || descripcion)::varchar AS descripcion FROM categorias_programaticas WHERE id_escenario=$escEnEje ORDER BY id",
												0,
												'busca_cp',
												'busca_cp',
												'', 
												'', 
												'id', 
												'descripcion', 
												'', 
												'80', 
												'', 
												'Seleccione...')?>
			</td>
		</tr>
		
		<tr>
			<td>C&oacute;digo de Partida</td>
			<td>Partida Presupuestaria</td>
		</tr>
		<tr>
			<td><input type="text" name="busca_id_pp" id="busca_id_pp" maxlength="13" /></td>
			<td>
				<?=helpers::superCombo($conn, 
												"SELECT id, (id || ' - ' || descripcion)::varchar AS descripcion FROM partidas_presupuestarias WHERE id_escenario=$escEnEje AND madre is null ORDER BY id",
												0,
												'busca_pp',
												'busca_pp', 
												'', 
												'', 
												'id', 
												'descripcion', 
												'', 
												80, 
												'', 
												'Seleccione...')?>
			</td>
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
<script type="text/javascript">
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

function traeParPreDesdeUpdater(escenario){
	var url = 'updater_selects.php';
	var pars = 'combo=parprePorEsc&escenario=' + escenario;
	var updater = new Ajax.Updater('cont_partidas' , 
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
function busca(id_escenario, cod_cp, id_cp, cod_pp, id_pp, pagina){
	var url = 'updater_busca_pp_cp.php';
	var pars = '&id_escenario=' + id_escenario + '&cod_cp=' + cod_cp + '&id_cp=' + id_cp + '&cod_pp=' + cod_pp + '&id_pp=' + id_pp+'&ms='+new Date().getTime()+ '&pagina='+pagina;
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
	busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'1'); 
});
Event.observe('busca_id_cp', "keyup", function () { 
  if ($F('busca_id_cp').length == 10 || $F('busca_id_cp') == '')
	   busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'1'); 
});
Event.observe('busca_cp', "change", function () { 
	busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'1'); 
});
Event.observe('busca_id_pp', "keyup", function () { 
  if ($F('busca_id_cp').length == 13 || $F('busca_id_cp') == '')
	   busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'1'); 
	busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'1'); 
});
Event.observe('busca_pp', "change", function () { 
	busca($F('busca_escenarios'), $F('busca_id_cp'), $F('busca_cp'), $F('busca_id_pp'), $F('busca_pp'),'1'); 
});

	function sumaTotal(){
		var disponible = usaFloat($('presupuesto_original').value) + usaFloat($('aumentos').value) - usaFloat($('disminuciones').value);
		$('disponible').value = muestraFloat(disponible);
		
	}
</script>
<?
$validator->create_message("error_esc", "escenarios", "*");
$validator->create_message("error_catpro", "categorias_programaticas", "*");
$validator->create_message("error_parpre", "partidas_presupuestarias", "*");
$validator->create_message("error_ppto", "presupuesto_original", "*");
$validator->print_script();
require ("comun/footer.php");
?>
