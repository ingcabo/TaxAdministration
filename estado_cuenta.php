<?
require ("comun/ini.php");
// Creando el objeto Proveedores
$oEstadoCuenta = new estadoCuenta;
//die($_REQUEST['accion']);
$accion = $_REQUEST['accion'];
$json_det = $_REQUEST['json_det'];


if($accion == 'Guardar')
{

	$msj = $oEstadoCuenta->add($conn, $_REQUEST['bancos'], $_REQUEST['nro_cuenta'],guardaFecha($_REQUEST['fecha_desde']), guardaFecha($_REQUEST['fecha_hasta']), guardafloat($_REQUEST['saldo_ini_ban']), guardafloat($_REQUEST['saldo_final']), $json_det);
	if($msj===true)
		$msj = REG_ADD_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'Actualizar')
{
	$msj = $oEstadoCuenta->set($conn, $_REQUEST['id'], $_REQUEST['bancos'], $_REQUEST['nro_cuenta'],$_REQUEST['fecha_desde'], $_REQUEST['fecha_hasta'], guardafloat($_REQUEST['saldo_ini_ban']), guardafloat($_REQUEST['saldo_final']), $json_det);
	if ($msj === true)
		$msj = REG_SET_OK;
	else
		$msj = ERROR;
}
elseif($accion == 'del')
{
	$msj = $oEstadoCuenta->del($conn, $_REQUEST['id']);
	if($msj===true)
		$msj = REG_DEL_OK;
	else
		$msj = ERROR;
}



