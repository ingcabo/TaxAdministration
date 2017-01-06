<?
require ("comun/ini.php");
// Creando el objeto tasa_inscripcion
$oentradas = new entradas;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if(empty($status)){ $status=0; }




if($accion == 'Guardar'){
	if($oentradas->add($conn, $_REQUEST['descripcion'], guardafloat($_REQUEST['monto']), $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oentradas->set($conn, $_REQUEST['id'],	$_REQUEST['descripcion'], guardafloat($_REQUEST['monto']), $status))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oentradas->del($conn, $_REQUEST['id']))
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

$centradas=$oentradas->get_all($conn, $start_record,$page_size);
$pag=new paginator($oentradas->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Entradas</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($centradas)){ ?>
<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Monto</td>
<td>Status</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($centradas as $entradas) { 
?> 
<tr class="filas"> 
<td><?=$entradas->id?></td>
<td align="left"><?=$entradas->descripcion?></td>
<td align="right"><?=muestrafloat($entradas->monto)?></td>
<td align="center"><?php if($entradas->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$entradas->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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