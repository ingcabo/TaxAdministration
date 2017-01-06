<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 10;

if (!$pagina) 
{
	$inicio = 0;
	$pagina=1;
}
else 
{
	$inicio = ($pagina - 1) * $tamano_pagina;
} 
$nombre = $_GET['nombre'];

$cRequisitos = requisitos::buscar($conn, $nombre, $tamano_pagina, $inicio, "nombre");
$total = requisitos::total_registro_busqueda($conn, $nombre);

if(is_array($cRequisitos) && count($cRequisitos)>0)
{
?>
<table id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="15%">C&oacute;digo</td>
		<td>Nombre</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<? 
	foreach($cRequisitos as $requisitos) 
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$requisitos->id?></td>
		<td><?=$requisitos->nombre?></td>
		<td align="center"><a href="?accion=del&id=<?=$requisitos->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$requisitos->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
			echo '<span style="cursor:pointer" onclick="busca($(\'hidden_nombre\').value, '.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
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
