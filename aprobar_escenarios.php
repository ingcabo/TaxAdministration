<?
require ("comun/ini.php");
// Creando el objeto escenarios
$oEscenario = new escenarios;
$accion = $_REQUEST['accion'];
if($accion == 'Aprobar Escenario'){
	if($oEscenario->set($conn, 
						$_REQUEST['id'],
						$_REQUEST['escenarios'], 
						$_REQUEST['descripcion'], 
						$_REQUEST['ano'], 
						$_REQUEST['detalle'], 
						$_REQUEST['factor'], 
						$_REQUEST['formulacion'],
						'true'))
		$msj = ESC_APR;
	else
		$msj = ERROR;
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cEscenario=$oEscenario->get_all_sin_aprobar($conn, $start_record,$page_size);
$pag=new paginator($oEscenario->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Escenarios</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cEscenario)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Escenario Base</td>
<td>A&ntilde;o</td>
<td>Descripci&oacute;n</td>
<td>Aprobado</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cEscenario as $escenario) { 
?> 
<tr class="filas"> 
<td><?=$escenario->id?></td>
<td><?=$escenario->base?></td>
<td><?=$escenario->ano?></td>
<td><?=$escenario->descripcion?></td>
<td>No</td>
<td align="center">
<a href="#" onclick="updater(<?=$escenario->id?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<? require ("comun/footer.php"); ?>