<?
require ("comun/ini.php");

// Creando el objeto tipo_producto
$today=date("Y-m-d");
$otipo_producto = new tipo_producto;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($otipo_producto->add($conn, $_REQUEST['tipo_familia'], $_REQUEST['descripcion'],  $_REQUEST['observacion'], $today, $_REQUEST['partidas_presupuestarias'], $_REQUEST['grupos_proveedores'], $_REQUEST['codigo2'])){
		$msj = REG_ADD_OK;
	} else {
		$msj = ERROR;
	}	
}elseif($accion == 'Actualizar'){
	if($otipo_producto->set($conn, $_REQUEST['id'], $_REQUEST['tipo_familia'], $_REQUEST['descripcion'],  $_REQUEST['observacion'], $today, $_REQUEST['partidas_presupuestarias'], $_REQUEST['grupos_proveedores'], $_REQUEST['codigo2'])){
		$msj = REG_SET_OK;
	} else {
		$msj = ERROR;
	}
}elseif($accion == 'del'){
	if($otipo_producto->del($conn, $_REQUEST['id'])){
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
<span class="titulo_maestro">Maestro de Tipo Producto</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Partida Presupuestaria:</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_pp','','width:400px','','busca_pp','busca_pp','',
			"SELECT id,id||' - '||descripcion AS descripcion FROM puser.partidas_presupuestarias WHERE id_escenario = '$escEnEje' AND id LIKE ('402%') OR id LIKE ('404%') ORDER BY id",'','','150')?></td>
		</tr>
		<tr>
			<td>Grupos de Proveedores:</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_grupo_prov','','','','busca_grupo_prov','busca_grupo_prov','',
			"SELECT id, nombre AS descripcion FROM puser.grupos_proveedores ORDER BY nombre")?></td>
		</tr>
		<tr>
			<td colspan="3">Observaciones</td>
		</tr>
		<tr>
			<td><input style="width:300px" type="text" name="busca_descrip" id="busca_descrip" /></td>
		</tr>
		<tr>
			<td colspan="3">Codigo</td>
		</tr>
		<tr>
			<td><input style="width:300px" type="text" name="busca_codigo" id="busca_codigo" /></td>
		</tr>
		<tr>
			<td colspan="3">Familia de Productos</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::superCombo($conn, "SELECT id as id, (codigo || ' ' || descripcion) as descripcion FROM puser.tipo_familia", '', 'busca_familia','busca_familia','','','id' ,'descripcion' ,'id')?></td>
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
	/*var cadena = '';	
	cadena = $('tipo_familia').options[document.getElementById('tipo_familia').value].text;
	alert(cadena);
	cadena.replace(/^\s+/, ""); //esto simula un trimleft
	$('codigo').value = cadena.substr(0,4);*/
	var url = 'json.php';
		var pars = 'op=tipo_producto&id='+ $('tipo_familia').value;
		var Request = new Ajax.Request(
			url,
			{
				
				method: 'get',
				parameters: pars,
				onLoading:function(request){}, 
				onComplete:function(request){
					//alert(request.responseText);
					var jsonData = eval('(' + request.responseText + ')');
					//alert(JsonData);
					$('codigo').value = jsonData[0];
					$('codigo2').value = jsonData[1];
				}
			}
		);
	
}


function busca(id_pp, id_grupo_prov, descripcion,codigo,familia, pagina){
	var url = 'updater_busca_tipo_producto.php';
	var pars = '&id_pp=' + id_pp + '&grupo_prov=' + id_grupo_prov + '&descripcion=' + descripcion + '&ms='+new Date().getTime()+ '&pagina='+pagina+'&codigo='+codigo+'&idFamilia='+familia;
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

Event.observe('busca_pp', "change", function () { 
	   busca($F('busca_pp'), $F('busca_grupo_prov'), $F('busca_descrip'),$F('busca_codigo'), $F('busca_familia'),'1'); 
});
Event.observe('busca_grupo_prov', "change", function () { 
	busca($F('busca_pp'), $F('busca_grupo_prov'), $F('busca_descrip'),$F('busca_codigo'), $F('busca_familia'),'1');  
});

Event.observe('busca_descrip', "keyup", function (event) { 
	if(event.keyCode == 13)
	busca($F('busca_pp'), $F('busca_grupo_prov'), $F('busca_descrip'),$F('busca_codigo'), $F('busca_familia'),'1'); 
});

Event.observe('busca_familia', "change", function () { 
	   busca($F('busca_pp'), $F('busca_grupo_prov'), $F('busca_descrip'), $F('busca_codigo'), $F('busca_familia'),'1'); 
});

Event.observe('busca_codigo', "keyup", function (event) { 
	if(event.keyCode == 13)
	busca($F('busca_pp'), $F('busca_grupo_prov'), $F('busca_descrip'),$F('busca_codigo'), $F('busca_familia'),'1'); 
});

</script>
<? 
$validator->create_message("error_descrip", "descripcion", "*");
$validator->create_message("error_tipo_familia", "tipo_familia", "*");
$validator->create_message("error_partidas_presupuestarias", "partidas_presupuestarias", "*");
$validator->create_message("error_grupos_proveedores", "grupos_proveedores", "*");
$validator->print_script();
require ("comun/footer.php");
?>

