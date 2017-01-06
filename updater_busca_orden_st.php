<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 20;
if (!$pagina) 
{
    $inicio = 10;
    $pagina=1;
}
else 
{
    $inicio = ($pagina - 1) * $tamano_pagina;
}
$id_tipo_documento = $_GET['id_tipdoc'];
$id_proveedor = $_GET['id_proveedor'];
$id_ue = $_GET['id_ue'];
$nrodoc = $_GET['nrodoc'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$observaciones = $_GET['observaciones'];
$cOrdenServicioTrabajo = orden_servicio_trabajo::buscar($conn, 
						$id_tipo_documento, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc, 
						$observaciones,
						$tamano_pagina, $inicio);
$total = orden_servicio_trabajo::total_buscar($conn, 
						$id_tipo_documento, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc, 
						$observaciones);
?>
<? if(is_array($cOrdenServicioTrabajo)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Numero de Documento</td>
<td>Orden:</td>
<td>Observaciones:</td>
<td>Status:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cOrdenServicioTrabajo as $ordenServicioTrabajo) { 
?> 
<tr class="filas"> 
<td><?=$ordenServicioTrabajo->nrodoc?></td>
<td><?=$ordenServicioTrabajo->id_tipo_documento == '002' ? "Servicio" : "Trabajo" ?></td>
<td><?=$ordenServicioTrabajo->observaciones?></td>
<td><? if($ordenServicioTrabajo->status=='1') {
		echo "Registrada";
		} elseif ($ordenServicioTrabajo->status=='2') {
			echo "Aprobada";
			} else {
				echo "Anulada";
				} ?></td>
<td align="center"><?=!empty($ordenServicioTrabajo->nrodoc) ? '<a href="orden_servicio_trabajo.pdf.php?id='.$ordenServicioTrabajo->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$ordenServicioTrabajo->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>

<? $total_paginas = ceil($total / $tamano_pagina);?>
<tr class="filas">
		<td colspan="7" align="center">
<? for ($j=1;$j<=$total_paginas;$j++){
		 if ($j==1){ ?>
			<span style="cursor:pointer" onclick="busca($F('busca_ue'), $F('busca_tipdoc'),$F('busca_proveedores'), $F('busca_observaciones'), $F('busca_fecha_desde'),	$F('busca_fecha_hasta'), $F('busca_nrodoc'),'<?=$j?>');"><?=$j?></span>
		<? }else {?>	
		<span style="cursor:pointer" onclick="busca($F('busca_ue'), $F('busca_tipdoc'),$F('busca_proveedores'), $F('busca_observaciones'), $F('busca_fecha_desde'),	$F('busca_fecha_hasta'), $F('busca_nrodoc'),'<?=$j?>');">- <?=$j?></span>
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