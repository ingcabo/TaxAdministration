<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$odivision = new division;
$accion = $_REQUEST['accion'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj =$odivision->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre'], $_SESSION['EmpresaL']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj =$odivision->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre']);
}elseif($accion == 'del'){
	$msj =$odivision->del($conn, $_REQUEST['int_cod']);
}

$cdivision=$odivision->get_all($conn,$_SESSION['EmpresaL']);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Divisiones </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<? if(is_array($cdivision)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cdivision as $division) { 
?> 
<tr class="filas"> 
<td><?=$division->div_cod?></td>
<td align="center"><?=$division->div_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$division->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$division->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
