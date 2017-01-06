<?
require ("comun/ini.php");
// Creando el objeto alcaldia
$oAlcaldia = new alcaldia;
$del = $_REQUEST['del'];
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oAlcaldia->add($conn, $_REQUEST['id_nuevo'],
						$_REQUEST['descripcion'], 
						$_REQUEST['razon'], 
						$_REQUEST['domicilio'], 
						$_REQUEST['fecha_creacion'], 
						$_REQUEST['ciudad'], 
						$_REQUEST['estado'], 
						$_REQUEST['telefono'], 
						$_REQUEST['fax'], 
						$_REQUEST['web_site'], 
						$_REQUEST['cpostal'], 
						$_REQUEST['alcalde'], 
						$_REQUEST['personal'], 
						$_REQUEST['concejales']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oAlcaldia->set($conn, $_REQUEST['id_nuevo'],
						$_REQUEST['id'],
						$_REQUEST['descripcion'], 
						$_REQUEST['razon'], 
						$_REQUEST['domicilio'], 
						$_REQUEST['fecha_creacion'], 
						$_REQUEST['ciudad'], 
						$_REQUEST['estado'], 
						$_REQUEST['telefono'], 
						$_REQUEST['fax'], 
						$_REQUEST['web_site'], 
						$_REQUEST['cpostal'], 
						$_REQUEST['alcalde'], 
						$_REQUEST['personal'], 
						$_REQUEST['concejales']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($del == 's'){
	if($oAlcaldia->del($conn, $_REQUEST['id']))
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

$cAlcaldia=$oAlcaldia->get_all($conn, $start_record,$page_size);
$pag=new paginator($oAlcaldia->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Alcald&iacute;a</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cAlcaldia)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>Fecha Creaci&oacute;n</td>
<td>Ciudad</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cAlcaldia as $alcaldia) { 
?> 
<tr class="filas"> 
<td><?=$alcaldia->id?></td>
<td><?=$alcaldia->descripcion?></td>
<td><?=$alcaldia->fecha_creacion?></td>
<td><?=$alcaldia->ciudad?></td>
<td><a href="?del=s&id=<?=$alcaldia->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$alcaldia->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<table width="762" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><span class="paginator"><? $pag->print_page_counter()?></span></td>
		<td align="right"><span class="paginator"><? $pag->print_paginator("pulldown")?> </span></td>
	</tr>
</table>
<?
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_nom", "descripcion", "*");
$validator->create_message("error_raz", "razon", "*");
$validator->create_message("error_dom", "domicilio", "*");
$validator->create_message("error_fechac", "fecha_creacion", "*");
$validator->create_message("error_cdad", "ciudad", "*");
$validator->create_message("error_edo", "estado", "*");
$validator->create_message("error_tlf", "telefono", "*");
$validator->create_message("error_postal", "cpostal", "*");
$validator->create_message("error_alcalde", "alcalde", "*");
$validator->create_message("error_personal", "personal", "*");
//$validator->create_message("error_concejales", "concejales", "*");
$validator->print_script();
require ("comun/footer.php");
?>