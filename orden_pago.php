<?
include('adodb/adodb-exceptions.inc.php');
require ("comun/ini.php");
// Creando el objeto orden de pago

$oOrdenPago = new orden_pago;
$accion = $_REQUEST['accion'];

	#ACCION DE GUARDAR LA ORDEN DE PAGO#
	if($accion == 'Guardar'){
		$oOrdenPago->add($conn, 
								$_POST['nroref'],
								guardafecha($_POST['fecha']),
								$_POST['status'],
								$_POST['cond_pago'],
								$_POST['finan'],
								$_POST['tipsol_si'],
								guardaFloat($_POST['monto_si']),
								$_REQUEST['contenedor_partidas'],
								$_REQUEST['contenedor_facturas'], 
								$_REQUEST['contenedor_retenciones'],
								$_REQUEST['proveedores'],
								$_REQUEST['unidad_ejecutora'],
								$_REQUEST['descripcion'],
								$_REQUEST['nrodoccomp'],
								$_POST['banco'],
								$_POST['nro_cuenta'],
								$_REQUEST['id_plan_cuenta'],
								guardaFloat($_REQUEST['montoAnticipo'])
								);
								
	}
	
	#ACCION DE APROBAR LA ORDEN DE PAGO#
	elseif($accion == 'Aprobar'){
		if($_POST['tipsol_si']==0)
			$montoDoc= $_POST['monto_causar'];
		else
			$montoDoc= $_POST['monto_si'];
		
		$oOrdenPago->aprobar($conn, 
									$_REQUEST['nrodoc'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									//date("Y"),
									'2007',
									$_POST['descripcion'],
									$_POST['nrorefcomp'],
									guardafecha($_POST['fecha']),
									'2',
									$_POST['proveedores'],
									$_REQUEST['contenedor_partidas'],
									$montoDoc,
									$_POST['totalra'],
									$escEnEje
									);
	}

	#ACCION DE ACTUALIZAR LA SOLICITUD DE PAGO#
	elseif($accion == 'Actualizar'){
	
		$oOrdenPago->set($conn, 
								$_POST['nrodoc'], 
								$_POST['nroref'],
								guardafecha($_POST['fecha']),
								$_POST['status'],
								$_POST['cond_pago'],
								$_POST['finan'],
								$_POST['tipsol_si'],
								guardaFloat($_POST['monto_si']),
								$_REQUEST['contenedor_partidas'],
								$_REQUEST['contenedor_facturas'], 
								$_REQUEST['contenedor_retenciones'],
								$_REQUEST['proveedores'],
								$_REQUEST['unidad_ejecutora'],
								$_REQUEST['descripcion'],
								$_REQUEST['nrodoccomp'],
								$_POST['banco'],
								$_POST['nro_cuenta'],
								$_REQUEST['id_plan_cuenta'],
								guardaFloat($_REQUEST['montoAnticipo'])
								);
	}
	
	#ACCION DE ELIMINAR LA SOLICITUD DE PAGO#
	elseif($accion == 'del'){
		
		$oOrdenPago->del($conn, $_POST['id']);
		
	}elseif($accion =='Anular'){
		if($_POST['tipsol_si']==0)
			$montoDoc= $_POST['monto_causar'];
		else
			$montoDoc= $_POST['monto_si'];
		$oOrdenPago->anular($conn, 
									$_REQUEST['nrodoc'], 
									$usuario->id,
									$_POST['unidad_ejecutora'],
									//date("Y"),
									'2007',
									$_POST['descripcion'],
									'014',
									$_POST['nroref'],
									guardafecha($_POST['fecha']),
									$_REQUEST['status_old'],
									$_POST['proveedores'],
									$_REQUEST['contenedor_partidas'],
									$montoDoc,
									$_POST['totalra'],
									$_POST['motivo'],
									$escEnEje);
		
	}
	
	#LLENO LA VARIABLE CON EL MENSAJE DE LA OPERACION REALIZADA#
	$msj = $oOrdenPago->msj; 
	
	#ESTE EL LA CABECERA DE LA PAGINA#
	require ("comun/header.php");
?>
<? if(!empty($msj)){ ?><div id="msj" style="display:'';"><?=$msj?></div><br /><? }elseif(empty($msj)){ ?><div id="msj" style="display:none;"></div><br /><? }?>
<br />
<script type="text/javascript">var mygridfac,i=0, iret=0, ipp=0</script>
<span class="titulo_maestro">Orden de Pago</span>
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
			<td>Status de Documento</td>
		</tr>
		<tr>
			<td><input style="width:100px" type="text" name="busca_nrodoc" id="busca_nrodoc" /></td>
			<td><select name="busca_status" id="busca_status">
					<option value="0">Seleccione</option>
					<option value="1">Registrada</option>
					<option value="2">Aprobada</option>
					<option value="3">Anulada</option>
				</select> 
		</tr>
		<tr>
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

function Motivo(valor){
		if($('motivo').value==''){
			alert('Debe escribir el motivo por el cual anula la Orden de Pago');
			$('motivo').focus();
			return false
		} else {
			GuardarPP(); 
			actapr(valor);
			return true;
		}
	}	


function CargarGridPP(id,nroref){
	
	mygridpp.clearSelection();
	mygridpp.clearAll();
	//var idpc_iva = <?= $idpc_iva?>;
	
	var url = 'json.php';
	var pars = 'op=ordenpago&id='+ id;
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				var monto_causar=0;
				var baseImp = 0;
				var montoImp = 0;
				var JsonData = eval( '(' + request.responseText + ')');
				
				var IdParCat = new Array;
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						
						IdParCat[j] = new Array;
						var monto = parseFloat(JsonData[j]['monto']);
						//alert(monto);
						monto = monto.toFixed(2);
						
						mygridpp.getCombo(0).put(JsonData[j]['id_categoria_programatica'],JsonData[j]['categoria_programatica']);
						mygridpp.getCombo(1).put(JsonData[j]['id_partida_presupuestaria'],JsonData[j]['partida_presupuestaria']);
						mygridpp.addRow(JsonData[j]['idParCat'],JsonData[j]['id_categoria_programatica']+";"+JsonData[j]['id_partida_presupuestaria']+";"+muestraFloat(monto));
						IdParCat[j][0] = JsonData[j]['idParCat'];
						
						if(JsonData[j]['id_partida_presupuestaria']!='4031801000000') 
							baseImp = baseImp + parseFloat(JsonData[j]['monto']);
						else
							montoImp = montoImp + parseFloat(JsonData[j]['monto']);
						monto_causar = monto_causar + parseFloat(JsonData[j]['monto']);
						ipp++;
					}
					
					$('monto_causar').value = muestraFloat(monto_causar.toFixed(2));
					$('baseImp').value = muestraFloat(baseImp);
					$('montoImp').value = muestraFloat(montoImp);			
				}
			}
		}
	);
	
	//Para cargar el grid de facturas	
	mygridfac.clearSelection();
	mygridfac.clearAll();
	
	var url = 'json.php';
	var pars = 'op=facturassolicitud&nrodoc='+ id;
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
						
						mygridfac.addRow(j,Facturas[j]['nrofac']+","+Facturas[j]['nrocontrol']+","+Facturas[j]['fechafac']+","+Facturas[j]['montofac']+","+Facturas[j]['descuento']+","+Facturas[j]['monto_excento']+","+Facturas[j]['id_retencion']+","+Facturas[j]['iva']+","+Facturas[j]['base_imponible']+","+Facturas[j]['monto_iva']+","+Facturas[j]['iva_retenido'],j);
						mygridret.getCombo(0).put(Facturas[j]['nrofac'],Facturas[j]['nrofac']);
					}
					sumaTotalFacturas();
				}
			}
		});
	
	
	
	var url = 'json.php';
	var pars = 'op=ordenpagototales&id='+ nroref;
	var Request = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onLoading:function(request){}, 
			onComplete:function(request){
				
				var JsonData = eval( '(' + request.responseText + ')');
				//alert(JsonData);
				var IdParCat = new Array;
				
				if(JsonData){
					for(var j=0;j<JsonData.length;j++){
						var transito = (JsonData[j]['totTransito'] - JsonData[j]['totCausado']);
						$('compromiso').value = muestraFloat(JsonData[j]['totCompromiso']);
						$('causado').value = muestraFloat(JsonData[j]['totCausado']);
						$('disponibilidad').value = muestraFloat(JsonData[j]['totCompromiso']-JsonData[j]['totCausado']); 	
						$('transito').value = muestraFloat(transito.toFixed(2));
						
						
					}
				
								
				}
			}
		}
	);
	
	var tipo_doc = nroref.substr(0,3);
	if(tipo_doc == '010'){
		Effect.toggle('retNomina', 'blind');
		var url = 'json.php';
		var pars = 'op=aportesNomina&id='+ nroref;
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading: function(request){},
				onComplete: function(request){
					var JsonData = eval( '(' + request.responseText + ')');
					if(JsonData){
						$('aporteNomina').value = muestraFloat(JsonData);
					} else {
						$('aporteNomina').value = '0,00';
					}
				}
			}
		);
		
		$('nrodoccomp').value = nroref;
		var url = 'json.php';
		var pars = 'op=retencion_nomina&id='+ nroref;
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading:function(request){}, 
				onComplete:function(request){
					
					var JsonData = eval( '(' + request.responseText + ')');
					var IdParCat = new Array;
					
					if(JsonData){
						for(var j=0;j<JsonData.length;j++){
							mygridretno.addRow(j,JsonData[j]['id']+";"+muestraFloat(JsonData[j]['monto']));	
							
							
						}
						sumaMontoTotalRetencionesNom();
						//$('aporteNomina').value = '0,00';
					}
				}
			}
		);
	}else{
		if(tipo_doc == '011' || tipo_doc == '012' || tipo_doc == '001' || tipo_doc == '002' || tipo_doc == '009')
			Effect.toggle('anticipo', 'blind');
		$('aporteNomina').value = '0,00';
		//sumaMontoTotalRetencionesNom();	
		$('totalranom').value = '0,00';
		getMonto();
	}
	 
	} 

