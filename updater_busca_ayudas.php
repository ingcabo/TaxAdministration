<?
include ("comun/ini.php");
$id_proveedor = $_GET['id_proveedor'];
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
$cAyudas = ayudas::buscar($conn, 
						$id_proveedor, 
						$id_ue, 
						$fecha_desde, 
						$fecha_hasta, 
						$nrodoc, 
						$descripcion,'id',$inicio,$tamano_pagina);
$total = ayudas::totalRegistroBusqueda($conn,$id_proveedor,$id_ue,$fecha_desde,$fecha_hasta,$nrodoc,$descripcion);
?>
<? if(is_array($cAyudas)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n:</td>
<td>Status:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cAyudas as $Ayudas) { 
?> 
<tr class="filas"> 
<td><?=$Ayudas->id?></td>
<td><?=$Ayudas->descripcion?></td>
<td><? if ($Ayudas->status==1) 
			echo "Registrada";
			elseif ($Ayudas->status==2)
				echo "Aprobada";
				else
					echo "Anulada"; ?></td>
<td align="center"><?=!empty($Ayudas->nrodoc) ? '<a href="ayudas.pdf.php?id='.$Ayudas->id.'" title="Emitir Reporte"><img style="width:16px; height:10px" src="images/reporte.jpg" border="0" /></a>' : "" ?></td>
<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$Ayudas->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
	$total_paginas = ceil($total / $tamano_pagina);
?>
	<tr class="filas">
		<td colspan="7" align="center">
		<?
		for ($j=1; $j<=$total_paginas; $j++)
		{
			if ($j==1)
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
<? }else {
		echo "No hay registros en la bd";
} ?>