<?
require ("comun/ini.php");
// Creando el objeto profesiones
$oProfesiones = new profesiones;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oProfesiones->add($conn, $_REQUEST['id_nuevo'], $_REQUEST['descripcion']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oProfesiones->set($conn, $_REQUEST['id_nuevo'], $_REQUEST['id'], $_REQUEST['descripcion']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oProfesiones->del($conn, $_REQUEST['id']))
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

$cProfesiones=$oProfesiones->get_all($conn, $start_record,$page_size);
$pag=new paginator($oProfesiones->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de profesiones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cProfesiones)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cProfesiones as $profesiones) { 
?> 
<tr class="filas"> 
<td><?=$profesiones->id?></td>
<td><?=$profesiones->descripcion?></td>
<td><a href="?accion=del&id=<?=$profesiones->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$profesiones->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
