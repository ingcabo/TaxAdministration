<?
include ("comun/ini.php");
$pagina = $_REQUEST['pagina'];
$codcta = $_GET['cc'];
$td = $_GET['td'];
$escenario = $_GET['escenario'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
//echo $pagina."<br>";
if (!$pagina) 
{
    $inicio = 10;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * 20;
} 

//die(var_dump($_REQUEST));
$com = new comprobante($conn);
$cComprobante = $com->get_all($escenario, $codcta, $td, $fecha_desde, $fecha_hasta, $inicio, 20);
//var_dump($cComprobante);
$total = $com->total_registro_busqueda($escenario, $codcta, $td, $fecha_desde, $fecha_hasta);

if(is_array($cComprobante) && count($cComprobante) > 0)
{
?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
		<td width="63%">Descripci&oacute;n</td>
		<td  width="12%">Fecha</td>
		<td width="15%">Origen</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
	<? 
	foreach($cComprobante as $com) 
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$com->descrip?></td>
		<td align="center"><?=$com->fecha?></td>
		<td align="center"><?=$com->aux?></td>
		<td align="center">
			<a href="?accion=del&id=<?=$com->id?>" onclick="if (confirm('Si presiona Aceptar sera eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
		</td>
		<td align="center">
			<a href="#" onclick="updater('<?=$com->id?>&id_escenario=<?=$com->id_escenario?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
		</td>
	</tr>
	<?
	}

	$total_paginas = ceil($total / 20);
?>
	<tr class="filas">
		<td colspan="7" align="center">
		<? 
		for ($j=1;$j<=$total_paginas;$j++)
		{
			if ($j==1)
			{ 
				if ($j==$pagina)
					echo "<span class=\"actual\">".$j."</span>";
				else
					echo "<span style=\"cursor:pointer\" onclick=\"busca($('busca_cc').value,$('busca_td').value , $('busca_fecha_desde').value, $('busca_fecha_hasta').value , '".$j."');\">".$j."</span>";
			}
			else 
			{
				if ($j==$pagina)
					echo "- <span class=\"actual\">".$j."</span>";
				else
					echo "<span style=\"cursor:pointer\" onclick=\"busca($('busca_cc').value,$('busca_td').value , $('busca_fecha_desde').value, $('busca_fecha_hasta').value, '".$j."');\">- ".$j."</span>";
			}
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
