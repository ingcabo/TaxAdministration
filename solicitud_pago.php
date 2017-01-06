<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto solicitud de pago

$oSolicitudPago = new solicitud_pago;
$accion = $_REQUEST['accion'];
//die($accion);
	#ACCION DE GUARDAR LA SOLICITUD DE PAGO#
	if($accion == 'Guardar'){
	
		$oSolicitudPago->add($conn,
								$_POST['idtipodoc'], 
								$_POST['nroref'],
								guardafecha($_POST['fecha']),
								$_POST['status'],
								$_REQUEST['contenedor_partidas'],
								$_REQUEST['nrodoc'],
								$_POST['motivo'],
								$_POST['unidad_ejecutora'],
								$_POST['proveedores'],
								$_POST['pagar'],
								$_REQUEST['contenedor_facturas']
								);
								
	}
	
	#ACCION DE APROBAR LA SOLICITUD DE PAGO#
	elseif($accion == 'Aprobar'){
	
		$oSolicitudPago->aprobar($conn, 
									$_REQUEST['nrodoc'], 
									$_POST['motivo'],
									$_POST['nroref'],
									guardafecha($_POST['fecha']),
									$_REQUEST['contenedor_partidas'],
									$_POST['pagar']
									);
	}

	#ACCION DE ACTUALIZAR LA SOLICITUD DE PAGO#
	elseif($accion == 'Actualizar'){
	
		$oSolicitudPago->set($conn, 
								$_POST['nrodoc'], 
								$_POST['nroref'],
								guardafecha($_POST['fecha']),
								$_REQUEST['contenedor_partidas'],
								$_POST['motivo'],
								$_POST['unidad_ejecutora'],
								$_POST['proveedores'],
								$_POST['pagar'],
								$_REQUEST['contenedor_facturas']
								);
	}
	
	#ACCION DE ELIMINAR LA SOLICITUD DE PAGO#
	elseif($accion == 'del'){
		
		$oSolicitudPago->del($conn, $_POST['id']);
		
	}elseif($accion =='Anular'){
		//die("Entro");
		$oSolicitudPago->anular($conn, 
									$_REQUEST['nrodoc'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									date("Y"),
									$_POST['descripcion'],
									'014',
									$_POST['nroref'],
									guardafecha($_POST['fecha']),
									$_REQUEST['status_old'],
									$_POST['proveedores'],
									$_REQUEST['contenedor_partidas']);
		
	}
	
	#LLENO LA VARIABLE CON EL MENSAJE DE LA OPERACION REALIZADA#
	$msj = $oSolicitudPago->msj; 
	
	#ESTE EL LA CABECERA DE LA PAGINA#
	require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var mygridfac,i=0, iret=0, ipp=0</script>
<span class="titulo_maestro">Solicitud de Pago</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="divbuscador">
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table>
		<tr>
			<td colspan="3">Unidad Ejecutora</td>
		</tr>
		<tr>
			<td colspan="3"><?=helpers::combo_ue_cp($conn,'busca_ue','','','','','','',
			"SELECT DISTINCT id,id||' - '|| descripcion AS descripcion FROM unidades_ejecutoras")?></td>
		</tr>
		<tr>
			<td>Proveedor</td>
			<td>Descripci&oacute;n</td>
		</tr>
		<tr>
			<td>
			<?=helpers::combo_ue_cp($conn, 'busca_proveedores','','','','','','',
			"SELECT id, nombre AS descripcion FROM proveedores ORDER BY descripcion")?></td>
			<td><input style="width:300px" type="text" name="busca_descripcion" id="busca_descripcion" /></td>
		</tr>
		<tr>
			<td>N&ordm; de Documento</td>
			<td colspan="2">
				<table>
					<tr>
						<td style="width:125px">Desde</td>
						<td>Hasta</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
			<td colspan="2">
				<table>
					<tr>
						<td>
							<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" 
							onchange="validafecha(this);"/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
								<img border="0" alt="Seleccionar una fecha" src="images/calendarA.png" width="20" height="20" />
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
							<input style="width:100px" type="text" name="busca_fecha_hasta" id="busca_fecha_hasta"
							onchange="validafecha(this); "/>
						</td>
						<td>
							<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
								<img border="0" alt="Seleccionar una fecha" src="images/calendarA.png" width="20" height="20" />
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
<script type="text/javascript">
//PARTE NUEVA//

function CargarGridPP(id){
	//alert(id);
	mygridpp.clearSelection();
	mygridpp.clearAll();
	var url = 'json.php';
	var pars = 'op=pp_solicitud3&id='+ id + '&status=2';
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var JsonData = eval( '(' + request.responseText + ')');
				var transito = 0;
				var comprometido = 0;
				var montoIva = 0;
				var baseImp = 0;
				var IdParCat = new Array;
				//var SumMonto = 0;
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						
						IdParCat[j] = new Array;
						var monto_causar = parseFloat(JsonData[j]['montoporcausar']);
						if(monto_causar < 1) monto_causar = 0;
						mygridpp.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
						mygridpp.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);
						mygridpp.addRow(JsonData[j]['idParCat'],JsonData[j]['id_categoria_programatica']+";"+JsonData[j]['id_partida_presupuestaria']+";"+muestraFloat(monto_causar.toFixed(2)));
						IdParCat[j][0] = JsonData[j]['idParCat'];
						//SumMonto += parseFloat(JsonData[j]['montoporcausar']);
						//SumMonto += usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue());
						//ACUMULO EL CAUSADO Y EL COMPROMETIDO//	
						transito = 0 //Se hace de esta manera porque la funcion devuelve el monto total en transito
						transito += parseFloat(JsonData[j]['transito']);
						comprometido += parseFloat(JsonData[j]['comprometido']);
									
						ipp++;
					}
				var JsonIdParCat={"IdPartCat":IdParCat};
				$("idParCat").value=JsonIdParCat.toJSONString();
				if(isNaN(transito))
					transito = 0;	
					
				//alert('monto: ' + muestraFloat(SumMonto.toFixed(2)));	
				var disponible = parseFloat(comprometido) - transito;
				$('compromiso').value = muestraFloat(comprometido.toFixed(2));
				$('transito').value = muestraFloat(transito.toFixed(2));
				$('disponibilidad').value = muestraFloat(disponible.toFixed(2));
								
				}
			}
		}
	);  

	//Para cargar el grid de facturas en caso de que sea una caja chica	
	var tipo_doc = id.substr(0,3);
	if (tipo_doc = '013'){
		mygridfac.clearSelection();
		mygridfac.clearAll();
		
		var url = 'json.php';
		var pars = 'op=facturassolicitudCC&nrodoc='+ id;
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){}, 
				onComplete:function(request){
					var Facturas = eval( '(' + request.responseText + ')');
					//var Facturas = request.responseText;
					if(Facturas){
					
						for(var j=0;j<Facturas.length;j++){
							
							mygridfac.addRow(j,Facturas[j]['nrofac']+","+Facturas[j]['nrocontrol']+","+Facturas[j]['fechafac']+","+Facturas[j]['montofac']+","+Facturas[j]['descuento']+","+Facturas[j]['monto_excento']+",0,"+Facturas[j]['iva']+","+Facturas[j]['base_imponible']+","+Facturas[j]['monto_iva']+",0",j);
							mygridret.getCombo(0).put(Facturas[j]['nrofac'],Facturas[j]['nrofac']);
						}
						sumaTotalFacturas();
					}
				}
			});
	}
} 

