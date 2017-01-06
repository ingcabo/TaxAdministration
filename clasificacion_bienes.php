<?
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$num = 20;
if (!$pagina) {
    $inicio = 0;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * $num;
} 
// Creando el objeto parroquias
$oClasifBienes = new clasificacion_bienes;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oClasifBienes->add($conn, $_REQUEST['grupo'], $_REQUEST['codigo'], $_REQUEST['descripcion']);
}elseif($accion == 'Actualizar'){
	$msj = $oClasifBienes->set($conn, $_REQUEST['id'], $_REQUEST['grupo'], $_REQUEST['codigo'], $_REQUEST['descripcion']);
}elseif($accion == 'del'){
	if($oClasifBienes->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$cClasificacion=$oClasifBienes->buscar($conn,'','',$num,$inicio,'codigo');
$total_C = clasificacion_bienes::total_registro_busqueda($conn,'','','codigo');
$total = $total_C;
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Clasificador de Bienes</span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Descripci&oacute;n:</td>
			<td>
				<input type="text" name="busca_desc" id="busca_desc" />
				<input type="hidden" name="hid_desc" id="hid_desc" />
			</td>
		</tr>
		<tr>
			<td>
				Estado:
			</td>
			<td>
				<?=helpers::superCombo($conn, "SELECT id, codigo||' - '||descripcion AS descripcion FROM puser.clasificador_bienes WHERE padre = '1' ORDER BY codigo",'', 'search_grupo','search_grupo','','')?>
				<? //echo helpers::superCombo($conn,'SELECT id AS id, descripcion AS descripcion FROM puser.estado','','search_estado','search_estado','width:200px','buscaMunicxEstado(this.value);');?>
			</td>
		</tr>
		
	</table>
</fieldset>
<br />
<br />
<div id="busqueda">
<? if(is_array($cClasificacion)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cClasificacion as $clasificacion) { 
?> 
<tr class="filas"> 
<td><?=$clasificacion->subgrupo?></td>
<td><?=$clasificacion->descripcion?></td>
<td><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$clasificacion->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / $num);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<a href="clasificacion_bienes.php?pagina=<?=$j?>"><?=$j?></a>
			
		<? }else {?>
			<a href="clasificacion_bienes.php?pagina=<?=$j?>">-<?=$j?></a>	
		<? }
	 }?>
	</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
</div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script language="javascript" type="text/javascript">
	function buscaCorrelativoGrupo(idg){
		var url = 'json.php';
		var pars = 'op=clasificador_bienes&id_grupo=' + idg +'&ms='+new Date().getTime();
			
		var myAjax = new Ajax.Request(
				url, 
				{
				method: 'get', 
				parameters: pars,
				onComplete: function(peticion){
					
					var jsonData = peticion.responseText;
					//alert(jsonData.toString);
					if (jsonData == undefined) { return }
					$('codigo').value = jsonData.toString();
				}
				}
			);
		
	}
	
	var t;
	
	function buscador(descripcion, pagina, keyCode)
	{
		if ((keyCode>=65 && keyCode<=90) || (keyCode>=48 && keyCode<=57) || (keyCode>=96 && keyCode<=105) || keyCode==8 || keyCode==46)
		{
			
			clearTimeout(t);
			$('hid_desc').value = descripcion;
			var grupo = $('search_grupo').value;
			//var municipio = $('search_municipio').value;
			t = setTimeout("busca('"+descripcion+"','"+grupo+"','"+pagina+"')", 800);
		}
	}
	
	function busca(descripcion, grupo, pagina)
	{
		
		var url = 'updater_busca_clasificacion_bienes.php';
		var pars = 'descripcion=' + descripcion + '&id_grupo=' + grupo + '&ms='+new Date().getTime()+ '&pagina='+pagina;
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

	Event.observe('busca_desc', "keyup", function (evt) 
	{	buscador($F('busca_desc'), '1', evt.keyCode);	});
	
	Event.observe('search_grupo', "change", function () 
	{	buscador($F('busca_desc'),'1',66 );	});
	
	/*Event.observe('search_municipio', "change", function () 
	{	buscador($F('busca_desc'),'1',66 );	});*/
	
	
</script>
<?
$validator->create_message("error_grupo", "grupo", "*");
$validator->create_message("error_codigo", "codigo", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
