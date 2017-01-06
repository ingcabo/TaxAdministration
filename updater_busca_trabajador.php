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

$otrabajador = new trabajador;
$ctrabajador=$otrabajador->get_all($conn,$_SESSION['EmpresaL'],'B.dep_ord,A.int_cod', $busqueda, $textAux,$estatus,$num,$inicio);
$total = $otrabajador->total_registro_busqueda($conn, $_SESSION['EmpresaL'], $busqueda, $textAux,$estatus);

if(is_array($ctrabajador))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td>Nombre</td>
		<td>Apellido</td>
		<td>Departamento</td>
		<td>Cargo/Funci&oacute;n</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<? 
	foreach($ctrabajador as $trabajador) 
	{ 
?> 
	<tr class="filas"> 
		<td align="center"><?=$trabajador->tra_nom?></td>
		<td align="center"><?=$trabajador->tra_ape?></td>
		<td align="center"><?=$trabajador->dep_nom?></td>
		<?
		if($trabajador->tra_tipo){?>
		<td align="center"><?=$trabajador->fun_nom?></td>
		<? }
		else{?>
		<td align="center"><?=$trabajador->car_nom?></td>
		<? }
		?>
		<td align="center"><a href="?accion=del&int_cod=<?=$trabajador->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$trabajador->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
	</tr>
<?
	}

	$total_paginas = ceil($total / $num);
?>
	<tr class="filas">
		<td colspan="6" align="center">
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