//AGREGA UNA FILA EN EL GRID DE FACTURAS
function Agregar(){
	mygridfac.addRow(i,",,,,,,0,,,");
	i++;
}

//ELIMINAR UNA FILA EN EL GRID DE FACTURAS//
function Eliminar(){
	mygridfac.deleteRow(mygridfac.getSelectedId());
}

//CALCULAR LOS VALORES DE EL GRID DE FACTURAS//
function calcularMontoBaseImp(rowId,cellInd){
	
	if(cellInd=='4' || cellInd=='6' ){
		//CALCULO DEL MONTO BASE//
		if(mygridfac.cells(rowId,'3').getValue()==null)
			alert('Debe seleccionar el monto del Impuesto');
		else
			var r = 0;		
			r = ((parseFloat(mygridfac.cells(rowId,'4').getValue()) - parseFloat(mygridfac.cells(rowId,'6').getValue())) * 100 ) / (100 + parseFloat(mygridfac.cells(rowId,'3').getValue()));
			r = isNaN(r) ? '0' : r.toFixed(2);
			mygridfac.cells(rowId,'5').setValue(r);
			
			//CALCULO DEL MONTO IVA//
			r = 0;
			r = parseFloat(mygridfac.cells(rowId,'5').getValue()) *  (parseFloat(mygridfac.cells(rowId,'3').getValue()) / 100);
			r = isNaN(r) ? '0' : r.toFixed(2);
			mygridfac.cells(rowId,'7').setValue(r);
			
			//CALCULAR MONTO IVA RETENCIONES//
			r = 0;
			r = parseFloat(mygridfac.cells(rowId,'5').getValue()) *  ( parseFloat(mygridfac.cells(rowId,'3').getValue()) / 100) * parseInt($F('porcret')) / 100;
			r = isNaN(r) ? '0' : r.toFixed(2);
			mygridfac.cells(rowId,'8').setValue(r);
			sumaTotalFacturas();
		
	}
	
}
//SUMA EL TOTAL DE LAS FACTURAS//
function sumaTotalFacturas(){
	var total = 0;
	var r = 0;
	for(j=0;j<i;j++){
		if(mygridfac.getRowIndex(j)!=-1){
			total += parseFloat(mygridfac.cells(j,4).getValue()); 
		}
	}
	r = muestraFloat(total);
	$('total').value  = r;
	total_sol();
}

