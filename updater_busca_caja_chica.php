<?
include ("comun/ini.php");
$id_ciudadano = $_GET['id_ciudadano'];
$id_ue = $_GET['id_ue'];
$descripcion = $_GET['descripcion'];
$nrodoc = $_GET['nrodoc'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];

$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;

$cCajaChica = caja_chica::buscar($conn, $id_ciudadano, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, "id", $inicio, $max);
$total = caja_chica::totalRegsBusqueda($conn, $id_ciudadano, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion);

if(is_array($cCajaChica))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td>C&oacute;digo</td>
		<td>Descripci&oacute;n:</td>
		<td>Status:</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
	</tr>
<? 
	foreach($cCajaChica as $cajaChica) 
	{
	$nrodocCC = trim($cajaChica->nrodoc);
	?> 
	<tr class="filas"> 
		<td><?=$cajaChica->nrodoc?></td>
		<td><?=$cajaChica->descripcion?></td>
		<td align="center"><?=(($cajaChica->status=='1') ? 'Creado':($cajaChica->status=='2') ? 'Aprobado':'Anulado')?></td>
		<td align="center"><?=!empty($nrodocCC) ? '<a href="caja_chica.pdf.php?id='.$cajaChica->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
		<td align="center"><a href="?accion=del&id=<?=$cajaChica->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
		<td align="center"><a href="#" onclick="updater('<?=$cajaChica->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
			if ($j==$pagina)
				echo '<span class="actual">'.($j>1 ? ' - ':'').$j.'</span>';
			else
				echo '<span style="cursor:pointer" onclick="	busca($F(\'busca_ue\'), $F(\'busca_ciudadanos\'), $F(\'busca_descripcion\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'busca_nrodoc\'), '.$j.')">'.($j>1 ? ' - ':'').$j.'</span>';
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
	echo "No hay registros en la bd";
?>