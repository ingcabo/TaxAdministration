<?
require ("comun/ini.php");
// Creando el objeto publicidad
$oInscripcion_Publicidad = new inscripcion_publicidad;

$accion = $_REQUEST['accion'];
//die("s: ".$accion);

$today=date("d-m-Y");

									
//echo($accion);
if($accion == 'Guardar'){
	$status = 1;
	if($oInscripcion_Publicidad->add($conn, 
																		
																		guardafecha($today),
																		$_REQUEST['patente'],
																		$_REQUEST['id_contribuyente'],
																		$_REQUEST['id_solicitud'],
																		$_REQUEST['cod_ins'],
																		$_REQUEST['cod_publicidad'],
																		$status
																		)) 
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}elseif($accion == 'Actualizar'){
	if($oInscripcion_Publicidad->set($conn, 
														$_POST['id'],
														guardafecha($today),
																		$_REQUEST['desdemes'],
																		$_REQUEST['desdeanio'],			
																		$_REQUEST['hastames'],			
																		$_REQUEST['hastaanio'],					
																		$_REQUEST['patente'],						
																		$_POST['contribuyente'],																		
																		$_REQUEST['id_solicitud'],
																		$_POST['propaganda'],																		
																		$_POST['cant1'],
																		$_POST['unid1'],
																		$_POST['cant2'],
																		$_POST['unid2'],
																		$_POST['cant3'],
																		$_POST['unid3'],
																		$_POST['tot_med'],
																		$_POST['aforo'],
																		$_POST['precioTotalPu'],
																		$_REQUEST['espectaculo'],
																		guardafecha($_REQUEST['fec_ini']),
																		$_REQUEST['hor_ent'],
																		guardafecha($_REQUEST['fec_fin']),
																		$_REQUEST['hor_fin'],
																		$_REQUEST['entradas'],
																		$_POST['cant4'], 
																		$_POST['aforo1'],
																		$_POST['precioTotalEsp']))
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}elseif($accion == 'del'){
	if($oIncripcion_Publicidad->del($conn, $_GET['id']))
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

$cInscripcion_Publicidad=$oInscripcion_Publicidad->get_all($conn, $start_record,$page_size);
$pag=new paginator($oInscripcion_Publicidad->total,$page_size, self($_SERVER['SCRIPT_NAME']));
$i=$pag->get_total_pages();
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:none;"><?=$msj?></div><? } ?>
<br />
<span class="titulo_maestro">Inscripci&oacute;n Publicidad</span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
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




function publicidad_propaganda(publicidad, cod_inspector, id_solicitud)
	{
		var url = 'calculo_publicidad.php';
		var pars = 'publicidad='+publicidad+'&cod_inspector='+cod_inspector+'&solicitud='+id_solicitud;
		$('pars').innerHTML = pars;
		var myAjax = new Ajax.Updater(
			'formulario_publicidad', 
			url, 
			{
				method: 'get', 
				parameters: pars
			});
	}
	
function traePermisos(id_usuario, patente){
	if ((id_usuario == 1) || (id_usuario == 2))
	{
		var url = 'calculo_publicidad.php';
	}
	else  
	{
		var url = 'calculo_espectaculo.php';
	}
	var pars = 'id_usuario=' + id_usuario + '&patente=' + patente;
	var updater = new Ajax.Updater('formulario_publicidad', url,{
		parameters: pars,
		asynchronous:true, 
		evalScripts:true,
		onLoading:function(request){Element.show('cargando')}, 
		onComplete:function(request){Element.hide('cargando')},
		onSuccess:function(){ 
			new Effect.Highlight('formulario_publicidad', {startcolor:'#fff4f4', endcolor:'#ffffff'});
		} 
	}); 
} 

 

function traeContribuyenteDesdeXML(id_contribuyente){
	var url = 'xmlTraeContribuyente.php'; //alert(id_contribuyente);
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
function traePublicidadDesdeXML(nodoPublicidad, ut, tb, cant){
	var fila = getFila(nodoPublicidad.id); 
	var url = 'xmlTraePublicidad.php';
	var pars = 'id=' + nodoPublicidad.value + '&fila='+ fila + '&ut='+ ut + '&tb='+ tb + '&cant='+ cant;
	var salir = false;
	var listaNodos = document.getElementsByClassName('pubSeleccionada');
	var seleccionado = false;
	$A(listaNodos).each( function(e){
		if(nodoPublicidad.value == e.value){
			alert('Esta Publicidad ya ha sido seleccionada, por favor seleccione otra Publicidad');
			nodoPublicidad.value = 0;
			seleccionado = true;
			salir = true;
		}
	} );
	if(!seleccionado)
		$('intereses_' + fila).value = nodoPublicidad.value;
	if(!salir){
	//$('id_publicidad_' + fila).value = id_publicidad;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars,
				onComplete: function(originalRequest){
					var xmlDoc = originalRequest.responseXML;
					var x = xmlDoc.getElementsByTagName('publicidad');
					for(j=0;j<x[0].childNodes.length;j++){
						if (x[0].childNodes[j].nodeType != 1) continue;
						var nombre = x[0].childNodes[j].nodeName
						$(nombre).value = x[0].childNodes[j].firstChild.nodeValue; 
					}
					sumaSubTotal();
				}
			}
		);
	}
}


