<?
require ("comun/ini.php");
// Creando el objeto tasa_inscripcion
$otasa_inscripcion = new tasa_inscripcion;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }


$precio=guardafloat($_REQUEST['precio']);
if(empty($precio)){ $precio=0; }

if($accion == 'Guardar'){
	if($otasa_inscripcion->add($conn, $_REQUEST['anio'], guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta']), guardafloat($_REQUEST['monto']), $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($otasa_inscripcion->set($conn, $_REQUEST['id'],$_REQUEST['anio'], guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta']), 
	guardafloat($_REQUEST['monto']), $status ))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($otasa_inscripcion->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$ctasa_inscripcion=$otasa_inscripcion->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Tasa de Inscripci&oacute;n </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($ctasa_inscripcion)){ ?>
<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>A&ntilde;o</td>
<td>Fecha Desde</td>
<td>Fecha Hasta </td>
<td>Monto</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ctasa_inscripcion as $tasa_inscripcion) { 
?> 
<tr class="filas"> 
<td><?=$tasa_inscripcion->id?></td>
<td><?=$tasa_inscripcion->anio?></td>
<td align="center"><?=muestrafecha($tasa_inscripcion->fecha_desde)?></td>
<td align="center"><?=muestrafecha($tasa_inscripcion->fecha_hasta)?></td>
<td align="right"><?=muestrafloat($tasa_inscripcion->monto)?></td>
<td align="center"><?php if($tasa_inscripcion->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="tasa_inscripcion.php?accion=del&id=<?=$tasa_inscripcion->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$tasa_inscripcion->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

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

<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<? require ("comun/footer.php"); ?>