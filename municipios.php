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
// Creando el objeto municipios
$oMunicipios= new municipios;
$accion = $_REQUEST['accion'];
if($_REQUEST['alcaldia'])
	$alcaldia='T';
	else 
		$alcaldia='F';
if($accion == 'Guardar'){
	$msj = $oMunicipios->add($conn, $_REQUEST['descripcion'],$_REQUEST['estado'], $alcaldia);
}elseif($accion == 'Actualizar'){
	$msj = $oMunicipios->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['estado'], $alcaldia);
	
}elseif($accion == 'del'){
	if($oMunicipios->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cMunicipios=$oMunicipios->buscar($conn, '','', $num,$inicio);
$total_M = municipios::total_registro_busqueda($conn,'','');
$total = $total_M;
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Municipios</span>
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
				<? echo helpers::superCombo($conn,'SELECT id AS id, descripcion AS descripcion FROM puser.estado','','search_estado','search_estado','width:200px');?>
			</td>
		</tr>
	</table>
</fieldset>
<br />
<div id="busqueda">
<? if(is_array($cMunicipios)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cMunicipios as $municipios) { 
?> 
<tr class="filas"> 
<td><?=$municipios->id?></td>
<td><?=$municipios->descripcion?></td>
<td><a href="municipios.php?accion=del&id=<?=$municipios->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$municipios->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / $num);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<a href="municipios.php?pagina=<?=$j?>"><?=$j?></a>
			
		<? }else {?>
			<a href="municipios.php?pagina=<?=$j?>">-<?=$j?></a>	
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
var t;
	
	function buscador(descripcion, pagina, keyCode)
	{
		if ((keyCode>=65 && keyCode<=90) || (keyCode>=48 && keyCode<=57) || (keyCode>=96 && keyCode<=105) || keyCode==8 || keyCode==46)
		{
			clearTimeout(t);
			$('hid_desc').value = descripcion;
			var estado = $('search_estado').value;
			t = setTimeout("busca('"+descripcion+"','"+estado+"','"+pagina+"')", 800);
		}
	}
	
	function busca(descripcion, estado, pagina)
	{
		var url = 'updater_busca_municipios.php';
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
</script>

<?
$validator->create_message("error_estado", "estado", "* ");
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>