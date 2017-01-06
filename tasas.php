<?
require ("comun/ini.php");
// Creando el objeto Tasas
$oTasas = new tasas();
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oTasas->add($conn, $_POST['detalle'], $_POST['ordenanza'], $_POST['unit']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oTasas->set($conn, $_POST['id'], $_POST['detalle'], $_POST['ordenanza'], $_POST['unit']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oTasas->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cTasas=$oTasas->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Tasas</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cTasas)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Detalle</td>
<td>Ordenanza Articulo</td>
<td>Unidad Tributaria</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cTasas as $tasas) { 
?> 
<tr class="filas"> 
<td><?=$tasas->id?></td>
<td><?=$tasas->detalle?></td>
<td><?=$tasas->ordenanza?></td>
<td><?=$tasas->unit?></td>
<td><a href="?accion=del&id=<?=$tasas->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$tasas->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_det", "detalle", "*");
$validator->create_message("error_ord", "ordenanza", "*");
$validator->create_message("error_unit", "unit", "*");
$validator->print_script();
require ("comun/footer.php");
?>
