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
$estado = $_GET['estado'];
//$municipio = $_GET['municipio'];
$oParroquias= new parroquias;
$cParroquias=$oParroquias->buscar($conn, $descripcion, $estado, $tamano_pagina,$inicio, "descripcion");
$total = $oParroquias->total_registro_busqueda($conn,$descripcion,$estado,'descripcion');

if(is_array($cParroquias))
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
	foreach($cParroquias as $parroquias) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$parroquias->id?></td>
		<td><?=$parroquias->descripcion?></td>
		<td align="center"><a href="parroquias.php?accion=del&id=<?=$parroquias->id?>" onClick="if (confirm('Si presiona Aceptar ser� eliminada esta informaci�n')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onClick="updater('<?=$parroquias->id?>'); return false;" title="Modificar � Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
