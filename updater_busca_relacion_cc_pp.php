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

$cc = $_GET['cc'];
$pp = $_GET['pp'];
$escenario = $_REQUEST['escenario'];


$cRelacion_cc_pp = relacion_cc_pp::buscar($conn, $cc, $pp,$escenario, $tamano_pagina, $inicio);
$total = relacion_cc_pp::total_registro_busqueda($conn, $cc, $pp,$escenario);
//die(var_dump($cRelacion_cc_pp[0]));

if(is_array($cRelacion_cc_pp) && count($cRelacion_cc_pp)>0)
{
?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td width="45%">Cuentas Contables</td>
			<td width="45%">Partidas Presupuestarias</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
	</tr>
	<? 
	foreach($cRelacion_cc_pp as $relacion_cc_pp) 
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$relacion_cc_pp->cuenta_contable->codcta.' - '.$relacion_cc_pp->cuenta_contable->descripcion?></td>
		<td><?=$relacion_cc_pp->partida_presupuestaria->id.' - '.$relacion_cc_pp->partida_presupuestaria->descripcion?></td>
		<td align="center">
			<a href="?accion=del&id=<?=$relacion_cc_pp->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
		</td>
		<td align="center">
			<a href="#" onclick="updater('<?=$relacion_cc_pp->id?>&id_escenario=<?=$relacion_cc_pp->id_escenario?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
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
			echo "<span style=\"cursor:pointer\" onclick=\"busca($('hidden_cc').value, $('hidden_pp').value, ".$j.");\">".($j>1 ? ' - ':'').$j."</span>";
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
