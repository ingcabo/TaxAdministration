<?
require ("comun/ini.php");
// Creando el objeto grupos_proveedores
$oGruposProveedores = new grupos_proveedores;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oGruposProveedores->add($conn, $_REQUEST['id_nuevo'],
										$_REQUEST['organismos'],
										$_REQUEST['nombre'],  
										$_REQUEST['descripcion'],
										$_REQUEST['fecha']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oGruposProveedores->set($conn, $_REQUEST['id_nuevo'],
										$_REQUEST['id'], 
										$_REQUEST['organizaciones'],
										$_REQUEST['nombre'],  
										$_REQUEST['descripcion'],
										$_REQUEST['fecha']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oGruposProveedores->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cGruposProveedores=$oGruposProveedores->get_all($conn, $start_record,$page_size);
$pag=new paginator($oGruposProveedores->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Actualizaci&oacute;n de Requisitos por Grupos de Proveedor</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cGruposProveedores)){ ?>
<table id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cGruposProveedores as $gruposProveedores) { 
?> 
<tr class="filas"> 
<td><?=$gruposProveedores->id?></td>
<td><?=$gruposProveedores->descripcion?></td>
<td align="center">
<a href="#" onclick="updater('<?=$gruposProveedores->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
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
<table width="762" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><span class="paginator"><? $pag->print_page_counter()?></span></td>
		<td align="right"><span class="paginator"><? $pag->print_paginator("pulldown")?> </span></td>
	</tr>
</table>
<script type="text/javascript">
var i = 1
function addTR(){
	var x=$('tablita').insertRow($('tablita').rows.length)
	var y1=x.insertCell(0)
	var y2=x.insertCell(1)
	var y3=x.insertCell(2)
	var y4=x.insertCell(3)
	var y5=x.insertCell(4)
	var y6=x.insertCell(5)
	y1.innerHTML= "Categoria:"
	var cp = $('categorias_programaticas').cloneNode(true)
	y2.appendChild(cp)
	y3.innerHTML= "Partida Presupuestaria:"
	var pp = $('partidas_presupuestarias').cloneNode(true)
	y4.appendChild(pp)
	y5.innerHTML= "Monto:"
	var m = $('monto').cloneNode(false)
	m.nodeValue = 'aaa';
	y6.appendChild(m)
	i++
}
</script>
<? require ("comun/footer.php");?>
