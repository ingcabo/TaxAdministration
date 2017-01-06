<?
require ("comun/ini.php");
// Creando el objeto publicidad
$oPublicidad = new publicidad;
$today=date("d-m-Y");

$accion3= $_POST['accion3']; 

if($accion3 == 'Pagar' ){//die('hdgjhgdhgdhgjd');
		
	if($oPublicidad->set_publicidad($conn, $_REQUEST['id'], $_REQUEST['tipo_de_pago'], $_REQUEST['banco'],$_REQUEST['nro_documento'], $_REQUEST['monto']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}

$accion = $_REQUEST['accion'];
// datos normal
if($accion == 'Guardar' ){
	if($oPublicidad->add($conn, $_REQUEST['id_contribuyente'], $_REQUEST['patente'], $_REQUEST['id_solicitud'], $_REQUEST['cod_ins'], 
							 $_REQUEST['fec_desde'], $_REQUEST['fec_hasta'], $today, $_REQUEST['publicidad']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oPublicidad->set($conn, $_POST['id'], $_REQUEST['id_contribuyente'], $_REQUEST['patente'], $_REQUEST['id_solicitud'], $_REQUEST['cod_ins'],  $_REQUEST['fec_desde'], $_REQUEST['fec_hasta'],$today, $_REQUEST['publicidad']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oPublicidad->del($conn, $_GET['id']))
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}
//Seccion paginador
$page_size = 10;
if ($_GET['pg'])
	$start_record=($_GET['pg'] * $page_size) - $page_size;
else
	$start_record=0;

$cPublicidad=$oPublicidad->get_all($conn, $start_record,$page_size);
$pag=new paginator($oPublicidad->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<script>var mygrid,i=0</script>
<span class="titulo_maestro">Publicidad</span>
<center>
<div style="text-align:left" id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
</center>
<br />
<? if(is_array($cPublicidad)){ ?>
<table style=" margin-left: auto; margin-right: auto; font-size:10px; " align="center" border="0">
  <tr>
    <td width="69">Buscar Seg&uacute;n:</td>
    <td width="62">
		<select name="tipo" id="tipo">
			<option value="">Seleccione...</option>
			<option value="patent">Patente</option>
		</select>
	</td>
    <td width="60"><input type="text" name="valor" id="valor" value="" size="15"></td>
    <td width="86"><input type="button" value="Buscar" onClick="gettpl($('tipo').value, $('valor').value)"></td>
  </tr>
</table>
<br>
<div id="resultado"></div>
<? }else {
		echo "No hay registros en la bd";
} ?>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<table width="762" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><span class="paginator"><? $pag->print_page_counter()?></span></td>
		<td align="right"><span class="paginator"><? $pag->print_paginator("pulldown")?> </span></td>
	</tr>
	<div id="pars"></div>
	<div id="xxx"></div>
</table>
<script type="text/javascript">
/***************   Publicidad   ******************************/
function Pagar()
{
	var nodoMonto = document.getElementsByClassName('monto_fila');
	var sumaMonto = 0;
	$A(nodoMonto).each(function(e){ sumaMonto += parseFloat(usaFloat(e.value)); });
	if ( sumaMonto==parseFloat(usaFloat($('monto_total').value)) )
	{
		$('accion3').value="Pagar";
		document.form1.submit();
		//alert($('accion3').value);
	}
	else 
	{
		alert('Monto de pago difieren del Monto total');
	}
}

function gettpl(tipo, valor)
	{
		var url = 'resultado_publicidad_fija.php';
		var pars = 'tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}  

function traeContribuyenteDesdeXML(id_contribuyente){
	var url = 'xmlTraeContribuyente.php'; 
	var pars = 'id=' + id_contribuyente;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeContribuyente
		});
}

function traeContribuyente(originalRequest){
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('contribuyente');
	for(j=0;j<x[0].childNodes.length;j++){ 
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
		//alert('nombre: ' + nombre + ' - valor: ' + $(nombre).value);
	}
}

function Agregar(){
	mygrid.addRow(i,",,,,",i);
	i++;
}
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
}

//para guardar doatos desde aforo
function Guardar(){
var JsonAux,publicidad=new Array;
	mygrid.clearSelection()
	for(j=0;j<i;j++){
		if(!isNaN(mygrid.getRowId(j))){
			publicidad[j] = new Array;
			publicidad[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
			publicidad[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();
			publicidad[j][2]= mygrid.cells(mygrid.getRowId(j),2).getValue();			
			publicidad[j][3]= mygrid.cells(mygrid.getRowId(j),3).getValue();

		}//alert(publicidad);
	}
	JsonAux={"publicidad":publicidad};
	$("publicidad").value=JsonAux.toJSONString(); 
}

function realizarpago(id){//alert(id);
		var url = 'realizar.pago.publicidad.php';
		var pars = 'id='+ id;
		var myAjax = new Ajax.Updater(
			'deuda', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}
</script>

<?
$validator->create_message("error_patente", "patente", "(*)");
/*$validator->create_message("error_fecha", "fecha", "(*)");
$validator->create_message("error_fecha_entrega", "fecha_entrega", "(*)");
$validator->create_message("error_lugar_entrega", "lugar_entrega", "(*)");
$validator->create_message("error_cond_pago", "condicion_pago", "(*)");
$validator->create_message("error_ue", "unidad_ejecutora", "(*)");
$validator->create_message("error_nro_req", "nro_requisicion", "(*)");*/
$validator->print_script();
require ("comun/footer.php");
?>