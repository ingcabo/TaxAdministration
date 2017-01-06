<?
include ("comun/ini.php");
$id = $_GET['id'];
$descripcion = $_GET['descripcion'];
$cRA = retenciones_adiciones::buscar($conn, $id, $descripcion);
?>
<? if(is_array($cRA)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción de la unidad</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cRA as $RA) { 
?> 
<tr class="filas"> 
<td><?=$RA->id?></td>
<td><?=$RA->descripcion?></td>
<td><a href="?accion=del&id=<?=$RA->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$RA->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
