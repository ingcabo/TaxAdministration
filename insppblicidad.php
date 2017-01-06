<?
require ("comun/ini.php");
// Creando el objeto Inspector para Publicidad y Espectaculo
$oInsppblicidad = new insppblicidad;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar' ){
	if($oInsppblicidad->add($conn, $_POST['nombre'], $_POST['apellido'], $_POST['cedula'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oInsppblicidad->set($conn, $_POST['id'], $_POST['nombre'], $_POST['apellido'], $_POST['cedula'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oInsppblicidad->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cInsppblicidad=$oInsppblicidad->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Articulos para Sanciones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cInsppblicidad)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>id</td>
<td>Cedula</td>
<td>Nombre</td>
<td>Apellido</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cInsppblicidad as $insppblicidad) { 
?> 
<tr class="filas"> 
<td><?=$insppblicidad->id?></td>
<td><?=$insppblicidad->cedula?></td>
<td><?=$insppblicidad->nombre?></td>
<td><?=$insppblicidad->apellido?></td>
<td align="center"><?php if($insppblicidad->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$insppblicidad->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="insppblicidad.php?accion=del&id=<?=$insppblicidad->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_cedula", "cedula", "* Campo Vacio");
$validator->create_message("error_nombre", "nombre", "* Campo Vacio");
$validator->create_message("error_apellido", "apellido", "* Campo Vacio");
$validator->print_script();
require ("comun/footer.php"); ?>
