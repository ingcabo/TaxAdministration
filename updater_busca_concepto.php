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
$busqueda = $_GET['busqueda'];
$textAux = $_GET['textAux'];
$estatus = $_GET['estatus'];

$oconcepto = new concepto;
$cconcepto=$oconcepto->get_all($conn,'int_cod',$busqueda, $textAux, $estatus, $num, $inicio);
$total = $oconcepto->total_registro_busqueda($conn,$busqueda, $textAux, $estatus);

if(is_array($cconcepto))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="10%">C&oacute;digo</td>
		<td>Nombre</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cconcepto as $concepto) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$concepto->conc_cod?></td>
		<td align="center"><?=$concepto->conc_nom?></td>
		<td align="center"><a href="?accion=del&int_cod=<?=$concepto->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$concepto->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
