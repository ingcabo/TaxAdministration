<?
require ("comun/ini.php");
// Creando el objeto costo_publicidad
$ocosto_publicidad = new costo_publicidad;



$accion = $_REQUEST['accion'];

$status = $_REQUEST['status'];
//$text_status='';



if($accion == 'Guardar'){ 
	if($ocosto_publicidad->add($conn, $_REQUEST['descripcion'], guardafloat($_REQUEST['monto']), $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($ocosto_publicidad->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], guardafloat($_REQUEST['monto']), $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($ocosto_publicidad->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$ccosto_publicidad=$ocosto_publicidad->get_all($conn, $start_record,$page_size);

$pag=new paginator($ocosto_publicidad->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro Publicidad </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? 
if(is_array($ccosto_publicidad))
{ 
?>
	<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td>C&oacute;digo</td>
			<td>Descripci&oacute;n</td>
			<td>Status</td>
			<td>Monto</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<? 
			$i = 0;
			foreach($ccosto_publicidad as $costo_publicidad) { 
		?> 
		<tr class="filas"> 
			<td><?=$costo_publicidad->id_publicidad?></td>
			<td><?=$costo_publicidad->descripcion?></td>
			<td><?php if($costo_publicidad->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?> </td>
			<td align="right"><?=muestrafloat($costo_publicidad->monto)?></td>

<td align="center">
<a href="costo_publicidad.php?accion=del&id=<?=$costo_publicidad->id_publicidad?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
			</td>
			<td align="center">
				<a href="#" onclick="updater('<?=$costo_publicidad->id_publicidad?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
			</td>
		</tr>
		<? 
			$i++;
			}
		?>
	</table>
<? 
}
else 
{
	echo "No hay registros en la bd";
} 
?>
<br />
<div style="height:40px;padding-top:10px;">
	<p id="cargando" style="display:none;margin-top:0px;">
  		<img alt="Cargando" src="images/loading.gif" /> Cargando...
	</p>
</div>

<?
$validator->create_message("error_descripcion", "descripcion", "*");
$validator->create_message("error_monto", "monto", "*");
$validator->print_script();
 require ("comun/footer.php"); ?>
