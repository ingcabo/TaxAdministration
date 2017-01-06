<?
require ("comun/ini.php");
// Creando el objeto solicitud
$oSolicitud = new solicitud();
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oSolicitud->add($conn, $_POST['id_nuevo'], $_POST['detalle']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oSolicitud->set($conn, $_POST['id'], $_POST['detalle']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oSolicitud->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cSolicitud=$oSolicitud->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Solicitudes</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cSolicitud)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Tipo de Solicitud</td>

<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cSolicitud as $solicitud) { 
?> 
<tr class="filas"> 
<td><?=$solicitud->id?></td>
<td><?=$solicitud->detalle?></td>
<td><a href="?accion=del&id=<?=$solicitud->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$solicitud->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<?
$validator->create_message("error_det", "detalle", "*");
$validator->print_script();
require ("comun/footer.php");
?>