function traePublicidadEventualDesdeXML(nodoPublicidad, ut, cant, sancion){
	var fila = getFila(nodoPublicidad.id); 
	var url = 'xmlTraePublicidadEventual.php';
	var pars = 'id=' + nodoPublicidad.value + '&fila='+ fila + '&ut='+ ut + '&cant='+ cant + '&sancion=' + sancion;
	var salir = false;
	var listaNodos = document.getElementsByClassName('pubSeleccionada');
	var seleccionado = false;
	$A(listaNodos).each( function(e){
		if(nodoPublicidad.value == e.value){
			alert('Esta Publicidad ya ha sido seleccionada, por favor seleccione otra Publicidad');
			nodoPublicidad.value = 0;
			seleccionado = true;
			salir = true;
		}
	} );
	/*if(!seleccionado)
		$('intereses_' + fila).value = nodoPublicidad.value;*/
	if(!salir){
	//$('id_publicidad_' + fila).value = id_publicidad;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars,
				onComplete: function(originalRequest){
					var xmlDoc = originalRequest.responseXML;
					var x = xmlDoc.getElementsByTagName('publicidad');
					for(j=0;j<x[0].childNodes.length;j++){
						if (x[0].childNodes[j].nodeType != 1) continue;
						var nombre = x[0].childNodes[j].nodeName
						$(nombre).value = x[0].childNodes[j].firstChild.nodeValue; 
					}
					sumaSubTotal();
				}
			}
		);
	}
}


function traeEntradaDesdeXML(nodoEntradas){
	var fila = getFila(nodoEntradas.id); //alert(nodoEntradas.id); alert(nodoEntradas.value);
	var url = 'xmlTraeEntrada.php';
	var pars = 'id=' + nodoEntradas.value + '&fila='+ fila;
	var salir = false;
	var listaNodos = document.getElementsByClassName('entSeleccionada');
	var seleccionado = false;
	$A(listaNodos).each( function(e){
		if(nodoEntradas.value == e.value){
			alert('Esta Entrada ya ha sido seleccionada, por favor seleccione otra Entrada');
			$(nombre).value = 0;
			seleccionado = true;
			salir = true;
		}
	} );
	if(!seleccionado)
		$('entSeleccionada_' + fila).value = nodoEntradas.value;
	if(!salir){
	//$('id_publicidad_' + fila).value = id_publicidad;
		var myAjax = new Ajax.Request(
			url, 
			{
				method: 'get', 
				parameters: pars,
				onComplete: function(originalRequest){
					var xmlDoc = originalRequest.responseXML;
					var x = xmlDoc.getElementsByTagName('entradas');
					for(j=0;j<x[0].childNodes.length;j++){
						if (x[0].childNodes[j].nodeType != 1) continue;
						var nombre = x[0].childNodes[j].nodeName
						$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
					}
				}
			}
		);
	}
}
function traeEspectaculoDesdeXML(nodoEspectaculo){
	var url = 'xmlTraeEspectaculo.php';
	var fila = getFila(nodoEspectaculo.id);
	var pars = 'id=' + nodoEspectaculo.value;//alert(pars);
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeEspectaculo
		});
}

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

