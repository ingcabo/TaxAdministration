<?
include ("comun/ini.php");
$id_proveedor = $_REQUEST['id_proveedor'];
$id_banco = $_REQUEST['id_banco'];
$nrodoc = $_REQUEST['nrodoc'];
$fecha_desde = $_REQUEST['fecha_desde'];
$fecha_hasta = $_REQUEST['fecha_hasta'];
$cuenta = $_REQUEST['id_cuenta'];

$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;

$cOtrosPagos = otros_pagos_anteriores::buscar($conn,$id_proveedor,$id_banco,$fecha_desde,$fecha_hasta,$nrodoc,$cuenta,'id',$inicio,$tamano_pagina);

$total = otros_pagos_anteriores::totalRegsBusqueda($conn, $id_proveedor, $id_banco, $fecha_desde, $fecha_hasta, $nrodoc, $cuenta); 
?>
<? if(is_array($cOtrosPagos)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Nro. Documento</td>
<td>Proveedor:</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cOtrosPagos as $op) { 
?> 
<tr class="filas"> 
<td><?=$op->nrodoc?></td>
<td><?=$op->proveedor?></td>
<td ><?=($op->status==0 || empty($op->status))  ? 'Emitido' : 'Anulado'?></td>
<td align="center"><?=$op->status==0 ? '<a href="otros_pagos.pdf.php?id='.$op->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$op->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_bancos\'), $F(\'busca_nro_cuenta\'), $F(\'busca_proveedores\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'busca_nrodoc\'), '.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
		}
		?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong> </td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>


