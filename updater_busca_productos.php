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

$tipoProd = $_GET['tipo_producto'];
$descripcion = $_GET['descripcion'];

$cProductos = productos::buscar($conn, $tipoProd, $descripcion, $inicio, $tamano_pagina, "descripcion");
$total = productos::total_registro_busqueda($conn, $tipoProd, $descripcion, "descripcion");

if(is_array($cProductos))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="20%">Id</td>
		<td width="65%">Descripcion</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cProductos as $producto) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$producto->id?></td>
		<td><?=$producto->descripcion?></td>
		<td align="center"><a href="" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater(<?=$producto->id?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
		if ($j==1)
		{
			if ($j==$pagina)
				echo "<span>".$j."</span>";
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'busca_tp\').value,$(\'busca_descripcion\').value, '.$j.');">'.$j.'</span>';
		}
		else
		{
			if ($j==$pagina)
				echo "<span>- ".$j."</span>";
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'busca_tp\').value,$(\'busca_descripcion\').value, '.$j.');">- '.$j.'</span>';
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