//AGREGA UNA FILA EN EL GRID DE FACTURAS
function Agregar(){
	mygridfac.addRow(i,",,,,,,,0,,,");
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
			//alert('((' + parseFloat(mygridfac.cells(rowId,'4').getValue()) + '-' + parseFloat(mygridfac.cells(rowId,'6').getValue()) + ')' +' * '+ '100)/(100 + ' + parseFloat(mygridfac.cells(rowId,'3').getValue())+')');
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
	total_sol();
}

function AgregarRET(){
	if($('tipsol_si').value==0){
		mygridret.addRow(iret,"0;0;;0;;;0;");
	}else{
		mygridret.addRow(iret,"0;0;;0;;;0;");
	}
	iret++;
}

//ELIMINAR UNA FILA EN EL GRID DE FACTURAS//
function EliminarRET(){
	mygridret.deleteRow(mygridret.getSelectedId());
	sumaMontoTotalRetenciones();
}

function calcularMontoRetenciones(rowId,cellInd){
	if(cellInd=='1' || cellInd=='3'){
		
		//CALCULO DEL MONTO RETENCIONES//
		var r = 0;
		var porc = 0;
		var aux = 0;
		var calporcentaje = parseFloat(mygridret.cells(rowId,'1').getValue()) / 100;
		if(mygridret.cells(rowId,'3').getValue() == 75 || mygridret.cells(rowId,'3').getValue() == 100){
			aux = usaFloat(mygridret.cells(rowId,'2').getValue()) * parseFloat(calporcentaje);
			porc = mygridret.cells(rowId,'3').getValue() / 100;
			r = aux * porc;
			r = isNaN(r) ? '0' : r.toFixed(2);
			mygridret.cells(rowId,'4').setValue(r);
			sumaMontoTotalRetenciones();
		}else{
			alert('El porcentaje a retener es incorrecto');
			mygridret.cells(rowId,'4').setValue('0');
		}
	}
	
	
}
var entroCombo = 0;
	function busca_porc_ret(stage,rowId,cellInd){
		if(cellInd==0 && stage == 2){
			for(j=0;j<mygridfac.getRowsNum();j++){
				if(mygridfac.getRowIndex(j)!=-1){
					if(mygridret.cells(rowId,'0').getValue()==mygridfac.cells(j,0).getValue()){
						mygridret.cells(rowId,3).setValue(muestraFloat(mygridfac.cells(j,8).getValue()));
						return;
					}
				}
			}
		}
		
		if(cellInd==1 && stage == 2 && entroCombo==0) {
			entroCombo++;
			var url = 'json.php';
			var pars = 'op=busca_porc&id='+ mygridret.cells(rowId,1).getValue();
			var Request = new Ajax.Request(
				url,
				{
					method: 'get',
					parameters: pars,
					onLoading:function(request){}, 
					onComplete:function(request){
						
						var JsonData = eval( '(' + request.responseText + ')');
						mygridret.cells(rowId,'2').setValue(JsonData.porcentaje);
						mygridret.cells(rowId,'4').setValue(JsonData.sustraendo);
						var r = 0;
						var porc = 0;
						var aux = 0;
						var calporcentaje = parseFloat(mygridret.cells(rowId,'2').getValue()) / 100;
						//alert(calporcentaje);
						aux = usaFloat(mygridret.cells(rowId,'3').getValue()) * calporcentaje;
						//alert(aux);
						//porc = mygridret.cells(rowId,'4').getValue() / 100;
						r = aux; 
						r = isNaN(r) ? '0' : r.toFixed(2);
						mygridret.cells(rowId,'6').setValue(r);
						sumaMontoTotalRetenciones();
					}
				}
					
			);
			
		}else if(cellInd==1 && stage == 2){
			entroCombo= (entroCombo+1)%3;
		}
					
	}

