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
$oTerritorios = new territorios;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oTerritorios->add($conn, $_REQUEST['descripcion'], $_REQUEST['parroquias']);
}elseif($accion == 'Actualizar'){
	$msj = $oTerritorios->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['parroquias']);
}elseif($accion == 'del'){
	if($oTerritorios->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$cTerritorios=$oTerritorios->buscar($conn, $num,$inicio);
$total_P = territorios::total_registro_busqueda($conn);
$total = $total_P;
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Territorios</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cTerritorios)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cTerritorios as $territorios) { 
?> 
<tr class="filas"> 
<td><?=$territorios->id?></td>
<td><?=$territorios->descripcion?></td>
<td><a href="?accion=del&id=<?=$territorios->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$territorios->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / $num);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<a href="$territorios.php?pagina=<?=$j?>"><?=$j?></a>
			
		<? }else {?>
			<a href="$territorios.php?pagina=<?=$j?>">-<?=$j?></a>	
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
		if($('parroquias')){ $('parroquias').length=1; }; 
	}
	
	function traeParroquias(idm, idp){
		var url = 'updater_selects.php';
		var pars = 'combo=parroquias&idm=' + idm +'&idp=' + idp + '&ms='+new Date().getTime();
			
		var updater = new Ajax.Updater('divcomboparroquia', 
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
	function traeTerritorios(idp, idt){
	}
</script>
<?
$validator->create_message("error_estado", "estado", "*");
$validator->create_message("error_municipios", "municipios", "*");
$validator->create_message("error_parroquias", "parroquias", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
