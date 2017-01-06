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

$descripcion = $_GET['descripcion'];
$abreviacion = $_GET['abreviacion'];
$cUnidadesMedida = unidad_medida::buscar($conn, 
						$descripcion, 
						$abreviacion);
$total_tp = unidad_medida::total_registro_busqueda($conn, $descripcion, $abreviacion);
$total = $total_tp;
?>
<? if(is_array($cUnidadesMedida)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Abreviaci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cUnidadesMedida as $unidad_medida) { 
?> 
<tr class="filas"> 
<td><?=$unidad_medida->id?></td>
<td><?=$unidad_medida->descripcion?></td>
<td><?=$unidad_medida->abreviacion?></td>
<td><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$unidad_medida->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
			echo '<span style="cursor:pointer" onclick="busca($(\'search_descrip\').value,$(\'search_abrev\').value,'.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
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
