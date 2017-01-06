<?
require ("comun/ini.php");
// Creando el objeto Desincorporacion de Vehiculo
$oVeh_sanciones = new veh_sanciones;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar' ){
	if($oVeh_sanciones->add($conn, $_POST['descripcion'], $status, $_POST[monto], date("Y")))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oVeh_sanciones->set($conn, $_POST['id'], $_POST['descripcion'], $status, $_POST[monto], date("Y")))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oVeh_sanciones->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cVeh_sanciones=$oVeh_sanciones->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Sanciones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_sanciones)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>Codigo</td>
<td>Descripci&oacute;n</td>
<td>Monto</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cVeh_sanciones as $veh_sanciones) { 
?> 
<tr class="filas"> 
<td><?=$veh_sanciones->id?></td>
<td><?=$veh_sanciones->descripcion?></td>
<td><?=$veh_sanciones->monto?></td>
<td align="left"><?php if($veh_sanciones->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="veh_sanciones.php?accion=del&id=<?=$veh_sanciones->id?>" onclick="if (confirm('Si presiona Aceptar ser� eliminada esta informaci�n')){ return true;} else{return false;}" title="Modificar � Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$veh_sanciones->id?>'); return false;" title="Modificar � Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
//$validator->create_message("error_nombre", "nombre_corto", "*");
$validator->print_script();
require ("comun/footer.php"); ?>
