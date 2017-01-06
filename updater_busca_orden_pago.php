<?
include ("comun/ini.php");
$id_proveedor = $_GET['id_proveedor'];
$id_ue = $_GET['id_ue'];
$nrodoc = $_GET['nrodoc'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$descripcion = $_GET['descripcion'];
$status = $_GET['status'];

$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];

if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;

$cOrdenPago = orden_pago::buscar($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion , $status, "nrodoc", $inicio, $tamano_pagina);
$total = orden_pago::totalRegsBusqueda($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, $status);

if(is_array($cOrdenPago))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td>Nro. Documento</td>
		<td>Proveedor:</td>
		<td>Status:</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cOrdenPago as $op) 
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$op->nrodoc?></td>
		<td><?=$op->proveedor?></td>
		<? if($op->status == 1)
			$estado = "Registrado";
			elseif($op->status == 2)
				$estado = "Aprobado";
				else
					$estado = "Anulado"; ?>
		<td><?=$estado?></td>
		<td align="center"><?= ($op->status != '1') ? '<a href="orden_pago.pdf.php?id='.$op->nrodoc.'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
		<td align="center"><a href="#" onclick="updater('<?=$op->nrodoc?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
	<?
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
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_ue\'), $F(\'busca_proveedores\'), $F(\'busca_descripcion\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'busca_nrodoc\'), $F(\'busca_status\'), '.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
		}
		?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<?
}
else 
	echo "No hay registros en la bd";
?>