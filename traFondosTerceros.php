<?
require ("comun/ini.php");

	$OtraFondosTerceros = new traFondosTerceros;
	$oComprobante = new comprobante($conn);
	$accion = $_REQUEST['accion']; 
	//echo 'aqui '.$accion;
if($accion == 'Guardar'){
	#Busca el ultimo correlativo ingresado en la tabla de comprobantes contables
	$q = "SELECT numcom FROM contabilidad.com_enc WHERE id = (SELECT MAX(id) as max FROM contabilidad.com_enc)";
	$rs = $conn->Execute($q);
	$NumCom = sprintf("%05d", substr($rs->fields['numcom'],8)+1);
	$fech = split('/',$_REQUEST['fecha']);
	$numcom = $fech[2].'-'.$fech[1].'-'.$NumCom;
	$OtraFondosTerceros->add($conn,guardaFecha($_REQUEST['fecha']), $_REQUEST['nrodoc'],1, $numcom, $_REQUEST['descripcion'], $_REQUEST['transferencia']);
	#Aqui generamos el asiento contable
	$desc = 'Asiento de Transferencia de Fondos a Terceros';
	$oComprobante->Create($escEnEje,$fech[2],$fech[1],$numcom,$desc,guardafecha($_REQUEST['fecha']),'TFT','R',$_REQUEST['nrodoc'],$_REQUEST['json_det'],'1');
}elseif($accion=='Anular'){
	#Busca el ultimo correlativo ingresado en la tabla de comprobantes contables
	$q = "SELECT numcom FROM contabilidad.com_enc WHERE id = (SELECT MAX(id) as max FROM contabilidad.com_enc)";
	$rs = $conn->Execute($q);
	$NumCom = sprintf("%05d", substr($rs->fields['numcom'],8)+1);
	$fech = split('/',$_REQUEST['fecha']);
	$numcom = $fech[2].'-'.$fech[1].'-'.$NumCom;
	$OtraFondosTerceros->anular($conn,$_REQUEST['id'],date('Y-m-d'),2,$_REQUEST['mAnulado']);
	#Aqui generamos el asiento contable
	$desc = 'Asiento de Transferencia de Fondos a Terceros Anulada';
	$oComprobante->Create($escEnEje,$fech[2],$fech[1],$numcom,$desc,guardafecha($_REQUEST['fecha']),'TFT','A',$_REQUEST['nrodoc'],$_REQUEST['json_det'],'1');
}	

	//$msj = $oOrdenPago->msj; 	
	
	$msj = empty($ctaFondos) ? 'No existe cuenta asociada a la transferencia de fondos, Por favor realize el enlace' : $OtraFondosTerceros->msj;
	
	#ESTE EL LA CABECERA DE LA PAGINA#
require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<span class="titulo_maestro">Pagos Retenciones y Aportes Patronales</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<div id="divbuscador">
	<fieldset id="buscador">
		<legend>Buscar:</legend>
		<table>
			<tr>
				<td>N&ordm; de Documento</td>
			</tr>
			<tr>
				<td><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td style="width:125px">Desde</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td>
								<input style="width:100px"  type="text" name="busca_fecha" id="busca_fecha" 
								onchange="validafecha(this);"/>
							</td>
							<td>
								<a href="#" id="boton_busca_fecha" onClick="return false;">
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
									inputField        : "busca_fecha",
									button            : "boton_busca_fecha",
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



/* Metodos utilizados en el buscador */
function busca(fecha, nrodoc, pagina){
	var url = 'updater_busca_traterceros.php';
	var pars = '&nrodoc=' + nrodoc + '&fecha=' + fecha+ '&pagina=' + pagina;
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


Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_fecha'),
	$F('busca_nrodoc'), 1); 
	});


