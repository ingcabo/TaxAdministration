<?
require ("comun/ini.php");
// Creando el objeto usuarios
$oUsuarios = new usuarios;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){
	$msj = $oUsuarios->add($conn,
								$_REQUEST['cargos'], 
								$_REQUEST['profesiones'], 
								$_REQUEST['unidades_ejecutoras'],
								$_REQUEST['rif_letra'],
								$_REQUEST['cedula'],
								$_REQUEST['nombre'],
								$_REQUEST['apellido'],
								$_REQUEST['login'],
								$_REQUEST['password'],
								$_REQUEST['status']);
}elseif($accion == 'Actualizar'){
	$msj = $oUsuarios->set($conn, 
								$_REQUEST['id'],
								$_REQUEST['cargos'], 
								$_REQUEST['profesiones'], 
								$_REQUEST['unidades_ejecutoras'],
								$_REQUEST['rif_letra'],
								$_REQUEST['cedula'],
								$_REQUEST['nombre'],
								$_REQUEST['apellido'],
								$_REQUEST['login'],
								$_REQUEST['password'],
								$_REQUEST['status']);
}elseif($accion == 'del'){
	$msj = $oUsuarios->del($conn, $_REQUEST['id']);
}

$cUsuarios=$oUsuarios->get_all($conn, $start_record,$page_size);
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Maestro de Usuarios</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cUsuarios)){ ?>
<table id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>Apellido</td>
<td>C&eacute;dula</td>
<td>Cargo</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cUsuarios as $usuarios) { 
?> 
<tr class="filas"> 
<td><?=$usuarios->id?></td>
<td><?=$usuarios->nombre?></td>
<td><?=$usuarios->apellido?></td>
<td><?=$usuarios->cedula?></td>
<td><?=$usuarios->cargo?></td>
<td><a href="?accion=del&id=<?=$usuarios->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$usuarios->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_nom", "nombre", "*");
$validator->create_message("error_apel", "apellido", "*");
$validator->create_message("error_log", "login", "*");
$validator->create_message("error_pass", "password", "*");
$validator->create_message("error_ced", "rif_letra", "*");
$validator->create_message("error_ced", "cedula", "*");
$validator->create_message("error_cargo", "cargos", "*");
$validator->create_message("error_prof", "profesiones", "*");
$validator->create_message("error_st", "status", "*",14);
$validator->print_script();
require ("comun/footer.php");
?>
