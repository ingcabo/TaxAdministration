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

$id_pp = $_GET['id_pp'];
$observaciones = $_GET['descripcion'];
$grupo_prov = $_GET['grupo_prov'];
$codigo = $_GET['codigo'];
$idFamilia = $_GET['idFamilia'];
//die($codigo);
$ctipo_producto = tipo_producto::buscar($conn, 
						$id_pp, 
						$grupo_prov, 
						$observaciones,
						$codigo,
						$idFamilia);
$total_tp = tipo_producto::total_registro_busqueda($conn, $id_pp, $grupo_prov, $observaciones,$codigo,$idFamilia);
$total = $total_tp;
?>
<? if(is_array($ctipo_producto)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ctipo_producto as $tipo_producto) { 
?> 
<tr class="filas"> 
<td><?=$tipo_producto->id?></td>
<td><?=$tipo_producto->descripcion?></td>
<td><a href="?accion=del&id=<?=$tipo_producto->id?>" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$tipo_producto->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
			echo '<span style="cursor:pointer" onclick="busca($(\'busca_pp\').value,$(\'busca_grupo_prov\').value,$(\'busca_descrip\').value,'.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
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
