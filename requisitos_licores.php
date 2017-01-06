<?
require ("comun/ini.php");
$oRequisitos_licores= new requisitos_licores;

$accion = $_REQUEST['accion'];
		if($accion == 'Guardar'){
			if($oRequisitos_licores->add($conn, $_POST['id_nuevo'], $_POST['requisito']))
				$msj = REG_ADD_OK; 
			else
				$msj = ERROR;
		}elseif($accion == 'Actualizar'){
			if($oRequisitos_licores->set($conn, $_POST['id'], $_POST['requisito']))
				$msj = REG_SET_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'del'){
			if($oRequisitos_licores->del($conn, $_GET['id']))
				$msj = REG_DEL_OK;
			else
				$msj = ERROR;
		}

$cRequisitos_licores=$oRequisitos_licores->get_all($conn);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>";}?>
<br />
<span class="titulo_maestro">Maestro de Requisitos para Solicitud de Licores  </span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cRequisitos_licores)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Solicitud Nº</td>
<td>Requisitos de la Solicitud</td>
</tr>
<? 
$i = 0;
foreach($cRequisitos_licores as $requisitos_licores){
?> 
<tr class="filas"> 
<td><?=$requisitos_licores->id?></td>
<td align="center"><?=$requisitos_licores->requisito?></td>
<td align="center"><a href="" onClick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}" title="Eliminar Registro"><img  src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onClick="updater('<?=$requisitos_licores->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_id_nuevo", "id_nuevo", "Este campo no puede estar vacio");
$validator->create_message("error_requisito", "requisito", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); ?>