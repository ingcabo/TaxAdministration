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
$oParroquias = new parroquias;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oParroquias->add($conn, $_REQUEST['descripcion'], $_REQUEST['municipios']);
}elseif($accion == 'Actualizar'){
	$msj = $oParroquias->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['municipios']);
}elseif($accion == 'del'){
	if($oParroquias->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$cParroquias=$oParroquias->buscar($conn,'','',$num,$inicio,'descripcion');
$total_P = parroquias::total_registro_busqueda($conn,'','','descripcion');
$total = $total_P;
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Parroquias</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
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
				<? echo helpers::superCombo($conn,"SELECT m.id AS id, (e.descripcion||' - '||m.descripcion)::char(50) AS descripcion FROM puser.municipios m INNER JOIN puser.estado e ON (m.id_estado = e.id) ORDER BY descripcion",'','search_estado','search_estado','width:200px') ?>
				<? //echo helpers::superCombo($conn,'SELECT id AS id, descripcion AS descripcion FROM puser.estado','','search_estado','search_estado','width:200px','buscaMunicxEstado(this.value);');?>
			</td>
		</tr>
		
	</table>
</fieldset>
<div id="busqueda">
<? if(is_array($cParroquias)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cParroquias as $parroquias) { 
?> 
<tr class="filas"> 
<td><?=$parroquias->id?></td>
<td><?=$parroquias->descripcion?></td>
<td><a href="?accion=del&id=<?=$parroquias->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$parroquias->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / $num);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<a href="parroquias.php?pagina=<?=$j?>"><?=$j?></a>
			
		<? }else {?>
			<a href="parroquias.php?pagina=<?=$j?>">-<?=$j?></a>	
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
	function traeMunicipios(ide, idm){
		var url = 'updater_selects.php';
		var pars = 'combo=municipios&ide=' + ide +'&idm=' + idm + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divcombomunicipios', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargador_categorias')}, 
				onComplete:function(request){Element.hide('cargador_categorias')}
			});
		
	}
	
	var t;
	
	function buscador(descripcion, pagina, keyCode)
	{
		if ((keyCode>=65 && keyCode<=90) || (keyCode>=48 && keyCode<=57) || (keyCode>=96 && keyCode<=105) || keyCode==8 || keyCode==46)
		{
			
			clearTimeout(t);
			$('hid_desc').value = descripcion;
			var estado = $('search_estado').value;
			//var municipio = $('search_municipio').value;
			t = setTimeout("busca('"+descripcion+"','"+estado+"','"+pagina+"')", 800);
		}
	}
	
	function busca(descripcion, estado, pagina)
	{
		
		var url = 'updater_busca_parroquias.php';
		var pars = 'descripcion=' + descripcion + '&estado=' + estado + '&ms='+new Date().getTime()+ '&pagina='+pagina;
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
	
	Event.observe('search_estado', "change", function () 
	{	buscador($F('busca_desc'),'1',66 );	});
	
	/*Event.observe('search_municipio', "change", function () 
	{	buscador($F('busca_desc'),'1',66 );	});*/
	
	function buscaMunicxEstado(ide){
		var url = 'updater_selects.php';
		var pars = 'combo=municipios_buscador&ide=' + ide + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divmunicipios_buscador', 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){Element.show('cargador_categorias')}, 
				onComplete:function(request){Element.hide('cargador_categorias')}
			});
		
	}
	
</script>
<?
$validator->create_message("error_estado", "estado", "*");
$validator->create_message("error_municipios", "municipios", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
