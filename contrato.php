<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
$ocontrato = new contrato;
$accion = $_REQUEST['accion'];
$status=$_REQUEST['status'];
if($accion == 'Guardar' and !empty($_REQUEST['codigo'])){
	$msj =$ocontrato->add($conn, $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['tipo'], $_REQUEST['fecha'], $_SESSION['EmpresaL'], $_REQUEST['estatus']);
}elseif($accion == 'Actualizar' and !empty($_REQUEST['codigo'])){
	$msj =$ocontrato->set($conn, $_REQUEST['int_cod'], $_REQUEST['codigo'], $_REQUEST['nombre'], $_REQUEST['tipo'], $_REQUEST['fecha'], $status, $_REQUEST['estatus']);
}elseif($accion == 'del'){
	$msj =$ocontrato->del($conn, $_REQUEST['int_cod']);
}

$ccontrato=$ocontrato->get_all($conn, $_SESSION['EmpresaL'], $_POST['TipoB'], $_POST['textAux'], $_POST['TipoBE']);
require ("comun/header.php");

if(!empty($msj)){ ?><div id="msj" ><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Nominas </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<form name="formAux" method="post">
<table align="center" border="0" style="margin-left:10px" width="800px">
	<tr  >
		<td width="200px"></td>
		<td align="center">
			<table align="center" border="0" style="margin-left:10px" width="400px">
				<tr>
					<td>Buscar por:</td>
					<td>
						<select name="TipoB" >
							<option value="0" <?=$_POST['TipoB']==0 ? "selected" : ""?>>C&oacute;digo</option>
							<option value="1" <?=$_POST['TipoB']==1 ? "selected" : ""?>>Nombre</option>
						</select>
					</td>
					<td><input type="text" name="textAux" value="<?=$_POST['textAux']?>"></td>
					<td><input type="submit" value="Buscar"></td>
				</tr>
				<tr>
					<td>Estatus:</td>
					<td colspan="3">
						<select name="TipoBE" onChange="document.formAux.submit();" >
							<OPTION <?=$_POST['TipoBE']==0 ? "selected" : "" ?> value='0'>Activo</OPTION>
							<OPTION <?=$_POST['TipoBE']=='1' ? "selected" : "" ?> value='1'>Inactivo</OPTION>
						</select>
					</td>
				</tr>
			</table>
		</td>
		<td></td>
	</tr>
</table>
</form>

<? if(is_array($ccontrato)){ ?>
<table class="sortable" id="grid" cellpadding="0" cellspacing="1">
<tr class="cabecera"> 
<td>C&oacute;digo</td>
<td>Nombre</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<? 
$i = 0;
foreach($ccontrato as $contrato) { 
?> 
<tr class="filas"> 
<td><?=$contrato->cont_cod?></td>
<td align="center"><?=$contrato->cont_nom?></td>
<td align="center"><a href="?accion=del&int_cod=<?=$contrato->int_cod?>" onclick="if (confirm('Si presiona Aceptar será eliminada esta información')){ return true;} else{return false;}"  title="Eliminar Registro"><img src="images/eliminar.gif" border="0" ></a></td>
<td align="center"><a href="#" onclick="updater('<?=$contrato->int_cod?>'); return false;" title="Modificar ó Actualizar Registro" ><img src="images/actualizar.gif" width="16" height="10" border="0"></a></td>
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
<script type="text/javascript" >
var Bandera=true;
function ValidarTipoContrato(){
var FechaAux;
	FechaAux=$('fecha').value;
	FechaAux=FechaAux.split("/");
	Bandera=true;
	if($('tipo').options[$('tipo').selectedIndex].value=='1' && FechaAux[0]!='01' && FechaAux[0]!='16'){
		alert("Para contratos quicenales los dias de inicio de nomina son 01 y 16");
		Bandera=false;
	}
	if($('tipo').options[$('tipo').selectedIndex].value=='2' && FechaAux[0]!='01'){
		alert("Para contratos mensuales el dias de inicio de nomina es 01");
		Bandera=false;
	}
}
</script>
