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

$rif = $_GET['rif'];
$nombre = $_GET['nombre'];

$cProveedores = proveedores::buscarProveedoresContrato($conn, '', '', $rif, $nombre, $inicio, $tamano_pagina, "nombre");
$total = proveedores::totalRegistroContrato($conn, '', '', $rif, $nombre);

if(is_array($cProveedores))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="20%">RIF</td>
		<td width="65%">Nombre</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cProveedores as $proveedor) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$proveedor->rif?></td>
		<td><?=$proveedor->nombre?></td>
		<td align="center"><?='<a href="imprimir_ficha.pdf.php?id_proveedores='.$proveedor->id.'" target="_blank" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>'?></td>
		<td align="center"><a href="?accion=del&id=<?=$proveedor->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater(<?=$proveedor->id?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
				echo '<span style="cursor:pointer" onclick="busca($(\'busca_rif\').value,$(\'busca_nombre\').value, '.$j.');">'.$j.'</span>';
		}
		else
		{
			if ($j==$pagina)
				echo "<span>- ".$j."</span>";
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'busca_rif\').value,$(\'busca_nombre\').value, '.$j.');">- '.$j.'</span>';
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
