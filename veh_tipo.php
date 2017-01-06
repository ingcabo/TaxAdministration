<?
require ("comun/ini.php");
// Creando el objeto Marca de Vehiculo
$oVeh_tipo = new veh_tipo;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar' ){
	if($oVeh_tipo->add($conn, $_POST['descripcion'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oVeh_tipo->set($conn, $_POST['id'], $_POST['descripcion'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oVeh_tipo->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cVeh_tipo=$oVeh_tipo->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Tipos de Vehiculos</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_tipo)){ ?>
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
foreach($cVeh_tipo as $veh_tipo) { 
?> 
<tr class="filas"> 
<td><?=$veh_tipo->id?></td>
<td><?=$veh_tipo->descripcion?></td>
<td align="center"><?php if($veh_tipo->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="veh_tipo.php?accion=del&id=<?=$veh_tipo->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$veh_tipo->id?>');  title="Modificar ó Actualizar Registro" > <img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

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
