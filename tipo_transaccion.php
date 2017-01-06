<?
require ("comun/ini.php");
// Creando el objeto tipo_transaccion
$otipo_transaccion = new tipo_transaccion;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }

if($accion == 'Guardar'){
	if($otipo_transaccion->add($conn, $_REQUEST['descripcion'], $_REQUEST['partidas_presupuestarias'],  $_REQUEST['anio'], $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($otipo_transaccion->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], $_REQUEST['partidas_presupuestarias'],  $_REQUEST['anio'], $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($otipo_transaccion->del($conn, $_REQUEST['id']))
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

$ctipo_transaccion=$otipo_transaccion->get_all($conn, $start_record,$page_size);
$pag=new paginator($otipo_transaccion->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Tipos De Transacci&oacute;n </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($ctipo_transaccion)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>A&ntilde;o</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ctipo_transaccion as $tipo_transaccion) { 
?> 
<tr class="filas"> 
<td><?=$tipo_transaccion->id?></td>
<td align="center"><?=$tipo_transaccion->descripcion?></td>
<td align="center"><?=$tipo_transaccion->anio?></td>
<td align="center"><?php if($tipo_transaccion->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$tipo_transaccion->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<? require ("comun/footer.php"); ?>