<?
require ("comun/ini.php");
$oExpendios = new expendios;
$accion = $_REQUEST['accion'];
		if($accion == 'Guardar'){
			if($oExpendios->add($conn, $_POST['id_nuevo'], $_POST['nombre'], $_POST['direccion'], $_POST['representante']))
				$msj = REG_ADD_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'Actualizar'){
			if($oExpendios->set($conn, $_POST['id'], $_POST['nombre'], $_POST['direccion'], $_POST['representante']))
				$msj = REG_SET_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'del'){
			if($oExpendios->del($conn, $_GET['id']))
				$msj = REG_DEL_OK;
			else
				$msj = ERROR;
		}

$cExpendios=$oExpendios->get_all($conn);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>";} ?>
<br />
<span class="titulo_maestro">Maestro de Expendios </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cExpendios)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Codigo</td>
<td>Nombre del Establecimiento</td>
<td>Dirección Fiscal</td>
<td>Representante Legal</td>
</tr>
<? 
$i = 0;
foreach($cExpendios as $expendio){ 
?> 
<tr class="filas"> 
<td><?=$expendio->id?></td>
<td align="center"><?=$expendio->nombre?></td>
<td align="center"><?=$expendio->direccion?></td>
<td align="center"><?=$expendio->representante?></td>
<td align="center"><a href="?accion=del&id=<?=$expendio->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Eliminar Registro"><img  src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$expendio->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_nombre", "nombre", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); ?>