<?
include ("comun/ini.php");
$id_cedente = $_REQUEST['cc'];
$id_receptora = $_REQUEST['cr'];
$nrodoc = $_REQUEST['nrodocumento'];
$tdoc = $_REQUEST['tipodocumento'];
$fecha_desde = $_REQUEST['fecha_desde'];
$fecha_hasta = $_REQUEST['fecha_hasta'];
//die($nrocheque);
$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;

$cTransferencia = transferencia::buscar($conn, $id_cedente, $id_receptora, $fecha_desde, $fecha_hasta, $nrodoc, $tdoc, "id", $inicio, $tamano_pagina);
$total = transferencia::totalRegsBusqueda($conn, $id_cedente, $id_receptora, $fecha_desde, $fecha_hasta, $nrodoc, $tdoc);

if(is_array($cTransferencia))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td>Nro. Documento</td>
		<td>Cuenta Cedente:</td>
		<td>Cuenta Receptora:</td>
		<td>Estatus</td>
        <td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<? 
	foreach($cTransferencia as $op)
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$op->nrodoc?></td>
		<td ><?=$op->cedente?></td>
		<td ><?=$op->receptora?></td>
		<td ><?=($op->status==0 || empty($op->status))  ? 'Emitido' : 'Anulado'?></td>
        <td align="center"><?= ($op->status != '1') ? '<a href="transferencia.pdf.php?id='.$op->nrodoc.'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
		<td align="center"><a href="#" onclick="updater('<?=$op->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_bancos\'), $F(\'busca_nro_cuenta\'), $F(\'busca_proveedores\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'busca_nrodoc\'), $F(\'busca_nrocheque\'),'.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
		}
		?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong> </td>
	</tr>
</table>
<?
}
else
	echo "No hay registros en la bd";
?>


