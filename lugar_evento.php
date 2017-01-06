<?
require ("comun/ini.php");
// Creando el objeto Clasificacion Espectaculo
$olugar_evento = new lugar_evento;

$accion = $_REQUEST['accion'];

//$text_status='';
$status = $_REQUEST['status'];
if (empty($status))
{ 
$status=0; 
}


if($accion == 'Guardar'){ 
	if($olugar_evento->add($conn, $_REQUEST['descripcion'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($olugar_evento->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'],  $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($olugar_evento->del($conn, $_REQUEST['id']))
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

$clugar_evento=$olugar_evento->get_all($conn, $start_record,$page_size);

$pag=new paginator($olugar_evento->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro Lugar Evento </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? 
if(is_array($clugar_evento))
{ 
?>
	<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td>C&oacute;digo</td>
			<td>Descripci&oacute;n</td>
			<td>Status</td>
			<td>&nbsp;</td>
		</tr>
		<? 
			$i = 0;
			foreach($clugar_evento as $lugar_evento) { 
		?> 
		<tr class="filas"> 
			<td><?=$lugar_evento->id_lugar_evento?></td>
			<td><?=$lugar_evento->descripcion?></td>
			<td align="center"><?php if($lugar_evento->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
			<td align="center">
				<a href="#" onclick="updater('<?=$lugar_evento->id_lugar_evento?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
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
	//$validator->create_message("error_anio", "sdad", "*",1);
	$validator->create_message("error_descripcion", "descripcion", "*");
	
	/*$validator->create_message("error_desde", "fecha_desde", "*");
	$validator->create_message("error_hasta", "fecha_hasta", "*");*/
	$validator->print_script();
require ("comun/footer.php"); ?>
