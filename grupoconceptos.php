<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$ogrupoconceptos = new grupoconceptos;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj = $ogrupoconceptos->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj =$ogrupoconceptos->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], $status);
}elseif($accion == 'del'){
	$msj =$ogrupoconceptos->del($conn, $_REQUEST['int_cod']);
}

$cgrupoconceptos=$ogrupoconceptos->get_all($conn);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Grupos de Conceptos </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cgrupoconceptos)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cgrupoconceptos as $grupoconceptos) { 
?> 
<tr class="filas"> 
<td><?=$grupoconceptos->gconc_cod?></td>
<td align="center"><?=$grupoconceptos->gconc_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$grupoconceptos->int_cod?>" onclick="if (confirm('Si presiona Aceptar ser� eliminada esta informaci�n')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$grupoconceptos->int_cod?>'); return false;" title="Modificar � Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
$validator->create_message("error_codigo", "codigo", "Este campo no puede estar vacio");
$validator->create_message("error_desc", "nombre", "Este campo no puede estar vacio");
$validator->print_script();
require ("comun/footer.php"); ?>