function traeEspectaculo(originalRequest){
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('espectaculo');
	for(j=0;j<x[0].childNodes.length;j++){ 
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
		//alert('nombre: ' + nombre + ' - valor: ' + $(nombre).value);
	}
}
function togglePublicidad(){
	var fija = document.getElementsByClassName('fija');
	var eventual = document.getElementsByClassName('eventual');
	var espectaculo = document.getElementsByClassName('espectaculo');
	var comun = document.getElementsByClassName('comun');
	var per = document.getElementsByClassName('per');
	var id_solicitud = $('id_solicitud').value;
	
	if(id_solicitud == '001'){
		$A(fija).each(function(el){  el.removeAttribute('disabled'); });
		$A(comun).each(function(el){  el.removeAttribute('disabled'); });
		$A(eventual).each( function(el){  el.removeAttribute('disabled','disabled'); });
		$A(per).each( function(el){  el.removeAttribute('disabled','disabled'); });
		$A(espectaculo).each( function(el){  el.setAttribute('disabled','disabled'); });
	}else if(id_solicitud == '002'){
		$A(eventual).each( function(el){  el.removeAttribute('disabled'); });
		$A(comun).each(function(el){  el.removeAttribute('disabled'); });		
		$A(fija).each( function(el){  el.removeAttribute('disabled','disabled'); });
		$A(espectaculo).each( function(el){  el.setAttribute('disabled','disabled'); });
	}else if(id_solicitud == '003'){
		$A(espectaculo).each( function(el){  el.removeAttribute('disabled'); });
		$A(fija).each( function(el){  el.setAttribute('disabled','disabled'); });
		$A(eventual).each( function(el){  el.setAttribute('disabled','disabled'); });
		$A(per).each( function(el){  el.removeAttribute('disabled','disabled'); });
		$A(comun).each( function(el){  el.setAttribute('disabled','disabled'); });		
	}else{
		$A(fija).each( function(el){ el.setAttribute('disabled','disabled'); });
		$A(eventual).each( function(el){  el.setAttribute('disabled','disabled'); });
		$A(espectaculo).each( function(el){  el.setAttribute('disabled','disabled'); });
		$A(comun).each( function(el){  el.setAttribute('disabled','disabled'); });
		$A(per).each( function(el){  el.setAttribute('disabled','disabled'); });
	}
}

