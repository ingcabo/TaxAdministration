<?
require ("comun/ini.php");
// Creando el objeto costo_calcomania
$ocosto_calcomania = new costo_calcomania;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }


$precio=guardafloat($_REQUEST['precio']);
if(empty($precio)){ $precio=0; }

if($accion == 'Guardar'){
	if($ocosto_calcomania->add($conn, $_REQUEST['anio'], guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta']), guardafloat($_REQUEST['monto']), $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($ocosto_calcomania->set($conn, $_REQUEST['id'],$_REQUEST['anio'], guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta']), 
	guardafloat($_REQUEST['monto']), $status ))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($ocosto_calcomania->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$ccosto_calcomania=$ocosto_calcomania->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Costo Calcoman&iacute;a</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($ccosto_calcomania)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
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
foreach($ccosto_calcomania as $costo_calcomania) { 
?> 
<tr class="filas"> 
<td><?=$costo_calcomania->id?></td>
<td><?=$costo_calcomania->anio?></td>
<td align="center"><?=muestrafecha($costo_calcomania->fecha_desde)?></td>
<td align="center"><?=muestrafecha($costo_calcomania->fecha_hasta)?></td>
<td align="right"><?=muestrafloat($costo_calcomania->monto)?></td>
<td align="center"><?php if($costo_calcomania->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="costo_calcomania.php?accion=del&id=<?=$costo_calcomania->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$costo_calcomania->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

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
<? $validator->create_message("error_anio", "anio", "* Esta Vació");
$validator->print_script();
require ("comun/footer.php"); ?>