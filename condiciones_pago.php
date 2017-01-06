<?
require ("comun/ini.php");
// Creando el objeto tipos_pago
$oCondicionesPago = new condiciones_pago;
$accion = $_REQUEST['accion'];
	#ACCION AL MOMENTO DE GUARDAR#
	if($accion == 'Guardar'){
		
		if($oCondicionesPago->add($conn, $_POST['id_nuevo'], $_POST['descripcion'])){
			
			$msj = REG_ADD_OK;
		}
		else{
			
			$msj = CODIGO_YA_EXISTE;
		}
	
	#ACCION PARA ACTUALIZAR#		
	}elseif($accion == 'Actualizar'){
	
		if($oCondicionesPago->set($conn, $_POST['id_nuevo'], $_POST['id'], $_POST['descripcion']))
			$msj = REG_SET_OK;
		else
			$msj = ERROR;
	
	#ACCION PARA ELIMINAR#
	}elseif($accion == 'del'){
		
		if($oCondicionesPago->del($conn, $_REQUEST['id']))
			$msj = REG_DEL_OK;
		else
			$msj = ERROR;
			
	}

	$cCondicionesPago=$oCondicionesPago->get_all($conn);
	
	require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Maestro
de
Condiciones
de
Pago</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cCondicionesPago)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Descripción</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cCondicionesPago as $condicionesPago) { 
?> 
<tr class="filas"> 
<td><?=$condicionesPago->id?></td>
<td><?=$condicionesPago->descripcion?></td>
<td><a href="?accion=del&id=<?=$condicionesPago->id?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center">
<a href="#" onclick="updater('<?=$condicionesPago->id?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<?
$validator->create_message("error_cod", "id_nuevo", "*");
$validator->create_message("error_desc", "descripcion", "*");
$validator->print_script();
require ("comun/footer.php");
?>
