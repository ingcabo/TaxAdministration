<?
include ("comun/ini.php");
$id_proveedor = $_GET['id_proveedor'];
$id_ue = $_GET['id_ue'];
$observaciones = $_GET['observaciones'];
$nrodoc = $_GET['nrodoc'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];

$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;

$cOrden_compra = ordcompra::buscar($conn, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc, 
						$observaciones,'id', $inicio,$tamano_pagina);
						
$total = ordcompra::totalRegistroBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$observaciones);
?>
<? if(is_array($cOrden_compra)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td width="100px">Nº Documento </td>
<td>Observaciones</td>
<td>Status:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cOrden_compra as $ordcompra) {
?> 
<tr class="filas"> 
<td><?=$ordcompra->nrodoc?></td>
<td><?=$ordcompra->observaciones?></td>
<td><? if($ordcompra->status=='1') {
	echo "Registrada";
	} elseif($ordcompra->status=='2') {
		 echo "Aprobada";
		 } elseif($ordcompra->status=='3') {
		 	echo "Anulada";
			} else {
				echo "Recepcionada";
		} ?></td>
<td align="center"><?=!empty($ordcompra->nrodoc) ? '<a href="orden_compra.pdf.php?id='.$ordcompra->id.'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
<!--<td><a href="?accion=del&id=<?=$ordcompra->id?>&nrodoc=<?=$ordcompra->nrodoc?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>-->
<td align="center">
<a href="#" onclick="updater('<?=$ordcompra->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
	$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="7" align="center">
		<?
		for ($j=1; $j<=$total_paginas; $j++)
		{
			if ($j==$pagina)
				echo '<span class="actual">'.($j>1 ? ' - ':'').$j.'</span>';
			else
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_ue\'), $F(\'busca_proveedores\'), $F(\'busca_observaciones\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'busca_nrodoc\'), '.$j.')">'.($j>1 ? ' - ':'').$j.'</span>';
		}
		?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
