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

$id_rg = $_GET['nrorequi'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$motivo = $_GET['motivo'];
$cReq_Global = requisicion_global::buscar($conn, 
						$id_rg, 
						guardaFecha($fecha_desde),
						guardaFecha($fecha_hasta), 
						$motivo);
$total_rg = requisicion_global::total_registro_busqueda($conn, $id_rg, $fecha_desde, $fecha_hasta, $motivo);
$total = $total_rg;
?>
<? if(is_array($cReq_Global)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cReq_Global as $req_global) { 
?> 
<tr class="filas"> 
<td><?=$req_global->id?></td>
<td><?=$req_global->motivo?></td>
<td><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$req_global->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<?
}
$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="7" align="center">
	<?
	for ($j=1;$j<=$total_paginas;$j++)
	{
		if ($j==$pagina)
			echo '<span class="actual">'.($j>1 ? ' - ':'').$j.'</span>';
		else
			echo '<span style="cursor:pointer" onclick="busca($(\'nrorequi\').value,$(\'fecha_desde\').value,$(\'fecha_hasta\').value,$(\'search_motivo\')'.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
	}
	?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$_REQUEST['pagina']?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