function addPRE(){ 
	var x=$('tablitaPubFija').insertRow($('tablitaPubFija').rows.length);
	var i = $('tablitaPubFija').rows.length -1;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);
	var y5=x.insertCell(4);
	var y6=x.insertCell(5);
	var y7=x.insertCell(6);
	var y8=x.insertCell(7);
	var y9=x.insertCell(8);
	var y10=x.insertCell(9);

	var onblur;
	
	var propaganda = $('cod_publicidad_1').cloneNode(true);
	propaganda.setAttribute('id', 'cod_publicidad_' + i);
	propaganda.value = 0;
	y1.appendChild(propaganda);
	
	var cant1 = $('cant1_1').cloneNode(true);
	cant1.setAttribute('id', 'cant1_'+i);
	cant1.value = "";
	y2.appendChild(cant1);
	
	var unid1 = $('unid1_1').cloneNode(true);
	unid1.setAttribute('id', 'unid1_' + i);
	unid1.onchange = function(){			   		
								toggleFijaEventual($('cant1_'+i).value,this);
								montoTotal($('cant1_'+i).value,$('tot_med_'+i).value,$('aforo_'+i).value,this);
								sumaSubTotal();
					           };			
	unid1.value = "";
	y3.appendChild(unid1);
	
	/*var cant2 = $('cant2_1').cloneNode(false);
	cant2.setAttribute('id', 'cant2_' + i);
	cant2.value = "";
	y4.appendChild(cant2);
	
	var unid2 = $('unid2_1').cloneNode(true);
	unid2.setAttribute('id', 'unid2_' + i);
	unid2.value = "";
	y5.appendChild(unid2);
	
	var cant3 = $('cant3_1').cloneNode(false);
	cant3.setAttribute('id', 'cant3_' + i);

	cant3.onblur = function(){
										       tot_med_fija($('cant2_'+i),this,this.id);
											   montoTotal($('cant1_'+i).value,$('tot_med_'+i).value,$('aforo_'+i).value,this);
											   sumaSubTotal();
											};
	
	cant3.value = "";
	y6.appendChild(cant3);
	
	var unid3 = $('unid3_1').cloneNode(true);
	unid3.setAttribute('id', 'unid3_' + i);
	unid3.value = "";
	y7.appendChild(unid3);
	
	var tot_med = $('tot_med_1').cloneNode(true);
	tot_med.setAttribute('id', 'tot_med_' + i);
	tot_med.value = "";
	y8.appendChild(tot_med);*/
			
	var aforo = $('aforo_1').cloneNode(false);
	aforo.setAttribute('id', 'aforo_' + i);
	aforo.value = "";
	y8.appendChild(aforo);
	
	/*var pubSeleccionada = $('intereses_1').cloneNode(false);
	pubSeleccionada.setAttribute('id', 'intereses_' + i);
	pubSeleccionada.value = 0;
	y9.appendChild(pubSeleccionada);*/

	var total_pr = $('total_pr_1').cloneNode(false);
	total_pr.setAttribute('id', 'total_pr_' + i);	
	total_pr.value = 0;
	y9.appendChild(total_pr);
}
function delPRE() {
	if($('tablitaPubFija').rows.length <= 2){
		$('cod_publicidad_1').value = 0;
		$("cant1_1").value = "";
		$('unid1_1').value = "";
		$('aforo_1').value = "";
		$('total_pr_1').value = "";
		alert('No puede eliminar mas Publicidades Fijas');
	}else
		var x = $('tablitaPubFija').deleteRow($('tablitaPubFija').rows.length - 1);
}


