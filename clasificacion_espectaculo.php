<?
require ("comun/ini.php");
// Creando el objeto Uso de Vehiculo
$oclasificacion_espectaculo = new clasificacion_espectaculo;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar' ){
	if($oclasificacion_espectaculo->add($conn, $_POST['descripcion'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oclasificacion_espectaculo->set($conn, $_POST['id'], $_POST['descripcion'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oclasificacion_espectaculo->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cclasificacion_espectaculo=$oclasificacion_espectaculo->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro Clasificacion de Espectaculo</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cclasificacion_espectaculo)){ ?>
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
foreach($cclasificacion_espectaculo as $clasificacion_espectaculo) { 
?> 
<tr class="filas"> 
<td><?=$clasificacion_espectaculo->id?></td>
<td><?=$clasificacion_espectaculo->descripcion?></td>
<td align="center"><?php if($clasificacion_espectaculo->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$clasificacion_espectaculo->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="clasificacion_espectaculo.php?accion=del&id=<?=$clasificacion_espectaculo->id?>" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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
