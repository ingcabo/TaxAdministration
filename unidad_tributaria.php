<?
require ("comun/ini.php");
// Creando el objeto unidad tributaria
$ounidad_tributaria = new unidad_tributaria;

$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];

if (empty($status))
{ 
	$status=0; 
}


$precio=guardafloat($_REQUEST['precio']);

if (empty($precio))
{ 
	$precio=0; 
}


if($accion == 'Guardar'){
	if($ounidad_tributaria->add($conn, guardafecha($_REQUEST['fecha_desde']), guardafloat($_REQUEST['monto']), $status))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($ounidad_tributaria->set($conn, $_REQUEST['id'], guardafecha($_REQUEST['fecha_desde']), guardafecha($_REQUEST['fecha_hasta']), guardafloat($_REQUEST['monto']), $status ))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($ounidad_tributaria->del($conn, $_REQUEST['id']))
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

$cunidad_tributaria=$ounidad_tributaria->get_all($conn, $start_record,$page_size);
$pag=new paginator($ounidad_tributaria->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Unidad Tributaria </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cunidad_tributaria)){ ?>
<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Fecha Desde</td>
<td>Fecha Hasta </td>
<td>Monto</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cunidad_tributaria as $unidad_tributaria) { 
?> 
<tr class="filas"> 
<td><?=$unidad_tributaria->id?></td>
<td align="center"><?=muestrafecha($unidad_tributaria->fecha_desde)?></td>
<td align="center"><?=muestrafecha($unidad_tributaria->fecha_hasta)?></td>
<td align="right"><?=muestrafloat($unidad_tributaria->monto)?></td>
<td align="center"><?php if($unidad_tributaria->status==1) { echo "Activo"; }else{ echo "Inactivo"; } ?></td>
<td align="center">
<a href="#" onclick="updater('<?=$unidad_tributaria->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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