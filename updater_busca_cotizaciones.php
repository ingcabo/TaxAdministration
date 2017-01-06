<?
include ("comun/ini.php");
$id_ue = $_GET['id_ue'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$nrorequi = $_GET['nrequi'];
$estado = $_GET['estado'];
$oCotizaciones = actualiza_cotizacion::buscar($conn, 
						$fecha_desde, 
						$fecha_hasta,
						$nrorequi,
						'id',
						$escEnEje,
						$estado
						);
?>
<? if(is_array($oCotizaciones)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<!--<td>Unidad Ejecutora:</td>-->
<td>Status:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($oCotizaciones as $cotizaciones) { 
?> 
<tr class="filas"> 
<td><?=$cotizaciones->id?></td>
<!--<td><?=$cotizaciones->unidad_ejecutora?></td>-->
<td><?=$cotizaciones->nom_status?></td>
<td align="center"><? //if($cotizaciones->status=='07') echo '<a href="reporte_analisis_cotizacion.pdf.php?id_requisicion='.$cotizaciones->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>'; ?></td>
<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$cotizaciones->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>