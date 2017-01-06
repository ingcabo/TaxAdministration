<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$tamano_pagina = 20;

if (!$pagina) 
{
	$inicio = 0;
	$pagina = 1;
}
else 
{
	$inicio = ($pagina - 1) * $tamano_pagina;
} 
$codcta = $_GET['codigo_cuenta'];
$descripcion = $_GET['descrip_cuenta'];
$ano = $_GET['ano_cuenta'];

$cPlan_cuenta = plan_cuenta::buscar($conn, $codcta, $descripcion, $ano, $inicio, $tamano_pagina, "codcta::text");
//die(var_dump($cPlan_cuenta));
$total = plan_cuenta::total_registro_busqueda($conn, $codcta, $descripcion, $ano);

if(is_array($cPlan_cuenta) && count($cPlan_cuenta)>0)
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td width="20%">Codigo Cuenta</td>
		<td width="53%">Descripci&oacute;n</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cPlan_cuenta as $plan_cuenta) 
	{ 
?> 
	<tr class="filas"> 
		<td><?=$plan_cuenta->codcta?></td>
		<td><?=$plan_cuenta->descripcion?></td>
		<td align="center">
			<a href="?accion=del&id=<?=$plan_cuenta->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
		</td>
		<td align="center">
			<a href="#" onclick="updater(<?=$plan_cuenta->codcta?>); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
		</td>
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
			echo '<span style="cursor:pointer" onclick="busca($(\'cod_cta\').value,$(\'desc_cta\').value, '.$j.');">'.($j>1 ? ' - ':'').$j.'</span>';
	}
	?>
		</td>
	</tr>
	<tr class="filas">
		<td colspan="7" align="center"> Pagina <strong><?=$pagina?></strong> de <strong><?=$total_paginas?></strong></td>
	</tr>
</table>
<?
}
else 
{
	echo "No hay registros en la bd";
}
?>
