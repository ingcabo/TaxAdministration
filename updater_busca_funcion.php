<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$num = $_REQUEST['num'];

if (!$pagina) 
{
	$inicio = 10;
	$pagina=1;
}
else 
{
	$inicio = ($pagina - 1) * $num;
} 
//busqueda=' + busqueda +'&textAux='+textAux + '&estatus='+estatus
$busqueda = $_GET['busqueda'];
$textAux = $_GET['textAux'];
$estatus = $_GET['estatus'];

$ofuncion = new funcion;
$cfuncion=$ofuncion->get_all($conn,'fun_ord',$busqueda, $textAux, $estatus, $num, $inicio);
$total = $ofuncion->total_registro_busqueda($conn,$busqueda, $textAux, $estatus);

if(is_array($cfuncion))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="10%">Orden</td>
		<td>Nombre</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cfuncion as $funcion) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$funcion->fun_ord?></td>
		<td align="center"><?=$funcion->fun_nom?></td>
		<td align="center"><a href="?accion=del&int_cod=<?=$funcion->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$funcion->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
<?
	}

	$total_paginas = ceil($total / $num);
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
			<a href="#" onclick="busca( $('TipoB').options[$('TipoB').selectedIndex].value, $('textAux').value, $('TipoBE').options[$('TipoBE').selectedIndex].value, '<?=$j?>', '<?=$num?>'); return false;"><?=(($j==1) ? '':' - ').$j?></a>
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