function sumaMontoTotalRetenciones(){

	var total = 0;
	
	for(j=0;j<mygridret.getRowsNum();j++){
		if(mygridret.getRowIndex(j)!=-1){
			total += parseFloat(mygridret.cells(j,6).getValue()); 
			
		}
	}
	$('totalrafac').value = muestraFloat(total.toFixed(2));
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
	
	for(j=0;j<mygridpp.getRowsNum();j++){
		if(!isNaN(mygridpp.getRowId(j))){
		
			PPAux[j] = new Array;
			PPAux[j][0]= mygridpp.cells(mygridpp.getRowId(j),0).getValue();
			PPAux[j][1]= mygridpp.cells(mygridpp.getRowId(j),1).getValue();
			PPAux[j][2]= mygridpp.cells(mygridpp.getRowId(j),2).getValue();
			PPAux[j][3]= mygridpp.getRowId(j);
			
			
		}
	}
	JsonAux={"partidaspresupuestarias":PPAux};
	$("contenedor_partidas").value=JsonAux.toJSONString();
		
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

function GuardarRET(){
var JsonAux,RETAux=new Array;
	mygridret.clearSelection()
	//alert('filas: ' + mygridret.getRowsNum());
	for(j=0;j<mygridret.getRowsNum();j++){
		if(!isNaN(mygridret.getRowId(j))){
			RETAux[j] = new Array;
			RETAux[j][0]= mygridret.cells(mygridret.getRowId(j),0).getValue();
			RETAux[j][1]= mygridret.cells(mygridret.getRowId(j),1).getValue();
			RETAux[j][2]= mygridret.cells(mygridret.getRowId(j),2).getValue();
			RETAux[j][3]= mygridret.cells(mygridret.getRowId(j),3).getValue();
			RETAux[j][4]= mygridret.cells(mygridret.getRowId(j),4).getValue();
			RETAux[j][5]= mygridret.cells(mygridret.getRowId(j),5).getValue();
			RETAux[j][6]= mygridret.cells(mygridret.getRowId(j),6).getValue();
			
		}
	}
	
	JsonAux={"retenciones":RETAux};
	$("contenedor_retenciones").value=JsonAux.toJSONString();
		
}

function actapr(elemento){
	if((parseFloat($('monto_causar').value) <= 0) && (parseFloat($('monto_si').value) <= 0)){
		alert('Debe Existir un monto para poder crear la Orden de Pago');
		return false;
	}else{
		if(elemento.value == 'Guardar')
			$('accion').value = 'Guardar';
		else if(elemento.value == 'Actualizar')
			$('accion').value = 'Actualizar';
		else if(elemento.value == 'Aprobar')
			$('accion').value = 'Aprobar';
		else
			$('accion').value = 'Anular'; 
		 validate(); 
	}
		
}

/* Metodos utilizados en el buscador */
function busca(id_ue, id_proveedor, descripcion, fecha_desde, fecha_hasta, nrodoc,status, pagina){
	var url = 'updater_busca_orden_pago.php';
	var pars = '&id_ue=' + id_ue + '&id_proveedor=' + id_proveedor+ '&descripcion=' + descripcion;
	pars += '&nrodoc=' + nrodoc + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta + '&status=' + status + '&pagina=' + pagina;
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
	$F('busca_nrodoc'),
	$F('busca_status'), 1); 
});
Event.observe('busca_proveedores', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_status'), 1); 
});
Event.observe('busca_descripcion', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_status'), 1); 
});
Event.observe('busca_nrodoc', "keyup", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_status'), 1); 
	});
