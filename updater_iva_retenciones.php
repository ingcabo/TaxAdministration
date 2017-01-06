<?
include ("comun/ini.php");

$partida = $_REQUEST['id_partida'];

$cIva_retenciones = iva_retenciones::buscar($conn,$partida);

if(is_array($cIva_retenciones)){
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Partidas Presupuestarias</td>
<td>Iva</td>
<td>Retenci&oacute;n</td>
<td>A&ntilde;o</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cIva_retenciones as $ir) { 
?> 
<tr class="filas"> 
<td><?=$ir->nombre_partida->descripcion?></td>
<td><?=($ir->iva==1)? 'Si': 'No'?></td>
<td align="center"><?=($ir->retencion==1)? 'Si': 'No'?></td>
<td><?=$ir->anio?></td>
<td align="center">
<a href="iva_retenciones.php?accion=del&id=<?=$ir->id?>" title="Modificar ó Actualizar Registro" onclick="confirm('Desea Eliminar el Siguiente Registro'); return false;" ><img src="images/eliminar.gif" width="16" height="10" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$ir->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>