function validarMontoPP(stage,rowId,cellInd){
	
	//EN ESTE ESTADO CONVIERTO EL MONTO DE FORMATO VENEZOLANO AL FORMATO IMPERIALISTA//
	if (stage==0){
	
		if (cellInd==3){
			
			var valor = usaFloat(mygridpp.cells(rowId,3).getValue());
			mygridpp.cells(rowId,3).setValue(valor);	
			
		}
			
	}
	
	//EN ESTE ESTADO VERIFICO SI EL MONTO SE SOBREPASA, VALIDO QUE CUANDO ESTE VACIO COLOQUE 0,00, SUMO EL TOTAL DE LAS PARTIDAS SI SE COLOCO UN VALOR//
	if (stage==2){
	
		if (cellInd==3){
		
			if (parseFloat(mygridpp.cells(rowId,3).getValue()) !== parseFloat(usaFloat(mygridpp.cells(rowId,2).getValue()))){
			
				alert("El Monto debe ser igual que el monto permitido para causar");
				mygridpp.cells(rowId,'3').setValue('0,00');
				
				return false;
			
			}else if(mygridpp.cells(rowId,3).getValue()==''){
			
				mygridpp.cells(rowId,'3').setValue('0,00');
				return;
			
			}else{
				var valor = muestraFloat(mygridpp.cells(rowId,3).getValue());
				mygridpp.cells(rowId,'3').setValue(valor);
				calcularMontoCausado(rowId,cellInd);
			
			}
		}
	}
}

function calcularMontoCausado(rowId,cellInd){
	
	if(cellInd==3){
		var total = 0;
		for(j=0;j<ipp;j++){
			$('causado').value = mygridpp.getRowId(j);
			if(mygridpp.getRowId(j)!=undefined){
				total += usaFloat(mygridpp.cells(mygridpp.getRowId(j),3).getValue()); 
			}
		}
	}
	var compromiso = usaFloat($('compromiso').value);
	//compromiso = replace_caracter(compromiso, '.', ',')
	//alert(total);
	$('causado').value = muestraFloat(total);
	$('disponibilidad').value = parseFloat(compromiso) - usaFloat($('causado').value);

}

function GuardarPP(){
var JsonAux,PPAux=new Array;
	mygridpp.clearSelection()
	var idparcat = $('idParCat').value;
	
	var resto_compromiso = usaFloat($('disponibilidad').value);
	var comprometer = usaFloat($('pagar').value);
	var sumaPorc = 0;
	var sumaMonto = 0;
	var swith = 0;
	var auxTotal = 0;
	
	
	
	if(resto_compromiso == comprometer){
		swith = 1;
	}
	/*var sumMonto = 0;
	for(j=0;j<mygridpp.getRowsNum();j++){
		if(!isNaN(mygridpp.getRowId(j))){
			sumMonto += usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue());
		}
	}
	alert(muestraFloat(sumMonto.toFixed(2)));
	return false;*/
	for(j=0;j<mygridpp.getRowsNum();j++){
		if(!isNaN(mygridpp.getRowId(j))){
			if(swith==1){
				var monto = usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue());
			} else {
				var porc = (usaFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue())*100)/resto_compromiso;
				//alert(porc);
				sumaPorc = sumaPorc + porc;
				var monto = comprometer * (porc/100);
				
			}
			auxTotal += parseFloat(mygridpp.cells(mygridpp.getRowId(j),2).getValue());
			PPAux[j] = new Array;
			PPAux[j][0]= mygridpp.cells(mygridpp.getRowId(j),0).getValue();
			PPAux[j][1]= mygridpp.cells(mygridpp.getRowId(j),1).getValue();
			//PPAux[j][2]= mygridpp.cells(mygridpp.getRowId(j),2).getValue();
			PPAux[j][2]= monto;
			PPAux[j][3]= mygridpp.getRowId(j);
			//sumaMonto = sumaMonto + monto
			
		}
	}
	/*alert(' monto: '+ sumaMonto);
	return false;*/
	JsonAux={"partidaspresupuestarias":PPAux};
	$("contenedor_partidas").value=JsonAux.toJSONString();
	
}

