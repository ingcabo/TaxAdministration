<?
require ("comun/ini.php");

// Creando el objeto tipo_producto
$today=date("Y-m-d");
$oUnidadMedida = new unidad_medida;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oUnidadMedida->add($conn, $_REQUEST['descripcion'],  $_REQUEST['abreviacion'])){
		$msj = REG_ADD_OK;
	} else {
		$msj = ERROR;
	}	
}elseif($accion == 'Actualizar'){
	if($oUnidadMedida->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['abreviacion'])){
		$msj = REG_SET_OK;
	} else {
		$msj = ERROR;
	}
}elseif($accion == 'del'){
	if($oUnidadMedida->del($conn, $_REQUEST['id'])){
		$msj = REG_DEL_OK;
	} else {
		$msj = ERROR;
	}
}


require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br /><br>
<span class="titulo_maestro">Maestro de Unidades de Medida</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Descripcion:</td>
		</tr>
		<tr>
			<td colspan="3"><input type="text" name="search_descrip" id="search_descrip"  /></td>
		</tr>
		<tr>
			<td>Abreviacion:</td>
		</tr>
		<tr>
			<td>
			<input type="text" name="search_abrev" id="search_abrev"  /></td>
		</tr>
	</table>
</fieldset>
</div>
<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->

<script language="javascript" type="text/javascript">
function busca(descripcion, abreviacion, pagina){
	var url = 'updater_busca_unidad_medida.php';
	var pars = 'descripcion=' + descripcion + '&abreviacion=' + abreviacion +'&ms='+new Date().getTime()+ '&pagina='+pagina;
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

Event.observe('search_descrip', "keyup", function (event) { 
	   if(event.keyCode == 13)
	   busca($F('search_descrip'), $F('search_abrev'),'1'); 
});
Event.observe('search_abrev', "keyup", function (event) { 
	  if(event.keyCode == 13)
	  busca($F('search_descrip'), $F('search_abrev'),'1');  
});


</script>
<? 
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->create_message("error_abreviacion", "abreviacion", "*");

$validator->print_script();
require ("comun/footer.php");
?>