function addPR(){ 
	var x=$('tablitaPubFija').insertRow($('tablitaPubFija').rows.length);
	var i = $('tablitaPubFija').rows.length -1;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);
	var y5=x.insertCell(4);
	var y6=x.insertCell(5);
	var y7=x.insertCell(6);
	var y8=x.insertCell(7);
	var y9=x.insertCell(8);
	var y10=x.insertCell(9);

	var onblur;
	
	var propaganda = $('cod_publicidad_1').cloneNode(true);
	propaganda.setAttribute('id', 'cod_publicidad_' + i);
	propaganda.value = 0;
	y1.appendChild(propaganda);
	
	var cant1 = $('cant1_1').cloneNode(true);
	cant1.setAttribute('id', 'cant1_'+i);
	cant1.value = "";
	y2.appendChild(cant1);
	
	var unid1 = $('unid1_1').cloneNode(true);
	unid1.setAttribute('id', 'unid1_' + i);
	unid1.onchange = function(){			   		
								toggleFijaEventual($('cant1_'+i).value,this);
								montoTotal($('cant1_'+i).value,$('tot_med_'+i).value,$('aforo_'+i).value,this);
								sumaSubTotal();
					           };			
	unid1.value = "";
	y3.appendChild(unid1);
	
	/*var cant2 = $('cant2_1').cloneNode(false);
	cant2.setAttribute('id', 'cant2_' + i);
	cant2.value = "";
	y4.appendChild(cant2);
	
	var unid2 = $('unid2_1').cloneNode(true);
	unid2.setAttribute('id', 'unid2_' + i);
	unid2.value = "";
	y5.appendChild(unid2);
	
	var cant3 = $('cant3_1').cloneNode(false);
	cant3.setAttribute('id', 'cant3_' + i);

	cant3.onblur = function(){
										       tot_med_fija($('cant2_'+i),this,this.id);
											   montoTotal($('cant1_'+i).value,$('tot_med_'+i).value,$('aforo_'+i).value,this);
											   sumaSubTotal();
											};
	
	cant3.value = "";
	y6.appendChild(cant3);
	
	var unid3 = $('unid3_1').cloneNode(true);
	unid3.setAttribute('id', 'unid3_' + i);
	unid3.value = "";
	y7.appendChild(unid3);
	
	var tot_med = $('tot_med_1').cloneNode(true);
	tot_med.setAttribute('id', 'tot_med_' + i);
	tot_med.value = "";
	y8.appendChild(tot_med);*/
			
	var aforo = $('aforo_1').cloneNode(false);
	aforo.setAttribute('id', 'aforo_' + i);
	aforo.value = "";
	y8.appendChild(aforo);
	
	var pubSeleccionada = $('intereses_1').cloneNode(false);
	pubSeleccionada.setAttribute('id', 'intereses_' + i);
	pubSeleccionada.value = 0;
	y9.appendChild(pubSeleccionada);

	var total_pr = $('total_pr_1').cloneNode(false);
	total_pr.setAttribute('id', 'total_pr_' + i);	
	total_pr.value = 0;
	y10.appendChild(total_pr);
}
function delPR() {
	if($('tablitaPubFija').rows.length <= 2){
		$('cod_publicidad_1').value = 0;
		$("cant1_1").value = "";
		$('unid1_1').value = "";
		$('aforo_1').value = "";
		$('total_pr_1').value = "";
		alert('No puede eliminar mas Publicidades Fijas');
	}else
		var x = $('tablitaPubFija').deleteRow($('tablitaPubFija').rows.length - 1);
}
function addEV(){ 
	var x=$('tablitaEvento').insertRow($('tablitaEvento').rows.length);
	var i = $('tablitaEvento').rows.length - 1;
	var y1=x.insertCell(0);
	var y2=x.insertCell(1);
	var y3=x.insertCell(2);
	var y4=x.insertCell(3);

	var onblur;
	
	var entradas = $('entradas_1').cloneNode(true);
	entradas.setAttribute('id', 'entradas_' + i);
	entradas.value = 0;
	y1.appendChild(entradas);
	
	var cant4 = $('cant4_1').cloneNode(false);
	cant4.setAttribute('id', 'cant4_' + i);

	cant4.onblur = function(){
										       montoTotalEsp($('cant4_'+i).value,$('aforo1_'+i).value,this);
											   sumaSubTotalEsp();
											   montoTotalEspEv($('subtotal').value,paforo3.value,aforo3.value);
											};
	
	cant4.value = "";
	y2.appendChild(cant4);
	
	var aforo1 = $('aforo1_1').cloneNode(true);
	aforo1.setAttribute('id', 'aforo1_' + i);
	aforo1.value = "";
	y3.appendChild(aforo1);
	
	var entSeleccionada = $('entSeleccionada_1').cloneNode(false);
	entSeleccionada.setAttribute('id', 'entSeleccionada_' + i);
	entSeleccionada.value = 0;
	y3.appendChild(entSeleccionada);

	var precioTotalEsp = $('precioTotalEsp_1').cloneNode(false);
	precioTotalEsp.setAttribute('id', 'precioTotalEsp_' + i);
	
	y4.appendChild(precioTotalEsp);

}

function delEV() {
	if($('tablitaEvento').rows.length <= 2){
		$('entradas_1').value = 0;
		$("cant4_1").value = "";
		$('aforo1_1').value = "";
		$('precioTotalEsp_1').value = "";
	alert('No puede eliminar mas Espectaculos Publicos');
	}else
		var x = $('tablitaEvento').deleteRow($('tablitaEvento').rows.length - 1);
}
function tot_med_fija(a,b){//calcula el producto del area de las publicidades
var tot_med
var fila= getFila(b.id)
//alert(b.id);
tot_med=a.value*b.value; 
$('tot_med_'+fila).value=tot_med;
//alert('$('cant2').value');
}
function sumaSubTotal(){//calcula el total de la publicidad fija
	var nodoSubTotal = document.getElementsByName('precioTotalPu[]');
	var sumaSubTotal = 0;
	$A(nodoSubTotal).each(function(e){ sumaSubTotal += parseFloat(usaFloat(e.value));});
	$('total_general_pub').value = muestraFloat(sumaSubTotal);
}

