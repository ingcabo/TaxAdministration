<?
include ("comun/ini.php");

$anio = $_REQUEST['anio'];

$cIva = iva::buscar($conn,$anio);

if(is_array($cIva))
{
?>
	<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td>Partidas Presupuestarias</td>
			<td>Iva</td>
			<td>A&ntilde;o</td>
			<td width="5%">&nbsp;</td>
			<td width="5%">&nbsp;</td>
		</tr>
<? 
	foreach($cIva as $iva)
	{ 
?> 
		<tr class="filas"> 
			<td><?=$iva->nombre_partida->descripcion?></td>
			<td align="center"><?=$iva->porc_iva?></td>
			<td align="center"><?=$iva->anio?></td>
			<td align="center"><a href="iva.php?accion=del&id=<?=$iva->id?>" title="Eliminar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
			<td align="center"><a href="#" onclick="updater('<?=$iva->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
		</tr>
<?
	}
?>
	</table>
<?
}
else 
	echo "No hay registros en la bd";
?>

