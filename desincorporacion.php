<?
require ("comun/ini.php");
// Creando el objeto desincorporacion
$odesincorporacion = new desincorporacion;

$accion = $_REQUEST['accion'];




if($accion == 'Desincorporar'){
	if($odesincorporacion->add($conn, $_REQUEST['id_contribuyente'], $_REQUEST['fec_desinc'], $_REQUEST['cod_desinc'], $_REQUEST['id']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
		
}



//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cdesincorporacion=$odesincorporacion->get_all($conn, $start_record,$page_size);
$pag=new paginator($odesincorporacion->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

	//	$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
	//	$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro Desincorporaci&oacute;n de Veh&iacute;culos </span>
<center>
<div id='formulario'><!--a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a--></div>
</center>
<br />

<table style=" margin-left: auto; margin-right: auto; font-size:10px; " align="center" border="0">
  <tr>
    <td width="69">Buscar Seg&uacute;n:</td>
    <td width="62">
		<select name="tipo" id="tipo">
			<option value="">Seleccione...</option>
			<option value="rif/c">Rif/C&eacute;dula</option>
			<option value="placa">Placa</option>
			<option value="serial">Serial Motor</option>
		</select>
	</td>
    <td width="60"><input type="text" name="valor" id="valor" value="" size="15"></td>
    <td width="86"><input type="button" value="Buscar" onClick="gettpl($('tipo').value, $('valor').value)"></td>
  </tr>
</table>
<br>
<div id="resultado"></div>

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>


<? require ("comun/footer.php"); ?>


	<script type="text/javascript">
	
	function close_div2()
	{
		$('formulario').innerHTML = '';
	}
	
	function gettpl(tipo, valor)
	{
		var url = 'resultado_desincorporacion.php?';
		var pars = '&tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}
</script>

<?
$validator->create_message("error_placa", "placa", "*");
$validator->create_message("error_serial_carroceria", "serial_carroceria", "*");
$validator->create_message("error_serial_motor", "serial_motor", "*");
//$validator->create_message("error_anio_pago", "anio_pago", "*",1);
$validator->create_message("error_anio_veh", "anio_veh", "*");
$validator->create_message("error_fec_desinc", "fec_desinc", "*");
$validator->create_message("error_peso_kg", "peso_kg", "*");



$validator->print_script();




?>

