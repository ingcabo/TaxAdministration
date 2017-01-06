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
$id_grupo = $_GET['id_grupo'];
//$municipio = $_GET['municipio'];
$oClasificacion= new clasificacion_bienes;
$cClasificacion=$oClasificacion->buscar($conn, $descripcion, $id_grupo, $tamano_pagina,$inicio, "codigo");
$total = $oClasificacion->total_registro_busqueda($conn,$descripcion,$id_grupo,'codigo');

if(is_array($cClasificacion))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="10%">C&oacute;digo</td>
		<td>Descripci&oacute;n</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cClasificacion as $clasificacion) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$clasificacion->subgrupo?></td>
		<td><?=$clasificacion->descripcion?></td>
		<td align="center"><a href="clasificacion_bienes.php?accion=del&id=<?=$clasificacion->id?>" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onClick="updater('<?=$clasificacion->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
<?
	}

	$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="4" align="center">
	<?
	for ($j=1;$j<=$total_paginas;$j++)
	{
		if ($j==$pagina)
		{
	?>
			<span class="actual"><?=(($j==1) ? '':' - ').$j?></span>
		<?
		}
		else
		{
		?>
			<a href="#" onClick="busca($('hid_desc').value,$('search_municipio').value,$('search_estado').value,'<?=$j?>'); return false;"><?=(($j==1) ? '':' - ').$j?></a>
	<?
		}
	}
	?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$_REQUEST['pagina']?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<?
}
else 
{
	echo "No hay registros en la bd";
}
?>
