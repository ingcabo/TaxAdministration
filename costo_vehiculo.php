<?
require ("comun/ini.php");
// Creando el objeto costo_vehiculo
$ocosto_vehiculo = new costo_vehiculo;
$accion = $_REQUEST['accion'];


$precio=guardafloat($_REQUEST['precio']);
if(empty($precio)){ $precio=0; }

if($accion == 'Guardar' and $_REQUEST['cod_veh']!=0){
	if($ocosto_vehiculo->add($conn, $_REQUEST['cod_veh'], guardafloat($_REQUEST['monto']), guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta'])))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($ocosto_vehiculo->set($conn, $_REQUEST['id'],$_REQUEST['cod_veh'], guardafloat($_REQUEST['monto']), guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta'])))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($ocosto_vehiculo->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
/*Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;
$ccosto_vehiculo=$ocosto_vehiculo->get_all($conn, $start_record,$page_size);
$pag=new paginator($ocosto_vehiculo->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
*/
$ccosto_vehiculo=$ocosto_vehiculo->get_all($conn);
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Costos de Veh&iacute;culos seg&uacute;n Gaceta</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($ccosto_vehiculo)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Tipo Vehiculo</td>
<td>Monto</td>
<td>Fecha Desde</td>
<td>Fecha Hasta </td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ccosto_vehiculo as $costo_vehiculo) { 
?> 
<tr class="filas"> 
<td><?=$costo_vehiculo->id?></td>
<td><?=$costo_vehiculo->desc_veh?></td>
<td align="right"><?=muestrafloat($costo_vehiculo->monto)?></td>
<td align="center"><?=muestrafecha($costo_vehiculo->fecha_desde)?></td>
<td align="center"><?=muestrafecha($costo_vehiculo->fecha_hasta)?></td>
<td align="center">
<a href="#" onclick="updater('<?=$costo_vehiculo->id?>'); return false;"  title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="costo_vehiculo.php?accion=del&id=<?=$costo_vehiculo->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Modificar ó Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_monto", "monto", "* Requerido",1);
$validator->create_message("error_desde", "fecha_desde", "*");
$validator->create_message("error_hasta", "fecha_hasta", "*");
$validator->print_script();
?>

<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<? require ("comun/footer.php"); ?>
