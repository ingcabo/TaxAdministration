<?
require ("comun/ini.php");
// Creando el objeto ramo_imp
$oramo_imp = new ramo_imp;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar'){
	if($oramo_imp->add($conn, $_REQUEST['ramo'], strtoupper($_REQUEST['descripcion']), strtoupper($_REQUEST['tipo_imp']),  $_REQUEST['anio'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oramo_imp->set($conn, $_REQUEST['id'], $_REQUEST['ramo'], strtoupper($_REQUEST['descripcion']), strtoupper($_REQUEST['tipo_imp']),  $_REQUEST['anio'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oramo_imp->del($conn, $_REQUEST['id']))
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

$cramo_imp=$oramo_imp->get_all($conn, $start_record,$page_size);
$pag=new paginator($oramo_imp->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Ramos </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cramo_imp)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Ramo</td>
<td>Descripci&oacute;n</td>
<td>A&ntilde;o</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cramo_imp as $ramo_imp) { 
?> 
<tr class="filas"> 
<td><?=$ramo_imp->id?></td>
<td><?=$ramo_imp->ramo?></td>
<td align="left"><?=$ramo_imp->descripcion?></td>
<td align="center"><?=$ramo_imp->anio?></td>
<td align="center"><?php if($ramo_imp->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="ramo_imp.php?accion=del&id=<?=$ramo_imp->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$ramo_imp->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_ramo", "ramo", "* Esta Vacio");
$validator->create_message("error_descripcion", "descrpcion", "* Esta Vacio");
$validator->create_message("error_tipo_imp", "tipo_imp", "* Esta Vacio");
$validator->create_message("error_anio", "anio", "* Esta Vacio");
$validator->print_script();
require ("comun/footer.php"); ?>