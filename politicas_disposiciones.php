<?
require ("comun/ini.php");
// Creando el objeto politicas_disposiciones
$oPoliticasDisposiciones = new politicas_disposiciones;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	if($oPoliticasDisposiciones->add($conn, 
									$_REQUEST['escenarios'], 
									$_REQUEST['gacetas'], 
									$_REQUEST['ano'], 
									$_REQUEST['texto1'], 
									$_REQUEST['texto2'], 
									$_REQUEST['texto3'], 
									$_REQUEST['texto4']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oPoliticasDisposiciones->set($conn, 
									$_REQUEST['id'],
									$_REQUEST['escenarios'], 
									$_REQUEST['gacetas'], 
									$_REQUEST['ano'], 
									$_REQUEST['texto1'], 
									$_REQUEST['texto2'], 
									$_REQUEST['texto3'], 
									$_REQUEST['texto4']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oPoliticasDisposiciones->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}else
	$oPoliticasDisposiciones->get($conn, $id, $escEnEje);

if(!empty($id))
	$boton="Actualizar";
else
	$boton = "Guardar";
require ("comun/header.php");
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cPoliticasDisposiciones=$oPoliticasDisposiciones->get_all($conn, $start_record,$page_size,$escEnEje);
$pag=new paginator($oPoliticasDisposiciones->total,$page_size, self($_SERVER['PHP_SELF']));
$i=$pag->get_total_pages();

?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<script src="js/tabpanel.js" type="text/javascript"></script>
<br />
<span class="titulo_maestro">Maestro de Politicas / Disposiciones</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cPoliticasDisposiciones)){ ?>
<div id="contenidobusqueda">
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Escenario</td>
<td>A&ntilde;o</td>
<td>Tipo de Gaceta</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cPoliticasDisposiciones as $politicasDisposiciones) { 
?> 
<tr class="filas"> 
<td><?=$politicasDisposiciones->id?></td>
<td><?=$politicasDisposiciones->escenario?></td>
<td><?=$politicasDisposiciones->ano?></td>
<td><?=$politicasDisposiciones->tipo_gaceta?></td>
<td><a href="?accion=del&id=<?=$politicasDisposiciones->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$politicasDisposiciones->id?>')"  title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
</tr>
<? $i++;
	}
?>
</table>
</div>
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
<? require ("comun/footer.php");?>
