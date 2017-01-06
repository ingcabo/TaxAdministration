<? require ("comun/ini.php");
// Creando el objeto banco
$oclasificacion_cuenta = new clasificacion_cuenta;
$accion = $_REQUEST['accion'];

#SECCION DE GUARDAR#
if($accion == 'Guardar' and !empty($_REQUEST['descripcion'])){
	if($oclasificacion_cuenta->add($conn, $_REQUEST['descripcion'], $_REQUEST['observacion']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;

#SECCION DE ACTULIZAR#
}elseif($accion == 'Actualizar' and !empty($_REQUEST['descripcion'])){
	if($oclasificacion_cuenta->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['observacion']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;

#SECCION DE ELIMINAR#
}elseif($accion == 'del'){
	if($oclasificacion_cuenta->del($conn, $_REQUEST['id']))
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

$cclasificacion_cuenta=$oclasificacion_cuenta->get_all($conn, $start_record,$page_size);
$pag=new paginator($oclasificacion_cuenta->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Clasificaci&oacute;n de Cuenta </span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cclasificacion_cuenta)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>id</td>
<td>Descripci&oacute;n</td>
<td>Observaciones</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cclasificacion_cuenta as $clasificacion) { 
?> 
<tr class="filas"> 
<td><?=$clasificacion->id?></td>
<td><?=$clasificacion->descripcion?></td>
<td align="center"><?=$clasificacion->observacion?></td>

<td align="center">
<a href="#" onClick="updater('<?=$clasificacion->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="clasificacion_cuenta.php?accion=del&id=<?=$clasificacion->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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
