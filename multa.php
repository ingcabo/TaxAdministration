<?
require ("comun/ini.php");
// Creando el objeto multas
$oMulta = new multa();
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oMulta->add($conn, $_POST['id_nuevos'], $_POST['detalle'], $_POST['ordenanza'], $_POST['multa']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oMulta->set($conn, $_POST['id'], $_POST['detalle'], $_POST['ordenanza'], $_POST['multa']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oMulta->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cMulta=$oMulta->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Licores Multas</span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cMulta)){?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Detalles</td>
<td>Ordenanza</td>
<td>Multa</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cMulta as $multa) { 
?> 
<tr class="filas"> 
<td><?=$multa->id?></td>
<td><?=$multa->detalle?></td>
<td><?=$multa->ordenanza?></td>
<td><?=$multa->multa?></td>
<td><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else {return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onClick="updater('<?=$multa->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
<? }else {
		echo "No hay registros en la bd";
} ?>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<?
$validator->create_message("error_detalle", "detalle", "Error en blanco");
$validator->create_message("error_ordenanza", "ordenanza", "Error en blanco");
$validator->create_message("error_multa", "multa", "Error en blanco");
$validator->print_script();
require ("comun/footer.php");
?>
