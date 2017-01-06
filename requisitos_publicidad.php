<?
require ("comun/ini.php");
// Creando el objeto requisitos
$oRequisitos_publicidad = new requisitos_publicidad;
$accion = $_REQUEST['accion'];
$estatus= $_REQUEST['estatus'];
if(empty($estatus)){$estatus=0;}

if($accion == 'Guardar'){
	if($oRequisitos_publicidad ->add($conn, $_REQUEST['id_solicitud'], $_REQUEST['requisito'],	$estatus ))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){

	if($oRequisitos_publicidad ->set($conn, $_REQUEST['id'], $_REQUEST['id_solicitud'], $_REQUEST['requisito'],	$estatus))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oRequisitos_publicidad ->del($conn, $_REQUEST['id']))
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

$cRequisitos_publicidad =$oRequisitos_publicidad ->get_all($conn, $start_record,$page_size);
$pag=new paginator($oRequisitos_publicidad->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de requisitos</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cRequisitos_publicidad)){ ?>
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
foreach($cRequisitos_publicidad as $requisitos_publicidad) { 
?> 
<tr class="filas"> 
<td><?=$requisitos_publicidad->id_solicitud?></td>
<td><?=$requisitos_publicidad->requisito?></td>
<td align="center"><?php if($requisitos_publicidad->estatus==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="requisitos_publicidad.php?accion=del&id=<?=$requisitos_publicidad->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$requisitos_publicidad->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
//$validator->create_message("error_requisito", "requisito", "* Requerido");
//$validator->print_script();
require ("comun/footer.php");?>
