<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$odepartamento = new departamento;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj =$odepartamento->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['division'], $_REQUEST['estatus'], $_REQUEST['orden'],$_REQUEST['unidad']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj =$odepartamento->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['division'], $_REQUEST['estatus'], $_REQUEST['orden'],$_REQUEST['unidad']);
}elseif($accion == 'del'){
	$msj =$odepartamento->del($conn, $_REQUEST['int_cod']);
}

$cdepartamento=$odepartamento->get_all($conn, $_SESSION['EmpresaL'],'A.dep_ord',(empty($_POST['EstatusB']) ? 0 : $_POST['EstatusB']));
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Departamentos </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<form name="formAux" method="post">
<table border="0" style="margin-left:10px" width="800">
	<tr align="center" >
		<td>
			Buscar:&nbsp;&nbsp;&nbsp;
			<select name="EstatusB" >
				<option value=0 <?=$_POST['EstatusB']==0 ? "selected" : ""?>>Activo</option>
				<option value=1 <?=$_POST['EstatusB']==1 ? "selected" : ""?>>Inactivos</option>
				<option value=2 <?=$_POST['EstatusB']==2 ? "selected" : ""?>>Todos</option>
			</select>
			&nbsp;&nbsp;&nbsp;<input type="submit" value="Buscar">
		</td>
	</tr>
</table>
</form>
<? if(is_array($cdepartamento)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>Orden</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($cdepartamento as $departamento) { 
?> 
<tr class="filas"> 
<td><?=$departamento->dep_ord?></td>
<td align="center"><?=$departamento->dep_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$departamento->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$departamento->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
