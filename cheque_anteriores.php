<?
require ("comun/ini.php");
// Creando el objeto solicitud de pago

$oCheque = new cheque_anteriores;
$accion = $_REQUEST['accion'];
$hoy = date('Y-m-d');	
#ACCION DE GUARDAR EL CHEQUE#
if($accion == 'Guardar'){
	$oCheque->add($conn,$_REQUEST['banco'],$_REQUEST['nro_cuenta'],$_REQUEST['nrocheque'],$_REQUEST['id_proveedor'],guardafecha($_REQUEST['fecha']),0,$_REQUEST['contenedor_ordenes'],$usuario->id,$escEnEje,$anoCurso,$_REQUEST['observacion'],$_REQUEST['total_cheque'],$_REQUEST['nomBenef'],$_REQUEST['concepto']);
}elseif($accion == 'Actualizar'){
	$oCheque->Anular($conn,$_REQUEST['nrodoc'],$escEnEje,$_REQUEST['status'],$_REQUEST['observacion'],$usuario->id,$anoCurso,$_REQUEST['nomBenef'],$_REQUEST['concepto']);
}
	
	

require ("comun/header.php");

$msj =  $oCheque->msg;

?>

<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var mygridfac,i=0</script>
<span class="titulo_maestro">Emisi&oacute;n de Cheques</span>
<div id="formulario">
<a href="#" onClick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />

<div id="contenidobuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table border="0">
		<tr>
			<td colspan="2">N&ordm; de Documento</td>
		</tr>
		<tr>
			<td colspan="2"><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
		</tr>
		<tr>
			<td width="100px">Banco:</td>
			<td>Cuenta:</td>
		</tr>
		<tr>
			<td>
			<?	$bn = new banco;
						$bBancos = $bn->get_all($conn);
						$div = "'busca_combo_cuentas'";
						echo helpers::superComboObj($bBancos, '', 'busca_bancos', 'busca_bancos','width:150px',"traeCuentasBancarias2(this.value,$div,'')",'id','descripcion', false, 20);
						
					?></td>
			<td><div id="busca_combo_cuentas"><select name="busca_nro_cuenta" onChange="comboNroCuentas()" id="busca_nro_cuenta">
										<option value="0">Seleccione..</option>
										</select>
				</div>	
			</td>
		</tr>
		<tr>
			<td>Proveedor</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, nombre AS descripcion FROM proveedores ORDER BY nombre")?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td >Desde</td>
			<td>Hasta</td>
		</tr>
		<tr>
			<td>
				<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" onChange="validafecha(this);"/>
				<a href="#" id="boton_busca_fecha_desde" onClick="return false;">
					<img border="0" alt="fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
			<script type="text/javascript">
				new Zapatec.Calendar.setup({
					firstDay          : 1,
					weekNumbers       : true,
					showOthers        : false,
					showsTime         : false,
					timeFormat        : "24",
					step              : 2,
					range             : [1900.01, 2999.12],
					electric          : false,
					singleClick       : true,
					inputField        : "busca_fecha_desde",
					button            : "boton_busca_fecha_desde",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
			<td>
				<input style="width:100px" type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" onChange="validafecha(this); "/>
				<a href="#" id="boton_busca_fecha_hasta" onClick="return false;">
					<img border="0" alt="fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
			<script type="text/javascript">
				new Zapatec.Calendar.setup({
					firstDay          : 1,
					weekNumbers       : true,
					showOthers        : false,
					showsTime         : false,
					timeFormat        : "24",
					step              : 2,
					range             : [1900.01, 2999.12],
					electric          : false,
					singleClick       : true,
					inputField        : "busca_fecha_hasta",
					button            : "boton_busca_fecha_hasta",
					ifFormat          : "%d/%m/%Y",
					daFormat          : "%Y/%m/%d",
					align             : "Br"
				 });
			</script>
			</td>
		</tr>
	</table>
</fieldset>
</div>

<br />
<div style="margin-bottom:10px" id="busqueda"></div>
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>
<?
	$validator->create_message("error_observacion", "observacion", "*");
	$validator->create_message("error_nrocheque", "nrocheque", "*");
	$validator->create_message("error_nombenef", "nomBenef", "*");
	$validator->create_message("error_concepto", "concepto", "*");
	$validator->print_script();
?>
<script type="text/javascript"> 
//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
function traeCuentasBancarias(id_banco, div, id_cuenta,disabled){
	var url = 'updater_selects.php';
	var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta + '&disabled=' + disabled +'&ms='+new Date().getTime();
	var updater = new Ajax.Updater(div, 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando_cuentas')}, 
			onComplete:function(request){Element.hide('cargando_cuentas')}
		}); 
}
function traeCuentasBancarias2(id_banco, div, id_cuenta){
	var url = 'updater_selects.php';
	var pars = 'combo=cuentas_bancarias2&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta +'&ms='+new Date().getTime();
	var updater = new Ajax.Updater(div, 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando_cuentas')}, 
			onComplete:function(request){Element.hide('cargando_cuentas')}
		}); 
}
/*
//FUNCION QUE PERMITE TRAER LAS ORDENES DE PAGO CUANDO SELECCIONAMOS UN PROVEEDOR//
function traeOrdenesPago(id_proveedor){
	var url = 'updater_selects.php';
	var pars = 'combo=ordenes_pagos&id_proveedor=' + id_proveedor +'&ms='+new Date().getTime();;
	var updater = new Ajax.Updater('divordenespago', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando_ordenes')}, 
			onComplete:function(request){Element.hide('cargando_ordenes')}
		}); 
} */

