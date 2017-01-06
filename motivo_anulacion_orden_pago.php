<? require ("comun/ini.php");
// Creando el objeto motivo
$omotivo_anulacion_orden_pago = new motivo_anulacion_orden_pago;
$accion = $_REQUEST['accion'];

#SECCION DE GUARDAR#
if($accion == 'Guardar' and !empty($_REQUEST['descripcion'])){
	if($omotivo_anulacion_orden_pago->add($conn, $_REQUEST['descripcion'], $_REQUEST['sumaresta']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;

#SECCION DE ACTULIZAR#
}elseif($accion == 'Actualizar' and !empty($_REQUEST['descripcion'])){
	if($omotivo_anulacion_orden_pago->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['sumaresta']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;

#SECCION DE ELIMINAR#
}elseif($accion == 'del'){
	if($omotivo_anulacion_orden_pago->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cmotivo_anulacion_orden_pago=$omotivo_anulacion_orden_pago->get_all($conn, $start_record,$page_size);
$pag=new paginator($omotivo_anulacion_orden_pago->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Anulacion </span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cmotivo_anulacion_orden_pago)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>id</td>
<td>Descripci&oacute;n</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cmotivo_anulacion_orden_pago as $maop) { 
?> 
<tr class="filas"> 
<td><?=$maop->id?></td>
<td><?=$maop->descripcion?></td>

<td align="center">
<a href="#" onClick="updater('<?=$maop->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="motivo_anulacion_orden_pago.php?accion=del&id=<?=$maop->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>