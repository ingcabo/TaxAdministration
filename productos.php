<?
require ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
if (!$pagina) {
    $inicio = 0;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * 20;
} 
// Creando el objeto productos
$today=date("Y-m-d");
$oproductos = new productos;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oproductos->add($conn, $_REQUEST['descripcion'], $_REQUEST['tipo_producto'], $_REQUEST['unidad_medida'], $_REQUEST['rop'],$_REQUEST['roq'],
						 guardaFloat($_REQUEST['ctd_minimo']), guardaFloat($_REQUEST['ctd_maximo']), $_REQUEST['ubic_fisica'], guardaFloat($_REQUEST['ctd_actual']), $_REQUEST['activo_inactivo_producto'], guardaFloat($_REQUEST['costo_std']),
						 guardaFloat($_REQUEST['costo_prm']), guardaFloat($_REQUEST['ultimo_costo']),$today,$_REQUEST['desc_completa'], $_REQUEST['grupo']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oproductos->set($conn, $_REQUEST['id'],$_REQUEST['descripcion'], $_REQUEST['tipo_producto'], $_REQUEST['unidad_medida'], $_REQUEST['rop'],$_REQUEST['roq'],
						 guardaFloat($_REQUEST['ctd_minimo']), guardaFloat($_REQUEST['ctd_maximo']), $_REQUEST['ubic_fisica'], guardaFloat($_REQUEST['ctd_actual']), $_REQUEST['activo_inactivo_producto'], guardaFloat($_REQUEST['costo_std']),
						 guardaFloat($_REQUEST['costo_prm']), guardaFloat($_REQUEST['ultimo_costo']),$today,$_REQUEST['desc_completa'], $_REQUEST['grupo']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oproductos->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador

$cproductos=$oproductos->buscar($conn,'','', 20,$inicio);
$total_P = productos::total_registro_busqueda($conn,'','');
$total = $total_P;
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Productos</span><br />
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td>Tipo De Producto:</td>
			<td width="130"><?=helpers::superCombo($conn,"SELECT id, descripcion FROM puser.tipo_producto ORDER BY descripcion",'','busca_tp','busca_tp','width:250px','buscador()','id','descripcion','',70)?></td>
			<td>Descripcion:</td>
			<td><input type="text" name="busca_descripcion" id="busca_descripcion" onkeyup="buscador()" style="width:200px" /></td>
		</tr>
	</table>
</fieldset>
<br />
<div id="busqueda">
<? if(is_array($cproductos)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cproductos as $productos) { 
?> 
<tr class="filas"> 
<td><?=$productos->id?></td>
<td><?=$productos->descripcion?></td>
<td><a href="?accion=del&id=<?=$productos->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$productos->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
<? $total_paginas = ceil($total / 20);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<a href="productos.php?pagina=<?=$j?>"><?=$j?></a>
			
		<? }else {?>
			<a href="productos.php?pagina=<?=$j?>">-<?=$j?></a>	
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
<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<script language="javascript" type="text/javascript">
var t;

	function buscador()
{
	clearTimeout(t);
	t = setTimeout("busca('"+$('busca_tp').value+"', '"+$('busca_descripcion').value+"',1)", 800);
}

	function busca(tipoProd, descripcion, pagina)
{
	var url  = 'updater_busca_productos.php';
	var pars = 'tipo_producto='+tipoProd+'&descripcion='+descripcion+'&pagina='+pagina+'&ms='+new Date().getTime();
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
</script>

<?php
$validator->create_message("error_nombre", "descripcion", "*");
$validator->create_message("error_tipo", "tipo_producto", "*");
$validator->create_message("error_unid_medi", "unidad_medida", "*");
$validator->create_message("error_minimo", "ctd_minimo", "*");
$validator->create_message("error_maximo", "ctd_maximo", "*");
$validator->create_message("error_ubicacion", "ubic_fisica", "*");
$validator->create_message("error_ctd_actual", "ctd_actual", "*");
$validator->create_message("error_act_ina", "activo_inactivo_producto", "*");
$validator->print_script();
 require ("comun/footer.php"); ?>
