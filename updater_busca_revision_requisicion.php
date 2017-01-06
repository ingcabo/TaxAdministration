<?
include ("comun/ini.php");
$id_ue = $_GET['id_ue'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$nrorequi = $_GET['nrorequi'];
$oRequisicion = revision_requisicion::buscar($conn, 
						$id_ue, 
						guardaFecha($fecha_desde), 
						guardaFecha($fecha_hasta),
						$nrorequi,
						'id',
						$escEnEje
						);
?>
<? if(is_array($oRequisicion)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Unidad Ejecutora:</td>
<td>Status:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($oRequisicion as $requisicion) { 
?> 
<tr class="filas"> 
<td><?=$requisicion->id?></td>
<td><?=$requisicion->unidad_ejecutora?></td>
<td><? if ($requisicion->status == '02') {
	echo "Aprobada";
	} elseif ($requisicion->status == '01') {
		echo "Registrada";
		} elseif ($requisicion->status == '03'){
			echo "Anulada"; 
			} else {
				echo "Recibida por Compras";
				}?></td>
<td align="center"><?=!empty($requisicion->nrodoc) ? '<a href="contrato_obras.pdf.php?id='.$contratoObras->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$requisicion->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>