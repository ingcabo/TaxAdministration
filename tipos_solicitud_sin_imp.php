<?
require ("comun/ini.php");
// Creando el objeto tipos_solicitud_sin_imp
$oTiposSolicitudesSinImp = new tipos_solicitud_sin_imp;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oTiposSolicitudesSinImp->add($conn, 
		$_POST['descripcion'], 
		$_POST['cta_contable'], 
		$_POST['anio'] ))
		$msj = REG_ADD_OK;
	else
		$msj = CODIGO_YA_EXISTE;
}elseif($accion == 'Actualizar'){
	if($oTiposSolicitudesSinImp->set($conn,
		$_POST['id'], 
		$_POST['descripcion'], 
		$_POST['cta_contable'], 
		$_POST['anio'] ))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oTiposSolicitudesSinImp->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cTiposSolicitudesSinImp=$oTiposSolicitudesSinImp->getAll($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Tipos de Ordenes de Pago sin Imputaci&oacute;n</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cTiposSolicitudesSinImp)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cTiposSolicitudesSinImp as $tiposSolicitudesSinImp) { 
?> 
<tr class="filas"> 
<td><?=$tiposSolicitudesSinImp->id?></td>
<td><?=$tiposSolicitudesSinImp->descripcion?></td>
<td><a href="?accion=del&id=<?=$tiposSolicitudesSinImp->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$tiposSolicitudesSinImp->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_cc", "cta_contable", "*");
$validator->create_message("error_anio", "anio", "*");
$validator->print_script();
require ("comun/footer.php");
?>
