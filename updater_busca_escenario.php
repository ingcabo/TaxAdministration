<?
include ("comun/ini.php");
$id = $_GET['id'];
$id_base = $_GET['id_base'];
$anio = $_GET['anio'];
$descripcion = $_GET['descripcion'];
$cEscenario = escenarios::buscar($conn, $id, $id_base, $anio, $descripcion);
if(is_array($cEscenario)){ 
?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Escenario Base</td>
<td>A&ntilde;o</td>
<td>Descripci&oacute;n</td>
<td>Aprobado</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cEscenario as $escenario) { 
?> 
<tr class="filas"> 
<td><?=$escenario->id?></td>
<td><?=$escenario->base?></td>
<td><?=$escenario->ano?></td>
<td><?=$escenario->descripcion?></td>
<td><?=($escenario->aprobado == 't')? "Si" : "No" ?></td>
<td><a href="?accion=del&id=<?=$escenario->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$escenario->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>