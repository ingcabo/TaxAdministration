<?
include ("comun/ini.php");
$id_proveedor = $_GET['id_proveedor'];
$id_ue = $_GET['id_ue'];
$nrodoc = $_GET['nroordcompra'];
$id_requisicion = $_GET['nrorequi'];
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

$cRecepcionOC = recepcion_orden_compra::buscar($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$id_requisicion,'id',$inicio,$tamano_pagina);

$total = recepcion_orden_compra::totalRegsBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$id_requisicion); 
?>
<? if(is_array($cRecepcionOC)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Observaciones</td>
<td>Aprobado:</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cRecepcionOC as $recepoc) {
?> 
<tr class="filas"> 
<td><?=$recepoc->id?></td>
<td><?=$recepoc->observaciones?></td>
<td><?=!empty($recepoc->nrodoc) ? "Si" : "No" ?></td>
<td align="center">
<a href="#" onClick="updater('<?=$recepoc->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_ue\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'nrorequi\'),'.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
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