//FUNCION QUE TRAE LA INFORMACION DEL NUMERO DEL CHEQUE QUE SE VA A EMITIR//
function traeUltimoCheque(id_cuenta){
	var url = 'json.php';
	var pars = 'op=ultimo_cheque&id_cuenta=' + id_cuenta;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == undefined) { return }
				if (jsonData != -1) { 
					$('nrocheque').value = jsonData.ultimo_cheque;
				}else{
					alert(" Chequera Agotada, Por Favor active otra chequera para la cuenta");
					$('nrocheque').value = '';
				}
			}
		});
}
function mostrar(){
	if ($('banco').value =='0'){
		alert("Primero debe Seleccionar un Banco.");
		return;
	}else if($('nro_cuenta').value=='0'){
		alert("Primero debe Seleccionar una Cuenta Bancaria.");
		return;
	}else if($('id_proveedor').value==''){
		alert("Primero debe Seleccionar un Proveedor");
		return;
	}else{
		var url = 'buscar_ordenes.php';
		var pars = 'provee='+$('id_proveedor').value+'&ano='+$('ano').value+'&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){}, 
				onComplete:function(request){
					
					Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
									showEffect:Element.show,hideEffect:Element.hide,
									showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
									
									}});
					
					}
				}
		);     	   
	}
}

function selOrdenes(id,monto,montopagado){
	Dialog.okCallback();
	if(mygridsp.getRowIndex(id)==-1){
		$('ordenes_pago').value = id;
			mygridsp.addRow(id,id+";"+muestraFloat(parseFloat(monto)+parseFloat(montopagado))+";"+muestraFloat(montopagado)+";"+muestraFloat(monto)+";"+muestraFloat(monto));
		sumaTotalCheque();
	}else{
		alert('La Orden de Pago Ya fue Agregada');
	}
}
/*
function montoOrdenPago(nrodoc){

	var url = 'json.php';
	var pars = 'op=SP&nrodoc=' + nrodoc;
	var monto_fac=0, monto_ret=0;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var jsonData = eval('(' + peticion.responseText + ')');
				monto_fac = (jsonData.montofac==null)? '0' : jsonData.montofac;
				monto_ret = jsonData.montoretencion;
				var total_solicitud = monto_fac - monto_ret;
				$('montopagado').value = muestraFloat(total_solicitud);
				
				
			}
		}
	);

} */
function Guardar(){
var JsonAux,PPAux=new Array;
	mygridsp.clearSelection()
	for(j=0;j<mygridsp.getRowsNum();j++){
		if(mygridsp.getRowId(j)){
			PPAux[j] = new Array;
			PPAux[j][0]= mygridsp.cells(mygridsp.getRowId(j),0).getValue();
			PPAux[j][1]= usaFloat(mygridsp.cells(mygridsp.getRowId(j),4).getValue());
		}
	}
	JsonAux={"ordenes":PPAux};
	$("contenedor_ordenes").value=JsonAux.toJSONString();
	if($("nrocheque").value==""){
		alert("Nro de cheque no puede estar vacio");
	}else{
		if($("status")){
			if($("status").value==1){
				if($("observacion").value==""){
					alert("Debe cololcar el Motivo")
				}
				validate();
			}else{
				document.form1.submit();
			}
		}else{
			document.form1.submit();
		}
	}
}

//ELIMINAR UNA FILA EN EL GRID//
function Eliminar(){
	mygridsp.deleteRow(mygridsp.getSelectedId());
	sumaTotalCheque();
}