function actapr(elemento){
	//alert($('nroref').value.substring(0,3));
	if(usaFloat($('pagar').value)== 0){
		alert('El monto a pagar debe ser mayor a 0');
		return false;
	
	} else if((elemento.value != 'Aprobar') && (elemento.value != 'Anular')){ 
		if(usaFloat($('pagar').value) > usaFloat($('disponibilidad').value))  {
			alert('El monto a pagar no puede ser mayor que el disponible');
			return false;
		}  else if (($('nroref').value.substring(0,3) != '010') && ($('nroref').value.substring(0,3) != '016')) {
				if(usaFloat($('pagar').value) != usaFloat($('total').value)){
					alert('El monto facturado debe coincidir con el monto imputado');
					return false;
				}
			}
			
	}else if(elemento.value == 'Guardar')
			$('accion').value = 'Guardar';
		else if(elemento.value == 'Actualizar')
			$('accion').value = 'Actualizar';
		else if(elemento.value == 'Aprobar')
			$('accion').value = 'Aprobar';
		else
			$('accion').value = 'Anular'; 
		 validate(); 
}

/* Metodos utilizados en el buscador */
function busca(id_ue, id_proveedor, descripcion, fecha_desde, fecha_hasta, nrodoc, pagina){
	var url = 'updater_busca_solicitud_pago.php';
	var pars = '&id_ue=' + id_ue + '&id_proveedor=' + id_proveedor+ '&descripcion=' + descripcion;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&pagina=' + pagina;
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

Event.observe('busca_ue', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});
Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});
Event.observe('busca_descripcion', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'), 1); 
});

function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_ue'), 
			$F('busca_proveedores'), 
			$F('busca_descripcion'), 
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

function getInfo(nrodoc){
	var url = 'json.php';
	var pars = 'op=movpre&nrodoc=' + nrodoc + '&momento=1';
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == undefined) { return }
				$('descripcion').value = jsonData.descripcion;
				$('motivo').value = jsonData.descripcion;
				$('ue').value = jsonData.unidad_ejecutora;
				$('proveedor').value = jsonData.proveedor;
				$('tipo_contribuyente').value = jsonData.tipo_contribuyente;
				$('ingreso_periodo_fiscal').value = muestraFloat(jsonData.ingreso_periodo_fiscal);
				$('tipdoc').value = jsonData.tipdoc + ' - ' + jsonData.tipo_documento;
				$('idtipodoc').value = jsonData.tipdoc;
				$('proveedores').value = jsonData.id_proveedor;
				$('unidad_ejecutora').value = jsonData.id_unidad_ejecutora;
				//$('porcret').value = (jsonData.tipo_contribuyente == 'ORDINARIO') ? '75' : '100';
			}
		});
}

function muestraFacrel(){
	if($F('nroref') != '0')
		Effect.toggle('facrelDiv', 'blind');
	else{
		alert('Debe seleccionar un documento');
		$('nroref').focus();
	}
}

function muestraRet(){
	if($F('nroref') != '0')
		Effect.toggle('RAdiv', 'blind');
	else{
		alert('Debe seleccionar un documento');
		$('nroref').focus();
	}
}

function muestracompromiso(monto){
	//alert(monto);
	$('compomiso').value = monto;
}

function mostrar_ventana(){
	 
	var url = 'buscar_compromisos_solicitud.php';
	var pars = 'ms='+new Date().getTime();
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				Dialog.closeInfo();
				Dialog.alert(request.responseText, {windowParameters: {width:600, height:400, 
								showEffect:Element.show,hideEffect:Element.hide,
								showEffectOptions: { duration: 1}, hideEffectOptions: { duration:1 }
								
								}});
				
				}
			}
	);     	   
}

