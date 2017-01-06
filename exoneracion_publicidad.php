<?php 
require ("comun/ini.php");
// Creando el objeto Exoneracion en Publicidad
$oExoneracion_publicidad = new exoneracion_publicidad;
$estatus=$_POST['estatus'];
$accion=$_POST['accion'];
if(empty($estatus)){$estatus=0;}
$tipo=$_POST['tipo'];
if($accion == 'Guardar' ){
	if($oExoneracion_publicidad->add($conn, $_POST['descripcion'], $estatus, $tipo))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oExoneracion_publicidad->set($conn, $_POST['id'], $_POST['descripcion'], $estatus, $tipo))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oExoneracion_publicidad->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cExoneracion_publicidad = $oExoneracion_publicidad->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Motivos de Exepciones y Exoneraciones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cExoneracion_publicidad)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>Codigo</td>
<td>Descripci&oacute;n</td>
<td>Tipo</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cExoneracion_publicidad as $exoneracion_publicidad) { 
?> 
<tr class="filas"> 
<td><?=$exoneracion_publicidad->id?></td>
<td><?=$exoneracion_publicidad->descripcion?></td>
<td align="left"><?php if($exoneracion_publicidad->tipo==1) { echo "Publicidad"; }else{ echo "Espectaculos"; } ?></td>
<td align="left"><?php if($exoneracion_publicidad->estatus==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$exoneracion_publicidad->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="exoneracion_publicidad.php?accion=del&id=<?=$exoneracion_publicidad->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td></tr>
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
//$validator->create_message("error_desc", "descripcion", "*");
//$validator->create_message("error_nombre", "nombre_corto", "*");
$validator->print_script();
require ("comun/footer.php"); ?>