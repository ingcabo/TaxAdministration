<?
require ("comun/ini.php");
// Creando el objeto Tipo de Transacccion
$oVeh_tipotran = new $veh_tipotran;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar' ){
	if($oVeh_tipotran->add($conn, $_POST['descripcion'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oVeh_tipotran->set($conn, $_POST['id'], $_POST['descripcion'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oVeh_tipotran->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cVeh_tipotran=$oVeh_tipotran->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Tipo de Transacciones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_tipotran)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>id</td>
<td>Descripci&oacute;n</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cVeh_tipotran as $veh_tipotran) { 
?> 
<tr class="filas"> 
<td><?=$veh_tipotran->id?></td>
<td><?=$veh_tipotran->descripcion?></td>
<td align="left"><?php if($veh_tipotran->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$veh_tipotran->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="veh_tipotran.php?accion=del&amp;id=<?=$veh_tipotran->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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
//$validator->create_message("error_desc", "descripcion", "*");
//$validator->create_message("error_nombre", "nombre_corto", "*");
$validator->print_script();
require ("comun/footer.php"); ?>