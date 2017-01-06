<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto financiamiento
$oFinanciamiento = new financiamiento;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oFinanciamiento->add($conn,
			  			$_REQUEST['descripcion']); //,
						//$_REQUEST['genera_retenciones'],  
						//$_REQUEST['genera_pagos_parciales']);
}elseif($accion == 'Actualizar'){
	$msj = $oFinanciamiento->set($conn,
						$_REQUEST['id'], 
						$_REQUEST['descripcion']); //,
						//$_REQUEST['genera_retenciones'],  
						//$_REQUEST['genera_pagos_parciales']);
}elseif($accion == 'del'){
	$msj = $oFinanciamiento->del($conn, $_REQUEST['id']);
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cFinanciamiento=$oFinanciamiento->get_all($conn, $start_record,$page_size);
$pag=new paginator($oFinanciamiento->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();
require ("comun/header.php");
if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Financiamiento</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cFinanciamiento)){ ?>
<table id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cFinanciamiento as $financiamiento) { 
?> 
<tr class="filas"> 
<td><?=$financiamiento->id?></td>
<td><?=$financiamiento->descripcion?></td>
<td><a href="?accion=del&id=<?=$financiamiento->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$financiamiento->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_desc", "descripcion", "*");
//$validator->create_message("error_genr", "genera_retenciones", "*", 2);
//$validator->create_message("error_genpp", "genera_pagos_parciales", "*", 2);
$validator->print_script();
require ("comun/footer.php");
?>
