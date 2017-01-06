<?
$text_status='';
require ("comun/ini.php");
$oVeh_mod = new veh_modelo;
$accion = $_REQUEST['accion'];
		if($accion == 'Guardar'){
			if($oVeh_mod->add($conn, $_POST['mod_nom'], $_POST['status'], $_POST['cod_mod']))
				$msj = REG_ADD_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'Actualizar'){
			if($oVeh_mod->set($conn, $_POST['id'], $_POST['mod_nom'], $_POST['status'], $_POST['cod_mod']))
				$msj = REG_SET_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'del'){ 
			if($oVeh_mod->del($conn, $_GET['id']))
				$msj = REG_DEL_OK;
			else
				$msj = ERROR;
		}

$cVeh_mod=$oVeh_mod->get_all($conn);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>";} ?>
<br />
<span class="titulo_maestro">Maestro de Modelos </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_mod)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>Codigo:</td>
<td>Descripcion del Modelo:</td>
<td>Marca:</td>
<td>Activo:</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cVeh_mod as $veh_mod){ 
if($veh_mod->status ==1) { $text_status='Activo'; }else{ $text_status='Inactivo'; }
?> 
<tr class="filas"> 
<td><?=$veh_mod->id?></td>
<td align="left"><?=$veh_mod->mod_nom?></td>
<td align="left"><?=$veh_mod->marca->descripcion?></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="?accion=del&id=<?=$veh_mod->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$veh_mod->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_mod_nom", "mod_nom", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); ?>