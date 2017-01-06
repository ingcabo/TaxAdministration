<?
require ("comun/ini.php");
// Creando el objeto tipos_solicitudes
$oTiposSolicitudes = new tipos_solicitudes;
$accion = $_REQUEST['accion'];

if($accion == 'Guardar'){
	if($oTiposSolicitudes->add($conn, $_POST['id_nuevo'], $_POST['descripcion']))
		$msj = REG_ADD_OK;
	else
		$msj = CODIGO_YA_EXISTE;
}elseif($accion == 'Actualizar'){
	if($oTiposSolicitudes->set($conn, $_POST['id_nuevo'], $_POST['id'], $_POST['descripcion']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	
	if($oTiposSolicitudes->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$ctipos_solicitudes=$oTiposSolicitudes->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Tipos de Solicitudes</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($ctipos_solicitudes)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ctipos_solicitudes as $tiposSolicitudes) { 
?> 
<tr class="filas"> 
<td><?=$tiposSolicitudes->id?></td>
<td><?=$tiposSolicitudes->descripcion?></td>
<td><a href="?accion=del&id=<?=$tiposSolicitudes->id?>" title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$tiposSolicitudes->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
