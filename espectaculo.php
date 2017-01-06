<?
require ("comun/ini.php");
// Creando el objeto publicidad
$oespectaculo = new espectaculo;
$today=date("d-m-Y"); 
$accion2= $_REQUEST['accion2']; 

if($accion2 == 'Pagar'){//die($_REQUEST['id']);
		
	if($oespectaculo->set_espectaculo($conn, $_REQUEST['id'], $_REQUEST['tipo_de_pago'], $_REQUEST['banco'],$_REQUEST['nro_documento'], $_REQUEST['monto']))
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}


$accion = $_REQUEST['accion'];
if($accion == 'Guardar'){//die($_REQUEST['entradas']);
			if($oespectaculo->add($conn, $_REQUEST['fec_ini'], $_REQUEST['fec_fin'], $_REQUEST['id_contribuyente'], $_REQUEST['lugar_evento'], $_REQUEST['cod_espectaculo'], $_REQUEST['patente'], $today, $_REQUEST['id_solicitud'], $_REQUEST['entradas']))
				$msj = REG_ADD_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'Actualizar'){
			if($oespectaculo->set($conn, $_REQUEST['id'], $_REQUEST['fec_ini'], $_REQUEST['fec_fin'], $_REQUEST['id_contribuyente'], $_REQUEST['lugar_evento'], $_REQUEST['cod_espectaculo'], $_REQUEST['patente'], $today, $_REQUEST['id_solicitud'], $_REQUEST['entradas']))
				$msj = REG_SET_OK;
			else
				$msj = ERROR;
		}elseif($accion == 'del'){ 
			if($oespectaculo->del($conn, $_GET['id']))
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

$cespectaculo=$oespectaculo->get_all($conn, $start_record,$page_size);
$pag=new paginator($oespectaculo->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<script>var mygrid,i=0</script>
<span class="titulo_maestro">Espect&aacute;culo</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<? if(is_array($cespectaculo)){ ?>
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
//********* Utilizado en el Form Para traer contribuyentes
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

//trae eventos
function traeLugarEvento(nodoEspectaculo, fechainicio, fechafin){
	var url = 'xmlTraeLugarEvento.php';
//	var fila = getFila(nodoEspectaculo.id);
	var pars = 'id=' + nodoEspectaculo.value + '&fec_ini=' + fechainicio + '&fec_fin=' + fechafin;//alert(pars);
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				alert(peticion.responseText)
			}
		});
}
//********** trae a los contribuyentes
function gettpl(tipo, valor)
	{
		var url = 'resultado_espectaculo.php';
		var pars = 'tipo='+tipo+'&valor='+valor;

		var myAjax = new Ajax.Updater(
			'resultado', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}  
function Agregar(){
	mygrid.addRow(i,",,,",i);
	i++;
}
function Eliminar(){
	mygrid.deleteRow(mygrid.getSelectedId());
}
//para guardar doatos desde aforo
function Guardar(){
var JsonAux,entradas=new Array;
	mygrid.clearSelection()
	for(j=0;j<i;j++){
		if(!isNaN(mygrid.getRowId(j))){
			entradas[j] = new Array;
			entradas[j][0]= mygrid.cells(mygrid.getRowId(j),0).getValue();
			entradas[j][1]= mygrid.cells(mygrid.getRowId(j),1).getValue();
			entradas[j][2]= mygrid.cells(mygrid.getRowId(j),2).getValue();			
		}
	}//alert(entradas);
	JsonAux={"entradas":entradas};
	$("entradas").value=JsonAux.toJSONString(); 
}

function realizarpago(id){//alert(id);
		var url = 'realizar.pago.espectaculo.php';
		var pars = 'id='+ id;
		var myAjax = new Ajax.Updater(
			'deuda', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
		
	}

function Pagar()
{
	var nodoMonto = document.getElementsByClassName('monto_fila');
	var sumaMonto = 0;
	$A(nodoMonto).each(function(e){ sumaMonto += parseFloat(usaFloat(e.value)); });
	if ( sumaMonto==parseFloat(usaFloat($('monto_total').value)) )
	{
		$('accion2').value="Pagar";
		document.form1.submit();
	//	document.location.href='recibo.pago.pdf.php?nro_recibo=';
	}
	else 
	{
		alert('Monto de las formas de pago difieren del monto total');
	}
}//alert($('accion2').value);
</script>

<?
require ("comun/footer.php");
?>