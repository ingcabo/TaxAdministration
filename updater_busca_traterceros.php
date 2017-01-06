<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$num = 20;

if (!$pagina) 
{
	$inicio = 10;
	$pagina=1;
}
else 
{
	$inicio = ($pagina - 1) * $num;
} 
$nrodoc = $_GET['nrodoc'];
$fecha = $_GET['fecha'];

$cTra = traFondosTerceros::buscar($conn, $nrodoc, $fecha, $inicio, $tamano_pagina);
$total = traFondosTerceros::total_registro_busqueda($conn, $nrodoc, $fecha);

if(is_array($cTra))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="40%">Descripcion</td>
		<td width="10%">Codigo</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cTra as $tra) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$tra->descripcion?></td>
		<td><?=$tra->nrodoc?></td>
		<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onClick="updater('<?=$tra->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
			<a href="#" onClick="busca($('busca_fecha').value, $('busca_nrodoc')value, '<?=$j?>', '<?=$num?>'); return false;"><?=(($j==1) ? '':' - ').$j?></a>
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