require ("comun/header.php");
?>
<div id="msj" <?=empty($msj) ? 'style="display:none"':''?>><?=$msj?></div><br />
<br />
<span class="titulo_maestro">Estado de Cuenta:</span>
<div id="formulario">
<a href="#" onclick="updater(0); return false;">Agregar Nuevo Registro</a>
</div>
<br />
<br />
<div>
<fieldset id="buscador">
	<legend>Buscar:</legend>
	<table width="350px">
		<tr>
			<td>Cuenta Bancaria</td>
		</tr>
		<tr>
			<td>
			<?=helpers::superCombo($conn,
											"SELECT A.id, (CASE WHEN B.descripcion is null THEN A.nro_cuenta ELSE A.nro_cuenta||' - '||B.descripcion END)  AS descripcion FROM finanzas.cuentas_bancarias AS A LEFT JOIN puser.clasificacion_cuenta AS B ON A.id_clasificacion_cuenta=B.id ORDER BY A.nro_cuenta::text",
											'', 
											'cta_banc', 
											'cta_banc',
											'',
											'buscador()',
											'id',
											'descripcion',
											'',
											'',
											'');
			?>
			</td>
		</tr>
		<tr>
			<td>Desde:</td>
			<td>Hasta:</td>
		<tr>
			<td style="width:150">
				<input style="width:100px"  type="text" name="busca_fecha_desde" id="busca_fecha_desde" onchange="validarRangoFechas(this);"/>
				<a href="#" id="boton_busca_fecha_desde" onclick="return false;">
					<img border="0" alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
				<script type="text/javascript">
					new Zapatec.Calendar.setup
					({
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
				<input style="width:100px"  type="text" name="busca_fecha_hasta" id="busca_fecha_hasta" onchange="validarRangoFechas(this);"/>
				<a href="#" id="boton_busca_fecha_hasta" onclick="return false;">
					<img border="0" alt="Seleccionar Fecha" src="images/calendarA.png" width="20" height="20" />
				</a>  
				<script type="text/javascript">
					new Zapatec.Calendar.setup
					({
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
<div style="margin-bottom:10px" id="busqueda"><div>
<br />
<br />
<div style="height:40px;padding-top:10px;">
<p id="cargando" style="display:none;margin-top:0px;">
  <img alt="Cargando" src="images/loading.gif" /> Cargando...
</p>
</div>

<script type="text/javascript">
	var iEc=0;
	var saldo; 
	var creditoOld; 
	var debitoOld;
	var j;
	
	
	function validarRangoFechas(fecha)
	{
		var valido = validafecha(fecha);
		if (valido == 0)
		{
			fecha_desde = new Date();
			fecha_hasta = new Date();
			if ($('busca_fecha_desde').value!='' && $('busca_fecha_hasta').value!='')
			{
				fecha_desde.setDate($('busca_fecha_desde').value.substr(0,2));
				fecha_desde.setMonth(parseInt($('busca_fecha_desde').value.substr(3,2))-1);
				fecha_desde.setFullYear($('busca_fecha_desde').value.substr(6,4));

				fecha_hasta.setDate($('busca_fecha_hasta').value.substr(0,2));
				fecha_hasta.setMonth(parseInt($('busca_fecha_hasta').value.substr(3,2))-1);
				fecha_hasta.setFullYear($('busca_fecha_hasta').value.substr(6,4));
				
				if (fecha_desde.getTime() > fecha_hasta.getTime())
					valido = -2;
			}

			if (valido==0)
			{
				busca($('cta_banc').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
				return true;
			}
		}

		if (valido == -1)
		{
			alert("Fecha Incorrecta");
			$(fecha).value = "";
			return false;
		}
		else if (valido == -2)
		{
			alert("Rango de fechas invalido");
			$(fecha).value = "";
			return false;
		}
	}
	
	function validafecha(fecha)
	{
		var upper = 31;
		if(/^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha.value)) 
		{ // dd/mm/yyyy
			if(RegExp.$2 == '02')
			{
				upper = 28;
				if (!(RegExp.$3%4) && ((RegExp.$3%100) || !(RegExp.$3%400)))
					upper = 29;
			}
				
			if((RegExp.$1 > upper) || (RegExp.$2 > 12)) 
				return -1;
		}
		else if(fecha.value != '') 
			return -1;
			
		return 0;
	}

	function buscador()
	{
		busca($('cta_banc').value, $('busca_fecha_desde').value, $('busca_fecha_hasta').value, 1);
	}
	
	function busca(id_cta, fecha_desde, fecha_hasta, pagina)
	{
		var url = 'updater_busca_estado_cuenta.php';
		var pars = '&id_cuenta=' + id_cta + '&fecha_desde=' + fecha_desde+ '&fecha_hasta=' + fecha_hasta +'&ms='+new Date().getTime()+ '&pagina='+pagina;
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

	//FUNCION QUE TRAE LAS CUENTAS BANCARIAS AL MOMENTO DE SELECCIONAR UN BANCOS//
	function traeCuentasBancarias(id_banco, div, id_cuenta, disabled)
	{
		var url = 'updater_selects.php';
		var pars = 'combo=cuentas_bancarias&id_banco=' + id_banco + '&id_cuenta=' + id_cuenta +'&onchange=llamarSaldo(this.value)&disabled='+disabled +'&conciliacion=Si&ms='+new Date().getTime();
		var updater = new Ajax.Updater(div, 
			url,
			{
				method: 'get',
				parameters: pars,
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request){if (!id_cuenta)Element.show('cargando_cuentas')}, 
				onComplete:function(request)
				{
					Element.hide('cargando_cuentas');
					$('fecha_desde').value = '';
					$('fecha_hasta').value = '';
					$('saldo_ini_ban').value = '0,00';
					$('saldo_fin_ban').value = '0,00';
					$('saldo_ini_lib').value = '0,00';
					$('saldo_fin_lib').value = '0,00';
					$('saldo_conc').value = '0,00';
					$('saldo_trans').value = '0,00';
					mygrid.clearAll();
					mygrid.clearSelection();
					Element.hide('msj');
				}
			}); 
	}
	
	function calculaMovimientos(stage, rowId, cellInd)
	{
		
		
		if(stage==0  && (cellInd == 3 || cellInd == 4 )) {
			//alert('entro');
			var valor = usaFloat(mygrid.cells(rowId,4).getValue());
			creditoOld = valor.toFixed(2);
			//alert("debito " + usaFloat(mygrid.cells(rowId,3).getValue()));
			var valor = usaFloat(mygrid.cells(rowId,3).getValue());
			debitoOld = valor.toFixed(2);
			//alert(debitoOld);	
			//alert('cred old ' + creditoOld + ' deb old ' + debitoOld);		
			if (cellInd == 3)
				mygrid.cells(rowId,3).setValue(debitoOld.toString());	
			else
				mygrid.cells(rowId,4).setValue(creditoOld.toString());
		}
		
		else if(stage==1 && cellInd==5)
		{ //antes
			if(confirm(String.fromCharCode(191) + "Esta seguro que desea eliminar este asiento?"))
			{
				var debitoElimina;
				var creditoElimina;
				var rowId = mygrid.getSelectedId();
				debitoElimina = usaFloat(mygrid.cells(rowId,3).getValue());
				creditoElimina = usaFloat(mygrid.cells(rowId,4).getValue());
				//alert("debito: " + debitoElimina + " credito: "+ creditoElimina);
				calculaSaldo(4, debitoElimina.toFixed(2), creditoElimina.toFixed(2));
				mygrid.deleteSelectedItem(); //eliminar la fila seleccionada
				mygrid.clearSelection();
			}
		}
		else if(stage==2 && (cellInd == 3 || cellInd == 4 )){	
			if (mygrid.cells(rowId,3).getValue()!='0,00' && mygrid.cells(rowId,4).getValue()!='0,00')
			{
				alert("Los valores de Debito y Credito no pueden contener valores distintos de cero al mismo tiempo.");
				mygrid.cells(rowId,cellInd).setValue('0,00');
				return false;
				
				if (cellInd == 4)
					mygrid.cells(rowId,3).setValue('0,00');
				else
					mygrid.cells(rowId,4).setValue('0,00');
			}
			
			//alert('debito ' + debitoOld + ' credito ' + creditoOld);
			var debito = usaFloat(mygrid.cells(rowId, 3).getValue());
			//alert(debito);
			
			if (isNaN(debito)){
				mygrid.cells(rowId,3).setValue(muestraFloat(debitoOld));
				debito = 0;
			} else {
				mygrid.cells(rowId,3).setValue(muestraFloat(debito))
				calculaSaldo(2,debito, debitoOld);	
				
			}
			var credito = usaFloat(mygrid.cells(rowId, 4).getValue());
			//alert(creditoOld);
			if (isNaN(credito)){
				mygrid.cells(rowId,4).setValue(muestraFloat(creditoOld));
				credito = 0;
			} else {
				mygrid.cells(rowId,4).setValue(muestraFloat(credito))
				calculaSaldo(3,credito, creditoOld);	
			}
		}
	}
	
	function calculaSaldo(valor, movimiento, montoOld){
		var saldo = 0;
		//alert('movimiento ' + movimiento + 'old ' + montoOld);
		//return false;
		//alert("saldo final: "+$('saldo_final').value);
		auxSaldo = usaFloat($('saldo_final').value);
		saldo = parseFloat(auxSaldo.toFixed(2));
		//alert("saldo: "+saldo);
		if(isNaN(saldo))
			saldo = 0;
		if(valor==1){
			var sumDebito = 0;
			var sumCredito = 0;
			//det = [];
			for(j=0;j<mygrid.getRowsNum();j++)
			{
				if(!isNaN(mygrid.getRowId(j)))
				{
					rowId = mygrid.getRowId(j);
					//det.push(rowId)
					auxDebito = usaFloat(mygrid.cells(rowId,3).getValue());
					auxCredito = usaFloat(mygrid.cells(rowId,4).getValue());
					sumDebito = sumDebito + auxDebito.toFixed(2);
					sumCredito = sumCredito + auxCredito.toFixed(2);
				}
			}
			//alert(det);
			//return false;
			//alert('monto: ' + usaFloat($('saldo_ini_ban').value) + '-' + sumDebito.toFixed(2) + '+' + sumCredito.toFixed(2));
			//return;
			saldo = usaFloat($('saldo_ini_ban').value) - parseFloat(sumDebito) + parseFloat(sumCredito);
		}else if(valor==2){
			//alert(montoOld);
			//alert('saldo: '+saldo+' monto: '+montoOld.toFixed(2));
			saldo = saldo  + parseFloat(montoOld);
			//alert('saldo1 :'+saldo);
			//alert('saldo: '+saldo+' movimiento: '+movimiento.toFixed(2));
			saldo = saldo - parseFloat(movimiento); 
			//alert('saldo2: '+saldo);
			
		}else if(valor==3){
			//alert('saldo: '+saldo+' monto: '+montoOld.toFixed(2));
			saldo = saldo - parseFloat(montoOld);
			//alert('saldo1 :'+saldo);
			//alert('saldo: '+saldo+' movimiento: '+movimiento.toFixed(2));
			saldo = saldo + parseFloat(movimiento);
			//alert('saldo2: '+saldo);
		}
		// Para esta parte de la funcion se utilizan estas variables para no hacer una nueva funcion la conclusion real deberia ser esta
		// saldo = saldo + debitoOld - creditoOld
		else if(valor == 4){
			//alert('saldo ' + saldo + ' debitoOld ' + movimiento + ' creditoOld ' + creditoOld);
			saldo = saldo + parseFloat(movimiento);
			saldo = saldo - parseFloat(montoOld);
			//alert(saldo);
		
		}	
		//alert('saldo :' + saldo);
		//return;
		$('saldo_final').value = muestraFloat(saldo.toFixed(2));
	}
	
	
	
	function llamarSaldo(value)
	{
		if (value==0 || value=='0')
			return false;
		
		var url = 'json.php';
		var pars = 'op=traeFechasEstadoCta&id_cta_banc='+value+'&ms='+new Date().getTime;
		var Request = new Ajax.Request(
			url,
			{
				method: 'get',
				parameters: pars,
				onLoading: function(request){Element.show('cargando_cta');},
				onComplete: function(request)
				{
					
					jsonData = eval('(' + request.responseText + ')');
					$('fecha_desde').value = jsonData[0];
					$('fecha_hasta').value = jsonData[1];
					$('id_cta_cont').value = jsonData[2].plan_cuenta;
					var fecha_hasta = new Date();
					var hoy = new Date();
					fecha_hasta.setDate($('fecha_hasta').value.substr(0,2));
					fecha_hasta.setMonth(parseInt($('fecha_hasta').value.substr(3,2),10) - 1);
					fecha_hasta.setFullYear($('fecha_hasta').value.substr(6,4));
					
					if (fecha_hasta.getTime() > hoy.getTime())
					{
						$('msj').innerHTML = "No puede conciliar un mes que no ha culminado";
						Element.show('msj');
						Element.hide('cargando_cta');
						$('boton').disabled = true;
						return false;
					}
					else
						$('boton').disabled = false;

					Element.hide('cargando_cta');
					traerSaldo($('fecha_desde').value, 'D');
					traerSaldo($('fecha_hasta').value, 'H');
				}
			}
		);
	}
		
	
	function agregarEC()
	{
		
		if($('fecha_desde').value == ''){
			alert('Debe selccionar una cuenta para cargar el estado de cuenta');
			return false;
		}
		else if($('tipo_movimiento').value == ''){
			alert('Debe seleccionar un tipo de transaccion');
			$('tipo_movimiento').focus();
			return false;
		}
		else if($('num_documento').value == ''){
			alert('Debe Escribir el Numero del Documento');
			$('num_documento').focus();
			return false;
		}
		else if($('fec_documento').value == ''){
			alert('Debe seleccionar la fecha de la transaccion');
			$('fec_documento').focus();
			return false;
		}
		var fecDoc = $('fec_documento').value;
		var fecDesde = $('fecha_desde').value;
		var fecHasta = $('fecha_hasta').value;
		fecDoc = cambiaFormatoFecha(fecDoc);
		fecDesde = cambiaFormatoFecha(fecDesde);
		fecHasta = cambiaFormatoFecha(fecHasta);
		/*var fecDoc = $('fec_documento').value;
		var fecDesde = $('fecha_desde').value;
		var fecHasta = $('fecha_hasta').value;*/
		
		var esta = false;
		for (j=0; j<mygrid.getRowsNum() && !esta; j++){
			if ($('num_documento').value == mygrid.cells(mygrid.getRowId(j), 1).getValue()){
				esta = true;
			}
		}
		//alert("fec Doc: " +fecDoc + " fec Desde: " +fecDesde+ " fec Hasta: "+ fecHasta);		
		if (esta){
			alert("Esa numero de documento ya ha sido agregado");
			$('num_documento').focus();
			$('num_documento').value = '';
			return false;
		}
		
		else if((fecDoc<fecDesde) || (fecDoc>fecHasta)){
			alert('La fecha de la transaccion debe estar en el rango de fechas del estado de cuenta');
			$('fec_documento').focus();
			return false;
		} 
		else {
			
			//mygrid.addRow(iEc,$('tipo_movimiento').value+";"+$('num_documento').value+";"$('fec_documento').value+";0,00;0,00;images/delete.gif" );
			mygrid.addRow($('num_documento').value,$('tipo_movimiento').value+";"+$('num_documento').value+";"+$('fec_documento').value+";0,00;0,00;images/delete.gif" );
			iEc++;
		}
	}
	
	function cambiaFormatoFecha(fecha){
		var tempfec = fecha;
		trozosfec=tempfec.split("/");
		var diaini= trozosfec[0];
		var mesini=	trozosfec[1];
		var anoini= trozosfec[2];
		var fechaMod = new Date(mesini+"/"+diaini+"/"+anoini);//formato mes dia año
		fechaMod = new Date(fechaMod);
		return fechaMod;
	}
	
	
	
	function GuardarDet()
	{
		if (($('saldo_ini_ban').value == '0,00'))
		{
			alert("Los saldos no pueden estar en 0");
			return false;
		}
		
		//var JsonAux = new Array;
		var lon = mygrid.getRowsNum();
		
		if (lon == 0) 
			return false;
		//alert("longitud: "+lon);
		var JsonAux, edoCta = new Array;
		for(j=0; j<lon; j++)
		{
			rowId = mygrid.getRowId(j);
			//JsonAux.push({tipo_movimiento:mygrid.cells(rowId,0).getValue(), nro_documento:mygrid.cells(rowId,1).getValue(), fecha_documento:mygrid.cells(rowId,2).getValue(), debitos:usaFloat(mygrid.cells(rowId,3).getValue()), creditos:usaFloat(mygrid.cells(rowId,4).getValue())});
			//alert("j: "+j);
			edoCta[j] = new Array;
			edoCta[j][0]= mygrid.cells(rowId,0).getValue();
			edoCta[j][1]= mygrid.cells(rowId,1).getValue();
			edoCta[j][2]= mygrid.cells(rowId,2).getValue();
			edoCta[j][3]= usaFloat(mygrid.cells(rowId,3).getValue());
			edoCta[j][4]= usaFloat(mygrid.cells(rowId,4).getValue());
			edoCta[j][5]= rowId;		
		}
		JsonAux={"edoCta":edoCta};
		$('json_det').value = JsonAux.toJSONString();
		//return false;
		$('bancos').disabled = false;
		$('nro_cuenta').disabled = false;  
		validate();
	}
	
	function doOnRowSelected(Id, Index){
		//alert('Id ' + Id + ' Index ' + Index);
		creditoOld = usaFloat(mygrid.cells(Id,4).getValue());
		debitoOld = usaFloat(mygrid.cells(Id,3).getValue());
	}
	
	
	
	
	
</script>

<?
$validator->create_message("error_bancos", "bancos", "*");
$validator->create_message("error_nro_cuenta", "nro_cuenta", "*");
$validator->print_script();
?>
<? require ("comun/footer.php"); ?>