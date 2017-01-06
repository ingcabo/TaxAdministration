<? require ("comun/ini.php");
#Creando el objeto Iva Retenciones#
$oIva_retenciones = new iva_retenciones;
$accion = $_REQUEST['accion'];

#SECCION DE GUARDAR#
if($accion == 'Guardar' and !empty($_REQUEST['idpartida'])){
	if($oIva_retenciones->add($conn, $_REQUEST['idpartida'], $_REQUEST['iva'],$_REQUEST['retencion'], $_REQUEST['anio']))
		$msj = REG_ADD_OK;
	else
		$msj = AGREGADA_PARAMETRIZACION_PARTIDA;

#SECCION DE ACTULIZAR#
}elseif($accion == 'Actualizar' and !empty($_REQUEST['idpartida'])){
	if($oIva_retenciones->set($conn, $_REQUEST['id'], $_REQUEST['idpartida'], $_REQUEST['iva'], $_REQUEST['retencion'], $_REQUEST['anio']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;

#SECCION DE ELIMINAR#
}elseif($accion == 'del'){
	if($oIva_retenciones->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

require ("comun/header.php");
?>	
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Iva y Retenciones </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

	<table cellpadding="0" border="0" style="width:710px" cellspacing="0" align="center">
		<tr>
			<td><legend>Buscar:</legend></td>
		</tr>
		<tr>
			<td colspan="3">Seleccione Partida Presupuestaria</td>
		</tr>	
		<tr>
			<? $pp = new partidas_presupuestarias;
				$arr = $pp->get_all($conn);
			?>
			<td colspan="3"><?=helpers::superComboObj($arr, '', 'idpartidabus', 'idpartidabus', 'width:400px', '', 'id', 'descripcion', false, 57)?></td>
		</tr>
	</table>

<br />
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>


<script>
/* Metodos utilizados en el buscador */
function busca(id){
	var url = 'updater_iva_retenciones.php';
	var pars = '&id_partida=' + id;
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

Event.observe('idpartidabus', "change", function () { 
	busca($F('idpartidabus')); 
});
</script>
<?
$validator->create_message("error_partida", "idpartida", "*");
$validator->create_message("error_iva", "iva", "*");
$validator->create_message("error_retencion", "retencion", "*");
$validator->create_message("error_anio", "anio", "*");


$validator->print_script();
?>
<? require ("comun/footer.php"); ?>