/*function total_impuesto(aforo, tb, ut, cant){
	var fila = getFila(aforo.id);
	impuesto_anual = (parseFloat(usaFloat(aforo)) * parseInt(ut));
	tasa_bancaria = tb/100; 
	intereses = (parseInt(cant)) * (parseFloat(tasa_bancaria) * 30 / 3600) * 1.2 * (parseFloat(impuesto_anual));
	$('total_pr_'+ fila).value = (parseFloat(impuesto_anual)) + (parseFloat(intereses));
}*/

	
function montoTotal(cant1,aforo,a,ut,tb){ //calcula el total de cada publicidad fija
	var fila = getFila(a.id); 
	impuesto_anual = (parseFloat(usaFloat(aforo)) * parseInt(ut));
	tasa_bancaria = tb/100; 
	$('intereses_'+ fila).value = muestraFloat(parseInt(cant1)) * (parseFloat(tasa_bancaria) * 30 / 3600) * 1.2 * (parseFloat(impuesto_anual));
	//$('total_pr_'+ fila).value = muestraFloat(parseFloat(impuesto_anual) + parseFloat($('intereses_'+ fila).value));
	$('total_pr_'+ fila).value = muestraFloat(parseInt(cant1)) * parseFloat(usaFloat(aforo));
}

function sumaSubTotalEsp(){//para mostrar el subtotal en espectaculos
	var nodoSubTotal = document.getElementsByName('precioTotalEsp[]');
	var sumaSubTotal = 0;
	$A(nodoSubTotal).each(function(e){ sumaSubTotal += parseFloat(usaFloat(e.value)); });
	$('subtotal').value = muestraFloat(sumaSubTotal);
}
function montoTotalEsp(cant4,aforo1,c){ //calcula el totalde cada evento
	var fila = getFila(c.id); //alert('fila= '+fila); alert('cantidad= '+c.value);
	var prec_tot_even = muestraFloat(parseInt(c.value) * parseFloat(usaFloat(aforo1)));
	$('precioTotalEsp_'+ fila).value = muestraFloat(parseInt(c.value) * parseFloat(usaFloat(aforo1)));
}
function montoTotalEspEv(subtotal,paforo3,aforo3){ //calcula el total de los eventos con el impuesto
	$('total_general_Ev').value = muestraFloat((parseFloat(usaFloat($('subtotal').value))*(parseFloat(usaFloat(paforo3)))/100)+parseFloat(usaFloat(aforo3)));
}
function toggleFijaEventual(cant1,a){
		var fila = getFila(a.id);
		var eventual = document.getElementsByClassName('eventual');
		var unid1 = $('unid1_'+ fila).value; 
		if(unid1 != '4'){//alert('unid1= '+unid1);
		$('unid2_'+ fila).value=""; 
		$('unid3_'+ fila).value="";
		$('tot_med_'+ fila).value=1;
		$A(eventual).each( function(el){ el.setAttribute('disabled','disabled'); });
}else{
		$A(eventual).each( function(el){  el.removeAttribute('disabled','disabled'); });
		$('tot_med_'+ fila).value=0;
}
	}
function getFila(nombre){
	var aNombre = nombre.split("_");
	return aNombre[aNombre.length - 1];
}
</script>

<?
/*$validator->create_message("error_cond_op", "condicion_operacion", "(*)");
$validator->create_message("error_fecha", "fecha", "(*)");
$validator->create_message("error_fecha_entrega", "fecha_entrega", "(*)");
$validator->create_message("error_lugar_entrega", "lugar_entrega", "(*)");
$validator->create_message("error_cond_pago", "condicion_pago", "(*)");
$validator->create_message("error_ue", "unidad_ejecutora", "(*)");
$validator->create_message("error_nro_req", "nro_requisicion", "(*)");*/
$validator->print_script();
require ("comun/footer.php");
?>