function validafecha(fecha){
	var upper = 31;
	if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) { // dd/mm/yyyy
		if(RegExp.$2 == '02') upper = 29;
		if((RegExp.$1 <= upper) && (RegExp.$2 <= 12)) {
			busca($F('busca_fecha'),
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

var t;
function busca_popup(descripcion) {
 	
  clearTimeout(t); 
	t = setTimeout("busca_solicitud()", 1500); 
};

	

	

	function CargarGridPP(id){
	
	mygridpp.clearSelection();
	mygridpp.clearAll();
	//var idpc_iva = <?= $idpc_iva?>;
	
	var url = 'json.php';
	var pars = 'op=transferencia&id='+ id;
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var totMonto = 0;
				var JsonData = eval( '(' + request.responseText + ')');
				//alert(JsonData);
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						
							if((JsonData[j]['ctaProveedor'] == null) && (JsonData[j]['idCta'] != 673)) {
								var monto= parseFloat(JsonData[j]['monto']);
								totMonto += monto;
								monto = monto.toFixed(2);
								
								mygridpp.getCombo(0).put(JsonData[j]['idCta'],JsonData[j]['descripcion']);
								mygridpp.addRow(JsonData[j]['idCta'],JsonData[j]['idCta']+";"+muestraFloat(monto));
								//totMonto += monto;
								//alert(totMonto);
							}
					}
					$('monto_causar').value = muestraFloat(totMonto);
				}
			}
		}
	);
	
}

	function GuardarPP(idCtaTra,status,accion)
	{
		
		var JsonAux = new Array;
		var JsonAuxServ = new Array;
		var transfer = new Array;
		var lon = mygridpp.getRowsNum();
		mygridpp.clearSelection()
		if (lon == 0) 
			return false;

		if(status==1){
		for(j=0; j<lon; j++)
			{
				rowId = mygridpp.getRowId(j);
				transfer[j] = new Array;
				transfer[j][0]= mygridpp.cells(mygridpp.getRowId(j),0).getValue();
				transfer[j][1]= mygridpp.cells(mygridpp.getRowId(j),1).getValue();
				JsonAux.push({id_cta:mygridpp.cells(rowId,0).getValue(), descrip:'', debe:usaFloat(mygridpp.cells(rowId,1).getValue()), haber:0, id_esc:<?=$escEnEje?>});
			}
			
				JsonAux.push({id_cta:idCtaTra, descrip:'', debe:0, haber:usaFloat($('monto_pagar').value), id_esc:<?=$escEnEje?>});
		}else if(status==2){
			if($('mAnulado').value==''){
				alert('Debe especificar el motivo de la anulacion');
				return false;
			}
			for(j=0; j<lon; j++)
			{
				rowId = mygridpp.getRowId(j);
				JsonAux.push({id_cta:mygridpp.cells(rowId,0).getValue(), descrip:'', debe:0, haber:usaFloat(mygridpp.cells(rowId,1).getValue()), id_esc:<?=$escEnEje?>});
			}
			
				JsonAux.push({id_cta:idCtaTra, descrip:'', debe:usaFloat($('monto_pagar').value), haber:0, id_esc:<?=$escEnEje?>});
		}
		JsonAuxServ={"transfer":transfer};
		$("transferencia").value=JsonAuxServ.toJSONString(); 	
		$('json_det').value = JsonAux.toJSONString();
		$('accion').value = accion;
		document.form1.submit();
	}
	
	function mostrar_ventana(){
	 
	//var tipo = "()";
	var url = 'buscar_ordenesXtransferir.php';
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

	function busca_popup()
{
	clearTimeout(t);
	t = setTimeout("buscaRetAportes()", 800);
}

	function buscaRetAportes()
{
	//var tipo = "('P','B')";
	//el parametro status se pasa vacio para que no filtre solo los activos, en caso de que se quiera esto se tiene que pasar status=A
	var url = 'buscar_ordenesXtransferir.php';
	var pars = 'desc='+$('search_descrip').value+'&opcion=2&ms='+new Date().getTime();
		
	var updater = new Ajax.Updater('divcuentas', 
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

	function selDocumento(id, descripcion, saldo){
	
	$('descCta').value = descripcion;
	$('hIdCta').value = id;
	$('hSaldo').value = saldo;
	$('saldo').value = muestraFloat(saldo);
	Dialog.okCallback();

}

	function agregarCta(){
	
	if($('hIdCta').value == ''){
		alert ("Debe selecionar una cuenta.");
		return;
	}else if($('saldo').value=="" || parseFloat($('saldo').value)<=0){
		
		alert("Primero debe colocar el monto a transferir.");
		return;
	
	}else if(parseFloat($('hSaldo').value) < usaFloat($('saldo').value)){
		alert("El saldo disponible en la cuenta es menor al requerido");
		$('saldo').value='0,00';
		return;	 
	
	}else{
		
		for(j=0;j<mygridpp.getRowsNum();j++){
			
			if (mygridpp.getRowId(j)!=undefined){
				if (mygridpp.cells(mygridpp.getRowId(j),'0').getValue() == $('hIdCta').value){
						
					alert('Esta cuenta ya ha sido seleccionada, por favor seleccione otra cuenta');
					return false;

				}
			
			}
			
			
		}
				
		mygridpp.addRow($('hIdCta').value,$('hIdCta').value+";"+$('saldo').value);
		sumaTotal();
		$('saldo').value='0,00';
	}
} 

	function sumaTotal(){
		var totalCuenta = 0;
		for(j=0;j<mygridpp.getRowsNum();j++){
			if(mygridpp.getRowId(j)!= undefined){
				totalCuenta += usaFloat(mygridpp.cells(mygridpp.getRowId(j),1).getValue());
			}
		}
		$('monto_pagar').value = (isNaN(totalCuenta))? '0,00' : muestraFloat(totalCuenta);
	
	}
	
	function eliminaCta(){
		mygridpp.deleteRow(mygridpp.getSelectedId());
		sumaTotal();
	}
	
	function ver_ctas(){
		Effect.toggle('ctasDiv', 'blind');
	}	
	
</script>
<? require("comun/footer.php");?>