Event.observe('busca_status', "change", function () { 
	busca($F('busca_ue'), 
	$F('busca_proveedores'), 
	$F('busca_descripcion'), 
	$F('busca_fecha_desde'), 
	$F('busca_fecha_hasta'),
	$F('busca_nrodoc'),
	$F('busca_status'), 1); 
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
			$F('busca_nrodoc'),
			$F('busca_status'), 1); 
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
	var pars = 'op=solpag&nrodoc=' + nrodoc;
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get', 
			parameters: pars,
			onComplete: function(peticion){
				var jsonData = eval('(' + peticion.responseText + ')');
				if (jsonData == undefined) { return }
				$('descripcion').value = jsonData.descripcion;
				$('unidad_ejecutora').value = jsonData.id_unidad_ejecutora;
				$('proveedor').value = jsonData.proveedor;
				
				$('tipo_contribuyente').value = jsonData.tipo_contribuyente;
				$('ingreso_periodo_fiscal').value = !isNaN(parseFloat(jsonData.ingreso_periodo_fiscal)) ? muestraFloat(jsonData.ingreso_periodo_fiscal) : '0,00';
				//$('tipdoc').value = jsonData.tipdoc + ' - ' + jsonData.tipo_documento;
				$('proveedores').value = jsonData.id_proveedor;
				//$('idtipodoc').value = jsonData.tipdoc;
				//$('unidad_ejecutora').disabled = true;
				$('buscadorpro').style.display = 'none';
				
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
	 
	var url = 'buscar_solicitudes.php';
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
	var url = 'buscar_solicitudes.php';
	var pars = 'id_ue='+$('search_ue').value+ '&id_prov='+ $('search_prov').value +'&nrodoc='+$('search_nrodoc').value+'&opcion=2&ms'+new Date().getTime();
		
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
		var total=0;
		var totalrafac = isNaN(usaFloat($('totalrafac').value)) ? 0 : usaFloat($('totalrafac').value);
		var totalranom = isNaN(usaFloat($('totalranom').value)) ? 0 : usaFloat($('totalranom').value);
		var aporteNomina = isNaN(usaFloat($('aporteNomina').value)) ? 0 : usaFloat($('aporteNomina').value);
		var totalAnticipo = isNaN(usaFloat($('montoAnticipo').value)) ? 0 : usaFloat($('montoAnticipo').value);
		var ivaRet = isNaN(usaFloat($('ivaRet').value)) ? 0 : usaFloat($('ivaRet').value);
		//var totRet = usaFloat($('totalrafac').value) + usaFloat($('totalranom').value) + usaFloat($('aporteNomina').value);
		var totRet = totalrafac + totalranom + aporteNomina + totalAnticipo + ivaRet;
		$('totalra').value = muestraFloat(totRet)
		//alert('ret: ' + totRet);
		if($('tipsol_si').value==0){
			var monto_causar = isNaN(usaFloat($('monto_causar').value)) ? 0 : usaFloat($('monto_causar').value);
			total = monto_causar - usaFloat($('totalra').value); 
		} else {
			total = usaFloat($('monto_si').value) - usaFloat($('totalra').value);
		}
		//alert('tot: '+ total);
		$('total_soli').value = muestraFloat(total);
	}
	
	function checkSustraendo(rowId, cellInd, checked)
	{
		var monto_ret;
		var sustraendo;
		var retencion;
		monto_ret = parseFloat(mygridret.cells(rowId, 6).getValue());
		sustraendo = parseFloat(mygridret.cells(rowId, 4).getValue());
		if (!isNaN(monto_ret) && !isNaN(sustraendo))
		{
			if (checked == 1)
			{
				if (monto_ret >= sustraendo)
				{
					retencion = monto_ret - sustraendo;
					mygridret.cells(rowId, 6).setValue(retencion.toFixed(2));
					
				
				}
				else
				{
					mygridret.cells(rowId, 6).setValue('0');
				}
			}
			else
			{
				var r = 0;
				var porc = 0;
				var aux = 0;
				var calporcentaje = parseFloat(mygridret.cells(rowId,'2').getValue()) / 100;
				aux = usaFloat(mygridret.cells(rowId,'3').getValue()) * parseFloat(calporcentaje);
				//porc = mygridret.cells(rowId,'3').getValue() / 100;
				r = aux;
				r = isNaN(r) ? '0' : r.toFixed(2);
				mygridret.cells(rowId,'6').setValue(r);
						
			}
			sumaMontoTotalRetenciones();
		}
	}
	
	function mostrar_ventana_prov(){
	//var tipo = "('P', 'A', 'B', 'C')";
	 
	var url = 'buscar_proveedores_orden.php';
	var pars = 'tipo=&ms='+new Date().getTime();
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
	
	function selDocumento_prov(id, nombre, tipo_contribuyente, ingreso_fiscal){
	//alert(id+','+ nombre+','+ tipo_contribuyente+','+ ingreso_fiscal);
	$('proveedor').value = nombre;
	$('proveedores').value = id;
	$('tipo_contribuyente').value = tipo_contribuyente;
	$('ingreso_periodo_fiscal').value = ingreso_fiscal;
	//$('unidad_ejecutora').disabled = true;
	$('buscadorsp').style.display = 'none';
	Dialog.okCallback();

}

	function busca_popup_prov()
{
	clearTimeout(t);
	t = setTimeout("buscaProveedor()", 800);
}

	function buscaProveedor()
{
	//var tipo;
	//	tipo = "('P','A','B','C')";

	var url = 'buscar_proveedores_orden.php';
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

	function sumaMontoTotalRetencionesNom(){

	var total = 0;
	
	for(j=0;j<mygridretno.getRowsNum();j++){
		if(mygridretno.getRowIndex(j)!=-1){
			total += usaFloat(mygridretno.cells(j,1).getValue()); 
			
		}
	}
	$('totalranom').value = muestraFloat(total.toFixed(2));
	total_sol();
	
}
function getMonto() {
 	
  clearTimeout(t); 
	t = setTimeout("total_sol()", 7500); 
};

	function muestraRetAnt(){
		Effect.toggle('anticipo', 'blind');
	}
	
	
	var oldNumFact = '';
	function num_fact(stage,rowId,cellInd) {
	
		if((stage==0) && (rowId==0)){
			oldNumFact = mygridfac.cells(rowId,0).getValue();
		}
		 if ((stage==2) && (cellInd==0)){
			if(mygridfac.cells(rowId,0).getValue()==''){
				alert('El numero de factura no puede estar vacio');
				mygridfac.cells(rowId,0).setValue(oldNumFact.toString());
				return false;
			} else {
				for(j=0;j<mygridfac.getRowsNum();j++){
					//alert('j: ' + j + ' rowId '+ rowId);
					if((j!==rowId)){
						//alert('entro');
						if(mygridfac.cells(rowId,0).getValue() == mygridfac.cells(j,0).getValue()){
							alert('El numero de factura ya existe')
							mygridfac.cells(rowId,0).setValue(oldNumFact.toString());
							return false;
						}
					}
				}	
				cargar_combo_ret_fact();
			}	
		}
		
	}
	
	function cargar_combo_ret_fact(){
		//mygridret.clearcombobox()
		mygridret.getCombo(0).put('','Seleccione');
		for(i=0;j<mygridfac.getRowsNum();i++){
			mygridret.getCombo(0).put(i,mygridfac.cells(i,0).getValue())
		}
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
							
							r = ((parseFloat(mygridfac.cells(rowId,'3').getValue()) - parseFloat(mygridfac.cells(rowId,'5').getValue())) * 100 ) / (100 + parseFloat(JsonData.porcentaje));
							//alert('((' + parseFloat(mygridfac.cells(rowId,'4').getValue()) + '-' + parseFloat(mygridfac.cells(rowId,'6').getValue()) + ')' +' * '+ '100)/(100 + ' + parseFloat(mygridfac.cells(rowId,'3').getValue())+')');
							r = isNaN(r) ? '0' : r.toFixed(2);
							mygridfac.cells(rowId,'8').setValue(r);
							
							//CALCULO DEL MONTO IVA//
							r = 0;
							r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  (parseFloat(JsonData.porcentaje) / 100);
							r = isNaN(r) ? '0' : r.toFixed(2);
							mygridfac.cells(rowId,'9').setValue(r);	
							//CALCULAR MONTO IVA RETENCIONES//
							r = 0;
							r = parseFloat(mygridfac.cells(rowId,'8').getValue()) *  ( parseFloat(JsonData.porcentaje) / 100) * parseFloat(JsonData.porcRet) / 100;
							r = isNaN(r) ? '0' : r.toFixed(2);
							mygridfac.cells(rowId,'10').setValue(r);
							sumaTotalFacturas();
					}
				}
					
			);
			}
		}/*else if((cellInd==3 || cellInd==4) && stage == 2 && entroComboFact==1){
			entroComboFact= (entroCombo+1)%3;
		}*/
					
	}
	
	//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
function traeCuentasBancarias(id_banco, div, id_cuenta,disabled){
	var url = 'updater_selects.php';
	var pars = 'combo=cuentas_bancarias4&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta + '&disabled=' + disabled +'&ms='+new Date().getTime();
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
	
</script>

<?
//$validator->create_message("error_nroref", "nroref", "*");
$validator->create_message("error_unidad_ejecutora", "unidad_ejecutora", "*");
$validator->create_message("error_cpago", "cond_pago", "*");
$validator->create_message("error_finan", "finan", "*");
$validator->print_script();
require ("comun/footer.php");
?>

