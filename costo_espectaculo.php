<?
require ("comun/ini.php");
// Creando el objeto costo_espectaculo
$ocosto_espectaculo = new costo_espectaculo;
$accion = $_REQUEST['accion'];
$status = $_REQUEST['status'];
if(empty($status)){$status==0;}
if($accion == 'Guardar'){ 
	if($ocosto_espectaculo->add($conn, $_REQUEST['descripcion'], guardafloat($_REQUEST['valor']), $status, $_REQUEST['categoria'], $_REQUEST['nacionalidad']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($ocosto_espectaculo->set($conn, $_REQUEST['id'], $_REQUEST['descripcion'], guardafloat($_REQUEST['valor']), $status, $_REQUEST['categoria'], $_REQUEST['nacionalidad']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($ocosto_espectaculo->del($conn, $_REQUEST['id']))
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

$ccosto_espectaculo=$ocosto_espectaculo->get_all($conn, $start_record,$page_size);

$pag=new paginator($ocosto_espectaculo->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro Tipo Espect&aacute;culo </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($ccosto_espectaculo)){ ?>
	<table align="center" class="sortable" id="grid" cellpadding="0" cellspacing="1">
		<tr class="cabecera"> 
			<td>C&oacute;digo</td>
			<td>Descripci&oacute;n</td>
			<td>Categoria</td>
			<td>Nacional / Internacional</td>
			<td>Valor Impuesto</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
<? 
$i = 0;
foreach($ccosto_espectaculo as $costo_espectaculo) { 
?> 
<tr class="filas"> 
			<td><?=$costo_espectaculo->id_espectaculo?></td>
			<td><?=$costo_espectaculo->descripcion?></td>
			<td><?=$costo_espectaculo->categoria?></td>
			<td><?=$costo_espectaculo->nacionalidad?></td>
<td align="right"><?=muestrafloat($costo_espectaculo->valor)?>&nbsp;<?php if($costo_espectaculo->valor=='p') { echo "U.T"; }else{ echo "%"; } ?></td>
<td align="center">
<a href="costo_espectaculo.php?accion=del&id=<?=$costo_espectaculo->id_espectaculo?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a>
			</td>
			<td align="center">
				<a href="#" onclick="updater('<?=$costo_espectaculo->id_espectaculo?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a>
			</td>
		</tr>
		<? $i++;
		}?>
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
	$validator->create_message("error_valor", "valor", "*");
	$validator->create_message("error_descripcion", "descripcion", "*");
	$validator->print_script();
 require ("comun/footer.php"); ?>
