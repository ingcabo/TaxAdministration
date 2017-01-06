<?
include ("comun/ini.php");

$pagina = $_REQUEST['pagina'];
$inicio = ($pagina - 1) * REGISTROS_PAGINA;

$fecha_desde = $_REQUEST['fecha_desde'];
$fecha_hasta = $_REQUEST['fecha_hasta'];
$tipdoc = $_REQUEST['tipdoc'];
$tipmov = $_REQUEST['tipmov'];
$nrodoc = $_REQUEST['nrodoc'];
$descripcion = $_REQUEST['descripcion'];
$cMovimientosPresupuestarios=movimientos_presupuestarios::buscar($conn, 
	$fecha_desde, 
	$fecha_hasta, 
	$tipdoc, 
	$tipmov, 
	$nrodoc,
	$descripcion,
	REGISTROS_PAGINA,
	$inicio);
$total = movimientos_presupuestarios::total_registro_busqueda($conn, 
	$fecha_desde, 
	$fecha_hasta, 
	$tipdoc, 
	$tipmov, 
	$nrodoc,
	$descripcion);
if(is_array($cMovimientosPresupuestarios)){
?>
<table id="grid" cellpadding="0" cellspacing="1" >
	<tr class="cabecera"> 
		<td>N&ordm; Documento</td>
		<td>N&ordm; Referencia</td>
		<td>Tipo de Documento</td>
		<td>Fecha</td>
		<td>Monto</td>
		<td>Descripci&oacute;n</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<? 
$i = 0;
foreach($cMovimientosPresupuestarios as $movimientosPresupuestarios) { 
?> 
	<tr class="filas"> 
		<td><?=$movimientosPresupuestarios->nrodoc?></td>
		<td><?=$movimientosPresupuestarios->nroref?></td>
		<td><?=$movimientosPresupuestarios->tipo_documento?></td>
		<td><?=muestrafecha($movimientosPresupuestarios->fecha)?></td>
		<td align="center"><?=muestrafloat($movimientosPresupuestarios->get_suma_monto($conn, $movimientosPresupuestarios->nrodoc))?></td>
		<td><?=$movimientosPresupuestarios->descripcion?></td>
		<td>
			<a href="movimientos_presupuestarios.php?accion=del&id=<?=$movimientosPresupuestarios->nrodoc?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
		</td>
		<td align="center">
			<a  onclick="updater('<?=$movimientosPresupuestarios->nrodoc?>&id_momento=<?=$movimientosPresupuestarios->status?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0" style="cursor:pointer"></a>
		</td>
	</tr>
<? $i++;
	}
$total_paginas = ceil($total / REGISTROS_PAGINA);?>
<tr class="filas">
		<td colspan="8" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onClick="updater_consulta($('consulta_fecha_desde').value, 
																						$('consulta_fecha_hasta').value, 
																						$('consulta_tipos_documentos').value, 
																						$('consulta_momentos_presupuestarios').value, 
																						$('consulta_nrodoc').value, 
																						$('consulta_descripcion').value, 
																						'<?=$j?>');"><?=$j?></span>
		<? }else {?>	
			<span style="cursor:pointer" onClick="updater_consulta($('consulta_fecha_desde').value, 
																						$('consulta_fecha_hasta').value, 
																						$('consulta_tipos_documentos').value, 
																						$('consulta_momentos_presupuestarios').value, 
																						$('consulta_nrodoc').value, 
																						$('consulta_descripcion').value, 
																						'<?=$j?>');">- <?=$j?></span>
		<? }
	 }?>
	</td>
	</tr>
	<tr class="filas">
		<td colspan="8" align="center"> Pagina <strong><?=$_REQUEST['pagina']?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
