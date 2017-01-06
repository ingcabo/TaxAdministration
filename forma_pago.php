<?
require ("comun/ini.php");
// Creando el objeto forma_pago
$oforma_pago = new forma_pago;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar'){
	if($oforma_pago->add($conn, $_REQUEST['descripcion'], $_REQUEST['efectivo'], $_REQUEST['nombre_corto'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oforma_pago->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['efectivo'], $_REQUEST['nombre_corto'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oforma_pago->del($conn, $_REQUEST['id']))
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

$cforma_pago=$oforma_pago->get_all($conn, $start_record,$page_size);
$pag=new paginator($oforma_pago->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Formas de Pago </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cforma_pago)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Efectivo</td>
<td>Nombre Corto</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cforma_pago as $forma_pago) { 
?> 
<tr class="filas"> 
<td><?=$forma_pago->id?></td>
<td><?=$forma_pago->descripcion?></td>
<td align="center"><?=$forma_pago->efectivo?></td>
<td align="center"><?=$forma_pago->nombre_corto?></td>
<td align="center"><?php if($forma_pago->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="forma_pago.php?accion=del&id=<?=$forma_pago->id?>" onclick="if (confirm('Si presiona Aceptar sera eliminada esta informacion')){ return true;} else{return false;}" title="Eliminar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$forma_pago->id?>'); return false;" title="Modificar o Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

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
$validator->create_message("error_efectivo", "efectivo", "*");
$validator->create_message("error_nombre_corto", "nombre_corto", "*");

$validator->print_script();
?>
<? require ("comun/footer.php"); ?>