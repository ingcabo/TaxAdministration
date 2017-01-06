<?
require ("comun/ini.php");
// Creando el objeto Uso de Vehiculo
$oVeh_tvehgaceta = new veh_tvehgaceta;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar' ){
	if($oVeh_tvehgaceta->add($conn, $_POST['descripcion'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oVeh_tvehgaceta->set($conn, $_POST['id'], $_POST['descripcion'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oVeh_tvehgaceta->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cVeh_tvehgaceta=$oVeh_tvehgaceta->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Tipo de Vehiculo Segun Gaceta</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_tvehgaceta)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>Codigo</td>
<td>Descripci&oacute;n</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cVeh_tvehgaceta as $veh_tvehgaceta) { 
?> 
<tr class="filas"> 
<td><?=$veh_tvehgaceta->id?></td>
<td><?=$veh_tvehgaceta->descripcion?></td>
<td align="center"><?php if($veh_tvehgaceta->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="veh_tvehgaceta.php?accion=del&id=<?=$veh_tvehgaceta->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>

<td align="center">
<a href="#" onclick="updater('<?=$veh_tvehgaceta->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_desc", "descripcion", "* Esta Vacio");
//$validator->create_message("error_nombre", "nombre_corto", "*");
$validator->print_script();
require ("comun/footer.php"); ?>
