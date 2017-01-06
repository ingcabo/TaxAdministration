<?
require ("comun/ini.php");
// Creando el objeto requisitos
$oTipo_solicitud = new tipo_solicitud;
$accion = $_REQUEST['accion'];
$estatus= $_REQUEST['estatus'];
if(empty($estatus)){ $estatus=0; }

if($accion == 'Guardar'){
	if($oTipo_solicitud->add($conn, $_REQUEST['descripcion'],
										$estatus ))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){

	if($oTipo_solicitud->set($conn, $_REQUEST['id'],
										$_REQUEST['descripcion'],
										$estatus))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oTipo_solicitud->del($conn, $_REQUEST['id']))
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

$cTipo_solicitud =$oTipo_solicitud->get_all($conn, $start_record,$page_size);
$pag=new paginator($oTipo_solicitud->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro Tipos de Solicitudes</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cTipo_solicitud)){ ?>
<table id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cTipo_solicitud as $tipo_solicitud) { 
?> 
<tr class="filas"> 
<td><?=$tipo_solicitud->id?></td>
<td><?=$tipo_solicitud->descripcion?></td>
<td align="center"><?php if($tipo_solicitud->estatus==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="tipo_solicitud.php?accion=del&id=<?=$tipo_solicitud->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$tipo_solicitud->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<table width="762" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><span class="paginator"><? $pag->print_page_counter()?></span></td>
		<td align="right"><span class="paginator"><? $pag->print_paginator("pulldown")?> </span></td>
	</tr>
</table>
<? 
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");?>
