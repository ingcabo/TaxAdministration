<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 20;

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

$oProveedor = new proveedores;
$cProveedores = $oProveedor->buscar($conn, $tamano_pagina, $inicio, "nombre", $nombre);
$total = $oProveedor->total_registro_busqueda($conn, "nombre", $nombre);

if(is_array($cProveedores) && count($cProveedores)>0)
{
?>
<table id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="23">Id</td>
		<td width="328">Proveedor</td>
		<td width="37">Status</td>
	</tr>
	<? 
	foreach($cProveedores as $proveedor) 
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$proveedor->id?></td>
		<td><?=$proveedor->nombre?></td>
		<td align="center"><?=$proveedor->status?></td>
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
			if ($j==1)
				echo '<span class="actual">'.$j.'</span>';
			else
				echo '<span style="cursor:pointer" onclick="busca($(\'hidden_nombre\').value, '.$j.');"> - '.$j.'</span>';
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