function selDocumento(id){

	$('nroref').value = id;
	Dialog.okCallback();

}

function busca_solicitud(){
	
	var url = 'buscar_compromisos_solicitud.php';
	var pars = 'id_ue='+$('search_ue').value+ '&tipdoc='+ $('search_tip_doc').value +'&nrodoc='+$('search_nrodoc').value+'&opcion=2&ms'+new Date().getTime();
		
	var updater = new Ajax.Updater('divsolicitudes', 
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

var t;
function busca_popup(descripcion) {
 	
  clearTimeout(t); 
	t = setTimeout("busca_solicitud()", 1500); 
};

	function total_sol(){
		var total = usaFloat($('total').value) - usaFloat($('totalra').value);
		$('total_soli').value = total;
	}	
	
	var entroComboFact = 0;
	function busca_ret_iva(stage,rowId,cellInd){
		
		if(cellInd==6 && stage == 2 && entroComboFact==0) {
			if(mygridfac.cells(rowId,'6').getValue()==0)
				alert('Debe seleccionar el monto del Impuesto');
			else if((mygridfac.cells(rowId,'3').getValue()==null) || (mygridfac.cells(rowId,'3').getValue()==0)){
				alert('Debe existir un monto para la factura');
			}else{	
				//entroComboFact++;
				var url = 'json.php';
				var pars = 'op=busca_porc&id='+ mygridfac.cells(rowId,'6').getValue();
				var Request = new Ajax.Request(
					url,
					{
						method: 'get',
						parameters: pars,
						onLoading:function(request){}, 
						onComplete:function(request){
							
							var JsonData = eval( '(' + request.responseText + ')');
							var r = 0;	
							if((mygridfac.cells(rowId,'5').getValue())==null)
								mygridfac.cells(rowId,'5').setValue(r.toString());	
							
							if((mygridfac.cells(rowId,'4').getValue())==null)
								mygridfac.cells(rowId,'4').setValue(r.toString());	
								
							mygridfac.cells(rowId,'7').setValue(JsonData.porcentaje.toString());
							//CALCULO BASE IMPONIBLE
							r = ((parseFloat(mygridfac.cells(rowId,'3').getValue()) - parseFloat(mygridfac.cells(rowId,'5').getValue())) * 100 ) / (100 + parseFloat(JsonData.porcentaje));
							//alert('((' + parseFloat(mygridfac.cells(rowId,'4').getValue()) + '-' + parseFloat(mygridfac.cells(rowId,'6').getValue()) + ')' +' * '+ '100)/(100 + ' + parseFloat(mygridfac.cells(rowId,'3').getValue())+')');
							//alert(r);
							if(mygridfac.cells(rowId,'8').getValue()==0)
								r = isNaN(r) ? '0' : r.toFixed(2);
							else
								r = parseFloat(mygridfac.cells(rowId,'8').getValue());
							//alert('BI ' + r);
							mygridfac.cells(rowId,'8').setValue(r);
							
							//CALCULO DEL MONTO IVA//
							r = 0;
							if(mygridfac.cells(rowId,'9').getValue()==0){
								r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  (parseFloat(JsonData.porcentaje) / 100);
								r = isNaN(r) ? '0' : r.toFixed(2);
							}else{	
								r = parseFloat(mygridfac.cells(rowId,'9').getValue());
							}
							//alert('IVA ' + r);	
							mygridfac.cells(rowId,'9').setValue(r);	
							//CALCULAR MONTO IVA RETENCIONES//
							r = 0;
							if(mygridfac.cells(rowId,'10').getValue()==0){
								//r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  ( parseFloat(JsonData.porcentaje) / 100) * parseFloat(JsonData.porcRet) / 100;
								r = parseFloat(mygridfac.cells(rowId,'9').getValue()) * parseFloat(JsonData.porcRet) / 100;
								//alert(r);
							}else{
								r = parseFloat(mygridfac.cells(rowId,'10').getValue());
							}
							//alert('IVA RET ' + r);	
							r = isNaN(r) ? '0' : r.toFixed(2);
							mygridfac.cells(rowId,'10').setValue(r);
							sumaTotalFacturas();
					}
				}
					
			);
			}
		} else if(cellInd==8 && stage == 2 && entroComboFact==0) {
			if(mygridfac.cells(rowId,'8').getValue()==0)
				r = isNaN(r) ? '0' : r.toFixed(2);
			else
				r = parseFloat(mygridfac.cells(rowId,'8').getValue());
			//alert('BI ' + r);
			mygridfac.cells(rowId,'8').setValue(r);
			sumaTotalFacturas();
		
		} else if(cellInd==9 && stage == 2 && entroComboFact==0) {
			r = 0;
			if(mygridfac.cells(rowId,'9').getValue()==0){
				r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  (parseFloat(JsonData.porcentaje) / 100);
				r = isNaN(r) ? '0' : r.toFixed(2);
			}else{	
				r = parseFloat(mygridfac.cells(rowId,'9').getValue());
			}
			//alert('IVA ' + r);	
			mygridfac.cells(rowId,'9').setValue(r);	
			sumaTotalFacturas();
		
		} else if(cellInd==10 && stage == 2 && entroComboFact==0) {
			r = 0;
			if(mygridfac.cells(rowId,'10').getValue()==0){
				//r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  ( parseFloat(JsonData.porcentaje) / 100) * parseFloat(JsonData.porcRet) / 100;
				r = parseFloat(mygridfac.cells(rowId,'9').getValue()) * parseFloat(JsonData.porcRet) / 100;
				//alert(r);
			}else{
				r = parseFloat(mygridfac.cells(rowId,'10').getValue());
			}
			//alert('IVA RET ' + r);	
			r = isNaN(r) ? '0' : r.toFixed(2);
			mygridfac.cells(rowId,'10').setValue(r);
			sumaTotalFacturas();
		}
					
	}
	
	//SUMA EL TOTAL DE LAS FACTURAS//
function sumaTotalFacturas(){
	var total = 0;
	var total_iva = 0;
	var total_iva_ret = 0;
	var r = 0;
	for(j=0;j<mygridfac.getRowsNum();j++){
		if(mygridfac.getRowIndex(j)!=-1){
			total += parseFloat(mygridfac.cells(j,3).getValue()) - parseFloat(mygridfac.cells(j,4).getValue()); 
			total_iva += parseFloat(mygridfac.cells(j,9).getValue());
			total_iva_ret += parseFloat(mygridfac.cells(j,10).getValue());
		}
	}
	r = muestraFloat(total);
	$('total').value  = r;
	$('iva').value = muestraFloat(total_iva);
	$('ivaRet').value = muestraFloat(total_iva_ret);
	//total_sol();
}

function GuardarFAC(){
	var JsonAux,FACAux=new Array;
		mygridfac.clearSelection()
		for(j=0;j<mygridfac.getRowsNum();j++){
			if(!isNaN(mygridfac.getRowId(j))){
				FACAux[j] = new Array;
				FACAux[j][0]= mygridfac.cells(mygridfac.getRowId(j),0).getValue(); // numero de factura
				FACAux[j][1]= mygridfac.cells(mygridfac.getRowId(j),1).getValue(); // numro control
				FACAux[j][2]= mygridfac.cells(mygridfac.getRowId(j),2).getValue(); //fecha 
				FACAux[j][3]= mygridfac.cells(mygridfac.getRowId(j),3).getValue(); //Monto Doc
				FACAux[j][4]= mygridfac.cells(mygridfac.getRowId(j),4).getValue(); //Descuento
				FACAux[j][5]= mygridfac.cells(mygridfac.getRowId(j),5).getValue(); //Monto Exc
				FACAux[j][6]= mygridfac.cells(mygridfac.getRowId(j),6).getValue(); //Id retencion
				FACAux[j][7]= mygridfac.cells(mygridfac.getRowId(j),7).getValue(); //Porcentaje
				FACAux[j][8]= mygridfac.cells(mygridfac.getRowId(j),8).getValue(); //Base imponible
				FACAux[j][9]= mygridfac.cells(mygridfac.getRowId(j),9).getValue(); //Monto del Iva
				FACAux[j][10]= mygridfac.cells(mygridfac.getRowId(j),10).getValue(); //Monto retenido
				
			}
		}
		JsonAux={"facturas":FACAux};
		$("contenedor_facturas").value=JsonAux.toJSONString();
			
	}
		


</script>

<?
$validator->create_message("error_nroref", "nroref", "*");
$validator->create_message("error_motivo", "motivo", "*");
//$validator->create_message("error_finan", "finan", "*");
$validator->print_script();
require ("comun/footer.php");
?>
