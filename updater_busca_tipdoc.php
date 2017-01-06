<?
include ("comun/ini.php");
$id = $_GET['id'];
$id_mp = $_GET['id_mp'];
$descripcion = $_GET['descripcion'];
$cTiposDocumentos = tipos_documentos::buscar($conn, $id, $id_mp, $descripcion);
?>
<? if(is_array($cTiposDocumentos)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Momento Presupuestario</td>
<td>Descripci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cTiposDocumentos as $tiposDocumentos) { 
?> 
<tr class="filas"> 
<td><?=$tiposDocumentos->id?></td>
<td><?=$tiposDocumentos->momento_presupuestario?></td>
<td><?=$tiposDocumentos->descripcion?></td>
<td><a href="?accion=del&id=<?=$tiposDocumentos->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$tiposDocumentos->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>