/* Metodos utilizados en el buscador */
function busca(id_banco, id_cuenta, id_proveedor, fecha_desde, fecha_hasta, nrodoc, pagina){
	var url = 'updater_busca_cheque_anterior.php';
	var pars = '&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta + '&id_proveedor=' + id_proveedor;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde + '&fecha_hasta=' + fecha_hasta + '&pagina=' + pagina;
	//alert(pars);
	var updater = new Ajax.Updater('busqueda', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		}); 
}

Event.observe('busca_bancos', "change", function () { 
	busca($F('busca_bancos'),
	$F('busca_nro_cuenta'), 
	$F('busca_proveedores'),
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
}); 

Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_bancos'),
	$F('busca_nro_cuenta'), 
	$F('busca_proveedores'),
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
}); 

Event.observe('busca_nrodoc', "change", function () { 
	busca($F('busca_bancos'),
	$F('busca_nro_cuenta'), 
	$F('busca_proveedores'),
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
}); 
function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_bancos'),
				  $F('busca_nro_cuenta'), 
				  $F('busca_proveedores'), 
				  $F('busca_fecha_desde'), 
				  $F('busca_fecha_hasta'),
				  $F('busca_nrodoc'), 1);  
		} else {
			alert("Fecha incorrecta");
			fecha.value = "";
		}
	}else if(fecha.value != '') {
		alert("Fecha incorrecta");
		fecha.value = "";
	}
}
function comboNroCuentas(){
	busca($F('busca_bancos'),
		  $F('busca_nro_cuenta'), 
		  $F('busca_proveedores'), 
		  $F('busca_fecha_desde'), 
		  $F('busca_fecha_hasta'),
		  $F('busca_nrodoc'), 1);  
}

function sumaTotalCheque(){
	var total = 0;
	for(j=0;j<mygridsp.getRowsNum();j++){
		if(mygridsp.getRowId(j)){
			total += parseFloat(usaFloat(mygridsp.cells(mygridsp.getRowId(j),4).getValue())); 
		}
	}
	$('total_cheque').value  = muestraFloat(total);;
}
function mostrar_ventana(){
	if ($('banco').value =='0'){
		alert("Primero debe Seleccionar un Banco.");
		return;
	}else if($('nro_cuenta').value=='0'){
		alert("Primero debe Seleccionar una Cuenta Bancaria.");
		return;
	}else if($('ano').value=='0'){
		alert("Primero debe Seleccionar un Año a Consultar");
		return;
	}else{
		mygridsp.clearSelection();
		mygridsp.clearAll();
		$('ordenes_pago').value='';
		var url = 'buscar_proveedores.php';
		var pars = 'pc=1&ano='+$('ano').value+'&ms='+new Date().getTime();
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){}, 
				onComplete:function(request){
					
					Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
									showEffect:Element.show,hideEffect:Element.hide,
									showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
									
									}});
					
					}
				}
		);     	   
	}
}
function selDocumento(id, nombre){

	$('nombrepro').value = nombre;
	$('id_proveedor').value = id;
	Dialog.okCallback();

}
function traeProveedorDesdeXML(id_proveedor){
	var url = 'xmlTraeProveedor.php';
	var pars = 'id=' + id_proveedor;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: traeProveedor
		});
}
function traeProveedor(originalRequest){
	
	var xmlDoc = originalRequest.responseXML;
	var x = xmlDoc.getElementsByTagName('proveedor');
	for(j=0;j<x[0].childNodes.length;j++){
		if (x[0].childNodes[j].nodeType != 1) continue;
		var nombre = x[0].childNodes[j].nodeName
		if($(nombre)){
			$(nombre).value = x[0].childNodes[j].firstChild.nodeValue;
			$(nomBenef).value = x[0].childNodes[j].firstChild.nodeValue;
		}
	}
}
var t;
function busca_popup()
{
	clearTimeout(t);
	t = setTimeout("buscaProveedor()", 800);
}

function buscaProveedor()
{
	var tipo;
	var url = 'buscar_proveedores.php';
	var pars = 'tipo='+$('tipo_prov').value+'&rif='+$('search_rif_prov').value+ '&nombre='+ $('search_nombre_prov').value+'&opcion=2&ms'+new Date().getTime();
		
	var updater = new Ajax.Updater('divProveedores', 
		url,
		{
			method: 'get',
			parameters: pars,
			asynchronous:true, 
			evalScripts:true,
			onLoading:function(request){Element.show('cargando')}, 
			onComplete:function(request){Element.hide('cargando')}
		});
}

</script>


<? require ("comun/footer.php");?>