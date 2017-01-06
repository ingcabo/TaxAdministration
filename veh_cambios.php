<?
require ("comun/ini.php");
require ("comun/header.php");
// Creando el objeto vehiculo
$oVeh_cambios = new veh_cambios;

$accion = $_REQUEST['accion'];

$concesionario=$_REQUEST['concesionario'];
if(empty($concesionario))
{ 
	$concesionario='0'; 
}

$rif=$_REQUEST['rif_letra']."-".$_REQUEST['rif_numero']."-".$_REQUEST['rif_cntrl'];

$primera_vez=$_REQUEST['primera_vez'];
if(empty($primera_vez))
{ 
	$primera_vez=0; 
}

$anio_pago=$_REQUEST['anio_pago'];
if (empty($anio_pago))
{ 
	$anio_pago=0; 
}

$precio=guardafloat($_REQUEST['precio']);
if(empty($precio))
{ 
	$precio=0; 
}

$linea=guardafloat($_REQUEST['cod_lin']);
if(empty($linea))
{ 
	$linea=0; 
}


if(empty($_REQUEST['id_contribuyente_nue']))
{
	$id_contribuyente_nue = $_REQUEST['id_contribuyente'];
	$id_contribuyente = $_REQUEST['id_contribuyente'];
}
else
{
	$id_contribuyente = $_REQUEST['id_contribuyente'];
	$id_contribuyente_nue = $_REQUEST['id_contribuyente_nue'];
}

if(empty($_REQUEST['cod_col_nue']))
{
	$cod_col_nue = $_REQUEST['cod_col'];
	$cod_col = $_REQUEST['cod_col'];
}
else
{
	$cod_col = $_REQUEST['cod_col'];
	$cod_col_nue = $_REQUEST['cod_col_nue'];
}

if(empty($_REQUEST['serial_motor_nue']))
{
	$serial_motor_nue = $_REQUEST['serial_motor'];
	$serial_motor = $_REQUEST['serial_motor'];
}
else
{
	$serial_motor = $_REQUEST['serial_motor'];
	$serial_motor_nue = $_REQUEST['serial_motor_nue'];
}

if(empty($_REQUEST['placa_nue']))
{
	$placa_nue = $_REQUEST['placa'];
	$placa = $_REQUEST['placa'];
}
else
{
	echo $placa = $_REQUEST['placa'];
	echo $placa_nue = $_REQUEST['placa_nue'];
}


if($accion == 'Actualizar')
{ 
	$oVeh_cambios->add($conn, $id_contribuyente, $_REQUEST['serial_carroceria'], $placa, date('Y-m-d'), date('Y-m-d'), $_REQUEST['anio_veh'], $serial_motor, $_REQUEST['cod_mar'], $_REQUEST['cod_mod'], $cod_col, $_REQUEST['cod_uso'], $_REQUEST['cod_tip'], $_REQUEST['fec_compra'], guardafloat($_REQUEST['peso_kg']), 0, $precio, $_REQUEST['observacion'], $_REQUEST['cod_exo'], $anio_pago, $linea, $concesionario, $primera_vez);
			
	$oVeh_cambios->set($conn, $_REQUEST['id'], $id_contribuyente_nue, $_REQUEST['serial_carroceria'], $placa_nue, date('Y-m-d'), $_REQUEST['anio_veh'], $serial_motor_nue, $_REQUEST['cod_mar'], $_REQUEST['cod_mod'], $cod_col_nue, $_REQUEST['cod_uso'], $_REQUEST['cod_tip'], $_REQUEST['fec_compra'], guardafloat($_REQUEST['peso_kg']), 0, $precio,$_REQUEST['observacion'], $_REQUEST['cod_exo'], $anio_pago, $linea, $concesionario, $primera_vez);
		

}


if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Cambios </span>

<div id="formulario">
<!--<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>--></div>
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

<div id="pars"></div>
<? 
	require ("comun/footer.php");
?>

<script type="text/javascript">
	
	function gettpl(tipo, valor)
	{
		var url = 'resultado_veh_cambios.php';
		var pars = 'tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
	}  
	
	function realizar_cambio(cod_cambio)
	{
		//	alert(primera_vez+'<-oops');
		var url = 'realizar.cambio.php';
		var pars = 'cod_cambio='+cod_cambio;
		$('pars').innerHTML = pars;
	
		var myAjax = new Ajax.Updater(
			'deuda', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}
</script>



