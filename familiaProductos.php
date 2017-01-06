<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
//include("adodb/adodb-exceptions.inc.php");

// Creando el objeto tipo_producto
$today=date("Y-m-d");
$familiaProducto = new familiaProductos;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$codigo = $_REQUEST['codigo'].'-'.$_REQUEST['codigo2'];
	$msj = $familiaProducto->add($conn, $_REQUEST['tipo_producto_clasif'], $_REQUEST['descripcion'],  $codigo);
	
}elseif($accion == 'Actualizar'){
	$codigo = $_REQUEST['codigo'].'-'.$_REQUEST['codigo2'];
	$msj = $familiaProducto->set($conn, $_REQUEST['id'], $_REQUEST['tipo_producto_clasif'], $_REQUEST['descripcion'], $codigo);

}elseif($accion == 'del'){
	if($familiaProducto->del($conn, $_REQUEST['id'])){
		$msj = REG_DEL_OK;
	} else {
		$msj = ERROR;
	}
}
//Seccion paginador

//$ctipo_producto=$otipo_producto->buscar($conn, $id_pp, $grupo_prov, $observacion, 20, $inicio);
//$total_TP = tipo_producto::total_registro_busqueda($conn);
//$total = $total_TP;

require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br /><br>
<span class="titulo_maestro">Maestro de Familia de Productos</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>
			<?=helpers::superCombo($conn, 'tipo_producto_clasif','', 'tipo_producto_clasif','tipo_producto_clasif','','','id' ,'descripcion' ,'id')?></td>
		</tr>
		<tr>
			<td colspan="3">Descripci&oacute;n:</td>
		</tr>
		<tr>
			<td><input style="width:300px" type="text" name="busca_descrip" id="busca_descrip" /></td>
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
function validaCodigo(valor){
	var letra = '';	
	letra = $('tipo_producto_clasif').options[document.getElementById('tipo_producto_clasif').value].text;
	letra.replace(/^\s+/, ""); //esto simula un trimleft
	$('codigo').value = letra.substr(0,1);
}

function busca(id_familia, descripcion, pagina){
	var url = 'updater_busca_familiaProducto.php';
	var pars = '&id_familia=' + id_familia + '&descripcion=' + descripcion + '&ms='+new Date().getTime()+ '&pagina='+pagina;
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

Event.observe('tipo_producto_clasif', "change", function () { 
	   busca($F('tipo_producto_clasif'), $F('busca_descrip'),'1'); 
});

Event.observe('busca_descrip', "keyup", function (event) { 
	if(event.keyCode == 13)
	busca($F('tipo_producto_clasif'), $F('busca_descrip'),'1'); 
});

</script>
<? 
$validator->create_message("error_descrip", "descripcion", "*");
$validator->create_message("error_tipo_producto_clasif", "tipo_producto_clasif", "*");
$validator->print_script();
require ("comun/footer.php");
?>

