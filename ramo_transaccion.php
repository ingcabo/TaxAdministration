<?
require ("comun/ini.php");
// Creando el objeto ramo_transaccion
$oramo_transaccion = new ramo_transaccion;
$accion = $_REQUEST['accion'];

$oramo_transaccion->get($conn, $_REQUEST['id']);


if($accion == 'Guardar'){

	if($oramo_transaccion->add($conn, $_REQUEST['id_ramo_imp'], $_REQUEST['anio'],  $_REQUEST['tipo_transaccion']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
		
}elseif($accion == 'Actualizar'){
	if($oramo_transaccion->set($conn, $_REQUEST['id'], $_REQUEST['id_ramo_imp'], $_REQUEST['anio'],  $_REQUEST['tipo_transaccion']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oramo_transaccion->del($conn, $_REQUEST['id']))
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

$cramo_transaccion=$oramo_transaccion->get_all($conn, $start_record,$page_size);
$pag=new paginator($oramo_transaccion->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");

		//$validator->create_message("id_title", "email", "(Inv&iacute;lido)", 3); //CREO EL MENSAJE DE VALIDACION Y EL OBJETO QUE VOY A CONTROLAR
		//$validator->print_script();  //IMPRIMO EL SCRIPT
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? echo "<br>"; } ?>
<br />
<span class="titulo_maestro">Maestro de Ramo Transacci&oacute;n</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />


<table style=" margin-left: auto; margin-right: auto; font-size:10px; " border="0">
<tr>
<td >
Buscar Seg&uacute;n: <select name="tipo" id="tipo" onChange="traecombo(this.value);">
						<option value="0">Seleccione</option>
						<option value="1">Ramo</option>
						<option value="2">Transacci&oacute;n</option>
						<option value="3">A&ntilde;o</option>
					 </select> 
</td>
<td>
 <div id="combo_select"></div>	
</td>
<td></td>
</tr>
</table>


<div id="resultado"></div>		 
					

<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<!-- <a href="#" onclick="alert($('formulario').innerHTML)">AAAA</a> -->
<? require ("comun/footer.php"); ?>

<script language="javascript"> 


	function trae_resultado(tipo, valor){
		var url = 'ramo_transaccion.resultado.php?';
		var pars = 'tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
	
	}

	function traecombo(tipo)
	{
		var url = 'ramo_transaccion.combo.php?';
		var pars = 'tipo='+tipo;

		var myAjax = new Ajax.Updater(
			'combo_select', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}

var i = 1
function addTR(){
	var x=$('tablita').insertRow($('tablita').rows.length)
	var y1=x.insertCell(0)

	var cp = $('tipo_transaccion').cloneNode(true)
	
	y1.appendChild(cp)

	i++
}
</script>