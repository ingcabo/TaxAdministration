<?
require ("comun/ini.php");
// Creando el objeto Tasas bancarias
$oVeh_tasabanco = new veh_tasabanco;
$accion = $_REQUEST['accion'];

if($accion == 'Guardar' ){
	if($oVeh_tasabanco->add($conn, $_POST['mes'], $_POST['anio'], $_POST['monto']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oVeh_tasabanco->set($conn, $_POST['id'], $_POST['mes'], $_POST['anio'], $_POST['monto']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oVeh_tasabanco->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
$cVeh_tasabanco=$oVeh_tasabanco->get_all($conn);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro de Tasas Bancarias</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_tasabanco)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>Codigo</td>
<td>Mes</td>
<td>A�o</td>
<td>Monto</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cVeh_tasabanco as $veh_tasabanco) { 
?> 
<tr class="filas"> 
<td><?=$veh_tasabanco->id?></td>
<td><?=$veh_tasabanco->mes?></td>
<td><?=$veh_tasabanco->anio?></td>
<td><?=$veh_tasabanco->monto?></td>
<td align="center">
<a href="veh_tasabanco.php?accion=del&id=<?=$veh_tasabanco->id?>" onclick="if (confirm('Si presiona Aceptar ser� eliminada esta informaci�n')){ return true;} else{return false;}" title="Modificar � Actualizar Registro" ><img src="images/eliminar.gif" width="16" height="10" border="0"></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$veh_tasabanco->id?>'); return false;" title="Modificar � Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>

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

<?
$validator->create_message("error_mes", "mes", "* Este campo esta vaci�");
$validator->create_message("error_anio", "anio", "* Este campo esta vaci�");
$validator->create_message("error_monto", "monto", "* Este campo esta vaci�");
$validator->print_script();
require ("comun/footer.php"); ?>
