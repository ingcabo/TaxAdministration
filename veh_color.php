<?
$text_status='';
require ("comun/ini.php");
$oVeh_color = new veh_color;
$accion = $_REQUEST['accion'];
		if($accion == 'Guardar'){
			if($oVeh_color->add($conn, $_POST['color_nom'], $_POST['status']))
				$msj = REG_ADD_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'Actualizar'){
			if($oVeh_color->set($conn, $_POST['id'], $_POST['color_nom'], $_POST['status']))
				$msj = REG_SET_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'del'){ 
			if($oVeh_color->del($conn, $_GET['id']))
				$msj = REG_DEL_OK;
			else
				$msj = ERROR;
		}

$cVeh_color=$oVeh_color->get_all($conn);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>";} ?>
<br />
<span class="titulo_maestro">Maestro de Color </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cVeh_color)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1" align="center">
<tr class="cabecera"> 
<td>Codigo:</td>
<td>Descripcion del Color:</td>
<td>Estatus</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cVeh_color as $veh_color){ 
if($veh_color->status ==1) { $text_status='Activo'; }else{ $text_status='Inactivo'; }
?> 
<tr class="filas"> 
<td><?=$veh_color->id?></td>
<td align="left"><?=$veh_color->color_nom?></td>
<td align="center"><?=$text_status?></td>
<td align="center"><a href="?accion=del&id=<?=$veh_color->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$veh_color->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_color_nom", "color_nom", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); ?>