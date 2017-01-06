<?
require ("comun/ini.php");
// Creando el objeto contribuyente
$oContribuyente = new contribuyente;
$accion = $_REQUEST['accion'];
$today=date("d/m/Y");
$contribuyente=$_REQUEST['contribuyente'];
if(empty($contribuyente)){ $contribuyente="N"; }

$fec_nacimiento=guardafecha($_REQUEST['fec_nacimiento']);
$fec_registro=guardafecha($_REQUEST['fec_registro']);
$fec_desincorporacion=guardafecha($_REQUEST['fec_desincorporacion']);

if($rif='--'){ $rif=''; }

if($accion == 'Guardar'){
	if($oContribuyente->add($conn, $_REQUEST['tipo_persona'], $_REQUEST['nacionalidad'], $_REQUEST['tipo_ident'], $_REQUEST['identificacion'],
							$contribuyente, $_REQUEST['razon_social'], $fec_nacimiento, $_REQUEST['pasaporte'], $_REQUEST['primer_nombre'], 
							$_REQUEST['segundo_nombre'], $_REQUEST['primer_apellido'], $_REQUEST['segundo_apellido'], $_REQUEST['direccion'],
							$_REQUEST['domicilio_fiscal'], $fec_registro, $fec_desincorporacion, $_REQUEST['direccion_eventual'], $_REQUEST['telefono'],
							$_REQUEST['celular'], $_REQUEST['email'], $_REQUEST['rif_letra'], $_REQUEST['rif_numero'], $_REQUEST['rif_cntrl'], 
							$_REQUEST['rif_letra']."-".$_REQUEST['rif_numero']."-".$_REQUEST['rif_cntrl'], $_REQUEST['fax'], $_REQUEST['estado'], 
							 $_REQUEST['municipios'], $_REQUEST['parroquias'] ))
		$msj = REG_ADD_OK;
	else
	//if (DUPLICATED){ $msj='Oops';}
		 $msj = DUPLICATED;
}elseif($accion == 'Actualizar'){
	if($oContribuyente->set($conn, $_REQUEST['id'], $_REQUEST['tipo_persona'], $_REQUEST['nacionalidad'], $_REQUEST['tipo_ident'], $_REQUEST['identificacion'],
							$contribuyente, $_REQUEST['razon_social'], $fec_nacimiento, $_REQUEST['pasaporte'], $_REQUEST['primer_nombre'], 
							$_REQUEST['segundo_nombre'], $_REQUEST['primer_apellido'], $_REQUEST['segundo_apellido'], $_REQUEST['direccion'],
							$_REQUEST['domicilio_fiscal'], $fec_registro, $fec_desincorporacion, $_REQUEST['direccion_eventual'], $_REQUEST['telefono'],
							$_REQUEST['celular'], $_REQUEST['email'], $_REQUEST['rif_letra'], $_REQUEST['rif_numero'], $_REQUEST['rif_cntrl'], 
							$_REQUEST['rif_letra']."-".$_REQUEST['rif_numero']."-".$_REQUEST['rif_cntrl'], $_REQUEST['fax'], $_REQUEST['estado'], 
							 $_REQUEST['municipios'], $_REQUEST['parroquias'] ))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oContribuyente->del($conn, $_REQUEST['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$page_size = 25;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cContribuyente=$oContribuyente->get_all($conn, $start_record,$page_size);
$pag=new paginator($oContribuyente->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Contribuyentes </span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<table align="center" style=" margin-left: auto; margin-right: auto; font-size:10px; " border="0">
  <tr>
    <td width="69">Buscar Seg&uacute;n:</td>
    <td width="62">
		<select name="tipo" id="tipo">
			<option value="">Seleccione...</option>
			<option value="rif/c">Identificaci&oacute;n</option>
			<option value="nombre">Nombre/Apellido</option>
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

<script>
	function show_rif() {
		$('C').disabled=true;
		$('P').disabled=true;
		$('S/I').disabled=true;
		$('C').value='';
		$('P').value='';
		$('S/I').value='';
	
		$('R').disabled=false;
		$('rif_letra').disabled=false;
		//$('rif_letra').value='J';		

		$('rif_numero').disabled=false;
		$('rif_cntrl').disabled=false;

		$('identificacion').disabled=false;
		$('identificacion').value='';

		$('razon_social').disabled=false;

		('fec_nacimiento').disabled=true;
		('fec_nacimiento').value='';
	
		$('primer_nombre').disabled=true;
		$('primer_apellido').disabled=true;
		$('segundo_nombre').disabled=true;
		$('segundo_apellido').disabled=true;
	
		$('primer_nombre').value='';
		$('primer_apellido').value='';
		$('segundo_nombre').value='';
		$('segundo_apellido').value='';


		$('nacionalidadv').disabled=false;
		$('nacionalidade').disabled=false;

		$('pasaporte').disabled=true;
		$('pasaporte').value='';

		$('direccion_eventual').disabled=false;
		$('domicilio_fiscal').disabled=false;

	
	}
	
	function hide_rif() {
		$('R').checked=false;
		$('R').disabled=false;
		$('C').disabled=false;
		$('P').disabled=false;
		$('S/I').disabled=false;
		$('rif_letra').disabled=true;
		
		$('rif_numero').disabled=true;
		$('rif_cntrl').disabled=true;
		$('identificacion').disabled=false;
	
		('fec_nacimiento').disabled=false;

		$('razon_social').disabled=true;
		$('razon_social').value='';

		$('primer_nombre').disabled=false;
		$('primer_apellido').disabled=false;
		$('segundo_nombre').disabled=false;
		$('segundo_apellido').disabled=false;

		$('nacionalidadv').disabled=false;
		$('nacionalidade').disabled=false;
		$('pasaporte').disabled=false;

		$('direccion_eventual').disabled=true;
		$('domicilio_fiscal').disabled=true;

	}

	function hide_campos_rif() {
		$('rif_letra').disabled=true;
		$('rif_numero').disabled=true;
		$('rif_cntrl').disabled=true;
		$('identificacion').disabled=false;
	}

	function unhide_campos_rif() {
		$('rif_letra').disabled=false;
		$('rif_numero').disabled=false;
		$('rif_cntrl').disabled=false;
		$('identificacion').disabled=false;
	}

	function rif_ident(){//el valor del rif lo coloco en identificacion
		
		$('identificacion').value=$('rif_letra').value+'-'+$('rif_numero').value+'-'+$('rif_cntrl').value;
		
	}

function gettpl(tipo, valor)
	{ 
		var url = 'resultado_contribuyente.php?';
		var pars = 'tipo='+tipo+'&valor='+valor;
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
$validator->create_message("error_identificacion", "identificacion", "* Campo Requerido");
//$validator->create_message("error_primer_nombre", "primer_nombre", "* Campo Requerido");
//$validator->create_message("error_primer_apellido", "primer_apellido", "* Campo Requerido");	
//$validator->create_message("error_razon_social", "razon_social", "* Campo Requerido");
//$validator->create_message("error_rif_numero", "rif_numero", "* Campo Requerido");
$validator->create_message("error_direccion", "direccion", "* Campo Requerido");
$validator->create_message("error_tipo_persona", "tipo_persona", "* Campo Requerido",2);
$validator->create_message("error_tipo_ident", "tipo_ident", "* Campo Requerido",2);
$validator->create_message("error_nacionalidad", "nacionalidad", "* Campo Requerido",2);
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>