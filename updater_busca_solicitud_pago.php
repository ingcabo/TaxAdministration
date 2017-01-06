<?
include ("comun/ini.php");
$id_proveedor = $_GET['id_proveedor'];
$id_ue = $_GET['id_ue'];
$nrodoc = $_GET['nrodoc'];
$fecha_desde = $_GET['fecha_desde'];
$fecha_hasta = $_GET['fecha_hasta'];
$descripcion = $_GET['descripcion'];

$tamano_pagina = 20;
$pagina = $_REQUEST['pagina'];
if (!$pagina)
{
	$pagina = 1;
	$inicio = 0;
}
else
	$inicio = ($pagina - 1) * $tamano_pagina;

$cSolicitud_pago = solicitud_pago::buscar($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, $escEnEje, "nrodoc", $inicio, $tamano_pagina);
$total = solicitud_pago::totalRegsBusqueda($conn, $id_proveedor, $id_ue, $fecha_desde, $fecha_hasta, $nrodoc, $descripcion, $escEnEje);

if(is_array($cSolicitud_pago))
{
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
	<tr class="cabecera"> 
		<td>Nro. Documento</td>
		<td>Descripci&oacute;n:</td>
		<td>Status:</td>
		<td width="5%">&nbsp;</td>
		<!--td>&nbsp;</td-->
		<td width="5%">&nbsp;</td>
	</tr>
	<? 
	foreach($cSolicitud_pago as $sp)
	{ 
	?> 
	<tr class="filas"> 
		<td><?=$sp->nrodoc?></td>
		<td><?=$sp->descripcion?></td>
		<?
		if($sp->status=='1')
			$nom_status = "Registrado";
			elseif($sp->status=='2')
				$nom_status = "Aprobado";
				else
					$nom_status = "Anulado";
		?>
		<td><? echo $nom_status ?></td>
		<td align="center"><?=($sp->status=='2') ? '<a href="solicitud_pago.pdf.php?id='.$sp->nrodoc.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
		<!--td><a href="?accion=del&amp;id=<?=$sp->nrodoc?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0"></a></td-->
		<td align="center"><a href="#" onclick="updater('<?=$sp->nrodoc?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
		
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
				echo '<span style="cursor:pointer" onclick="busca($F(\'busca_ue\'), $F(\'busca_proveedores\'), $F(\'busca_descripcion\'), $F(\'busca_fecha_desde\'), $F(\'busca_fecha_hasta\'), $F(\'busca_nrodoc\'), '.$j.')">'.($j>1 ? ' - ':'').$j.'</span>';
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