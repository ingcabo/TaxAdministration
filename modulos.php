<?
require ("comun/ini.php");
// Creando el objeto modulos
$oModulos = new modulos();
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oModulos->add($conn, $_POST['descripcion'], $_POST['orden']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oModulos->set($conn, $_POST['id'], $_POST['descripcion'], $_POST['orden']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oModulos->del($conn, $_POST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}

$cModulos=$oModulos->getAll($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de M&oacute;dulos</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cModulos)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cModulos as $modulos) { 
?> 
<tr class="filas"> 
<td><?=$modulos->id?></td>
<td><?=$modulos->descripcion?></td>
<td><a href="?accion=del&id=<?=$modulos->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$modulos->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
